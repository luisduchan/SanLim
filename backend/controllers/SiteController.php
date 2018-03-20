<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\modules\customerpo\models\CustomerPo;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\POCusDetail;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup','index'],
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'test'],
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
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        POCusDetail::updatePOContainer();
                
        
        $MasterDate = 12;
        $PresenDate = date("d");
        $groups ="";
        $group ="";

        if($PresenDate <= $MasterDate){
           $group = strtoupper(date("M")).'-'.date("Y");     
           for($i=1; $i<5; $i++){
                $monthplus = mktime(0, 0, 0, date("m")+$i, date("d"),   date("Y"));
                $monthkey = strtoupper(date("M", $monthplus)).'-'.date("Y", $monthplus);
                $monthvalue = strtoupper(date("Y", $monthplus)).'/'.date("m", $monthplus);
                //$monthlist = array($monthkey=>$monthvalue);
                $monthlist[$monthkey]=$monthvalue;
           }   
        }
        else{
            $nextmonth = mktime(0, 0, 0, date("m")+1, date("d"),   date("Y"));
            $group = strtoupper(date("M", $nextmonth)).'-'.date("Y", $nextmonth);
            for($i=2; $i<6; $i++){
                $monthplus = mktime(0, 0, 0, date("m")+$i, date("d"),   date("Y"));
                $monthkey = strtoupper(date("M", $monthplus)).'-'.date("Y", $monthplus);
                $monthvalue = strtoupper(date("Y", $monthplus)).'/'.date("m", $monthplus);
                $monthlist[$monthkey]=$monthvalue;
           }   
        }

        $groups = $monthlist;

        

        //$groups = Yii::$app->params['current_order_groups'];     
        $colors = ['bg-aqua', 'bg-green', 'bg-yellow', 'bg-red', 'bg-navy', 'bg-maroon', 'bg-lime'];
        $dataTotalOrders = [];
        $dateType = 'expected_aseembling_date';
        $customerPoModel = new CustomerPo();
        $groupKeys = array_keys($groups);
        for ($i = 0; $i < count($groupKeys); $i++) {
            $dataTotalOrders[$i]['name'] = $groupKeys[$i];
            $dataTotalOrders[$i]['color'] = $colors[$i];
            $notDelay = ($i == 0 ) ? FALSE : TRUE;
            $scheduleOption = ($i == 0 ) ? 'all' : 'fixed';
            list($customers, $months, $mainData) = $customerPoModel->getNeedPlanGroupByCustomer($groupKeys[$i], $dateType, $notDelay, $scheduleOption);
            $dataTotalOrders[$i]['customer'] = array_column($mainData, 'cus_name');



            $dataTotalOrders[$i]['total'] = array_sum(array_column($mainData, 'total'));
            if($months){
            foreach ($months as $month) {
                $dataTotalOrders[$i]['months']['O ' . $month] = array_column($mainData, $month);
            }
            }else{
                $dataTotalOrders[$i]['months']['O ' . $groups[$groupKeys[$i]]] = [];
            }

            $dataTotalOrders[$i]['total_order_detail'] = array_combine(array_column($mainData, 'cus_name'), array_column($mainData, 'total'));
            $dataTotalOrders[$i]['total_order_detail'] = array_filter($dataTotalOrders[$i]['total_order_detail'], function($a) {
                return ($a != 0);
            });
//            } else {
//                $dataTotalOrders[$i]['total'] = array_sum(array_column($mainData, $groups[$groupKeys[$i]]));
//                $dataTotalOrders[$i]['months']['O ' . $groups[$groupKeys[$i]]] = array_column($mainData, $groups[$groupKeys[$i]]);
//                if (array_column($mainData, $groups[$groupKeys[$i]])) {
//                    $dataTotalOrders[$i]['total_order_detail'] = array_combine(array_column($mainData, 'cus_name'), array_column($mainData, $groups[$groupKeys[$i]]));
//                    $dataTotalOrders[$i]['total_order_detail'] = array_filter($dataTotalOrders[$i]['total_order_detail'], function($a) {
//                        return ($a != 0);
//                    });
//                } else {
//                    $dataTotalOrders[$i]['total_order_detail'] = [];
//                }
//            }
        }
//        die();
        //schedule

       
        

        //$group = Yii::$app->params['current_schedule_group'];
        $sqlData = $customerPoModel->getSchedule($group);
        list($customers, $monthsSchedule, $mainDataSchedule) = ArrayTool::convertColumsToHeader('cus_name', 'months', 'scheduled_cont', $sqlData);
        $totalScheduleArr = [];
        $dataTotalSchedule['months'] = $monthsSchedule;
        foreach ($monthsSchedule as $month) {
            $totalScheduleArr[$month] = array_sum(array_column($mainDataSchedule, $month));
        }
        $dataTotalSchedule['name'] = $group;
        $dataTotalSchedule['total_schedule'] = $totalScheduleArr;

        $sqlScheduleOrder = $customerPoModel->getListNeedPlan($group, $dateType);
        list($customers, $months, $mainData) = ArrayTool::convertColumsToHeader('cus_name', 'months', 'total_cont', $sqlScheduleOrder);

        foreach ($months as $month) {
            $scheduleOrder[$month] = array_column($mainData, $month);
        }

        $scheduleChart = ArrayTool::merge2array($scheduleOrder, $dataTotalSchedule['total_schedule'], 'months', 'order', 'schedule');
        return $this->render('index', ['dataTotalOrders' => $dataTotalOrders,
                    'dataTotalSchedule' => $dataTotalSchedule,
                    'scheduleChart' => $scheduleChart,
                    'groups' => $groups,
        ]);
    }

    public function actionTest() {
        return $this->render('test');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
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
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
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

                //return $this->goHome();
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

            //return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}
