<?php

namespace backend\modules\scorecard\controllers;

use yii\web\Controller;
use backend\modules\scorecard\forms\LazboyScoreCardForm;
use backend\modules\common\models\ReportGroup;
use backend\modules\scorecard\models\Scorecard;
use backend\modules\common\models\CustomerPOCommon;
use backend\modules\scorecard\models\LazboyScorecard;


class LazboyController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $lazboyScorecardFrom = new LazboyScoreCardForm();
        $lazboyScorecardModel = new LazboyScorecard();
        $allPos= [];
        $mainData = [];
        $dateFrom = FALSE;
        $dateTo = FALSE;
        if ($lazboyScorecardFrom->load(\Yii::$app->request->get()) && $lazboyScorecardFrom->validate()) {
            $dateFrom = (empty($lazboyScorecardFrom->dateFrom) ? NULL : $lazboyScorecardFrom->dateFrom);
            $dateTo = (empty($lazboyScorecardFrom->dateTo) ? NULL : $lazboyScorecardFrom->dateTo);
//            $allPos = $lazboyScorecardModel->getAllPO($dateFrom, $dateTo);
            list($arrayMissing, $arrayLate, $arrayOnTime) = $lazboyScorecardModel->getlazBoycorceCardPos($dateFrom, $dateTo);
            $mainData[0]['type_code'] = 'on_time';
            $mainData[0]['type'] = 'On Time';
            $mainData[0]['total_quantity'] = $arrayOnTime['total'];
            $mainData[1]['type_code'] = 'late';
            $mainData[1]['type'] = 'Late';
            $mainData[1]['total_quantity'] = $arrayLate['total'];
            $mainData[2]['type_code'] = 'not_finish';
            $mainData[2]['type'] = 'Not Ship/Not Finish';
            $mainData[2]['total_quantity'] = $arrayMissing['total'];
//            var_dump($allPos);die();
        }
        return $this->render('index',[
            'lazboyScorecardFrom' => $lazboyScorecardFrom,
            'allPos' => $allPos,
            'mainData' => $mainData,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function actionScorecarddetail() {
        $request = \Yii::$app->request;
        $lazboyScorecardModel = new LazboyScorecard();
        $dateFrom = (empty($request->get('dateFrom')) ? NULL : $request->get('dateFrom'));
        $dateTo = (empty($request->get('dateTo')) ? NULL : $request->get('dateTo'));
        $type = (empty($request->get('type')) ? NULL : $request->get('type'));
        if ($dateFrom && $dateTo && $type) {
            list($arrayMissing, $arrayLate, $arrayOnTime) = $lazboyScorecardModel->getlazBoycorceCardPos($dateFrom, $dateTo);
            switch ($type) {
                case 'on_time': $mainData = $arrayOnTime['po'];
                    break;
                case 'late': $mainData = $arrayLate['po'];
                    break;
                case 'not_finish': $mainData = $arrayMissing['po'];
                    break;
                case 'all': $mainData = $lazboyScorecardModel->getAllPO($dateFrom, $dateTo);
                    break;
            }
        }
        return $this->render('scorecarddetail', [
                    'mainData' => $mainData,
        ]);
    }

}
