<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use app\models\MaterialOutputForm;
use yii\helpers\ArrayHelper;
use \yii\web\ForbiddenHttpException;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Fill;
use \PHPExcel_Writer_IWriter;
use \PHPExcel_Worksheet;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
    public function actionMaterialoutput()
    {
        if(Yii::$app->user->can('material_ouput_report')){
        $materialOut = new MaterialOutputForm();
        if ($materialOut->load(\Yii::$app->request->post()) && $materialOut->validate()) {
            $item_no = (empty($materialOut->item_no) ? NULL : $materialOut->item_no);
            $item_cat = (empty($materialOut->item_cat) ? NULL : $materialOut->item_cat);
            $location = (empty($materialOut->location) ? NULL : $materialOut->location);
            $date_from = (empty($materialOut->date_from) ? NULL : $materialOut->date_from);
            $date_to = (empty($materialOut->date_to) ? NULL : $materialOut->date_to);
//            var_dump($materialOut->location);die();
            $sqlQuery = 'SELECT a.[No_] as itemNo, a.[Metre23] as metric'
                    .' FROM [SAN LIM FURNITURE VIETNAM LTD$Item] a WITH(NoLock)'
                    .' ,[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] b WITH(NoLock)'
                    .' WHERE a.[No_]<>\'\' AND a.[No_]=b.[Item No_]'
                    .' AND b.[Entry Type] IN (1,3,5,6) AND b.[Quantity]<0'
                    .' AND b.[Item No_] LIKE COALESCE(:item_no, b.[Item No_])'
                    .' AND b.[Item Category Code] = COALESCE(:item_cat, b.[Item Category Code])'
                    .' AND b.[Location Code] = COALESCE(:location_code, b.[Location Code])'
                    .' AND b.[Posting Date] >= COALESCE(:date_from,b.[Posting Date])'
                    .' AND b.[Posting Date] <= COALESCE(:date_to,b.[Posting Date])';
            $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
            $sqlCommand->bindValue(':item_no',$item_no);
            $sqlCommand->bindValue(':item_cat',$item_cat);
            $sqlCommand->bindValue(':location_code',$location);
            $sqlCommand->bindValue(':date_from',$date_from);
            $sqlCommand->bindValue(':date_to',$date_to);
            $result = $sqlCommand->queryAll();
            $arrColumName = ['ItemNo', 'Metric'];
            $arrColumTech = ['itemNo', 'metric'];
            if(empty($result)){
                Yii::$app->session->setFlash('error', 'No data found');
            }else{
                Yii::$app->session->setFlash('success', 'Downloading report');
                $this->generateExcel($arrColumName, $arrColumTech, $result);

            }
//            var_dump($result);die();
            

        }else{
            
        }
        $sqlQueryLocation = 'SELECT Code, Name FROM [SAN LIM FURNITURE VIETNAM LTD$Location] WITH (NoLock)';
        $sqlCommandLoaction = Yii::$app->dbMS->createCommand($sqlQueryLocation);
        $location = ArrayHelper::map($sqlCommandLoaction->queryAll(), 'Code', 'Name');
        
        $sqlItemCat = 'SELECT Code, Code FROM [SAN LIM FURNITURE VIETNAM LTD$Item Category] WITH(NoLock)';
        $cmdItemCat = Yii::$app->dbMS->createCommand($sqlItemCat);
        $item_cat = ArrayHelper::map($cmdItemCat->queryAll(), 'Code', 'Code');

        return $this->render('material_output', ['material_out'=>$materialOut,
            'location'=>$location,
            'item_cat'=>$item_cat]);
        }else{
            throw new ForbiddenHttpException('You don\'t have permission on this page. Please contact Sanlim Administrator!');
        }
    }
    public function generateExcel($arrColumName, $arrColumTech, $arrData, $sheetName = 'Sheet1', $fileName = Null){
        if(empty($arrData)){
            return FALSE;
        }
        $noOfColumn = count($arrColumName);
        
        $objPHPExcel = new \PHPExcel();
        $sheet=0;

        $objPHPExcel->setActiveSheetIndex($sheet);
        $lineNo = 1;
//        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->setTitle($sheetName);
        $letterColumn = 'A';
        for($i=0; $i<$noOfColumn; $i++){
            $objPHPExcel->getActiveSheet()->setCellValue($letterColumn . $lineNo, $arrColumName[$i]);
            $letterColumn ++;
        }
        $letterColumn = 'A';
        foreach ($arrData as $row) {  
            $lineNo++;
            $letterColumn = 'A';
            for($i=0; $i<$noOfColumn; $i++){
//                var_dump($row);die();
                $objPHPExcel->getActiveSheet()->setCellValue($letterColumn.$lineNo,$row[$arrColumTech[$i]]);
                $letterColumn++;
            }
        }
        ob_end_clean();
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        if(is_null($fileName)){
            $filename = "ExcelReport_".date("d-m-Y-His").".xls";
        }
        header('Content-Disposition: attachment;filename='.$filename .' ');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}
