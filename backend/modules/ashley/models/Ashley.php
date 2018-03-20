<?php

namespace backend\modules\ashley\models;

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
class Ashley extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function getDb1() {
        return \Yii::$app->db;
    }

    public function getQtyTotalBlanketOrder($customer, $blanketNo) {

               /* $querySql = 'SELECT
                    sl.No_ item_code,
                    sl.Description description,
                    SUM (sl.Quantity) blanket_qty_total
                    FROM
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl,
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh,
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] it
                    WHERE
                    sl.[Document No_] = sh.No_
                    AND sl.No_ = it.No_
                    AND sh.[Sell-to Customer No_] = :customer
                    AND sl.[Document Type] = 4
                    AND sh.[Requested Delivery Date] > \'12/17/2017\'
                    --AND sh.[Requested Delivery Date] <= \'12/23/2017\'
                    --AND sh.[Requested Delivery Date] <= \'2017-12-23\'
                    AND sh.[Requested Delivery Date] <= DATEADD(wk, +1, DATEADD(wk, DATEDIFF(wk, 0,getdate()), -2))
                    AND it.[Item Category Code] = \'FG\'
                    GROUP BY
                    sl.No_,
                    sl.Description
                    ORDER BY
                    sl.No_';
                    */

                    $querySql = 'SELECT
                    sl.No_ item_code,
                    sl.Description description,
                    SUM (sl.Quantity) blanket_qty_total
                    FROM
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl,
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh,
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] it
                    WHERE
                    sl.[Document No_] = sh.No_
                    AND sl.No_ = it.No_
                    AND sh.[Sell-to Customer No_] = :customer
                    AND sl.[Document Type] = 4
                    AND sh.[Requested Delivery Date] <= 
                                        (Select sh.[Requested Delivery Date] from [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh WHERE sh.No_= :blanketNo AND sh.[Sell-to Customer No_] = :customer1)
                    AND it.[Item Category Code] = \'FG\'
                                        AND sl.No_ LIKE concat(SUBSTRING(:blanketNo1,1,4),:txt)
                    GROUP BY
                    sl.No_,
                    sl.Description
                    ORDER BY
                    sl.No_';




        
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':customer', $customer);
        $sqlCommand->bindValue(':customer1', $customer);
        $sqlCommand->bindValue(':blanketNo', $blanketNo);
        $sqlCommand->bindValue(':blanketNo1', $blanketNo);
        $sqlCommand->bindValue(':txt', '%');
//        var_dump($sqlCommand);die();

        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];

                $sqlResult[$i]['blanket_qty_total'] = round($row['blanket_qty_total'], 0);               
            }
//            var_dump($sqlResult);
            return $sqlResult;

        }
        return [];
    }

    public function getblanketQuantity($customer, $blanketNo) {
        $QtyBlanket = $this->getQtyTotalBlanketOrder($customer, $blanketNo);
        if (!$QtyBlanket) {
            return [];
        }
        return $QtyBlanket;
    }



    public function getAshleyQtyAjustment() {

        $querySql1 = 'SELECT Code item_code, Description description, Quantity adjust_qty FROM ashley_quantity_adjustment';
        $sqlCommand1 = Yii::$app->db->createCommand($querySql1);
        $sqlResult1 = $sqlCommand1->queryAll();
        if ($sqlResult1) {
            return $sqlResult1;
        }
        return [];
    }

    public function getAshleyQtySerial($customer, $blanketNo) {

        /*$querySql2 = 'SELECT UPPER(item_code) item_code, count(serial_qty) serial_qty
                    FROM(
                    SELECT DISTINCT sh.[Sell-to Customer No_], Rtrim(sr.ITEMS) item_code, sh.[Package Tracking No_], sr.QTY serial_qty, sr.FORMULAS  from 
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh,
                    T16_SERIES_NO sr
                    WHERE 
                    SUBSTRING(sr.IK,1,7) = SUBSTRING(sh.[Package Tracking No_], 1, 7)
                    AND sr.QTY =1
                    --AND sh.[Sell-to Customer No_] = :customer
                    AND sh.[Bill-to Customer No_] = :customer
                    --AND sr.Systime < \'2017-12-21\'
                    AND sr.Systime >= \'2017-12-21\'
                    ) tb
                    GROUP BY  UPPER(item_code)
                    ORDER BY UPPER(item_code)';*/

        $querySql2 = 'SELECT UPPER(item_code) item_code, count(formulas) serial_qty
                    FROM(
                        SELECT DISTINCT sh.[Sell-to Customer No_], Replace(Rtrim(sr.ITEMS),:a,:b) item_code, sh.[Package Tracking No_],
                        -- sr.QTY serial_qty, 
                         sr.FORMULAS formulas from 
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh,
                        T16_SERIES_NO sr
                        WHERE 
                        SUBSTRING(sr.IK,1,7) = SUBSTRING(sh.[Package Tracking No_], 1, 7)
                        --AND sr.QTY =1
                        AND UPPER(Rtrim(sr.ITEMS)) LIKE concat(SUBSTRING(:blanketNo,1,4),:txt)
                        --AND sh.[Sell-to Customer No_] = :customer
                        AND sh.[Bill-to Customer No_] = :customer
                        AND sh.[Requested Delivery Date] <= 
                        (Select sh.[Requested Delivery Date] from [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh WHERE sh.No_= :blanketNo1 AND sh.[Sell-to Customer No_] = :customer1)
                    ) tb
                    GROUP BY  UPPER(item_code)
                    ORDER BY UPPER(item_code)';



        $sqlCommand2 = Yii::$app->dbMS->createCommand($querySql2);
        $sqlCommand2->bindValue(':customer', $customer);
        $sqlCommand2->bindValue(':customer1', $customer);
        $sqlCommand2->bindValue(':blanketNo', $blanketNo);
        $sqlCommand2->bindValue(':blanketNo1', $blanketNo);
        $sqlCommand2->bindValue(':txt', '%');
        $sqlCommand2->bindValue(':a', ' ');
        $sqlCommand2->bindValue(':b', '');
        $sqlResult2 = $sqlCommand2->queryAll();
        if ($sqlResult2) {
            return $sqlResult2;
        }
        return [];
    }


     public function findBlanket($blanketNo) {

        $querySql4 = 'SELECT DISTINCT No_ blanket_no FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                        WHERE sh.[Document Type] = 4
                        AND sh.[Requested Delivery Date] >= \'9/1/2017\'
                        AND sh.[Sell-to Customer No_] = \'C01000\'
                        AND sh.NotRealOrder != 1
                        AND sh.No_ LIKE :blanket_no1
                        UNION
                        SELECT DISTINCT No_ blanket_no FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                        WHERE sh.[Document Type] = 4
                        AND sh.[Requested Delivery Date] >= \'9/1/2017\'
                        AND sh.[Sell-to Customer No_] = \'C47000\'
                        AND sh.NotRealOrder != 1
                        AND sh.No_ LIKE :blanket_no2';

        $sqlCommand4 = Yii::$app->dbMS->createCommand($querySql4);
        $sqlCommand4->bindValue(':blanket_no1', $blanketNo . '%');
        $sqlCommand4->bindValue(':blanket_no2', $blanketNo . '%');
        $sqlResult4 = $sqlCommand4->queryAll();
        if ($sqlResult4) {
            return array_column($sqlResult4, 'blanket_no');
        }
        return [];
    }



}
