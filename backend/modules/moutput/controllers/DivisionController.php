<?php

namespace backend\modules\moutput\controllers;
use backend\modules\common\models\ReportGroup;
use backend\modules\moutput\forms\DivisionF;
use backend\modules\moutput\models\Moutput;
use backend\modules\common\models\ArrayTool;

use yii\web\Controller;

/**
 * Default controller for the `moutput` module
 */
class DivisionController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $reportGroupModel = new ReportGroup();
        $allGroups = $reportGroupModel->getValueKey();
        $divisionF = new DivisionF();
        $mainData = [];
        $monthData = [];
        $itemNo = '';
        if ($divisionF->load(\Yii::$app->request->get()) && $divisionF->validate()) {
            $itemNo = (empty($divisionF->item_no) ? NULL : $divisionF->item_no);
            $dateFrom = (empty($divisionF->date_from) ? NULL : $divisionF->date_from);
            $dateTo = (empty($divisionF->date_to) ? NULL : $divisionF->date_to);

            $moutputModel = new Moutput();
            $data = $moutputModel->getDivisionData($itemNo, $dateFrom, $dateTo);
            list($monthData, $mainData) = ArrayTool::convertColumsToHeaderCustom('division_code', 'month', 'total', $data, ['description','location']);
//            var_dump($mainData);die();

        }
//        $this->registerJs(
//    "var chart = $('#chart_moutput_division').highcharts(); $('#chart_type').click(function() {console.log(chart);});",
//    View::POS_READY,
//    'my-button-handler'
//);
        return $this->render('index',[
            'allGroups' => $allGroups,
            'divisionF' => $divisionF,
            'mainData' => $mainData,
            'monthData' => $monthData,
            'itemNo' => $itemNo
        ]);
    }
    public function actionDetail()
    {
        $reportGroupModel = new ReportGroup();
        $allGroups = $reportGroupModel->getValueKey();
        $divisionF = new DivisionF();
        $mainData = [];
        $monthData = [];
        $itemNo = '';
        if ($divisionF->load(\Yii::$app->request->get()) && $divisionF->validate()) {
            $itemNo = (empty($divisionF->item_no) ? NULL : $divisionF->item_no);
            $dateFrom = (empty($divisionF->date_from) ? NULL : $divisionF->date_from);
            $dateTo = (empty($divisionF->date_to) ? NULL : $divisionF->date_to);

            $moutputModel = new Moutput();
            $data = $moutputModel->getDivisionData($itemNo, $dateFrom, $dateTo);
            list($monthData, $mainData) = ArrayTool::convertColumsToHeaderCustom('division_code', 'month', 'total', $data, ['description','location']);
//            var_dump($mainData);die();

        }
//        $this->registerJs(
//    "var chart = $('#chart_moutput_division').highcharts(); $('#chart_type').click(function() {console.log(chart);});",
//    View::POS_READY,
//    'my-button-handler'
//);
        return $this->render('detail',[
            'allGroups' => $allGroups,
            'divisionF' => $divisionF,
            'mainData' => $mainData,
            'monthData' => $monthData,
            'itemNo' => $itemNo
        ]);
    }
}
