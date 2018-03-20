<?php

namespace backend\modules\common\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use backend\modules\common\models\DateTimeTool;
use DateTime;
use backend\modules\common\models\ReportGroup;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class POCusDetail extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function tableName() {
        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader]';
    }

    public function getListCus($groupCodes, $dateCode) {
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($groupCodes);
        $result = [];
        $dateField = $this->getDateField($dateCode);
        $querySql = 'SELECT CustomerName customer, CustomerNo customer_no
                    FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    WHERE ' . $dateField . ' BETWEEN :start_date AND :end_date
                    GROUP BY CustomerName, CustomerNo
                    ORDER BY CustomerName';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
//        foreach ($groupCodes as $i => $groupCode) {
//            $sqlCommand->bindValue(':group' . $i, $groupCode);
//        }
        $sqlCommand->bindValue(':start_date', $period[0]);
        $sqlCommand->bindValue(':end_date', $period[1]);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            foreach ($sqlResult as $row) {
                $result[$row['customer_no']] = $row['customer'];
            }
            return $result;
        }
        return FALSE;
    }

    public function getDateTypeList() {
        $dateTypeList = [
//            'request_date' => 'Customer Request Date',
            'confirm_date' => 'Confirm Ship Date',
            'expected_warehouse_date' => 'Expected Warehouse Date',
            'expected_aseembling_date' => 'Expected Assembling Date',
        ];
        return $dateTypeList;
    }

    public function getDateField($dateCode) {
        $dateColum = [
//            'request_date' => 'cph.OriginalReqShipDateFrom',
            'confirm_date' => 'cph.CommitReqShipDateFrom',
            'expected_warehouse_date' => 'cph.ReqWHDate',
            'expected_aseembling_date' => 'cph.PPCDate',
        ];
        return $dateColum[$dateCode];
    }

    public function getTotalOrder($groupCode, $dateCode, $poType = NULL) {
        $reportGroupModel = new ReportGroup();
        $exCondition = '';
        if ($poType && $poType == 'late') {
            $exCondition = ' AND (((CASE WHEN cph.CommitReqShipDateTo > \'1753-01-01\' THEN cph.CommitReqShipDateTo ELSE cph.CommitReqShipDateFrom END) < sh.[Estimated ETD]))';
        }
        if ($poType && $poType == 'not_plan') {
            $exCondition = ' AND sh.[Estimated ETD] <= \'1753-01-01 00:00:00.000\'';
        }
        $period = $reportGroupModel->getPeriod($groupCode);
        $dateField = $this->getDateField($dateCode);
        $querySql = 'SELECT CustomerName customer, ROUND(SUM(cpl.Quantity*iuom.CUFT)/2350,2) total_cont
                    FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo=cpl.PONo)
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (cpl.ItemNo=iuom.[Item No_] AND iuom.Code=\'CTNS\')
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                            sh.[Document Type] = 4
                            AND sh.No_ = cpl.[Blanket PO#]
                    )
                    WHERE ' . $dateField . ' BETWEEN :date_from AND :date_to' . $exCondition .
                ' GROUP BY CustomerName
                    ORDER BY CustomerName';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':date_from', $period[0]);
        $sqlCommand->bindValue(':date_to', $period[1]);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return ArrayHelper::map($sqlResult, 'customer', 'total_cont');
        }
        return FALSE;
    }

    public function getPOsBaseCustomerGroup($customer, $dateStart, $dateEnd, $dateTypeCode, $poType = NULL) {
        $exCondition = '';
        if ($poType && $poType == 'late') {
            $exCondition = ' AND ((CASE WHEN cph.CommitReqShipDateTo > \'1753-01-01\' THEN cph.CommitReqShipDateTo ELSE cph.CommitReqShipDateFrom END) < sh.[Estimated ETD])';
        }
        if ($poType && $poType == 'notplan') {
            $exCondition = ' AND sh.[Estimated ETD] <= \'1753-01-01 00:00:00.000\'';
        }
        $dateField = $this->getDateField($dateTypeCode);
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
                            sh.[Estimated ETD] schedule_etd_date
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
                            cph.CustomerName = :cus_name
                    AND ' . $dateField . ' BETWEEN :date_start
                    AND :date_end' . $exCondition .
                ' GROUP BY
                            cph.PONo,
                            cpl.[Blanket PO#],
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
                            cpl.[Blanket PO#],
                            cph.PODate,
                            cph.PONo;';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':cus_name', $customer);
        $sqlCommand->bindValue(':date_start', $dateStart);
        $sqlCommand->bindValue(':date_end', $dateEnd);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $sqlResult[$i]['request_date_start'] = strtotime($sqlResult[$i]['request_date_start']);
                $sqlResult[$i]['request_date_end'] = strtotime($sqlResult[$i]['request_date_end']);
                $sqlResult[$i]['confirm_date_start'] = strtotime($sqlResult[$i]['confirm_date_start']);
                $sqlResult[$i]['confirm_date_end'] = strtotime($sqlResult[$i]['confirm_date_end']);
                $sqlResult[$i]['schedule_etd_date'] = strtotime($sqlResult[$i]['schedule_etd_date']);
                $sqlResult[$i]['schedule_assembly_date_start'] = strtotime($sqlResult[$i]['schedule_assembly_date_start']);
                $sqlResult[$i]['schedule_assembly_date_end'] = strtotime($sqlResult[$i]['schedule_assembly_date_end']);
            }
            return $sqlResult;
        }
        return [];
    }

    public function checkCuft() {
        $sql = 'SELECT
                        DISTINCT cpl.ItemNo item_no
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo = cpl.PONo)
                LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                        cpl.ItemNo = iuom.[Item No_]
                        AND iuom.Code = \'CTNS\'
                )
                WHERE
                        (
                                iuom.CUFT IS NULL
                                OR iuom.CUFT <= 0
                        )
                AND cpl.ItemNo <> \'\' AND cph.[Order Type] <> 2;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return FALSE;
    }

    public static function updatePOContainer() {
//        var_dump(DateTimeTool::convertTimeZone(date("Y-m-d H:i:s")));
        $sql = 'SELECT
                        cph.PONo po_no,
                        cph.CustomerName customer_name,
                        SUM (cpl.Quantity * uom.CUFT) / 2350 total_container,
                        CASE
                WHEN MAX (cpl.CreateDate) >= MAX (
                        cpl.[Last Modified Date Time]
                ) THEN
                        FORMAT(MAX (cpl.CreateDate), \'yyyy-MM-dd HH:mm:ss\')
                ELSE
                        FORMAT(MAX (cpl.[Last Modified Date Time]), \'yyyy-MM-dd HH:mm:ss\')
                END last_modified,
                CustomerNo customer_no,
                CustomerName customer_name,
                FORMAT(CommitReqShipDateFrom, \'yyyy-MM-dd HH:mm:ss\') confirm_date_from,
                FORMAT(CommitReqShipDateTo, \'yyyy-MM-dd HH:mm:ss\') confirm_date_to
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cpl.PONo = cph.PONo)
                LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] AS uom ON cpl.ItemNo = uom.[Item No_]
                AND uom.Code = \'CTNS\'
                GROUP BY
                        cph.PONo,
                        cph.CustomerName,
                CustomerNo,
                CustomerName,
                FORMAT(CommitReqShipDateFrom, \'yyyy-MM-dd HH:mm:ss\'),
                FORMAT(CommitReqShipDateTo, \'yyyy-MM-dd HH:mm:ss\')
                HAVING
                        CASE
                WHEN MAX (cpl.CreateDate) >= MAX (
                        cpl.[Last Modified Date Time]
                ) THEN
                        MAX (cpl.CreateDate)
                ELSE
                        MAX (
                                cpl.[Last Modified Date Time]
                        )
                END >= DATEADD(
                        DAY,
                        DATEDIFF(DAY, 0, GETDATE()),
                        - 5
                )
                ORDER BY
                        CASE
                WHEN MAX (cpl.CreateDate) >= MAX (
                        cpl.[Last Modified Date Time]
                ) THEN
                        MAX (cpl.CreateDate)
                ELSE
                        MAX (
                                cpl.[Last Modified Date Time]
                        )
                END DESC;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlResult = $sqlCommand->queryAll();
        if (!$sqlResult) {
            return True;
        }
        foreach ($sqlResult as $row) {
            $poNo = $row['po_no'];
            $totalContainer = $row['total_container'];
            $lastModified = $row['last_modified'];
            $customerName = $row['customer_name'];
            $customerNo = $row['customer_no'];
            $confirmDateFrom = $row['confirm_date_from'];
            $confirmDateTo = $row['confirm_date_to'];
            $sql = 'SELECT
                    *
                    FROM
                            purchase_order_log
                    WHERE
                            purchase_order_no = :po_no
                    AND nav_update_date = :nav_update_date;';
            $sqlCommand = Yii::$app->db->createCommand($sql);
            $sqlCommand->bindValue(':po_no', $poNo);
            $sqlCommand->bindValue(':nav_update_date', $lastModified);
            $poLog = $sqlCommand->queryAll();
            if (!$poLog) {
                $sqlInsert = 'INSERT INTO purchase_order_log (
                                    purchase_order_no,
                                    total_container,
                                    create_date,
                                    nav_update_date,
                                    customer_code,
                                    customer_name,
                                    confirm_date_from,
                                    confirm_date_to
                            )
                            VALUES(
                                    :purchase_order_no,
                                    :total_container,
                                    :create_date,
                                    :nav_update_date,
                                    :customer_code,
                                    :customer_name,
                                    :confirm__date_from,
                                    :confirm_date_to
                                    )';
                $parameters = array(':purchase_order_no' => $poNo,
                    ':total_container' => $totalContainer,
                    ':create_date' => date("Y-m-d h:i:sa")
                );
                $sqlCommand = Yii::$app->db->createCommand($sqlInsert);
                $sqlCommand->bindValue(':purchase_order_no', $poNo);
                $sqlCommand->bindValue(':total_container', round($totalContainer, 3));
                $sqlCommand->bindValue(':create_date', date("Y-m-d H:i:s"));
                $sqlCommand->bindValue(':nav_update_date', $lastModified);
                $sqlCommand->bindValue(':customer_code', $customerNo);
                $sqlCommand->bindValue(':customer_name', $customerName);
                $sqlCommand->bindValue(':confirm__date_from', $confirmDateFrom);
                $sqlCommand->bindValue(':confirm_date_to', $confirmDateTo);
                $sqlCommand->execute();
            }
        }
    }

    public function getPODetail($poNo) {
        
    }

//    public function getCustomerList($reportGroup)
}
