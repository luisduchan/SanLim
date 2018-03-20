<?php

namespace backend\modules\scorecard\models;

use Yii;
use backend\modules\common\models\ReportGroup;
use DateTime;
use backend\modules\common\models\DateTimeTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Scorecard extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function tableName() {
        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]';
    }

    public function getWSIAllPoWithLateDay($group, $baseOnCofirmShipDate) {
        $allPos = $this->getAllPO($group, '', $baseOnCofirmShipDate);
        if (!$allPos) {
            return [];
        }
        for ($i = 0; $i < count($allPos); $i++) {
            $row = $allPos[$i];
            list($maxETDDate, $maxLCLDate, $allLineIgnore) = $this->getWSILateDay($row['po_no'], $row['confirm_etd_to']);
            $lateETD = FALSE;
            $lateETDDisplay = FALSE;
            $lateLCL = FALSE;
//            $confirmDateTo = DateTime::createFromFormat('m/d/Y', $row['confirm_etd_to']);
            $confirmDateTo = strtotime($row['confirm_etd_to']);
            if ($allLineIgnore) {
                $allPos[$i]['max_delay_day'] = 0;
                $allPos[$i]['max_delay_day_display'] = 'exception';
            } elseif ($maxLCLDate === FALSE && $maxETDDate === FALSE) {
                $allPos[$i]['max_delay_day'] = NULL;
                $allPos[$i]['max_delay_day_display'] = NULL;
            } else {
                if ($maxLCLDate) {
//                $maxLCLDate = DateTime::createFromFormat('m/d/Y', $maxLCLDate);
                    $maxLCLDate = strtotime($maxLCLDate);
//                $maxLCLDate = strtotime($maxLCLDate)
                    $lateLCL = ($maxLCLDate - $confirmDateTo) / (60 * 60 * 24);
                }
                if ($maxETDDate) {
//                $maxETDDate = DateTime::createFromFormat('m/d/Y', $maxETDDate);
                    $maxETDDate = strtotime($maxETDDate);
                    $lateETD = ($maxETDDate - strtotime("+7 day", $confirmDateTo)) / (60 * 60 * 24);
                    $lateETDDisplay = ($maxETDDate - $confirmDateTo) / (60 * 60 * 24);
                }
                if ($lateETD === FALSE || $lateLCL === FALSE) {
                    $allPos[$i]['max_delay_day'] = $lateLCL === FALSE ? $lateETD : $lateLCL;
                    $allPos[$i]['max_delay_day_display'] = $lateLCL === FALSE ? $lateETDDisplay : $lateLCL;
                } else {
                    $allPos[$i]['max_delay_day'] = ($lateLCL < $lateETD) ? $lateETD : $lateLCL;
//                    $allPos[$i]['max_delay_day_display'] = ($lateLCL < $lateETD) ? $lateETDDisplay : (($lateETDDisplay > $lateLCL) ? $lateETDDisplay : $lateLCL);
                    $allPos[$i]['max_delay_day_display'] = ($lateLCL < $lateETD) ? $lateETDDisplay : $lateLCL;
                }
            }
        }
        return $allPos;
    }

    public function getWSILatePos($group) {
        $allPos = $this->getWSIAllPoWithLateDay($group);
        if (!$allPos) {
            return [];
        }
        $result = [];
        for ($i = 0; $i < count($allPos); $i++) {
            $row = $allPos[$i];
            if ($row['max_delay_day'] > 0 && $row['ignore'] == 0 && $row['remain_quantity'] == 0) {
                $result[] = $row;
            }
        }
        return $result;
    }

    public function getWSIScorceCardPos($group, $baseOnCofirmShipDate) {
        $allPos = $this->getWSIAllPoWithLateDay($group, $baseOnCofirmShipDate);
        if (!$allPos) {
            return [];
        }
        $onTimePos = [];
        $latePos = [];
        $shortShipPos = [];
        $missingPos = [];
//        var_dump($allPos);
        for ($i = 0; $i < count($allPos); $i++) {
            $row = $allPos[$i];

            if (($row['remain_quantity'] == 0 && $row['ignore'] == 1) || ($row['remain_quantity'] == 0 && $row['max_delay_day'] !== NULL && $row['max_delay_day'] <= 0)) {
                $onTimePos[] = $row;
            } elseif ($row['remain_quantity'] == 0 && $row['max_delay_day'] > 0) {
                $latePos[] = $row;
            } elseif ($row['total_shipped'] == 0) {
                $missingPos[] = $row;
            } else {
                $shortShipPos[] = $row;
            }
        }
        return [$onTimePos, $latePos, $shortShipPos, $missingPos];
    }

