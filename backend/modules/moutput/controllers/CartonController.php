<?php

namespace backend\modules\moutput\controllers;
use backend\modules\common\models\ReportGroup;
use backend\modules\moutput\forms\GeneralF;
use backend\modules\moutput\models\Moutput;
use backend\modules\common\models\ArrayTool;

use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Default controller for the `moutput` module
 */
class CartonController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $mainData = [];
        $dateFrom = '2017-07-01';
        $dateTo = '2019-01-26';
        $itemNo = 'CB.%';

        $mOutputModel = new Moutput();
        $mainData = $mOutputModel->getCartonOutput($itemNo, $dateFrom, $dateTo);
        // var_dump($mainData);die();


        return $this->render('index',[
            'mainData' => $mainData,
            'dateTo' => $dateTo,
            'dateFrom' => $dateFrom,
        ]);
    }

    public function actionOutputDivision()
    {
        $mainData = [];
        $request = \Yii::$app->request;
        $monthYear = (empty($request->get('month')) ? '' : $request->get('month'));
        $itemNo = 'CB.%';
        $mOutputModel = new Moutput();
        $mainData = $mOutputModel->getOutputDivision($itemNo, $monthYear);
        // var_dump($monthYear);die();


        return $this->render('output_division',[
            'mainData' => $mainData,
            'monthYear' => $monthYear,
        ]);
    }
    public function actionOutputBlanket()
    {
        $mainData = [];
        $request = \Yii::$app->request;
        $monthYear = (empty($request->get('month')) ? '' : $request->get('month'));
        $divisionCode = (empty($request->get('division')) ? '' : $request->get('division'));
        $locationCode = (empty($request->get('location')) ? '' : $request->get('location'));
        $itemNo = 'CB.%';
        $mOutputModel = new Moutput();
        $mainData = $mOutputModel->getOutputBlanket($itemNo, $monthYear, $divisionCode, $locationCode);
        // var_dump($mainData);die();


        return $this->render('output_blanket',[
            'mainData' => $mainData,
            'monthYear' => $monthYear,
            'divisionCode' => $divisionCode,
            'locationCode' => $locationCode,
        ]);
    }
    public function actionOutputLegerEntry()
    {
        $mainData = [];
        $request = \Yii::$app->request;
        $monthYear = (empty($request->get('month')) ? '' : $request->get('month'));
        $divisionCode = (empty($request->get('division')) ? '' : $request->get('division'));
        $locationCode = (empty($request->get('location')) ? '' : $request->get('location'));
        $blanket = (empty($request->get('blanket')) ? '' : $request->get('blanket'));
        $ik = (empty($request->get('ik')) ? '' : $request->get('ik'));
        $itemNo = 'CB.%';
        $mOutputModel = new Moutput();
        $mainData = $mOutputModel->getOutputLegerEntry($itemNo, $monthYear, $divisionCode, $locationCode, $ik);
        // var_dump($mainData);die();


        return $this->render('output_leger_entry',[
            'mainData' => $mainData,
        ]);
    }
}
