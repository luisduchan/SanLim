<?php

namespace backend\modules\common\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use DateTime;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\DateTimeTool;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class POCus extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function tableName() {
        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]';
    }

    public function getListCus($groupCodes) {
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($groupCodes);
        $placeholders = '';
        $result = [];
        for ($i = 0; $i < count($groupCodes) - 1; $i++) {
            $placeholders .= ':group' . $i . ',';
        }
        $placeholders .= ':group' . $i;
        $querySql = 'SELECT sh.[Sell-to Customer Name 2] customer
                    FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                    WHERE sh.[Document Type] = 4
                        AND (sh.[Your Reference] IN (' . $placeholders . ') OR sh.[Factory Cof_ Ship Date Start] BETWEEN :start_date AND :end_date)
                    GROUP BY sh.[Sell-to Customer Name 2]
                    ORDER BY sh.[Sell-to Customer Name 2]';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        foreach ($groupCodes as $i => $groupCode) {
            $sqlCommand->bindValue(':group' . $i, $groupCode);
        }
        $sqlCommand->bindValue(':start_date', $period[0]);
        $sqlCommand->bindValue(':end_date', $period[1]);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            foreach ($sqlResult as $row) {
                $result[] = $row['customer'];
            }
            return $result;
        }
        return FALSE;
    }

    public function getScheduledQuantity($reportGroup) {
        $querySql = 'SELECT
                            RTRIM(LTRIM(
                                            sh.[Sell-to Customer Name 2]
                                    )
                            ) customer,
                            ROUND(
                                    SUM (sl.Quantity * uom.CUFT) / 2350 + (
                                                            SELECT SUM([Conts Adjmt])
                                                            FROM
                                                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]
                                                            WHERE
                                                                     RTRIM(LTRIM([Sell-to Customer Name 2])) = RTRIM(LTRIM(sh.[Sell-to Customer Name 2]))
                                                            AND [Document Type] = 4
                                                            AND [Your Reference] = :report_group1
                                                            AND Finished = 0
                                    ),
                                    2
                            ) AS total_sheduled
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sh.No_ = sl.[Document No_]
                    AND sh.[Document Type] = sl.[Document Type]
                    AND sl.[Document Type] = 4
                    AND sl.Type = 2
                    LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] AS uom ON sl.No_ = uom.[Item No_]
                    AND uom.Code = \'CTNS\'
                    WHERE
                            sh.[Document Type] = 4
                    AND sh.[Your Reference] = :report_group2
                    AND (sh.NotRealOrder = 0)
                    GROUP BY
                            RTRIM(
                                    LTRIM(
                                            sh.[Sell-to Customer Name 2]
                                    )
                            ),
                            sh.[Your Reference]
                    ORDER BY
                            RTRIM(
                                    LTRIM(
                                            sh.[Sell-to Customer Name 2]
                                    )
                            );';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':report_group1', $reportGroup);
        $sqlCommand->bindValue(':report_group2', $reportGroup);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return ArrayHelper::map($sqlResult, 'customer', 'total_sheduled');
        }
        return FALSE;
    }

    public function getListPO($customer, $group) {

        $querySql = 'SELECT
                        sh_m.No_,
                        sh_m.[Sell-to Customer No_] cus_no,
                        sh_m.[Order Date] order_date,
                        sh_m.[Requested Delivery Date],
                        sh_m.[Requested Ship Date End],
                        sh_m.[Factory Cof_ Ship Date Start],
                        sh_m.[Factory Cof_ Ship Date End],

                        (SELECT SUM(sl1.Quantity * uom.CUFT)
                        FROM dbo.[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh1
                                        LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl1 ON sh1.No_ = sl1.[Document No_]
                                            AND sh1.[Document Type] = sl1.[Document Type]
                                            AND sl1.[Document Type] = 4
                                            AND sl1.Type = 2
                                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] AS uom ON sl1.No_ = uom.[Item No_]
                                            AND uom.Code = \'CTNS\'
                        WHERE sh1.No_ = sh_m.No_ OR sh1.[Related Order#] = sh_m.No_ OR sh_m.[Related Order#] = sh1.No_) / 2350 total_on_po,

                        (SELECT COALESCE(SUM(sl2.Quantity * uom.CUFT)/ 2350,0) + sh2.[Conts Adjmt]
                        FROM dbo.[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh2
                                        LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl2 ON sh2.No_ = sl2.[Document No_]
                                            AND sh2.[Document Type] = sl2.[Document Type]
                                            AND sl2.[Document Type] = 4
                                            AND sl2.Type = 2
                                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] AS uom ON sl2.No_ = uom.[Item No_]
                                            AND uom.Code = \'CTNS\'
                        WHERE sh2.No_ = sh_m.No_
                        GROUP BY sh2.[Conts Adjmt])  total_scheduled,

                        sh_m.[Created Date] created_date,
                        sh_m.[Modified Date] modified_date,
                        sh_m.[Scheduled Ass_ Date Start] ass_date_start,
                        sh_m.[Scheduled Ass_ Date End] ass_date_end

                    FROM
                        dbo.[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh_m

                    WHERE
                        [Sell-to Customer Name 2] = :customer
                        AND [Your Reference] = :group
                        AND sh_m.[Document Type] = 4
                    GROUP BY sh_m.No_,
                                sh_m.[Sell-to Customer No_],
                                sh_m.[Order Date],
                                sh_m.[Requested Delivery Date],
                                sh_m.[Requested Ship Date End],
                                sh_m.[Factory Cof_ Ship Date Start],
                                sh_m.[Factory Cof_ Ship Date End],
                                sh_m.[Conts Adjmt],
                                sh_m.[Created Date],
                                sh_m.[Modified Date],
                                sh_m.[Related Order#],
                                sh_m.[Scheduled Ass_ Date Start],
                                sh_m.[Scheduled Ass_ Date End]
                    ORDER BY
                        [Factory Cof_ Ship Date Start]';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':customer', $customer);
        $sqlCommand->bindValue(':group', $group);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return FALSE;
    }

    public function getOrderQty($groupCode) {
        $groupModel = new ReportGroup();
        $group = $groupModel->getGroup($groupCode);
        $startDate = $group['start_date'];
        $endDate = $group['end_date'];

        $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s.u', $startDate);
        $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s.u', $endDate);
//        var_dump($startDate);
//        var_dump($startDateTime);
//        var_dump($group);
//        die();
        $startDate = $startDateTime->format('Y-m-d');
        $endDate = $endDateTime->format('Y-m-d');
        $querySql = 'SELECT
                        RTRIM(LTRIM(sh.[Sell-to Customer Name 2])) customer,ROUND(
                            SUM (sl.Quantity * uom.CUFT) / 2350,2) AS total_ordered
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sh.No_ = sl.[Document No_]
                            AND sh.[Document Type] = sl.[Document Type]
                            AND sl.[Document Type] = 4
                            AND sl.Type = 2
                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] AS uom ON sl.No_ = uom.[Item No_]
                            AND uom.Code = \'CTNS\'
                    WHERE
                        sh.[Document Type] = 4
                        AND sh.[Factory Cof_ Ship Date Start] BETWEEN :start_date AND :end_date
                    GROUP BY RTRIM(LTRIM(sh.[Sell-to Customer Name 2]))
                    ORDER BY
                        RTRIM(LTRIM(sh.[Sell-to Customer Name 2]))';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':start_date', $startDate);
        $sqlCommand->bindValue(':end_date', $endDate);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return ArrayHelper::map($sqlResult, 'customer', 'total_ordered');
            ;
        }
        return FALSE;
    }

    public function getDetail($poNO) {
        $header = [];
        $lines = [];
        $headerSql = 'SELECT
                            [No_] po_no,
                            [Sell-to Customer No_] cust_no,
                            [Sell-to Customer Name 2] cust_name,
                            FORMAT([Created Date], \'MM/dd/yyyy\') create_date,
                            IK ik,
                            [Related Order#] related_order,
                            FORMAT([Order Date], \'MM/dd/yyyy\') order_date,
                            [Your Reference] report_group,
                            FORMAT([Requested Delivery Date], \'MM/dd/yyyy\') request_ship_date_start,
                            FORMAT([Requested Ship Date End], \'MM/dd/yyyy\') request_ship_date_end,

                            FORMAT([Scheduled Ass_ Date Start], \'MM/dd/yyyy\') ass_date_start,
                            FORMAT([Scheduled Ass_ Date End], \'MM/dd/yyyy\') ass_date_end,
                            [Location Code] location_code,
                            [Conts Adjmt] cont_adjmt

                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]
                    WHERE
                            No_ = :po_no';
        $sqlCommand = Yii::$app->dbMS->createCommand($headerSql);
        $sqlCommand->bindValue(':po_no', $poNO);
        $header = $sqlCommand->queryOne();

        if ($header) {
            if (!empty(trim($header['po_no']))) {
                $poNO = trim($header['po_no']);
            }
            $header['cont_adjmt'] = round($header['cont_adjmt'], 2);
            $header['ass_date'] = DateTimeTool::getDateDiplay($header['ass_date_start'], $header['ass_date_end']);
            $header['request_ship_date'] = DateTimeTool::getDateDiplay($header['request_ship_date_start'], $header['request_ship_date_end']);
            $sqlSaleLine = 'SELECT
                                    sl.No_ item_no,
                                    sl.Description description,
                                    image.nxpimg05 image,
                                    CAST (
                                            ROUND(uom.CUFT, 2) AS DECIMAL (6, 2)
                                    ) cuft,
                                    CAST (sl.Quantity AS DECIMAL(6, 2)) quantity,
                                    ROUND(
                                            CAST (
                                                    sl.Quantity * uom.CUFT AS DECIMAL (11, 2)
                                            ),
                                            2
                                    ) total_cuft,
                                    ROUND(
                                            CAST (
                                                    sl.Quantity * uom.CUFT /2350  AS DECIMAL (11, 2)
                                            ),
                                            2
                                    ) total_conatiner,
                                    ROUND(
                                            CAST (
                                                    sl.[Quantity Shipped] AS DECIMAL (11, 2)
                                            ),
                                            2
                                    ) qty_shipped
                            FROM
                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl OUTER APPLY (
                                            SELECT
                                                    TOP 1 *
                                            FROM
                                                    nxpimg
                                            WHERE
                                                    nxpimg01 = sl.No_
                                    ) image
                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] uom ON (
                                    sl.No_ = uom.[Item No_]
                                    AND uom.Code = \'CTNS\'
                            )
                            WHERE
                                    [Document No_] = :po_no AND sl.No_ <> \'\'';
            $sqlCommand = Yii::$app->dbMS->createCommand($sqlSaleLine);
            $sqlCommand->bindValue(':po_no', $poNO);
            $lines = $sqlCommand->queryAll();
//            $lines = (new \yii\db\Query())
//                            ->select('*')
//                            ->from('[SAN LIM FURNITURE VIETNAM LTD$Sales Line]')
//                            ->where(['Document No_' => $poNO])->all(\Yii::$app->dbMS);
            return [$header, $lines];
        }
        return [FALSE, FALSE];
    }

    public function getImage($itemNo) {

        $imageDLOB = (new \yii\db\Query())
                        ->select('nxpimg05')
                        ->from('[nxpimg]')
                        ->where(['nxpimg01' => $itemNo])->all(\Yii::$app->dbMS);
//                        ->where(['nxpimg01' => $itemNo])->all(\Yii::$app->dbMS_NW);
//        $querySql = 'SELECT nxpimg01 FROM [nxpimg] WHERE nxpimg01=:itemNo';
//        $sqlCommand = Yii::$app->dbMS_NW->createCommand($querySql);
//        $sqlCommand->bindValue(':itemNo', $itemNo);
////        var_dump($sqlCommand);die();
//        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);
//        die();
        return $imageDLOB;
    }

    public function getOderStatus($customerNo, $startDate, $endDate, $dateType) {
        $dateColum = ['request_date' => 'cph.OriginalReqShipDateFrom',
            'confirm_date' => 'cph.CommitReqShipDateFrom',
            'expected_warehouse_date' => 'cph.ReqWHDate',
            'expected_aseembling_date' => 'cph.PPCDate',
        ];

        $querySql = 'SELECT item.[Product Group Code] prod_group_code,' . $dateColum[$dateType] . ' selected_date, ROUND(SUM(cpl.Quantity*uom.CUFT/2350),2) total_order
            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item ON (cpl.ItemNo=item.No_)
            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] uom ON (cpl.ItemNo = uom.[Item No_] AND uom.Code = \'CTNS\')
            WHERE ' . $dateColum[$dateType] . ' BETWEEN :date_from AND :date_to';
        if ($customerNo) {
            $querySql .= ' AND cph.CustomerNo=:cus_no';
        }
        $querySql .= ' GROUP BY item.[Product Group Code],' . $dateColum[$dateType]
                . ' ORDER BY ' . $dateColum[$dateType] . ' ,item.[Product Group Code]';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':date_from', $startDate);
        $sqlCommand->bindValue(':date_to', $endDate);
        if ($customerNo) {
            $sqlCommand->bindValue(':cus_no', $customerNo);
        }
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $sqlResult[$i]['selected_date'] = date("m/d/Y", strtotime($sqlResult[$i]['selected_date']));
            }
            return $sqlResult;
        }
        return FALSE;
    }

    public function getOderStatusKeyVal($customerNo, $startDate, $endDate, $dateType) {
        $sqlResult = $this->getOderStatus($customerNo, $startDate, $endDate, $dateType);
        if ($sqlResult) {
            $result = [];
            $header = array_column($sqlResult, 'selected_date');
            $header = array_unique($header);
            $header = array_values($header);
            for ($iRow = 0; $iRow < count($sqlResult); $iRow++) {
                for ($i = 0; $i < count($header); $i++) {
                    if ($sqlResult[$iRow]['selected_date'] == $header[$i]) {
                        $result[$sqlResult[$iRow]['prod_group_code']][$header[$i]] = $sqlResult[$iRow]['total_order'];
                    } else {
                        if (!isset($result[$sqlResult[$iRow]['prod_group_code']][$header[$i]])) {
                            $result[$sqlResult[$iRow]['prod_group_code']][$header[$i]] = 0;
                        }
                    }
                }
            }
            return [$header, $result];
        }
        return FALSE;
    }

    public function getOderStatusFormated($customerNo, $startDate, $endDate, $dateType) {
        list($header, $sqlResult) = $this->getOderStatusKeyVal($customerNo, $startDate, $endDate, $dateType);
        if ($sqlResult) {
            $result = [];
            $i = 0;
            foreach ($sqlResult as $productGroup => $detail) {
                $result[$i]['product_group'] = $productGroup;
                foreach ($detail as $selectedDate => $total) {
                    $result[$i][$selectedDate] = $total;
                }
                $i++;
            }
            return [$header, $result];
        }
        return FALSE;
    }

    public function getRatioBlanketPo($blanketNo, $reportGroup) {
        if (!$blanketNo) {
            return 1;
        }
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($reportGroup);
        $getBlanketOrderSql = 'SELECT [Related Order#] related_order
                            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                            WHERE [No_]= :blanketNo AND sh.[Related Order#] <> \'\' ';

        $sqlCommand = Yii::$app->dbMS->createCommand($getBlanketOrderSql);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlResult = $sqlCommand->queryOne();
        if (!$sqlResult) {
            return 1;
        }
        $blanketNo = (isset($sqlResult['related_order']) && $sqlResult['related_order'] != '') ? $sqlResult['related_order'] : $blanketNo;
//        $orginalBlanketOrder = $sqlResult['no'];
        $getTotalContSql = 'SELECT SUM(sl.Quantity * uom.CUFT)/2350 total_cont
                            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (sl.[Document No_] = sh.No_)
                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] uom ON (sl.No_ = uom.[Item No_] AND uom.Code = \'CTNS\')
                            WHERE [Document No_]= :blanketNo AND sh.[Related Order#] <> \'\' ';

        $sqlCommand = Yii::$app->dbMS->createCommand($getTotalContSql);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlResult = $sqlCommand->queryOne();

        $totalCont = round($sqlResult['total_cont'], 2);
        if ($totalCont <= 0) {
            return 1;
        }
        $getVirBlanketOrder = 'SELECT [Conts Adjmt]  cont_adjmt
                                FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                                WHERE [Related Order#] = :blanketNo
                                AND [Scheduled Ass_ Date End] NOT BETWEEN :date_from AND :date_start AND sh.[Document Type]=4';
        $sqlCommand = Yii::$app->dbMS->createCommand($getVirBlanketOrder);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlCommand->bindValue(':date_from', $period[0]);
        $sqlCommand->bindValue(':date_start', $period[1]);
        $sqlResult = $sqlCommand->queryAll();
        if (!$sqlResult) {
            return 1;
        }
        $element = 0;
        foreach ($sqlResult as $row) {
//            $tempElement = $row['cont_adjmt'];
            $tempElement = round($row['cont_adjmt'], 2);
            if ($tempElement < 0) {
                $element += - $tempElement;
            } else {
                $element += $totalCont - $tempElement;
            }
        }
        return $element / $totalCont;
    }

    public function getRatioBlanketPoInPast($blanketNo, $reportGroup) {
        if (!$blanketNo) {
            return 1;
        }
        $reportGroup = strtoupper($reportGroup);
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($reportGroup);
        $getBlanketOrderSql = 'SELECT [Related Order#] related_order
                            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                            WHERE [No_]= :blanketNo AND [Related Order#] <> \'\'';
        $sqlCommand = Yii::$app->dbMS->createCommand($getBlanketOrderSql);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlResult = $sqlCommand->queryOne();
        if (!$sqlResult || !isset($sqlResult['related_order']) || $sqlResult['related_order'] == '') {
            return 1;
        }
        $blanketNo = (isset($sqlResult['related_order']) && $sqlResult['related_order'] != '') ? $sqlResult['related_order'] : $blanketNo;
        $getTotalContSql = 'SELECT SUM(sl.Quantity * uom.CUFT)/2350 total_cont
                            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (sl.[Document No_] = sh.No_)
                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] uom ON (sl.No_ = uom.[Item No_] AND uom.Code = \'CTNS\')
                            WHERE [Document No_]= :blanketNo AND sh.[Related Order#] <> \'\' ';

        $sqlCommand = Yii::$app->dbMS->createCommand($getTotalContSql);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlResult = $sqlCommand->queryOne();

        $totalCont = round($sqlResult['total_cont'], 2);
        if ($totalCont <= 0) {
            return 1;
        }
        $getVirBlanketOrder = 'SELECT [Conts Adjmt]  cont_adjmt
                                    FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]
                                    WHERE [Related Order#] = :blanketNo
                                    AND [Scheduled Ass_ Date End] BETWEEN :date_from AND :date_to';
        $sqlCommand = Yii::$app->dbMS->createCommand($getVirBlanketOrder);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlCommand->bindValue(':date_from', $period[0]);
        $sqlCommand->bindValue(':date_to', $period[1]);
        $sqlResult = $sqlCommand->queryAll();
        if (!$sqlResult) {
            return 1;
        }
        $element = 0;
        foreach ($sqlResult as $row) {
            $tempElement = round($row['cont_adjmt'], 2);
            if ($tempElement < 0) {
                $element += $totalCont + $tempElement;
            } else {
                $element += $tempElement;
            }
        }
        return $element / $totalCont;
    }

//    public function getRatioBlanketPo($blanketNo, $reportGroup){
//        if(!$blanketNo){
//            return 1;
//        }
//        $reportGroupModel = new ReportGroup();
//        $period = $reportGroupModel->getPeriod($reportGroup);
////        var_dump($period);
//        $getBlanketOrderSql = 'SELECT [No_]
//                            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
//                            WHERE [No_]= :blanketNo AND sh.[Related Order#] <> \'\' ';
//        $sqlCommand = Yii::$app->dbMS->createCommand($getBlanketOrderSql);
//        $sqlCommand->bindValue(':blanketNo', $blanketNo);
//        $sqlResult = $sqlCommand->queryOne();
//        if(!$sqlResult){
//            return 1;
//        }
//        $getTotalContSql = 'SELECT SUM(sl.Quantity * uom.CUFT)/2350 total_cont
//                            FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
//                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (sl.[Document No_] = sh.No_)
//                            LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] uom ON (sl.No_ = uom.[Item No_] AND uom.Code = \'CTNS\')
//                            WHERE [Document No_]= :blanketNo AND sh.[Related Order#] <> \'\' ';
//
//        $sqlCommand = Yii::$app->dbMS->createCommand($getTotalContSql);
//        $sqlCommand->bindValue(':blanketNo', $blanketNo);
//        $sqlResult = $sqlCommand->queryOne();
//
//        $totalCont = round($sqlResult['total_cont'],2);
//        if($totalCont<=0){
//            return 1;
//
//        }
//        $getVirBlanketOrder = 'SELECT [Conts Adjmt]  cont_adjmt
//                                FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]
//                                WHERE [Related Order#] = :blanketNo
//                                AND [Scheduled Ass_ Date End] BETWEEN :date_from AND :date_start';
//        $sqlCommand = Yii::$app->dbMS->createCommand($getVirBlanketOrder);
//        $sqlCommand->bindValue(':blanketNo', $blanketNo);
//        $sqlCommand->bindValue(':date_from', $period[0]);
//        $sqlCommand->bindValue(':date_start', $period[1]);
//        $sqlResult = $sqlCommand->queryOne();
//        if(!$sqlResult || $sqlResult['cont_adjmt']==0){
//            return 1;
//        }
//        $element = round($sqlResult['cont_adjmt'],2);
//        if($element<0){
//            $element = $totalCont + $element;
//        }
//        return $element/$totalCont;
//
//
//    }
    public function checkAssDateandReportGroup() {
        $sql = 'SELECT
                        sh.[Sell-to Customer Name 2],
                        sh.No_ no,
                        sh.[Your Reference],
                        sh.[Scheduled Ass_ Date Start]
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Report Group Setup] rgs ON sh.[Your Reference] = rgs.Code
                WHERE
                        sh.[Document Type] = 4
                AND rgs.[End Date] > \'2017-07-31\'
                AND (
                        (
                                sh.[Scheduled Ass_ Date Start] NOT BETWEEN rgs.[Begin Date]
                                AND rgs.[End Date]
                                AND sh.[Scheduled Ass_ Date Start] > \'2017-01-01\'
                        )
                        OR (
                                sh.[Scheduled Ass_ Date End] NOT BETWEEN rgs.[Begin Date]
                                AND rgs.[End Date]
                                AND sh.[Scheduled Ass_ Date End] > \'2017-01-01\'
                        )
                )';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return FALSE;
    }

//    public function getCustomerList($reportGroup)
}
