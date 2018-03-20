<?php

namespace app\modules\overview\controllers;

use yii\web\Controller;
use backend\modules\overview\forms\PlanF;
use common\modules\sanlim\models\Date;
use backend\modules\overview\models\ItemOverview;
use backend\modules\overview\models\Overview;
use backend\modules\common\models\ReportGroup;
use backend\modules\overview\forms\ScheduleF;
use backend\modules\overview\forms\CusPOReport;
use backend\modules\common\models\POCus;
use backend\modules\common\models\Customer;

/**
 * Default controller for the `overview` module
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
        $poCusModel = new POCus();
        $arrScheduleQty = [];
        $arrOrderQty = [];
        $mainData = [];
        $groups = [];
        if ($scheduleF->load(\Yii::$app->request->get()) && $scheduleF->validate()) {
            $groups = (empty($scheduleF->reportGroup) ? NULL : $scheduleF->reportGroup);
//            $group = $groups[0];
            $customers = $poCusModel->getListCus($groups);
            foreach($customers as $value){
                $mainData[$value]['customer'] = $value;
            }
            foreach ($groups as $group) {
                $arrScheduleQty = $poCusModel->getScheduledQuantity($group);
                $arrScheduleOder = $poCusModel->getOrderQty($group);
                foreach($customers as $value){
                    if(isset($arrScheduleQty[$value])){
                        $mainData[$value]['total_sheduled_' . $group] = $arrScheduleQty[$value];
                    }else{
                        $mainData[$value]['total_sheduled_' . $group] = 0;
                    }
                    if(isset($arrScheduleOder[$value])){
                        $mainData[$value]['total_ordered_' . $group] = $arrScheduleOder[$value];
                    }else{
                        $mainData[$value]['total_ordered_' . $group] = 0;
                    }
                    $mainData[$value]['report_group_' . $group] = $group;
                }
            }
//            var_dump($mainData);die();
            

//            var_dump($mainData);die();
            
        }
        return $this->render('index', ['scheduleF' => $scheduleF,
            'reportGroups' => $allGroups,
            'groups' => $groups,
            'mainData' => $mainData]);
    }
    public function actionPolist() {
        $scheduleF = new ScheduleF();
        $poCusModel = new POCus();
        $arrPO = [];
        $request = \Yii::$app->request;
        $customer = (empty($request->get('customer')) ? NULL : $request->get('customer'));
        $group = (empty($request->get('group')) ? NULL : $request->get('group'));
        if ($customer && $group) {
            $arrPO = $poCusModel->getListPO($customer,$group);
            
        }
        return $this->render('polist', ['scheduleF' => $scheduleF,
            'arrPO' => $arrPO]);
    }
    public function actionPodetail() {
        $poCusModel = new POCus();
        $header = [];
        $lines = [];
        $request = \Yii::$app->request;
        $poNO = (empty($request->get('pono')) ? NULL : $request->get('pono'));
        if ($poNO) {
            list($header,$lines) = $poCusModel->getDetail($poNO);
        }
        return $this->render('podetail', ['header' => $header,
            'lines' => $lines,
            ]);
    }
    public function actionItemdetail() {
        $poCusModel = new POCus();
        $request = \Yii::$app->request;
        $itemNO = (empty($request->get('itemno')) ? NULL : $request->get('itemno'));
        $imageBLODs = '';
        if ($itemNO) {
            $imageBLODs = $poCusModel->getImage($itemNO);
        }
        return $this->render('itemdetail',[
            'imageBLODs' => $imageBLODs,
            'itemNO' => $itemNO,
            ]);
    }
    public function actionPostatus() {
        $cusPOReportF = new CusPOReport();
        $customerModel = new Customer();
        
        $customerList = $customerModel->getListCusKeyVal();
        $dateTypeList = ['request_date' => 'Customer Request Date',
            'confirm_date' => 'Confirm Ship Date',
            'expected_warehouse_date' => 'Expected Warehouse Date',
            'expected_aseembling_date' => 'Expected Assembling Date',
        ];
        $poStatusData = [];
        $header = [];
        if ($cusPOReportF->load(\Yii::$app->request->get()) && $cusPOReportF->validate()) {
            $cusCode = (empty($cusPOReportF->customer) ? NULL : $cusPOReportF->customer);
            $dateFrom = (empty($cusPOReportF->date_from) ? NULL : $cusPOReportF->date_from);
            $dateTo = (empty($cusPOReportF->date_to) ? NULL : $cusPOReportF->date_to);
            $dateType = (empty($cusPOReportF->date_type) ? NULL : $cusPOReportF->date_type);
            $poCusModel = new POCus();
            list($header,$poStatusData) = $poCusModel->getOderStatusFormated($cusCode, $dateFrom, $dateTo, $dateType);
//            var_dump($poStatusData);die();
            
            for($i=0;$i<count($poStatusData);$i++){
                foreach($header as $headers){
                    $poStatusData[$i][$headers] = round($poStatusData[$i][$headers],2);
                }
                
            }
        }
        
        return $this->render('postatus',[
            'cusPOReportF' => $cusPOReportF,
            'customerList' => $customerList,
            'dateTypeList' => $dateTypeList,
            'poStatusData' => $poStatusData,
            'header' => $header,
            ]);
    }
    

}
