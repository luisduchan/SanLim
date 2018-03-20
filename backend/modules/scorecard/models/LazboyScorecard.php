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
class LazboyScorecard extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public function getAllPO($dateFrom, $dateTo) {

        $querySql = 'SELECT
                            cph.POCustomer po_no,
                            cph.PONo technical_po_no,
                            cph.CommitReqShipDateFrom confirm_ship_date,
                            cph.[Order Type] po_type,
                    cpl.ItemNo item_no_technical,
                    CASE cph.[Order Type]
                    WHEN 1 THEN
                            LEFT (
                                    cpl.ItemNo,
                                    CHARINDEX(\'-\', cpl.ItemNo) - 1
                            ) + \'D\' + RIGHT (
                                    cpl.ItemNo,
                                    LEN(cpl.ItemNo) - CHARINDEX(\'-\', cpl.ItemNo) + 1
                            )
                    ELSE
                            cpl.ItemNo
                    END item_no,
                     cpl.Quantity ordered_quantity,
                     shipping.[SO No_] so,
                     shipping.[Shipment Calc_ Date] real_calc_ship_date,
                     sh.[Shipment Calc_ Date] transfter_notice_date,
                     CAST (
                            (
                                    SELECT
                                            SUM (csn.Quantity)
                                    FROM
                                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
                                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
                                            sh.No_ = csn.[SO No_]
                                            AND (sh.Ship = 1 OR sh.[Transport Method] = \'AIR\')
                                    )
                                    WHERE
                                            csn.[CustPO No_] = cph.PONo
                                    AND csn.[Item No_] = cpl.ItemNo
                            ) AS INT
                    ) total_shipped,
                    cpl.Ignore exception,
                    cpl.Remark remark
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo) OUTER APPLY (
                            SELECT
                                    TOP 1 csn.[Item No_],
                                    csn.[SO No_],
                                    sh.[Shipment Calc_ Date]
                            FROM
                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON (
                                    sl.[Document No_] = csn.[SO No_]
                                    AND sl.No_ = csn.[Item No_]
                            )
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sl.[Document No_] = sh.No_
                            WHERE
                                    csn.[CustPO No_] = cpl.PONo
                            AND csn.[Item No_] = cpl.ItemNo
                            AND (sh.Ship = 1 OR sh.[Transport Method] = \'AIR\')
                            ORDER BY
                                    sh.[Shipment Calc_ Date] ASC
                    ) shipping
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sl.No_ = cpl.ItemNo AND sl.[Document No_] = cpl.[Blanket PO#]
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (sl.[Document No_] = sh.No_ AND sh.[Document Type] = 4)
                    WHERE
                            cph.CustomerNo = \'C54000\'
                    AND cph.CommitReqShipDateFrom BETWEEN :date_from1
                    AND :date_to1
                    ORDER BY
                            cph.POCustomer,
                            cph.PONo,
                            cpl.ItemNo';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':date_from1', $dateFrom);
//        $sqlCommand->bindValue(':date_from2', $dateFrom);
        $sqlCommand->bindValue(':date_to1', $dateTo);
//        $sqlCommand->bindValue(':date_to2', $dateTo);
//        var_dump($sqlCommand);die();

        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['confirm_ship_date'] = DateTimeTool::getDateDiplay($row['confirm_ship_date']);
                $sqlResult[$i]['real_calc_ship_date'] = DateTimeTool::getDateDiplay($row['real_calc_ship_date']);
                $sqlResult[$i]['transfter_notice_date'] = DateTimeTool::getDateDiplay($row['transfter_notice_date']);
                $sqlResult[$i]['ordered_quantity'] = round($row['ordered_quantity'], 0);
                $sqlResult[$i]['short_ship'] = round($row['ordered_quantity'] - $row['total_shipped']);
                $sqlResult[$i]['exception_diplay'] = $row['exception'] ? 'yes' : '';
                $sqlResult[$i]['so'] = $row['so'] ? $row['so'] : '';
                $sqlResult[$i]['total_shipped'] = $row['total_shipped'] ? $row['total_shipped'] : 0;
                switch ($row['po_type']){
                    case 0: $sqlResult[$i]['po_type_display'] = 'WH';
                        break;
                    case 1: $sqlResult[$i]['po_type_display'] = 'Direct';
                        break;
                    case 2: $sqlResult[$i]['po_type_display'] = 'RP';
                        break;
                    default: $sqlResult[$i]['po_type_display'] = 'Undefined';
                        break;
                }
                if ($row['po_type'] == 1) {
                    $sqlResult[$i]['actual_day_delay'] = ($sqlResult[$i]['transfter_notice_date'] != '') ? (strtotime($row['transfter_notice_date']) - strtotime($row['confirm_ship_date'])) / (60 * 60 * 24) : 'not finish';
                    $sqlResult[$i]['not_ship'] = ($sqlResult[$i]['transfter_notice_date'] != '') ? FALSE : TRUE;
                } else {
//                     var_dump($row);
                    $sqlResult[$i]['actual_day_delay'] = ($row['real_calc_ship_date'] != False) ? (strtotime($row['real_calc_ship_date']) - strtotime($row['confirm_ship_date'])) / (60 * 60 * 24) : 'not ship';
                    $sqlResult[$i]['not_ship'] = ($row['real_calc_ship_date'] != False) ? FALSE : TRUE;
                }
            }
//            var_dump($sqlResult);
            return $sqlResult;

        }
        return [];
    }
    public function getlazBoycorceCardPos($dateFrom, $dateTo) {
        $allPos = $this->getAllPO($dateFrom, $dateTo);
        if (!$allPos) {
            return [];
        }
        $arrayOnTime['total'] = 0;
        $onTimePos = [];
        $arrayLate['total'] = 0;
        $latePos = [];
        $arrayMissing['total'] = 0;
        $missingPos = [];
        for ($i = 0; $i < count($allPos); $i++) {
            $row = $allPos[$i];
            if ($row['not_ship'] == TRUE && !$row['exception']) {
                $arrayMissing['total'] += $row['ordered_quantity'];
                $missingPos[] = $row;
            }elseif($row['actual_day_delay'] <= 13 || $row['exception']){
                $arrayOnTime['total'] += $row['ordered_quantity'];
                $onTimePos[] = $row;
            }else {
                $arrayLate['total'] += $row['ordered_quantity'];
                $latePos[] = $row;
            }
            $arrayMissing['po'] = $missingPos;
            $arrayLate['po'] = $latePos;
            $arrayOnTime['po'] = $onTimePos;
        }
        return [$arrayMissing, $arrayLate, $arrayOnTime];
    }
}
