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
class ItemOverview extends \yii\db\ActiveRecord {

    public function init() {
        
    }

    public function getPlanningItem($requestParam) {
        $itemNo = $requestParam['item_no'];
        $dateFrom = $requestParam['date_from'];
        $dateTo = $requestParam['date_to'];
        $dateFromOutSt = $requestParam['date_from_outst'];
        $dateToOutSt = $requestParam['date_to_outst'];
        
        $sqlQuery = 'SELECT DISTINCT'
                . ' i.[No_] AS item_no,'
                . ' (SELECT'
                . '         SUM (Quantity)'
                . '     FROM'
                . '         [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile_invetory'
                . '     WHERE'
                . '         ile_invetory.[Item No_] = ile_main.[Item No_]) AS stock,'
                . ' i.[Base Unit of Measure] AS uom, i.[Metre23] cubic'
                . ' FROM'
                . ' [SAN LIM FURNITURE VIETNAM LTD$Item] i WITH(NoLock)'
                . ' LEFT OUTER JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile_main ON (i.[No_] = ile_main.[Item No_])'
                . ' WHERE'
                . ' i.[No_] LIKE :item_no'
                . ' ORDER BY i.[No_]';
//                . ' AND [Posting Date] >= \'2016-01-06\';';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlResult = $sqlCommand->queryAll();

        
        $itemCommon = new ItemCommon();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $itemNo = $sqlResult[$i]['item_no'];
                //Convert to Cubic
                if ($sqlResult[$i]['uom'] == 'PCS' && $sqlResult[$i]['cubic'] > 0) {
                    $sqlResult[$i]['stock'] = $sqlResult[$i]['stock'] * $sqlResult[$i]['cubic'];
                }
                //Get material output
                $qutyOutput = $itemCommon->getMaterialOutput($itemNo, $dateFrom, $dateTo);
                $sqlResult[$i]['output'] = $qutyOutput;
                //Get outstanding
                $outStandingArr = $itemCommon->getOutStanding($itemNo, $dateFromOutSt, $dateToOutSt);
                foreach($outStandingArr as $monthYear => $outStanding){
                    $sqlResult[$i][$monthYear] = $outStanding;
                }
                
            }
        }
        

        return $sqlResult;
    }
    public function getList($page=0,$pageSize=100){
        $lineNo = $page * $pageSize;
        $sqlQuery = "SELECT * FROM `SAN LIM FURNITURE VIETNAM LTD\$Item` LIMIT $pageSize, $lineNo";
        $sqlCommand = Yii::$app->db->createCommand($sqlQuery);
//        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlResult = $sqlCommand->queryAll();
        return $sqlResult;
    }
    
}