//    public function getTotalPO($group, $cusNo) {
//
//    }
    public function getAllPO($group, $customerNo, $baseOnCofirmShipDate = False) {
        $customerNo = 'C21001';
        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($group);
        $dateField = $baseOnCofirmShipDate ? 'cph.CommitReqShipDateTo' : 'cph.CurrentShipDateTo';
        $querySql = 'SELECT DISTINCT
                            cph.PONo po_no,
                            cph.CustomerNo cust_no,
                            FORMAT (cph.PODate, \'MM/dd/yyyy\') AS order_date,
                            FORMAT (
                                    cph.CommitReqShipDateFrom,
                                    \'MM/dd/yyyy\'
                            ) confirm_etd_from,
                            FORMAT (
                                    cph.CommitReqShipDateTo,
                                    \'MM/dd/yyyy\'
                            ) AS confirm_etd_to,
                            FORMAT (
                                    cph.CurrentShipDateTo,
                                    \'MM/dd/yyyy\'
                            ) AS current_etd_end,
                            cph.Ignore ignore,
                            cph.[Noted 2] noted,
                            CAST (
                                    (
                                            SELECT
                                                    SUM (cpl1.Quantity)
                                            FROM
                                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl1
                                            WHERE
                                                    cpl1.PONo = cph.PONo
                                    ) AS INT
                            ) total_order,
                            CAST (
                                    (
                                            SELECT
                                                    SUM (csn.Quantity)
                                            FROM
                                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
                                                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (sh.No_ = csn.[SO No_] AND sh.Ship=1)
                                            WHERE
                                                    csn.[CustPO No_] = cph.PONo
                                    ) AS INT
                            ) total_shipped
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON cph.PONo = cpl.PONo
                    WHERE
                            cph.CustomerNo = :cust_no
                    AND  ' . $dateField . '  BETWEEN :confirm_date_from
                    AND :confirm_date_to';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':cust_no', $customerNo);
        $sqlCommand->bindValue(':confirm_date_from', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':confirm_date_to', date('Y-m-d', strtotime($period[1])));
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['remain_quantity'] = $row['total_order'] - $row['total_shipped'];
                if ($row['ignore'] == 1) {
                    $sqlResult[$i]['ignore_display'] = 'Yes';
                } else {
                    $sqlResult[$i]['ignore_display'] = '';
                }
                $sqlResult[$i]['confirm_etd'] = DateTimeTool::getDateDiplay($row['confirm_etd_from'],$row['confirm_etd_to']);
//                $sqlResult[$i]['current_etd'] = DateTimeTool::getDateDiplay($row['current_etd']);
//                $sqlResult[$i]['confirm_etd'] = ($row['confirm_etd_to']) ?
//                        ($row['confirm_etd_from']) . ' - '
//                        . ($row['confirm_etd_to']) : $row['confirm_etd_from'];
            }
            return $sqlResult;
        }
        return [];
    }

    public function getOnTimePo($group, $customerNo) {

    }

    public function getWSILateDay($poNo, $confirmETDTo) {
        $allLineIgnore = FALSE;
        $sql = 'SELECT
                        sh.[Transport Method] transport_method,FORMAT (
                                    MAX(sh.[Shipment Calc_ Date]),
                                    \'MM/dd/yyyy\'
                            ) max_date
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn ON (
                        csn.[CustPO No_] = cpl.PONo
                        AND csn.[Item No_] = cpl.ItemNo
                )
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sh.No_ = csn.[SO No_]
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sl.[Document No_] = sh.No_ AND sl.No_ = csn.[Item No_]
                WHERE cpl.PONo = :po_no AND sh.[Transport Method] IN (\'\',\'LCL\') AND sl.Ignore <> 1
                GROUP BY sh.[Transport Method];';
        $maxETDDate = FALSE;
        $maxLCLDate = FALSE;
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':po_no', $poNo);
        $sqlResult = $sqlCommand->queryALL();
        foreach ($sqlResult as $row) {
            if (!$maxETDDate && $row['transport_method'] == '') {
                $maxETDDate = $row['max_date'];
            }
            if (!$maxLCLDate && $row['transport_method'] == 'LCL') {
                $maxLCLDate = $row['max_date'];
            }
        }
        if ($maxETDDate === FALSE && $maxLCLDate === FALSE) {
            $sql = 'SELECT
                            sh.[Transport Method] transport_method,FORMAT (
                                        MAX(sh.[Shipment Calc_ Date]),
                                        \'MM/dd/yyyy\'
                                ) max_date
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn ON (
                            csn.[CustPO No_] = cpl.PONo
                            AND csn.[Item No_] = cpl.ItemNo
                    )
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sh.No_ = csn.[SO No_]
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sl.[Document No_] = sh.No_ AND sl.No_ = csn.[Item No_]
                    WHERE cpl.PONo = :po_no AND sh.[Transport Method] IN (\'\',\'LCL\') AND sl.Ignore = 1
                    GROUP BY sh.[Transport Method];';

            $sqlCommand = Yii::$app->dbMS->createCommand($sql);
            $sqlCommand->bindValue(':po_no', $poNo);
            $sqlResult = $sqlCommand->queryALL();
            if ($sqlResult) {
                $allLineIgnore = True;
            }
        }
        return [$maxETDDate, $maxLCLDate, $allLineIgnore];
    }

    public function getShippingDetail($poNo) {
        $querySql = 'SELECT
                            cph.PONo po_number,
                            cpl.ItemNo item_number,
                            --nxpimg05 image,
                            cpl.Description description,
                            CAST (
                                    cpl.Quantity AS DECIMAL (10, 2)
                            ) AS order_quantity,
                            CAST (
                                    csn.Quantity AS DECIMAL (10, 2)
                            ) AS shipped_quantity,
                            FORMAT (cph.PODate, \'MM/dd/yyyy\') AS order_date,
                            FORMAT (
                                    cph.CommitReqShipDateFrom,
                                    \'MM/dd/yyyy\'
                            ) AS cofirm_etd_from,
                            FORMAT (
                                    cph.CommitReqShipDateTo,
                                    \'MM/dd/yyyy\'
                            ) AS confirm_etd_to,
                            FORMAT (
                                    sh.[Shipment Calc_ Date],
                                    \'MM/dd/yyyy\'
                            ) AS actual_etd,
                            csn.[SO No_] so_number,
                            CASE WHEN cph.CommitReqShipDateTo > \'1753-01-01\' THEN
                                DATEDIFF(
                                        DAY,
                                        cph.CommitReqShipDateTo,
                                        sh.[Shipment Calc_ Date]
                                )
                                ELSE DATEDIFF(
                                        DAY,
                                        cph.CommitReqShipDateFrom,
                                        sh.[Shipment Calc_ Date]
                                ) END AS delay,
                            sh.[Package Tracking No_] sik,
                            sh.Ship completely_shipped,
                            sh.[Transport Method] transport_method,
                            sl.Ignore ignore,
                            sh.Remark remark,
                            LEFT((SELECT
                                            TOP 1 sih.[Pre-Assigned No_]
                                    FROM
                                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Shipment Line] ssl
                                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Invoice Line] sil ON sil.[Shipment No_]=ssl.[Document No_]
                                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Invoice Header] sih ON sih.No_=sil.[Document No_]
                                    WHERE ssl.[Order No_] = sh.No_),4) invoice_number
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl OUTER APPLY (
                                            SELECT
                                                    TOP 1 *
                                            FROM
                                                    nxpimg
                                            WHERE
                                                    nxpimg01 = cpl.ItemNo
                                    ) image
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON cpl.PONo = cph.PONo
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn ON csn.[CustPO No_] = cpl.PONo
                    AND cpl.ItemNo = csn.[Item No_]
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sl.[Document No_] = csn.[SO No_]
                    AND sl.No_ = csn.[Item No_]
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sh.No_ = csn.[SO No_]
                    WHERE
                            cph.PONo = :po_no
                    AND cpl.Quantity > 0
                    --AND (csn.Quantity > 0 OR csn.Quantity IS NULL)
                    ORDER BY
                            cph.PONo,
                            cpl.ItemNo ASC';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);

        $sqlCommand->bindValue(':po_no', $poNo);


        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['ignore_display'] = ($row['ignore']) ? 'yes' : '';
                $sqlResult[$i]['completely_shipped_display'] = ($row['completely_shipped']) ? 'yes' : '';
                $sqlResult[$i]['shipped_quantity'] = ($sqlResult[$i]['shipped_quantity']) ? $sqlResult[$i]['shipped_quantity'] : 0;
                $sqlResult[$i]['actual_etd'] = ($sqlResult[$i]['actual_etd']) ? $sqlResult[$i]['actual_etd'] : '';
                $sqlResult[$i]['so_number'] = ($sqlResult[$i]['so_number']) ? $sqlResult[$i]['so_number'] : '';
                $sqlResult[$i]['sik'] = ($sqlResult[$i]['sik']) ? $sqlResult[$i]['sik'] : '';
                $sqlResult[$i]['invoice_number'] = ($sqlResult[$i]['invoice_number']) ? $sqlResult[$i]['invoice_number'] : '';
                $sqlResult[$i]['transport_method'] = ($sqlResult[$i]['transport_method']) ? $sqlResult[$i]['transport_method'] : '';
                $sqlResult[$i]['delay'] = ($sqlResult[$i]['delay'] !== NULL) ? $sqlResult[$i]['delay'] : '';
                $sqlResult[$i]['remark'] = ($sqlResult[$i]['remark']) ? $sqlResult[$i]['remark'] : '';
            }
            return $sqlResult;
        }
        return [];
    }

}
