<?php

namespace backend\modules\po\models;

use Yii;
use DateTime;
use DateInterval;
use DatePeriod;
use yii\helpers\ArrayHelper;
use common\modules\sanlim\models\NumberContainer;
use common\modules\sanlim\models\Component;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Po extends \yii\db\ActiveRecord
{    
    public function init()
    {
        
    }
    
    public function getPOSumary(array $parameter){
        $itemNo = 'WOODRBB%';
        $dateFrom = '2016-12-01';
        $dateTo = '2016-12-31';
        $sqlQuery = 'SELECT'
                . '     ph.[Buy-from Vendor Name] AS vendor,'
                . '     ph.[No_] AS po_no,'
                . '     SUM(pl.Quantity) AS total_qty,'
                . '     SUM(pl.[Quantity Received]) AS total_receipt_qty,'
                . '     CASE WHEN SUM(delivered.qty) - SUM(pl.[Quantity Received]) > 0 THEN SUM(delivered.qty) - SUM(pl.[Quantity Received]) ELSE 0 END AS total_waiting_qty,'
                . '     SUM(pl.[Outstanding Quantity]) AS total_outst_qty'
                . ' FROM'
                . '     [SAN LIM FURNITURE VIETNAM LTD$Purchase Line] AS pl,'
                . '     [SAN LIM FURNITURE VIETNAM LTD$Purchase Header] AS ph,'
                . '     xloe as delivered'
                . ' WHERE'
                . '     pl.[Document No_] = ph.[No_]'
                . '     AND delivered.orderno = ph.No_'
                . '     AND pl.No_ LIKE :itemNo'
                . '     AND ph.[Requested Receipt Date] BETWEEN :dateFrom AND :dateTo'
                . ' GROUP BY'
                . '     ph.[Buy-from Vendor Name],'
                . '     ph.[No_]';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        $sqlCommand->bindValue(':item_no',$itemNo);
//        $sqlCommand->bindValue(':item_cat',$item_cat);
//        $sqlCommand->bindValue(':location_code',$location);
        $sqlCommand->bindValue(':date_from',$dateFrom);
        $sqlCommand->bindValue(':date_to',$dateTo);
        $sqlResult = $sqlCommand->queryAll();
        return $sqlResult;
    }
//    public function converToChartSumary($dataSet){
//        
//    }
}
