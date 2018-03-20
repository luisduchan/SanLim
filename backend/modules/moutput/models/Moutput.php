<?php

namespace backend\modules\moutput\models;

use Yii;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\POCus;
use backend\modules\common\models\POCusDetail;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\DateTimeTool;


/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Moutput extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

//    public static function tableName() {
//        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]';
//    }

    public function getGeneralData($itemNo, $dateForm, $dateTo) {
        $querySql = 'SELECT
                            ile.[Item No_] item_no,
                            item.Description description,
                            FORMAT(ile.[Posting Date], \'yyyy/MM\') month,
                            SUM(ile.Quantity) * (-1) total
                    FROM
                            [SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile WITH (NoLock)
                    JOIN [SAN LIM FURNITURE VIETNAM LTD$Item] item WITH (NoLock) ON (ile.[Item No_] = item.[No_])
                    LEFT JOIN [SAN LIM FURNITURE VIETNAM LTD$Product Group] pg ON item.[Item Category Code] = pg.[Item Category Code]
                    AND item.[Product Group Code] = pg.[Code]
                    LEFT JOIN [SAN LIM FURNITURE VIETNAM LTD$Division_Transaction] dt ON (
                            dt.Code = UPPER (ile.[Division Code])
                            AND dt.Type = 0
                    )
                    WHERE
                            ile.[Entry Type] IN (1, 2, 3, 5, 6)
                    AND ile.[Item No_] LIKE :item_no
                    AND ile.[Posting Date] BETWEEN :date_from AND :date_to
                    AND item.[Item Category Code] <> \'FG\'
                    GROUP BY ile.[Item No_], item.Description,FORMAT(ile.[Posting Date], \'yyyy/MM\')';

        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':date_from', date('Y-m-d', strtotime($dateForm)));
        $sqlCommand->bindValue(':date_to', date('Y-m-d', strtotime($dateTo)));

        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {

            // var_dump($sqlResult);
            for($i=0; $i<count($sqlResult); $i++){
                $row = $sqlResult[$i];
                $dateString = $row['month'];
                $dateTime = strtotime($dateString . '/01');
                $firstDate = date('Y-m-d', $dateTime);
                $lastDate = date('Y-m-t', $dateTime);
                $cost = $this->getCostOutPut($row['item_no'], $firstDate, $lastDate);

                $sqlResult[$i]['total'] = $cost;
                // var_dump($sqlResult);
            }
            return $sqlResult;
        }
        return FALSE;
    }
    public function getDivisionData($itemNo, $dateForm, $dateTo) {
        $querySql = 'SELECT
                            dt.Code division_code,
                            dt.Description description,
                            ile.[Location Code] location,
                            FORMAT (
                                    ile.[Posting Date],
                                    \'yyyy/MM\'
                            ) month,
                            SUM (ile.Quantity) * (- 1) total
                    FROM
                            [SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile WITH (NoLock)
                    JOIN [SAN LIM FURNITURE VIETNAM LTD$Item] item WITH (NoLock) ON (ile.[Item No_] = item.[No_])
                    LEFT JOIN [SAN LIM FURNITURE VIETNAM LTD$Product Group] pg ON item.[Item Category Code] = pg.[Item Category Code]
                    AND item.[Product Group Code] = pg.[Code]
                    LEFT JOIN [SAN LIM FURNITURE VIETNAM LTD$Division_Transaction] dt ON (
                            dt.Code = UPPER (ile.[Division Code])
                            AND dt.Type = 0
                    )
                    WHERE
                            ile.[Entry Type] IN (1, 2, 3, 5, 6)
                    AND ile.[Item No_] = :item_no
                    AND ile.[Posting Date] BETWEEN :date_from
                    AND :date_to
                    AND item.[Item Category Code] <> \'FG\'
                    GROUP BY
                            dt.Code,
                            dt.Description,
                            ile.[Location Code],
                            FORMAT (
                                    ile.[Posting Date],
                                    \'yyyy/MM\')
                    ORDER BY ile.[Location Code], dt.Description';

        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':date_from', date('Y-m-d', strtotime($dateForm)));
        $sqlCommand->bindValue(':date_to', date('Y-m-d', strtotime($dateTo)));

        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for($i=0; $i<count($sqlResult); $i++){
                $row = $sqlResult[$i];
                $sqlResult[$i]['division_code'] =  '[' . $row['location'] . '] ' . $row['division_code'];
                $sqlResult[$i]['description'] =  '[' . $row['location'] . '] ' . $row['description'];
            }
            return $sqlResult;
        }
        return FALSE;
    }
    public function getCostOutPut($itemNo, $dateForm, $dateTo){
        $sql = 'SELECT
                    main.[Item No_],
                    SUM (main.quantity)  * (-1) quantity,
                    SUM (main.cost)  * (-1) cost
                FROM
                    (
                        SELECT
                            ile.[Item No_],
                            ile.[Entry No_],
                            SUM (Quantity) quantity,
                            (
                                SELECT
                                    SUM (ve.[Cost Amount (Actual)]) + SUM (
                                        ve.[Cost Amount (Expected)]
                                    )
                                FROM
                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Value Entry] ve
                                WHERE
                                    ile.[Entry No_] = ve.[Item Ledger Entry No_]
                            ) cost
                        FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile
                        WHERE
                            ile.[Item No_] = :item_no
                        AND ile.[Posting Date] BETWEEN :date_from
                        AND :date_to
                        AND ile.[Entry Type] IN (1, 2, 3, 4, 5, 6)
                        GROUP BY
                            ile.[Item No_],
                            ile.[Entry No_]
                    ) main
                GROUP BY
                    main.[Item No_]';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':date_from', date('Y-m-d', strtotime($dateForm)));
        $sqlCommand->bindValue(':date_to', date('Y-m-d', strtotime($dateTo)));
        $sqlResult = $sqlCommand->queryOne();

        if ($sqlResult) {
            return round($sqlResult['cost'],3);
        }
        return FALSE;
    }
    public function getCartonOutput($itemNo, $dateFrom, $dateTo){
        $sqlQuery = 'SELECT
                        FORMAT ([Posting Date], \'yyyy/MM\') month,
                        SUM (main.quantity) * (- 1) quantity,
                        SUM (main.cost) * (- 1) cost
                    FROM
                        (
                            SELECT
                                ile.[Item No_],
                                ile.[Entry No_],
                                ile.[Posting Date],
                                SUM (Quantity) quantity,
                                (
                                    SELECT
                                        SUM (ve.[Cost Amount (Actual)]) + SUM (
                                            ve.[Cost Amount (Expected)]
                                        )
                                    FROM
                                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Value Entry] ve
                                    WHERE
                                        ile.[Entry No_] = ve.[Item Ledger Entry No_]
                                ) cost
                            FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile
                            WHERE
                                ile.[Item No_] LIKE :item_no
                            AND ile.[Posting Date] BETWEEN :date_from
                            AND :date_to
                            AND ile.[Entry Type] IN (1, 2, 3, 4, 5, 6)
                            GROUP BY
                                ile.[Item No_],
                                ile.[Entry No_],
                                ile.[Posting Date]
                        ) main
                    GROUP BY
                        FORMAT ([Posting Date], \'yyyy/MM\')
                    ORDER BY
                        FORMAT ([Posting Date], \'yyyy/MM\')';

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':date_from', $dateFrom);
        $sqlCommand->bindValue(':date_to', $dateTo);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for($i = 0; $i < count($sqlResult); $i++){
                $row = $sqlResult[$i];
                $sqlResult[$i]['quantity'] = number_format($row['quantity'], 0, '.', ',');
                $sqlResult[$i]['cost'] = round($row['cost'],3);
                // $sqlResult[$i]['cost'] = money_format('%i', $row['cost']);
                $sqlResult[$i]['cost_display'] = number_format($row['cost'], 3, '.', ',');
            }
            // var_dump($sqlResult);die();
            return $sqlResult;
        }
        return FALSE;
    }

    public function getOutputDivision($itemNo, $monthYear){
        $sqlQuery = 'SELECT
                        main.[Location Code] location_code,
                        main.[Division Code] division_code,
                        dt.Description division_name,
                        SUM (main.quantity) * (- 1) quantity,
                        SUM (main.cost) * (- 1) cost
                    FROM
                        (
                            SELECT
                                ile.[Entry No_],
                                ile.[Location Code],
                                ile.[Division Code],
                                SUM (Quantity) quantity,
                                (
                                    SELECT
                                        SUM (ve.[Cost Amount (Actual)]) + SUM (
                                            ve.[Cost Amount (Expected)]
                                        )
                                    FROM
                                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Value Entry] ve
                                    WHERE
                                        ile.[Entry No_] = ve.[Item Ledger Entry No_]
                                ) cost
                            FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile
                            WHERE
                                ile.[Item No_] LIKE :item_no
                            AND ile.[Entry Type] IN (1, 2, 3, 4, 5, 6)
                            AND FORMAT ([Posting Date], \'yyyy/MM\') = :month_year
                            GROUP BY
                                ile.[Entry No_],
                                ile.[Location Code],
                                [Division Code]
                        ) main
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Division_Transaction] dt ON (dt.Code = main.[Division Code] AND dt.Type = 0)
                    GROUP BY
                        main.[Location Code],
                        main.[Division Code] ,
                        dt.Description
                    ORDER BY
                        main.[Location Code],
                        main.[Division Code]';

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':month_year', $monthYear);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for($i = 0; $i < count($sqlResult); $i++){
                $row = $sqlResult[$i];
                $sqlResult[$i]['quantity'] =  round($row['quantity'],3);
                $sqlResult[$i]['quantity_display'] = number_format($row['quantity'], 0, '.', ',');
                $sqlResult[$i]['cost'] = round($row['cost'],3);
                $sqlResult[$i]['cost_display'] = number_format($row['cost'], 3, '.', ',');
            }
            // var_dump($sqlResult);die();
            return $sqlResult;
        }
        return FALSE;
    }
    public function getOutputBlanket($itemNo, $monthYear, $divisionCode, $locationCode){
        $sqlQuery = 'SELECT
                    "blanket" = CASE
                WHEN main.IK <> \'\' THEN
                    (
                        SELECT
                            TOP 1 sh.No_
                        FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                        WHERE
                            sh.IK = main.IK
                        AND sh.[Document Type] = 4
                    )
                ELSE
                    \'\'
                END,
                    main.IK ik,
                    SUM (main.quantity) * (- 1) quantity,
                    SUM (main.cost) * (- 1) cost
                    
                FROM
                    (
                        SELECT
                            ile.[Entry No_],
                            ile.IK,
                            SUM (Quantity) quantity,
                            (
                                SELECT
                                    SUM (ve.[Cost Amount (Actual)]) + SUM (
                                        ve.[Cost Amount (Expected)]
                                    )
                                FROM
                                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Value Entry] ve
                                WHERE
                                    ile.[Entry No_] = ve.[Item Ledger Entry No_]
                            ) cost
                        FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile
                        WHERE
                            ile.[Item No_] LIKE :item_no
                        AND ile.[Entry Type] IN (1, 2, 3, 4, 5, 6)
                        AND FORMAT ([Posting Date], \'yyyy/MM\') = :month_year
                        AND ile.[Division Code] = :division_code
                        AND ile.[Location Code] = :location_code
                        GROUP BY
                            ile.IK,
                            ile.[Entry No_]
                    ) main
                GROUP BY
                    main.IK
                ORDER BY
                    main.IK';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':month_year', $monthYear);
        $sqlCommand->bindValue(':division_code', $divisionCode);
        $sqlCommand->bindValue(':location_code', $locationCode);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for($i = 0; $i < count($sqlResult); $i++){
                $row = $sqlResult[$i];
                $sqlResult[$i]['quantity'] = number_format($row['quantity'], 0, '.', ',');
                $sqlResult[$i]['cost'] = round($row['cost'],3);
                $sqlResult[$i]['cost_display'] = number_format($row['cost'], 3, '.', ',');
            }
            // var_dump($sqlResult);die();
            return $sqlResult;
        }
        return FALSE;
    }
    public function getOutputLegerEntry($itemNo, $monthYear, $divisionCode, $locationCode, $ik){
        $sqlQuery = 'SELECT
                        ile.[Entry No_] entry_no,
                        ile.[Item No_] item_no,
                        ile.IK ik,
                        ile.[Posting Date] posting_date,
                        ile.[Entry Type] entry_type,
                        ile.[Document No_] document_no,
                        ile.Description description,
                        ile.[Location Code] location_code,
                        ile.Quantity * (- 1) quantity,
                        ile.[Invoiced Quantity] invoiced_quty,
                        ile.[External Document No_] ext_doc_no,
                        ile.[Unit of Measure Code] uom,
                        ile.[Item Category Code] item_category_code,
                        ile.[Last Invoice Date] last_invoice_date,
                        ile.[Posting User] posting_user,
                        (
                            SELECT
                                SUM (ve.[Cost Amount (Actual)]) + SUM (
                                    ve.[Cost Amount (Expected)]
                                )
                            FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$Value Entry] ve
                            WHERE
                                ile.[Entry No_] = ve.[Item Ledger Entry No_]
                        ) * (- 1) cost 
                    FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile
                    WHERE
                        ile.[Item No_] LIKE :item_no
                    AND ile.[Entry Type] IN (1, 2, 3, 4, 5, 6)
                    AND FORMAT ([Posting Date], \'yyyy/MM\') = :month_year
                    AND ile.[Division Code] = :division_code
                    AND ile.[Location Code] = :location_code
                    AND ile.IK = :ik;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);

        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':month_year', $monthYear);
        $sqlCommand->bindValue(':division_code', $divisionCode);
        $sqlCommand->bindValue(':location_code', $locationCode);
        $sqlCommand->bindValue(':ik', $ik);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for($i = 0; $i < count($sqlResult); $i++){
                $row = $sqlResult[$i];
                $sqlResult[$i]['quantity'] = number_format($row['quantity'], 0, '.', ',');
                $sqlResult[$i]['cost'] = round($row['cost'],3);
                $sqlResult[$i]['cost_display'] = number_format($row['cost'], 3, '.', ',');
                $sqlResult[$i]['posting_date'] = DateTimeTool::getDateDiplay($row['posting_date']);
                $sqlResult[$i]['last_invoice_date'] = DateTimeTool::getDateDiplay($row['last_invoice_date']);
            }
            // var_dump($sqlResult);die();
            return $sqlResult;
        }
        return FALSE;
    }
}
