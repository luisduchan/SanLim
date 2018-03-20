<?php

namespace backend\modules\customerpo\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\overview\forms\CusPOReport;
use backend\modules\common\models\POCus;
use backend\modules\common\models\Customer;
use backend\modules\customerpo\forms\SummaryPOF;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\POCusDetail;
use backend\modules\customerpo\forms\NeedPlanF;
use backend\modules\customerpo\models\CustomerPo;
use DateTime;
use backend\modules\common\models\ArrayTool;
use DateInterval;
use yii\filters\AccessControl;
/**
 * Default controller for the `customerpo` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['custponeedplan', 'login', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['custponeedplan'],
                        'roles' => ['view_total_order'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionOrderstatus() {
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
            list($header, $poStatusData) = $poCusModel->getOderStatusFormated($cusCode, $dateFrom, $dateTo, $dateType);
        }

        return $this->render('orderstatus', [
                    'cusPOReportF' => $cusPOReportF,
                    'customerList' => $customerList,
                    'dateTypeList' => $dateTypeList,
                    'poStatusData' => $poStatusData,
                    'header' => $header,
        ]);
    }

    public function actionSummaryposchedule() {
        $summaryPOF = new SummaryPOF;
        $reportGroupModel = new ReportGroup();
        $poCusDetailModel = new POCusDetail();
        $allGroups = $reportGroupModel->getValueKey();
        $dateTypeList = $poCusDetailModel->getDateTypeList();
        $mainData = [];
        $poCusModel = new POCus();
        $dateType = FALSE;
        $groups = [];
        if ($summaryPOF->load(\Yii::$app->request->get()) && $summaryPOF->validate()) {
            $groups = (empty($summaryPOF->reportGroup) ? NULL : $summaryPOF->reportGroup);
            $dateType = (empty($summaryPOF->date_type) ? NULL : $summaryPOF->date_type);
            $customers = $poCusDetailModel->getListCus($groups, $dateType);
            foreach ($customers as $value) {
                $mainData[$value]['customer'] = $value;
            }
            foreach ($groups as $group) {
                $arrOrderQty = $poCusDetailModel->getTotalOrder($group, $dateType);
                $arrOrderLateQty = $poCusDetailModel->getTotalOrder($group, $dateType, 'late');
                $arrOrderNotPlanQty = $poCusDetailModel->getTotalOrder($group, $dateType, 'not_plan');
//                $arrScheduleOder = $poCusModel->getOrderQty($group);
                $period = $reportGroupModel->getPeriod($group);
                foreach ($customers as $custNO => $value) {
                    if (isset($arrOrderQty[$value])) {
                        $mainData[$value]['total_order_' . $group] = round($arrOrderQty[$value], 2);
                    } else {
                        $mainData[$value]['total_order_' . $group] = 0;
                    }
                    if (isset($arrOrderLateQty[$value])) {
                        $mainData[$value]['total_order_late_' . $group] = round($arrOrderLateQty[$value], 2);
                    } else {
                        $mainData[$value]['total_order_late_' . $group] = 0;
                    }
                    if (isset($arrOrderNotPlanQty[$value])) {
                        $mainData[$value]['total_order_notplan_' . $group] = round($arrOrderNotPlanQty[$value], 2);
                    } else {
                        $mainData[$value]['total_order_notplan_' . $group] = 0;
                    }
                    $mainData[$value]['report_group_' . $group] = $group;
                    $mainData[$value]['date_from_' . $group] = date("Y-m-d", strtotime($period[0]));
                    $mainData[$value]['date_to_' . $group] = date("Y-m-d", strtotime($period[1]));
                    $mainData[$value]['date_type'] = $dateType;
                    $mainData[$value]['cust_no'] = $custNO;
                }
            }
        }
        return $this->render('summaryposchedule', [
                    'summaryPOF' => $summaryPOF,
                    'allGroups' => $allGroups,
                    'dateTypeList' => $dateTypeList,
                    'mainData' => $mainData,
                    'groups' => $groups,
                    'dateType' => $dateType
        ]);
    }

    public function actionCustpolistforsummary() {
        $poCusDetailModel = new POCusDetail();
        $arrPO = [];
        $request = \Yii::$app->request;
        $customer = (empty($request->get('customer')) ? '' : $request->get('customer'));
        $group = (empty($request->get('group')) ? NULL : $request->get('group'));
        $dateType = (empty($request->get('date_type')) ? NULL : $request->get('date_type'));
        $poType = (empty($request->get('po_type')) ? NULL : $request->get('po_type'));
        $dateLimit = DateTime::createFromFormat('Y-m-d', '2009-02-15')->format('Y-m-d');
        if ($group) {
            $reportGroupModel = new ReportGroup();
            $period = $reportGroupModel->getPeriod($group);
            $arrPO = $poCusDetailModel->getPOsBaseCustomerGroup($customer, $period[0], $period[1], $dateType, $poType);
            if ($arrPO) {

//                var_dump($dateNULL);die();
                for ($i = 0; $i < count($arrPO); $i++) {
                    if ($arrPO[$i]['request_date_end'] > $dateLimit) {
                        $arrPO[$i]['cus_request_date'] = date("m/d", $arrPO[$i]['request_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['request_date_end']);
                    } else {
                        $arrPO[$i]['cus_request_date'] = date("m/d/Y", $arrPO[$i]['request_date_start']);
                    }
                    if ($arrPO[$i]['confirm_date_start'] < $dateLimit) {
                        $arrPO[$i]['confirm_etd'] = '';
                        $arrPO[$i]['diff_days'] = '';
                    } elseif ($arrPO[$i]['confirm_date_end'] > $dateLimit) {
                        $arrPO[$i]['confirm_etd'] = date("m/d", $arrPO[$i]['confirm_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['confirm_date_end']);
                        $arrPO[$i]['diff_days'] = ($arrPO[$i]['schedule_etd_date'] - $arrPO[$i]['confirm_date_end']) / (60 * 60 * 24);
                    } else {
                        $arrPO[$i]['confirm_etd'] = date("m/d/Y", $arrPO[$i]['confirm_date_start']);
                        $arrPO[$i]['diff_days'] = ($arrPO[$i]['schedule_etd_date'] - $arrPO[$i]['confirm_date_start']) / (60 * 60 * 24);
//                        $arrPO[$i]['diff_days'] =  date_diff(new DateTime(date('Y-m-d',$arrPO[$i]['schedule_etd_date'])), new DateTime(date('Y-m-d',$arrPO[$i]['confirm_date_start'])))->days;
                    }
                    if ($arrPO[$i]['schedule_etd_date'] < $dateLimit) {
                        $arrPO[$i]['schedule_etd_date'] = '';
                        $arrPO[$i]['diff_days'] = '';
                    } else {
                        $arrPO[$i]['schedule_etd_date'] = date("m/d/Y", $arrPO[$i]['schedule_etd_date']);
                    }
                    if ($arrPO[$i]['schedule_assembly_date_start'] < $dateLimit) {
                        $arrPO[$i]['schedule_assembly'] = '';
                    } elseif ($arrPO[$i]['schedule_assembly_date_end'] > $dateLimit) {
                        $arrPO[$i]['schedule_assembly'] = date("m/d", $arrPO[$i]['schedule_assembly_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['schedule_assembly_date_end']);
                    } else {
                        $arrPO[$i]['schedule_assembly'] = date("m/d/Y", $arrPO[$i]['schedule_assembly_date_start']);
                    }
                }
            }
        }
        return $this->render('custpolistforsummary', [
                    'arrPO' => $arrPO,
                    'dateLimit' => $dateLimit
        ]);
    }

    public function actionCustponeedplan() {
        $poCusDetailModel = new POCusDetail();
        $reportGroupModel = new ReportGroup();
        $poCustMode = new POCus();
        $needPlanF = new NeedPlanF();
        $allGroups = $reportGroupModel->getValueKey();
        $dateTypeList = $poCusDetailModel->getDateTypeList();
        $customerPoModel = new CustomerPo();
        $group = FALSE;
        $dateType = 'expected_aseembling_date';
        $months = [];
        $mainData = [];
        $dateName = '';

//        $checkReportGroupandAssDate = $poCustMode->checkAssDateandReportGroup();
//        if ($checkReportGroupandAssDate) {
//            $message = 'Please correct Report Group and Assembling Date of these'
//                    . ' Blanket POs: '
//                    . implode(', ', array_column($checkReportGroupandAssDate, 'no'));
//            Yii::$app->session->setFlash('error', $message);
//        }
        $checkCuft = $poCusDetailModel->checkCuft();
        if ($checkCuft) {
            $message = 'Please update cubic feet for these'
                    . ' Items: '
                    . implode(', ', array_column($checkCuft, 'item_no'));
            Yii::$app->session->setFlash('warning', $message);
        }
        $scheduledMonths = [];
        if ($needPlanF->load(\Yii::$app->request->get()) && $needPlanF->validate()) {
            $group = (empty($needPlanF->group) ? NULL : $needPlanF->group);
            $dateType = (empty($needPlanF->date_type) ? 'expected_aseembling_date' : $needPlanF->date_type);
            $notDelay = (empty($needPlanF->date_type) ? False : $needPlanF->not_delay);
            $notScheduled = (empty($needPlanF->not_scheduled) ? False : $needPlanF->not_scheduled);
//            $groupByProductGroup = (empty($needPlanF->group_by_product_group) ? False : $needPlanF->group_by_product_group);
            $fixedScheduleGroup = Yii::$app->params['current_schedule_group'];

            $listDateName = [
                'expected_aseembling_date' => 'Assembling Date',
                'expected_warehouse_date' => 'Warehouse Date',
                'confirm_date' => 'Cofirm Date'
            ];
            $dateName = $listDateName[$dateType];
            $scheduleOption = DateTime::createFromFormat('M-Y', $group) <= DateTime::createFromFormat('M-Y', $fixedScheduleGroup)->add(new DateInterval('P1M'))  ?  'all' : 'fixed';
            list($customers, $months, $mainData) = $customerPoModel->getNeedPlanGroupByCustomer($group, $dateType, $notDelay, $scheduleOption);
//            var_dump($scheduleOption);//die();

        }

        return $this->render('custponeedplan', [
                    'allGroups' => $allGroups,
                    'dateTypeList' => $dateTypeList,
                    'needPlanF' => $needPlanF,
                    'months' => $months,
                    'mainData' => $mainData,
                    'group' => $group,
                    'dateType' => $dateType,
                    'dateName' => $dateName,
                    'scheduledMonths' => $scheduledMonths,
        ]);
    }

    public function actionCustpolistforneedplan() {
        $poCusDetailModel = new POCusDetail();
        $arrPO = [];
        $customerPoModel = new CustomerPo();
        $request = \Yii::$app->request;
        $customer = (empty($request->get('customer')) ? '' : $request->get('customer'));
        $group = (empty($request->get('group')) ? NULL : $request->get('group'));
        $dateType = (empty($request->get('date_type')) ? NULL : $request->get('date_type'));
        $month = (empty($request->get('month')) ? NULL : $request->get('month'));
        $dateLimit = DateTime::createFromFormat('Y-m-d', '2009-02-15')->format('Y-m-d');

        if ($group) {
            $reportGroupModel = new ReportGroup();
            $period = $reportGroupModel->getPeriod($group);
            $arrPO = $customerPoModel->getPOsForPlan($customer, $group, $month, $dateType);
            if ($arrPO) {

//                var_dump($dateNULL);die();
                for ($i = 0; $i < count($arrPO); $i++) {
                    if ($arrPO[$i]['request_date_end'] > $dateLimit) {
                        $arrPO[$i]['cus_request_date'] = date("m/d", $arrPO[$i]['request_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['request_date_end']);
                    } else {
                        $arrPO[$i]['cus_request_date'] = date("m/d/Y", $arrPO[$i]['request_date_start']);
                    }
                    if ($arrPO[$i]['confirm_date_start'] < $dateLimit) {
                        $arrPO[$i]['confirm_etd'] = '';
                        $arrPO[$i]['diff_days'] = '';
                    } elseif ($arrPO[$i]['confirm_date_end'] > $dateLimit) {
                        $arrPO[$i]['confirm_etd'] = date("m/d", $arrPO[$i]['confirm_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['confirm_date_end']);
                        $arrPO[$i]['diff_days'] = ($arrPO[$i]['schedule_etd_date'] - $arrPO[$i]['confirm_date_end']) / (60 * 60 * 24);
                    } else {
                        $arrPO[$i]['confirm_etd'] = date("m/d/Y", $arrPO[$i]['confirm_date_start']);
                        $arrPO[$i]['diff_days'] = ($arrPO[$i]['schedule_etd_date'] - $arrPO[$i]['confirm_date_start']) / (60 * 60 * 24);
                    }
                    if ($arrPO[$i]['schedule_etd_date'] < $dateLimit) {
                        $arrPO[$i]['schedule_etd_date'] = '';
                        $arrPO[$i]['diff_days'] = '';
                    } else {
                        $arrPO[$i]['schedule_etd_date'] = date("m/d/Y", $arrPO[$i]['schedule_etd_date']);
                    }
                    if ($arrPO[$i]['schedule_assembly_date_start'] < $dateLimit) {
                        $arrPO[$i]['schedule_assembly'] = '';
                    } elseif ($arrPO[$i]['schedule_assembly_date_end'] > $dateLimit) {
                        $arrPO[$i]['schedule_assembly'] = date("m/d", $arrPO[$i]['schedule_assembly_date_start']) . ' - ' . date("m/d/Y", $arrPO[$i]['schedule_assembly_date_end']);
                    } else {
                        $arrPO[$i]['schedule_assembly'] = date("m/d/Y", $arrPO[$i]['schedule_assembly_date_start']);
                    }
                }
            }
        }
        return $this->render('custpolistforplan', [
                    'arrPO' => $arrPO,
                    'dateLimit' => $dateLimit,
                    'customer' => $customer,
                    'month' => $month,
                    'group' => $group
        ]);
    }

}
