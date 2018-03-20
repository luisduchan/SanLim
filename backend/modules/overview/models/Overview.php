<?php

namespace backend\modules\overview\models;

use Yii;
use common\modules\sanlim\models\ItemCommon;
use common\modules\sanlim\models\Date;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Overview extends \yii\db\ActiveRecord {

    public function init() {
        
    }

    public function getMaterialOutput($parameter) {
        $itemNo = $parameter['item_no'];
        $location = NULL;//$parameter['location'];
        $dateFrom = $parameter['date_from'];
        $dateTo = $parameter['date_to'];
//        $not_include_component = $parameter['not_include_component'];

        $resultData = [];
        $select = 'SELECT ile.[Item No_] AS item_no';

        $where = ' WHERE ile.[Item No_]<>\'\''
                . ' AND ile.[Entry Type] IN (1,3,5,6) AND ile.[Quantity]<0'
                . ' AND ile.[Item No_] LIKE COALESCE(:item_no, ile.[Item No_])'
                . ' AND ile.[Location Code] = COALESCE(:location_code, ile.[Location Code])'
                . ' AND ile.[Posting Date] BETWEEN :date_from'
                . ' AND :date_to';
//        if ($not_include_component == 1) {
//            $arrComponent = Component::find()->asArray()->All();
//            $arrComponent = array_column($arrComponent, 'item_no');
//            $where = $where . ' AND ile.[Item No_] NOT IN (\'' . implode('\',\'', $arrComponent) . '\')';
//        }
        $subQueryTemp = 'SELECT SUM (Quantity) * (-1) '
                . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry]';

        $subWhere = ' WHERE ile.[Item No_] = [Item No_]'
                . ' AND [Item No_]<>\'\''
                . ' AND [Location Code] = COALESCE(:location_code, [Location Code])'
                . ' AND [Entry Type] IN (1,3,5,6) AND [Quantity]<0';

        $arrPeriod = Date::getPeriod($dateFrom, $dateTo);
        $arrMonthYear = [];
        $resultHeader[0][] = 'Item No';
        foreach ($arrPeriod as $monthYear => $period) {
            $select .= ',(' . $subQueryTemp
                    . $subWhere . " AND [Posting Date] BETWEEN '$period[0]' AND '$period[1]') AS '$monthYear'";
            $resultHeader[0][] = $monthYear;
            $arrMonthYear[] = $monthYear;
        }
//        $numberContainerModel = new NumberContainer();
//        $numberContainer = $numberContainerModel->getNumContWoKey($arrMonthYear, $parameter['location']);
        $resultHeader[0][] = 'Unit of Measure';
        $select .= ' ,[Unit of Measure Code] as uom';
        $mainQuery = $select
                . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile'
                . $where
                . ' GROUP BY ile.[Item No_], ile.[Unit of Measure Code]'
                . ' ORDER BY ile.[Item No_]';

        $sqlCommand = Yii::$app->dbMS->createCommand($mainQuery);
        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':location_code', $location);
        $sqlCommand->bindValue(':date_from', $dateFrom);
        $sqlCommand->bindValue(':date_to', $dateTo);
        $sqlResult = $sqlCommand->queryAll();
        //convert PCS to Metric
        if (1) {
            $arrItemNo = array_column($sqlResult, 'item_no');
            $sqlItems = 'SELECT No_ AS item_no, Metre23'
                    . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item]'
                    . ' WHERE No_ IN (\'' . implode('\',\'', $arrItemNo) . '\')';
            $sqlItemsCommand = Yii::$app->dbMS->createCommand($sqlItems);
            $arrItems = $sqlItemsCommand->queryAll(\PDO::FETCH_KEY_PAIR);
            $i = 0;
            foreach ($sqlResult as $row) {
                if ($row['uom'] == 'PCS' & $arrItems[$row['item_no']] > 0) {
                    foreach ($arrMonthYear as $monthYear) {
                        $sqlResult[$i][$monthYear] = $sqlResult[$i][$monthYear] * $arrItems[$row['item_no']];
                    }
                    $sqlResult[$i]['uom'] = 'M3';
                }
                $i++;
            }
        }

//        $resultData = array_merge($resultHeader, $resultData);
        return $sqlResult;
    }

}
