<?php

namespace backend\modules\customerpo\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\customerpo\forms\ScheduleF;
use backend\modules\common\models\ReportGroup;
use backend\modules\customerpo\models\CustomerPo;
use DateTime;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\DateTimeTool;
/**
 * Default controller for the `customerpo` module
 */
class ScheduleController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $scheduleF = new ScheduleF();
        $reportGroupModel = new ReportGroup();
        $allGroups = $reportGroupModel->getValueKey();
        $customerPoModel = new CustomerPo();
        $mainData = [];
        $months = [];
        $group = FALSE;
        if ($scheduleF->load(\Yii::$app->request->get()) && $scheduleF->validate()) {
            $group = (empty($scheduleF->reportGroup) ? NULL : $scheduleF->reportGroup);
            $sqlData = $customerPoModel->getSchedule($group);
            list($customers, $months, $mainData) = ArrayTool::convertColumsToHeader('cus_name', 'months', 'scheduled_cont', $sqlData);
        }
        return $this->render('index', [
                    'scheduleF' => $scheduleF,
                    'reportGroups' => $allGroups,
                    'months' => $months,
                    'mainData' => $mainData,
                    'group' => $group,
        ]);
    }
    public function actionPolist() {
//        $poCusDetailModel = new POCusDetail();
        $arrPO = [];
        $customerPoModel = new CustomerPo();
        $request = \Yii::$app->request;
        $customer = (empty($request->get('customer')) ? '' : $request->get('customer'));
        $group = (empty($request->get('group')) ? NULL : $request->get('group'));
//        $dateType = (empty($request->get('date_type')) ? NULL : $request->get('date_type'));
        $month = (empty($request->get('month')) ? NULL : $request->get('month'));
        $dateLimit = DateTime::createFromFormat('Y-m-d', '2009-02-15')->format('Y-m-d');
        if ($group) {
            $reportGroupModel = new ReportGroup();
            $period = $reportGroupModel->getPeriod($group);
            $arrPO = $customerPoModel->getPoListSchedule($customer, $group, $month);
            if ($arrPO) {
                for ($i = 0; $i < count($arrPO); $i++) {
                    $arrPO[$i]['cus_request_date'] = DateTimeTool::getDateDiplay($arrPO[$i]['request_date_start'], $arrPO[$i]['request_date_end']);
                    $arrPO[$i]['schedule_assembly'] = DateTimeTool::getDateDiplay($arrPO[$i]['schedule_assembly_date_start'], $arrPO[$i]['schedule_assembly_date_end']);
                    $arrPO[$i]['confirm_etd'] = DateTimeTool::getDateDiplay($arrPO[$i]['confirm_date_start'], $arrPO[$i]['confirm_date_end']);
                    $arrPO[$i]['diff_days'] = DateTimeTool::getDiffDays($arrPO[$i]['confirm_date_start'], $arrPO[$i]['confirm_date_end'], $arrPO[$i]['schedule_etd_date']);
                    $arrPO[$i]['schedule_etd_date'] = DateTimeTool::getDateDiplay($arrPO[$i]['confirm_date_start']);
//                    if ($arrPO[$i]['request_date_end'] > $dateLimit) {
//                        $arrPO[$i]['cus_request_date'] = date("m/d", $arrPO[$i]['request_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['request_date_end']);
//                    } else {
//                        $arrPO[$i]['cus_request_date'] = date("m/d/Y", $arrPO[$i]['request_date_start']);
//                    }
//                    if ($arrPO[$i]['confirm_date_start'] < $dateLimit) {
////                        $arrPO[$i]['confirm_etd'] = '';
//                        $arrPO[$i]['diff_days'] = '';
//                    } elseif ($arrPO[$i]['confirm_date_end'] > $dateLimit) {
////                        $arrPO[$i]['confirm_etd'] = date("m/d", $arrPO[$i]['confirm_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['confirm_date_end']);
//                        $arrPO[$i]['diff_days'] = ($arrPO[$i]['schedule_etd_date'] - $arrPO[$i]['confirm_date_end']) / (60 * 60 * 24);
//                    } else {
////                        $arrPO[$i]['confirm_etd'] = date("m/d/Y", $arrPO[$i]['confirm_date_start']);
//                        $arrPO[$i]['diff_days'] = ($arrPO[$i]['schedule_etd_date'] - $arrPO[$i]['confirm_date_start']) / (60 * 60 * 24);
//                    }
//                    if ($arrPO[$i]['schedule_etd_date'] < $dateLimit) {
//                        $arrPO[$i]['schedule_etd_date'] = '';
//                        $arrPO[$i]['diff_days'] = '';
//                    } else {
//                        $arrPO[$i]['schedule_etd_date'] = date("m/d/Y", $arrPO[$i]['schedule_etd_date']);
//                    }

//                    if ($arrPO[$i]['schedule_assembly_date_start'] < $dateLimit) {
//                        $arrPO[$i]['schedule_assembly'] = '';
//                    } elseif ($arrPO[$i]['schedule_assembly_date_end'] > $dateLimit) {
//                        $arrPO[$i]['schedule_assembly'] = date("m/d", $arrPO[$i]['schedule_assembly_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['schedule_assembly_date_end']);
//                    } else {
//                        $arrPO[$i]['schedule_assembly'] = date("m/d/Y", $arrPO[$i]['schedule_assembly_date_start']);
//                    }
                }
            }
        }
        return $this->render('polistschedule', [
                    'arrPO' => $arrPO,
                    'dateLimit' => $dateLimit,
                    'customer' => $customer,
                    'month' => $month,
                    'group' => $group
        ]);
    }
}
