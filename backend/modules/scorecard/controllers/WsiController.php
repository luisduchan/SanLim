<?php

namespace backend\modules\scorecard\controllers;

use yii\web\Controller;
use backend\modules\scorecard\forms\WsiScoreCardForm;
use backend\modules\common\models\ReportGroup;
use backend\modules\scorecard\models\Scorecard;
use backend\modules\common\models\CustomerPOCommon;

/**
 * Default controller for the `scorecard` module
 */
class WsiController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $reportGroupModel = new ReportGroup();
        $wsiScoreCardForm = new WsiScoreCardForm();
        $allGroups = $reportGroupModel->getValueKey();
        $mainData = [];
        $group = FALSE;
        $baseOnCofirmShipDate = False;
        if ($wsiScoreCardForm->load(\Yii::$app->request->get()) && $wsiScoreCardForm->validate()) {
            $group = (empty($wsiScoreCardForm->reportGroup) ? NULL : $wsiScoreCardForm->reportGroup);
            $baseOnCofirmShipDate = (empty($wsiScoreCardForm->baseOnCofirmShipDate) ? NULL : $wsiScoreCardForm->baseOnCofirmShipDate);
            $scoreCard = new Scorecard();
            list($onTimePos, $latePos, $shortShipPos, $missingPos) = $scoreCard->getWSIScorceCardPos($group, $baseOnCofirmShipDate);
            $totalOnTime = count($onTimePos);
            $totalLate = count($latePos);
            $totalShortShip = count($shortShipPos);
            $totalMissing = count($missingPos);
            $totalPO = $totalOnTime + $totalLate + $totalShortShip + $totalMissing;
            $mainData = [
                ['type' => 'Total PO', 'total' => $totalPO, 'type_code' => 'all'],
                ['type' => 'On Time', 'total' => $totalOnTime, 'type_code' => 'ontime'],
                ['type' => 'Late', 'total' => $totalLate, 'type_code' => 'late'],
                ['type' => 'Not Finish', 'total' => $totalShortShip, 'type_code' => 'shortship'],
                ['type' => 'Not Ship', 'total' => $totalMissing, 'type_code' => 'missing'],
            ];
        }
        return $this->render('index', [
                    'allGroups' => $allGroups,
                    'mainData' => $mainData,
                    'wsiScoreCardForm' => $wsiScoreCardForm,
                    'group' => $group,
                    'baseOnCofirmShipDate' => $baseOnCofirmShipDate
        ]);
    }

    public function actionScorecarddetail() {
        $reportGroupModel = new ReportGroup();
        $wsiScoreCardForm = new WsiScoreCardForm();
        $allGroups = $reportGroupModel->getValueKey();
        $request = \Yii::$app->request;
        $mainData = [];
        $group = (empty($request->get('group')) ? NULL : $request->get('group'));
        $type = (empty($request->get('type')) ? NULL : $request->get('type'));
        $baseOnCofirmShipDate = (empty($request->get('baseOnCofirmShipDate')) ? NULL : $request->get('baseOnCofirmShipDate'));
        $typeDisplay = [
            'ontime' => 'On Time',
            'late' => 'Late',
            'shortship' => 'Not Finish',
            'missing' => 'Not Ship',
            'all' => 'All',
        ];
        if ($group && $type) {
            $scoreCard = new Scorecard();
            list($onTimePos, $latePos, $shortShipPos, $missingPos) = $scoreCard->getWSIScorceCardPos($group, $baseOnCofirmShipDate);
            switch ($type) {
                case 'ontime': $mainData = $onTimePos;
                    break;
                case 'late': $mainData = $latePos;
                    break;
                case 'shortship': $mainData = $shortShipPos;
                    break;
                case 'missing': $mainData = $missingPos;
                    break;
                case 'all': $mainData = $scoreCard->getWSIAllPoWithLateDay($group, $baseOnCofirmShipDate);
                    break;
            }
        }
        return $this->render('scorecarddetail', [
                    'mainData' => $mainData,
                    'group' => $group,
                    'type_name' => $typeDisplay[$type],
        ]);
    }

    public function actionShippingdetail() {
        $scoreCard = new Scorecard();
        $mainData = [];
        $customerPODetail = [];

        $request = \Yii::$app->request;
        $poNo = (empty($request->get('pono')) ? NULL : $request->get('pono'));
        if ($poNo) {
            $customerPOModel = new CustomerPOCommon();
            $customerPODetail = $customerPOModel->getPODetail($poNo);
            $mainData = $scoreCard->getShippingDetail($poNo);
//            var_dump($customerPODetail);
//            die();
        }

        return $this->render('shippingdetail', [
                    'mainData' => $mainData,
                    'poNo' => $poNo,
                    'header' => $customerPODetail
        ]);
    }

    public function actionAll() {
        $scoreCard = new Scorecard();
        $mainData = [];

        $request = \Yii::$app->request;
        $group = (empty($request->get('group')) ? NULL : $request->get('group'));
        if ($group) {
            $mainData = $scoreCard->getWSIAllPoWithLateDay($group);
//            var_dump($mainData);
//            die();
        }

        return $this->render('all', [
                    'mainData' => $mainData,
                    'group' => $group
        ]);
    }

}
