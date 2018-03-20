<?php

namespace common\modules\sanlim\models;

use Yii;
use common\modules\sanlim\models\Date;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class ItemCommon extends \yii\db\ActiveRecord {

    var $select = '';
    var $from = '';
    var $where = '';
    var $group_by = '';

    public function getMaterialOutput($itemNo, $dateFrom, $dateTo, $location = False, $converCubic = True) {
        $result = 0;
        $sqlQuery = 'SELECT SUM(ile.Quantity) * (-1) AS quantity, i.[Base Unit of Measure] AS uom, i.[Metre23] cubic'
                . ' FROM [SAN LIM FURNITURE VIETNAM LTD$Item] i WITH(NoLock)'
                . ' ,[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile WITH(NoLock)'
                . ' WHERE i.[No_] = ile.[Item No_]'
                . ' AND i.[No_] = :item_no'
                . ' AND ile.[Entry Type] IN (1,3,5,6) AND ile.[Quantity]<0'
                . ' AND ile.[Posting Date] BETWEEN :date_from AND :date_to'
                . ' GROUP BY i.[No_],'
                . ' i.[Base Unit of Measure],'
                . ' i.[Metre23]';

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':date_from', $dateFrom);
        $sqlCommand->bindValue(':date_to', $dateTo);
        $sqlResult = $sqlCommand->queryOne();
        $months = Date::getMonthBetweentDates($dateFrom, $dateTo);
        if ($sqlResult) {
            $result = $sqlResult['quantity'] / $months;
            if ($converCubic && $sqlResult['uom'] == 'PCS' && $sqlResult['cubic'] > 0) {
                $result = $result * $sqlResult['cubic'];
            }
        }
        return $result;
    }

    public function getOutStanding($itemNo, $dateFrom, $dateTo, $location = False, $converCubic = True) {
        $result = [];
        $dateField = 'pl.[DetailETA]';
        $arrPeriod = Date::getPeriod($dateFrom, $dateTo);
        $sqlQuery = 'SELECT'
                . '     SUM(pl.[Outstanding Quantity]) - CASE WHEN (SUM(delivered.qty) - SUM(pl.[Quantity Received])) > 0 THEN (SUM(delivered.qty) - SUM(pl.[Quantity Received])) ELSE 0 END  AS total_outst_qty,'
                . '     i.[Base Unit of Measure]  AS uom,'
                . '     i.[Metre23] AS cubic'
                . ' FROM'
                . '     [SAN LIM FURNITURE VIETNAM LTD$Purchase Line] AS pl WITH (NoLock)'
                . '     LEFT OUTER JOIN [SAN LIM FURNITURE VIETNAM LTD$Purchase Header] AS ph WITH (NoLock) ON pl.[Document No_] = ph.[No_]'
                . '     LEFT OUTER JOIN xloe as delivered WITH (NoLock) '
                . '         ON delivered.orderno = ph.[No_]'
                . '         AND delivered.orderline = pl.[Line No_] '
                . '         AND delivered.itemno = pl.[No_]'
                . '     LEFT OUTER JOIN [SAN LIM FURNITURE VIETNAM LTD$Item] i WITH (NoLock) ON (i.[No_] = pl.[No_])'
                . ' WHERE'
                . '     pl.No_ = :item_no'
                . '     AND ' . $dateField . ' >= COALESCE(:date_from,' . $dateField . ')'
                . '     AND ' . $dateField . ' <= COALESCE(:date_to,' . $dateField . ')'
                . ' GROUP BY ph.[No_], pl.[No_],'
                . ' i.[Base Unit of Measure],'
                . ' i.[Metre23]';

        foreach ($arrPeriod as $monthYear => $period) {
            $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
            $sqlCommand->bindValue(':item_no', $itemNo);
            $sqlCommand->bindValue(':date_from', $period[0]);
            $sqlCommand->bindValue(':date_to', $period[1]);
            $sqlResult = $sqlCommand->queryAll();
            $result[$monthYear] = 0;
            if ($sqlResult) {
                foreach($sqlResult as $row){
                    $result[$monthYear] += max($row['total_outst_qty'],0);
                }
                if ($converCubic && $row['uom'] == 'PCS' && $row['cubic'] > 0) {
                    $result[$monthYear] = $result[$monthYear] * $row['cubic'];
                }
            }
        }
//        die('aaa');
        return $result;
    }

}
