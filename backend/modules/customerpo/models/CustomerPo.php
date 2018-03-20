<?php

namespace backend\modules\customerpo\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use DateTime;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\POCus;
use yii\helpers\ArrayHelper;
use backend\modules\common\models\POCusDetail;
use backend\modules\common\models\ArrayTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class CustomerPo extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function tableName() {
        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]';
    }

    public function getListNeedPlan($group, $dateType, $notDelay = False) {
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($group);
        $blanketPOModel = new POCus();
        $poCusModel = new POCusDetail();
        $dateField = $poCusModel->getDateField($dateType);

        $querySql = 'SELECT
                            cph.CustomerName cus_name,
                            cph.CustomerNo cus_no,
                            sh.No_ blanket_no,
                            FORMAT(' . $dateField . ', \'yyyy/MM\') months,
                                    SUM (cpl.Quantity * ISNULL(iuom.CUFT,0)) / 2350
                                    total_cont
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            cpl.ItemNo = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                            sh.[Document Type] = 4
                            AND sh.No_ = cpl.[Blanket PO#]
                    )
                    WHERE
                    ((' . $dateField . ' <= :date_to1 AND sh.[Scheduled Ass_ Date Start] = \'1753-01-01 00:00:00.000\')
                    OR (' . $dateField . ' <= :date_from1 AND sh.[Scheduled Ass_ Date End] >= :date_from2)
                    OR ' . $dateField . ' BETWEEN :date_from3 AND :date_to3)
                    AND cph.[Order Type] IN(0,1)
                    GROUP BY
                            cph.CustomerName,
                            cph.CustomerNo,
                            sh.No_,
                            FORMAT(' . $dateField . ', \'yyyy/MM\')
                    ORDER BY cph.CustomerName,FORMAT(' . $dateField . ', \'yyyy/MM\')';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);

        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from3', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        // $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to3', date('Y-m-d', strtotime($period[1])));
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            $ratioArr = [];
            $result = [];
            for ($i = 0; $i < count($sqlResult); $i++) {
                if ($notDelay) {
                    if (date('Y/m', strtotime($period[0])) == $sqlResult[$i]['months']) {
                        $result[] = $sqlResult[$i];
                    }
                } else {
                    if (date('Y/m', strtotime($period[0])) != $sqlResult[$i]['months']) {
                        if (!isset($ratioArr[$sqlResult[$i]['blanket_no']]) || !isset($ratioArr[$sqlResult[$i]['blanket_no']][$group])) {
                            $ratioArr[$sqlResult[$i]['blanket_no']][$group] = $blanketPOModel->getRatioBlanketPo($sqlResult[$i]['blanket_no'], $group);
                        }
                        $ratio = $ratioArr[$sqlResult[$i]['blanket_no']][$group];
                        $sqlResult[$i]['total_cont'] = round($ratio * $sqlResult[$i]['total_cont'], 2);
                    }
                    $result[] = $sqlResult[$i];
                }
            }
            return $result;
        }
        return FALSE;
    }

    //TODO: fix months of scheduled
    public function getNeedPlanGroupByCustomer($group, $dateType, $notDelay = False, $scheduleOption = False) {
        $mainData = $this->getListNeedPlan($group, $dateType, $notDelay);
//        var_dump($mainData);die();
        list($customers, $months, $mainData) = ArrayTool::convertColumsToHeader('cus_name', 'months', 'total_cont', $mainData);
        if ($scheduleOption == 'all' || $scheduleOption == 'fixed') {
            $scheduledInPast = $this->getScheduleInPast($group, $dateType, $scheduleOption);
            if ($scheduledInPast) {

                $fixedScheduleGroup = Yii::$app->params['current_schedule_group'];
                $fixedScheduleMonth = DateTime::createFromFormat('M-Y', $fixedScheduleGroup);
                $fixedScheduleMonth = $fixedScheduleMonth->format('Y/m');
                foreach ($mainData as $custNameMainData => $mainDataRow) {
                    foreach ($scheduledInPast as $custScheduledName => $scheduledInPastRow) {
                        foreach ($scheduledInPastRow as $scheduledMonth => $totalScheduled) {
                            if ($scheduleOption == 'fixed') {
                                if ($fixedScheduleMonth == $scheduledMonth) {
                                    $txtscheduledMonth = 's' . $scheduledMonth;
                                    if (!in_array($txtscheduledMonth, $months)) {
                                        $months[] = $txtscheduledMonth;
                                    }
                                    if (!isset($scheduledMonths[$txtscheduledMonth])) {
                                        $scheduledMonths[$txtscheduledMonth] = $txtscheduledMonth;
                                    }
                                    if (!isset($mainData[$custNameMainData][$txtscheduledMonth])) {
                                        $mainData[$custNameMainData][$txtscheduledMonth] = 0;
                                    }
                                    if ($custNameMainData == $custScheduledName) {
                                        $mainData[$custNameMainData][$txtscheduledMonth] -= $totalScheduled;
                                        $mainData[$custNameMainData]['total'] -= $totalScheduled;
                                    }
                                }
                            } elseif ($scheduleOption == 'all') {
                                $txtscheduledMonth = 's' . $scheduledMonth;
                                if (!in_array($txtscheduledMonth, $months)) {
                                    $months[] = $txtscheduledMonth;
                                }
                                if (!isset($scheduledMonths[$txtscheduledMonth])) {
                                    $scheduledMonths[$txtscheduledMonth] = $txtscheduledMonth;
                                }
                                if (!isset($mainData[$custNameMainData][$txtscheduledMonth])) {
                                    $mainData[$custNameMainData][$txtscheduledMonth] = 0;
                                }
                                if ($custNameMainData == $custScheduledName) {
                                    $mainData[$custNameMainData][$txtscheduledMonth] -= $totalScheduled;
                                    $mainData[$custNameMainData]['total'] -= $totalScheduled;
                                }
                            }
                        }
                    }
                }
            }
        }
        return [$customers, $months, $mainData];
    }

    public function getScheduleInPast($group, $dateType, $scheduleOption = False) {
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($group);
        $blanketPOModel = new POCus();
        $poCusModel = new POCusDetail();
        $dateField = $poCusModel->getDateField($dateType);
//        if($groupByProductGroup){
//            $querySql = 'SELECT
//	item.[Product Group Code] cus_name,
//	item.[Product Group Code] cus_no,
//	sh.No_ blanket_no,
//	SUM (
//		cpl.Quantity * ISNULL(iuom.CUFT, 0)
//	) / 2350 total_cont,
//	FORMAT (
//		sh.[Scheduled Ass_ Date End],
//		 \'MM/yyyy\'
//	) month
//FROM
//	[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
//JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
//JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item ON item.No_ = cpl.ItemNo
//LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
//	cpl.ItemNo = iuom.[Item No_]
//	AND iuom.Code =  \'CTNS\'
//)
//JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
//	sh.[Document Type] = 4
//	AND sh.No_ = cpl.[Blanket PO#]
//)
//WHERE
//	' . $dateField . ' BETWEEN :date_from1
//AND :date_to1
//AND sh.[Scheduled Ass_ Date End] < :date_from2
//AND sh.[Scheduled Ass_ Date End] <>  \'1753-01-01 00:00:00.000\'
//AND cph.[Order Type] IN (0, 1)
//GROUP BY
//	item.[Product Group Code],
//	sh.No_,
//	FORMAT (
//		sh.[Scheduled Ass_ Date End],
//		 \'MM/yyyy\'
//	)
//UNION
//	SELECT
//		item.[Product Group Code] cus_name,
//                item.[Product Group Code] cus_no,
//		sh.No_ blanket_no,
//		SUM (
//			cpl.Quantity * ISNULL(iuom.CUFT, 0)
//		) / 2350 total_cont,
//		FORMAT (
//			sh.[Scheduled Ass_ Date End],
//			 \'MM/yyyy\'
//		) month
//	FROM
//		[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
//	JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
//        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item ON item.No_ = cpl.ItemNo
//	LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
//		cpl.ItemNo = iuom.[Item No_]
//		AND iuom.Code =  \'CTNS\'
//	)
//	JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
//		sh.[Document Type] = 4
//		AND sh.[Related Order#] = cpl.[Blanket PO#]
//		AND sh.[Related Order#] <>  \'\'
//	)
//	WHERE
//		' . $dateField . ' BETWEEN :date_from3
//	AND :date_to2
//	AND sh.[Scheduled Ass_ Date End] < :date_from4
//	AND sh.[Scheduled Ass_ Date End] <>  \'1753-01-01 00:00:00.000\'
//	GROUP BY
//		item.[Product Group Code],
//		sh.No_,
//		FORMAT (
//			sh.[Scheduled Ass_ Date End],
//			 \'MM/yyyy\'
//		)
//	ORDER BY
//		item.[Product Group Code],
//		FORMAT (
//			sh.[Scheduled Ass_ Date End],
//			 \'MM/yyyy\'
//		)';
//        }else{
        $querySql = 'SELECT
                            cph.CustomerName cus_name,
                            cph.CustomerNo cus_no,
                            sh.No_ blanket_no,
                            SUM (cpl.Quantity * ISNULL(iuom.CUFT,0)) / 2350 total_cont,
                            FORMAT(sh.[Scheduled Ass_ Date End], \'yyyy/MM\') month,
                            FORMAT(sh.[Scheduled Ass_ Date End], \'MMM-yyyy\') schedule_group
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            cpl.ItemNo = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                            sh.[Document Type] = 4
                            AND sh.No_ = cpl.[Blanket PO#]
                    )
                    WHERE
                    ' . $dateField . ' BETWEEN :date_from1 AND :date_to1
                    AND sh.[Scheduled Ass_ Date End] < :date_from2
                    AND sh.[Scheduled Ass_ Date End] <> \'1753-01-01 00:00:00.000\'
                    AND cph.[Order Type] IN(0,1)
                    GROUP BY
                            cph.CustomerName,
                            cph.CustomerNo,
                            sh.No_,
                            FORMAT(sh.[Scheduled Ass_ Date End], \'yyyy/MM\'),
                            FORMAT(sh.[Scheduled Ass_ Date End], \'MMM-yyyy\')
                    UNION
                    SELECT
                            cph.CustomerName cus_name,
                            cph.CustomerNo cus_no,
                            sh.[Related Order#] blanket_no,
                            SUM (cpl.Quantity * ISNULL(iuom.CUFT,0)) / 2350
                            total_cont,
                            FORMAT(sh.[Scheduled Ass_ Date End], \'yyyy/MM\') month,
                            FORMAT(sh.[Scheduled Ass_ Date End], \'MMM-yyyy\') schedule_group
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            cpl.ItemNo = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                            sh.[Document Type] = 4
                            AND sh.[Related Order#] = cpl.[Blanket PO#]
                            AND sh.[Related Order#] <> \'\'
                    )
                    WHERE
                    ' . $dateField . ' BETWEEN :date_from3 AND :date_to2
                    AND sh.[Scheduled Ass_ Date End] < :date_from4
                    AND sh.[Scheduled Ass_ Date End] <> \'1753-01-01 00:00:00.000\'
                    GROUP BY
                            cph.CustomerName,
                            cph.CustomerNo,
                            sh.[Related Order#],
                            FORMAT(sh.[Scheduled Ass_ Date End], \'yyyy/MM\'),
                            FORMAT(sh.[Scheduled Ass_ Date End], \'MMM-yyyy\')
                    ORDER BY cph.CustomerName, FORMAT(sh.[Scheduled Ass_ Date End], \'yyyy/MM\')';
//        }
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);

        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from3', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from4', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));

        $sqlResult = $sqlCommand->queryAll();
        $result = [];
        if ($sqlResult) {
            $ratioArr = [];
            $blanketAndMonth = [];
            for ($i = 0; $i < count($sqlResult); $i++) {
                if (!in_array($sqlResult[$i]['blanket_no'] . $sqlResult[$i]['month'], $blanketAndMonth)) {
                    $blanketAndMonth[] = $sqlResult[$i]['blanket_no'] . $sqlResult[$i]['month'];
                    if (!isset($ratioArr[$sqlResult[$i]['blanket_no']]) || !isset($ratioArr[$sqlResult[$i]['blanket_no']][$group])) {

                        $ratioArr[$sqlResult[$i]['blanket_no']][$group] = $blanketPOModel->getRatioBlanketPoInPast($sqlResult[$i]['blanket_no'], $sqlResult[$i]['schedule_group']);
                    }
                    $ratio = $ratioArr[$sqlResult[$i]['blanket_no']][$group];
                    if (!isset($result[$sqlResult[$i]['cus_name']])) {
                        $result[$sqlResult[$i]['cus_name']] = [];
                    }
                    if (!isset($result[$sqlResult[$i]['cus_name']][$sqlResult[$i]['month']])) {
                        $result[$sqlResult[$i]['cus_name']][$sqlResult[$i]['month']] = 0;
                    }
                    $result[$sqlResult[$i]['cus_name']][$sqlResult[$i]['month']] += round($ratio * $sqlResult[$i]['total_cont'], 2);
                }
            }   
//            var_dump($result);die();
            return $result;
        }
        return FALSE;
    }

    public function getPOsForPlan($customer, $group, $month, $dateType) {
        $poCusModel = new POCusDetail();
        $blanketPOModel = new POCus();
        $dateField = $poCusModel->getDateField($dateType);
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($group);
        $querySql = 'SELECT
                            cph.PONo po_no,
                            cpl.[Blanket PO#] blanket_po_no,
                            cph.PODate po_date,
                            cph.CommitReqShipDateFrom confirm_date_start,
                            cph.CommitReqShipDateTo confirm_date_end,
                            cph.OriginalReqShipDateFrom request_date_start,
                            cph.OriginalReqShipDateTo request_date_end,
                            cph.PPCDate expect_assembly_date,
                            cph.ReqWHDate expect_warehouse_date,
                            ROUND(
                                    SUM (cpl.Quantity * iuom.CUFT) / 2350,
                                    2
                            ) total_cont,
                            sh.[Scheduled Ass_ Date Start] schedule_assembly_date_start,
                            sh.[Scheduled Ass_ Date End] schedule_assembly_date_end,
                            sh.[Expected WH Date] schedule_warehouse_date,
                            sh.[Estimated ETD] schedule_etd_date,
                            sh.[Your Reference] report_group
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            cpl.ItemNo = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                            sh.[Document Type] = 4
                            AND sh.No_ = cpl.[Blanket PO#]
                    )
                    WHERE ((' . $dateField . '<= :date_to1 AND sh.[Scheduled Ass_ Date Start] = \'1753-01-01 00:00:00.000\')
                    OR (' . $dateField . ' <= :date_from1 AND sh.[Scheduled Ass_ Date End] >= :date_from2)
                    OR ' . $dateField . ' BETWEEN :date_from3 AND :date_to3)
                    AND FORMAT(' . $dateField . ', \'yyyy/MM\') = :month
                    AND cph.CustomerName = :cus_name
                    AND cph.[Order Type] IN(0,1)
                    GROUP BY
                        cph.PONo,
                        cpl.[Blanket PO#],
                        sh.[Your Reference],
                        cph.PODate,
                        cph.CommitReqShipDateFrom,
                        cph.CommitReqShipDateTo,
                        cph.OriginalReqShipDateFrom,
                        cph.OriginalReqShipDateTo,
                        cph.PPCDate,
                        cph.ReqWHDate,
                        sh.[Scheduled Ass_ Date Start],
                        sh.[Scheduled Ass_ Date End],
                        sh.[Expected WH Date],
                        sh.[Estimated ETD]
                    ORDER BY
                            sh.[Your Reference],
                            cpl.[Blanket PO#],
                            cph.PODate,
                            cph.PONo;';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from3', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        // $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to3', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':month', $month);
        $sqlCommand->bindValue(':cus_name', $customer);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            $ratioArr = [];
            for ($i = 0; $i < count($sqlResult); $i++) {
                $sqlResult[$i]['request_date_start'] = strtotime($sqlResult[$i]['request_date_start']);
                $sqlResult[$i]['request_date_end'] = strtotime($sqlResult[$i]['request_date_end']);
                $sqlResult[$i]['confirm_date_start'] = strtotime($sqlResult[$i]['confirm_date_start']);
                $sqlResult[$i]['confirm_date_end'] = strtotime($sqlResult[$i]['confirm_date_end']);
                $sqlResult[$i]['schedule_etd_date'] = strtotime($sqlResult[$i]['schedule_etd_date']);
                $sqlResult[$i]['schedule_assembly_date_start'] = strtotime($sqlResult[$i]['schedule_assembly_date_start']);
                $sqlResult[$i]['schedule_assembly_date_end'] = strtotime($sqlResult[$i]['schedule_assembly_date_end']);
                $expect_assembly_date = strtotime($sqlResult[$i]['expect_assembly_date']);
                $sqlResult[$i]['total_cont_on_po'] = $sqlResult[$i]['total_cont'];
                if ($expect_assembly_date < strtotime($period[0]) OR $expect_assembly_date > strtotime($period[1])) {
                    if (!isset($ratioArr[$sqlResult[$i]['blanket_po_no']]) || !isset($ratioArr[$sqlResult[$i]['blanket_po_no']][$group])) {
                        $ratioArr[$sqlResult[$i]['blanket_po_no']][$group] = $blanketPOModel->getRatioBlanketPo($sqlResult[$i]['blanket_po_no'], $group);
                    }
//                    $ratio = $blanketPOModel->getRatioBlanketPo($sqlResult[$i]['blanket_po_no'], $group);
                    $ratio = $ratioArr[$sqlResult[$i]['blanket_po_no']][$group];
                    $sqlResult[$i]['total_cont'] = $sqlResult[$i]['total_cont'] * $ratio;
                }
            }
            return $sqlResult;
        }
        return [];
    }

    public function getSchedule($groupCode) {

        $sql = 'SELECT
                        cph.CustomerName cus_name,
                        cph.CustomerNo cus_no,
                        sh.No_ blanket_no,
                        FORMAT (cph.PPCDate, \'yyyy/MM\') months,
                        SUM (
                                cpl.Quantity * ISNULL(iuom.CUFT, 0)
                        ) / 2350 total_cont,
                        sh.[Conts Adjmt] cont_adjmnt
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                        cpl.ItemNo = iuom.[Item No_]
                        AND iuom.Code = \'CTNS\'
                )
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                        sh.[Document Type] = 4
                        AND sh.No_ = cpl.[Blanket PO#]
                        AND sh.[Scheduled Ass_ Date End] BETWEEN :date_from1
                        AND :date_to1
                )
                WHERE cph.[Order Type] IN(0,1)
                GROUP BY
                        cph.CustomerName,
                        cph.CustomerNo,
                        sh.No_,
                        FORMAT (cph.PPCDate, \'yyyy/MM\'),
                        sh.[Conts Adjmt]
                UNION
                        SELECT
                                cph.CustomerName cus_name,
                                cph.CustomerNo cus_no,
                                sh1.[Related Order#] blanket_no,
                                FORMAT (cph.PPCDate, \'yyyy/MM\') months,
                                SUM (
                                        cpl.Quantity * ISNULL(iuom.CUFT, 0)
                                ) / 2350 total_cont,
                                sh1.[Conts Adjmt] cont_adjmnt
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                        LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                                cpl.ItemNo = iuom.[Item No_]
                                AND iuom.Code = \'CTNS\'
                        )
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh1 ON (
                                sh1.[Document Type] = 4
                                AND sh1.[Related Order#] = cpl.[Blanket PO#]
                                AND sh1.[Scheduled Ass_ Date End] BETWEEN :date_from2
                                AND :date_to2
                        )
                        WHERE
                                cpl.[Blanket PO#] <> \'\'
                        AND [Conts Adjmt] > 0
                        GROUP BY
                                cph.CustomerName,
                                cph.CustomerNo,
                                sh1.[Related Order#],
                                FORMAT (cph.PPCDate, \'yyyy/MM\'),
                                sh1.[Conts Adjmt]
                        ORDER BY
                                FORMAT (cph.PPCDate, \'yyyy/MM\'),
                                cph.CustomerName';

        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($groupCode);

        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));
        $sqlResult = $sqlCommand->queryAll();
        $blanketPOModel = new POCus();
        if ($sqlResult) {
            $ratio = [];
            $blanketAndMonth = [];
            for ($i = 0; $i < count($sqlResult); $i++) {
                if (!in_array($sqlResult[$i]['blanket_no'] . $sqlResult[$i]['months'], $blanketAndMonth)) {
                    $blanketAndMonth[] = $sqlResult[$i]['blanket_no'] . $sqlResult[$i]['months'];
                    if (!isset($ratioArr[$sqlResult[$i]['blanket_no']]) || !isset($ratioArr[$sqlResult[$i]['blanket_no']][$groupCode])) {
                        $ratioArr[$sqlResult[$i]['blanket_no']][$groupCode] = $blanketPOModel->getRatioBlanketPo($sqlResult[$i]['blanket_no'], $groupCode);
                    }
                    $ratio = $ratioArr[$sqlResult[$i]['blanket_no']][$groupCode];

                    $row = $sqlResult[$i];
                    $sqlResult[$i]['scheduled_cont'] = $sqlResult[$i]['total_cont'] * $ratio;
                } else {
                    $sqlResult[$i]['scheduled_cont'] = 0;
                }
            }
//            var_dump($blanketAndMonth);
            return $sqlResult;
        }
        return [];
    }

    public function getPoListSchedule($customer, $groupCode, $month) {
        $sql = 'SELECT
                        cph.PONo po_no,
                        cpl.[Blanket PO#] blanket_po_no,
                        cph.PODate po_date,
                        cph.CommitReqShipDateFrom confirm_date_start,
                        cph.CommitReqShipDateTo confirm_date_end,
                        cph.OriginalReqShipDateFrom request_date_start,
                        cph.OriginalReqShipDateTo request_date_end,
                        cph.PPCDate expect_assembly_date,
                        cph.ReqWHDate expect_warehouse_date,
                        FORMAT (cph.PPCDate, \'yyyy/MM\') months,
                        SUM (
                                cpl.Quantity * ISNULL(iuom.CUFT, 0)
                        ) / 2350 total_cont,
                        sh.[Scheduled Ass_ Date Start] schedule_assembly_date_start,
                        sh.[Scheduled Ass_ Date End] schedule_assembly_date_end,
                        sh.[Expected WH Date] schedule_warehouse_date,
                        sh.[Estimated ETD] schedule_etd_date,
                        sh.[Your Reference] report_group
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                        cpl.ItemNo = iuom.[Item No_]
                        AND iuom.Code = \'CTNS\'
                )
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                        sh.[Document Type] = 4
                        AND sh.No_ = cpl.[Blanket PO#]
                        AND sh.[Scheduled Ass_ Date End] BETWEEN :date_from1
                        AND :date_to1
                )
                WHERE cph.CustomerName = :cus_name1
                        AND FORMAT (cph.PPCDate, \'yyyy/MM\') = :month1
                        AND cph.[Order Type] IN(0,1)
                GROUP BY
                cph.PONo,
                        cph.PONo,
                        cpl.[Blanket PO#],
                        cph.PODate,
                        cph.CommitReqShipDateFrom,
                        cph.CommitReqShipDateTo,
                        cph.OriginalReqShipDateFrom,
                        cph.OriginalReqShipDateTo,
                        cph.PPCDate,
                        cph.ReqWHDate,
                        FORMAT (cph.PPCDate, \'yyyy/MM\'),
                        sh.[Conts Adjmt],
                        sh.[Conts Adjmt],
                        sh.[Scheduled Ass_ Date Start],
                        sh.[Scheduled Ass_ Date End],
                        sh.[Expected WH Date],
                        sh.[Estimated ETD],
                        sh.[Your Reference]
                UNION
                        SELECT
                                cph.PONo po_no,
                                cpl.[Blanket PO#] blanket_po_no,
                                cph.PODate po_date,
                                cph.CommitReqShipDateFrom confirm_date_start,
                                cph.CommitReqShipDateTo confirm_date_end,
                                cph.OriginalReqShipDateFrom request_date_start,
                                cph.OriginalReqShipDateTo request_date_end,
                                cph.PPCDate expect_assembly_date,
                                cph.ReqWHDate expect_warehouse_date,
                                FORMAT (cph.PPCDate, \'yyyy/MM\') months,
                                SUM (
                                        cpl.Quantity * ISNULL(iuom.CUFT, 0)
                                ) / 2350 total_cont,
                                sh1.[Scheduled Ass_ Date Start] schedule_assembly_date_start,
                                sh1.[Scheduled Ass_ Date End] schedule_assembly_date_end,
                                sh1.[Expected WH Date] schedule_warehouse_date,
                                sh1.[Estimated ETD] schedule_etd_date,
                                sh1.[Your Reference] report_group
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
                        LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                                cpl.ItemNo = iuom.[Item No_]
                                AND iuom.Code = \'CTNS\'
                        )
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh1 ON (
                                sh1.[Document Type] = 4
                                AND sh1.[Related Order#] = cpl.[Blanket PO#]
                                AND sh1.[Scheduled Ass_ Date End] BETWEEN :date_from2
                                AND :date_to2
                        )
                        WHERE
                                cpl.[Blanket PO#] <> \'\'
                                AND [Conts Adjmt] > 0
                                AND cph.CustomerName = :cus_name2
                                AND FORMAT (cph.PPCDate, \'yyyy/MM\') = :month2
                        GROUP BY
                                cph.PONo,
                                cpl.[Blanket PO#],
                                cph.PODate,
                                cph.CommitReqShipDateFrom,
                                cph.CommitReqShipDateTo,
                                cph.OriginalReqShipDateFrom,
                                cph.OriginalReqShipDateTo,
                                cph.PPCDate,
                                cph.ReqWHDate,
                                FORMAT (cph.PPCDate, \'yyyy/MM\'),
                                sh1.[Conts Adjmt],
                                sh1.[Scheduled Ass_ Date Start],
                                sh1.[Scheduled Ass_ Date End],
                                sh1.[Expected WH Date],
                                sh1.[Estimated ETD],
                                sh1.[Your Reference]
                        ORDER BY
                                sh.[Your Reference],
                                cpl.[Blanket PO#],
                                cph.PODate,
                                cph.PONo;';
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($groupCode);
        $blanketPOModel = new POCus();

        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':cus_name1', $customer);
        $sqlCommand->bindValue(':cus_name2', $customer);
        $sqlCommand->bindValue(':month1', $month);
        $sqlCommand->bindValue(':month2', $month);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            $ratioArr = [];
            for ($i = 0; $i < count($sqlResult); $i++) {
                $sqlResult[$i]['request_date_start'] = strtotime($sqlResult[$i]['request_date_start']);
                $sqlResult[$i]['request_date_end'] = strtotime($sqlResult[$i]['request_date_end']);
                $sqlResult[$i]['confirm_date_start'] = strtotime($sqlResult[$i]['confirm_date_start']);
                $sqlResult[$i]['confirm_date_end'] = strtotime($sqlResult[$i]['confirm_date_end']);
                $sqlResult[$i]['schedule_etd_date'] = strtotime($sqlResult[$i]['schedule_etd_date']);
                $sqlResult[$i]['schedule_assembly_date_start'] = strtotime($sqlResult[$i]['schedule_assembly_date_start']);
                $sqlResult[$i]['schedule_assembly_date_end'] = strtotime($sqlResult[$i]['schedule_assembly_date_end']);
                $expect_assembly_date = strtotime($sqlResult[$i]['expect_assembly_date']);
                $sqlResult[$i]['total_cont_on_po'] = $sqlResult[$i]['total_cont'];
//                if($expect_assembly_date < strtotime($period[0]) OR $expect_assembly_date > strtotime($period[1])){
                $ratio = $blanketPOModel->getRatioBlanketPo($sqlResult[$i]['blanket_po_no'], $groupCode);
                $sqlResult[$i]['total_cont'] = $sqlResult[$i]['total_cont'] * $ratio;
//                }
            }
            return $sqlResult;
        }
        return [];
    }

}
