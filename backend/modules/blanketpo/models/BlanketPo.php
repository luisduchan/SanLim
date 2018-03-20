<?php

namespace backend\modules\blanketpo\models;

use Yii;
use DateTime;
use DateInterval;
use DatePeriod;
use yii\helpers\ArrayHelper;
use common\modules\sanlim\models\NumberContainer;
use common\modules\sanlim\models\Component;
use common\modules\sanlim\models\Date;
use backend\modules\common\models\DateTimeTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class BlanketPo extends \yii\db\ActiveRecord {

    public function init() {

    }

    public function findBlanket($blanketNo) {
        
        $sqlHeader = 'SELECT
                        TOP 20 No_ blanket_no, [Requested Delivery Date] confirm_date
                    FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]
                    WHERE No_ LIKE :blanketNo
                        AND [Document Type]=4
                    ORDER BY [Requested Delivery Date] DESC;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlHeader);
        $sqlCommand->bindValue(':blanketNo', $blanketNo . '%');
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return [];
    }
    public function getBlanketPO($blanketNo, $itemNos, $custNO){

        $placeholders = '';
        if($itemNos){
            for ($i = 0; $i < count($itemNos) - 1; $i++) {
                $placeholders .= ':item_no' . $i . ',';
            }
            $placeholders .= ':item_no' . $i;
        }
        
        $sql = 'SELECT
            sh.No_ blanket_no,
            sh.[Related Order#] related_order,
            sh.[Conts Adjmt] cont_adjmt,
            sh.IK ik,
            sh.POD production_line,
            sh.[Cutting No_] cutting_no,
            sh.[Sell-to Customer Name 2] cust_name ,
            sh.[Sell-to Customer No_] cust_no,
            sh.Finished finished,
            sh.[Scheduled Ass_ Date Start] shceduled_date_start,
            sh.[Scheduled Ass_ Date End] scheduled_date_end,
            sh.[Order Date] order_date,
            sh.[Requested Delivery Date] cofirmed_etd_start,
            sh.[Requested Ship Date End] cofirmed_etd_end
        FROM
            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
        WHERE
            sh.No_ IN (
                SELECT DISTINCT
                    sh1.No_
                FROM
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh1
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl1 ON (sh1.No_ = sl1.[Document No_])
                WHERE
                    sh1.[Document Type] = 4';
                $sql .= $blanketNo ? ' AND sh1.No_ LIKE :blanket_no' : '';
                $sql .= $custNO ? ' AND sh1.[Sell-to Customer No_] = :cust_no': '';
                $sql .= $itemNos ? ' AND sl1.No_ IN ('  . $placeholders . ')' : '';

        $sql .= ')
        ORDER BY sh.[Requested Delivery Date] DESC;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        if($itemNos){
            foreach ($itemNos as $i => $itemNo) {
                $sqlCommand->bindValue(':item_no' . $i, $itemNo);
            }
        }
        if($blanketNo){
            $sqlCommand->bindValue(':blanket_no', $blanketNo);
        }
        if($custNO){
            $sqlCommand->bindValue(':cust_no', $custNO);
        }
        $sqlResult = $sqlCommand->queryAll();
        return $sqlResult;
    }


}