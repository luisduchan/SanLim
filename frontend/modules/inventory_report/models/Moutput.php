<?php

namespace frontend\modules\inventory_report\models;

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
class Moutput extends \yii\db\ActiveRecord
{
    var $select = '';
    var $from = '';
    var $where = '';
    var $group_by = '';
    
    public function init()
    {
        $this->from = ' FROM [SAN LIM FURNITURE VIETNAM LTD$Item] a WITH(NoLock) '
                . ' ,[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] b WITH(NoLock)';
        $this->where = ' WHERE a.[No_]<>\'\' AND a.[No_] = b.[Item No_]'
                        .' AND b.[Entry Type] IN (1,3,5,6) AND b.[Quantity]<0'
                        .' AND b.[Item No_] LIKE COALESCE(:item_no, b.[Item No_])'
                        .' AND b.[Item Category Code] = COALESCE(:item_cat, b.[Item Category Code])'
                        .' AND b.[Location Code] = COALESCE(:location_code, b.[Location Code])'
                        .' AND b.[Posting Date] >= COALESCE(:date_from,b.[Posting Date])'
                        .' AND b.[Posting Date] <= COALESCE(:date_to,b.[Posting Date])';
    }
    
    public function getSummary(array $parameter){
        $item_no = $parameter['item_no'];
        $item_cat = $parameter['item_cat'];
        $location = $parameter['location'];
        $date_from = $parameter['date_from'];
        $date_to = $parameter['date_to'];
        
        $this->select = 'SELECT b.[Location Code] as location,'
                        . ' cast(DATEPART(mm, [Posting Date]) as varchar ) + \'/\' +cast(DATEPART(yyyy, [Posting Date]) as varchar) as posting_date,'
                        . ' a.[No_] as item_no,'
                        . ' SUM(b.Quantity) * (-1) AS quantity ,'
                        . ' a.[Base Unit of Measure] as uom,'
                        . ' a.[Metre23] as metric';
        $this->group_by = ' GROUP BY b.[Location Code],'
                        .' cast(DATEPART(mm, [Posting Date]) as varchar ) + \'/\' +cast(DATEPART(yyyy, [Posting Date]) as varchar),'
                        .' a.[No_],'
                        . 'a.[Base Unit of Measure],'
                        . 'a.[Metre23]';
       
        $sqlQuery = $this->select
                . $this->from
                . $this->where
                . $this->group_by;
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);

        $sqlCommand->bindValue(':item_no',$item_no);
        $sqlCommand->bindValue(':item_cat',$item_cat);
        $sqlCommand->bindValue(':location_code',$location);
        $sqlCommand->bindValue(':date_from',$date_from);
        $sqlCommand->bindValue(':date_to',$date_to);
        
        $arrColumSumm = array('location' => 'Location',
                    'posting_date' => 'Posting Date',
                    'item_no' => 'Item No', 
                    'quantity' => 'Total Quantity',
                    'uom' => 'Unit Of Measure');
//                    'metric' => 'Metric/1 Unit',
//                    'total_metric' => 'Total Metric');
        $sqlResult = $sqlCommand->queryAll();
        if($parameter['pcs_metric'] == 1 & is_array($sqlResult)){
            $i = 0;
            foreach($sqlResult as $row){
                if($row['uom'] == 'PCS' & $row['metric'] > 0){
                    $sqlResult[$i]['quantity'] = $row['quantity'] * $row['metric'];
                    $sqlResult[$i]['uom'] = 'METRIC';
                }
                $i++;
            }
        }
        return [$arrColumSumm, $sqlResult];
    }
    public function getDetail(array $parameter){
        $item_no = $parameter['item_no'];
        $item_cat = $parameter['item_cat'];
        $location = $parameter['location'];
        $date_from = $parameter['date_from'];
        $date_to = $parameter['date_to'];
        
        $this->select = 'SELECT b.[Location Code] as location,'
                        . ' [Posting Date] as posting_date,'
                        . ' a.[No_] as item_no,'
                        . ' b.[Document No_] as doc_no,'
                        . ' b.Quantity * (-1) AS quantity,'
                        . ' a.[Base Unit of Measure] as uom,'
                        . ' a.[Metre23] * b.Quantity AS metric,'
                        . ' b. [Entry Date] as entry_date';
        
        $sqlQuery = $this->select
                . $this->from
                . $this->where;

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);

        $sqlCommand->bindValue(':item_no',$item_no);
        $sqlCommand->bindValue(':item_cat',$item_cat);
        $sqlCommand->bindValue(':location_code',$location);
        $sqlCommand->bindValue(':date_from',$date_from);
        $sqlCommand->bindValue(':date_to',$date_to);
        
        $arrColumDetail = array('location' => 'Location',
                        'posting_date' => 'Posting Date',
                        'item_no' => 'Item No',
                        'doc_no' => 'Document No',
                        'quantity' => 'Quantity',
                        'uom' => 'Unit Of Measure',
                        'metric' => 'Metric',
                        'entry_date' => 'Entry Date');
        
        return [$arrColumDetail, $sqlCommand->queryAll()];
    }
    
    public function getDataForChart($data, $dateFrom, $dateTo){
        $result = [];
        $arrTemp = [];
        $arrMonthYear = [];
        $dateFrom = (new DateTime($dateFrom))->modify('first day of this month');
        $dateTo = (new DateTime($dateTo))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($dateFrom, $interval, $dateTo);
        
        foreach ($period as $dt) {
            $monthYear = $dt->format("n/Y");
            $arrMonthYear[] = $monthYear;
        }
        $header = array_merge(['Item No'], $arrMonthYear);
        $result[] = array_merge($header, ['Unit of Measure']);
        $arrTotal = [];
        
        $numberContainerModel = new NumberContainer();
        $numberContainer = $numberContainerModel->getNumberContainer($arrMonthYear);
        $arrNumberContainer = [];
        foreach($arrMonthYear as $monthYear){
            $arrTotal[$monthYear] = 0;
            $arrNumberContainer[] = isset($numberContainer[$monthYear]) ? $numberContainer[$monthYear] : 0 ;
        }
        foreach($data as $row){
            if(!isset($arrTemp[$row['item_no']])){
                foreach($arrMonthYear as $monthYear){
                    $arrTemp[$row['item_no']][$monthYear] = 0;
                   
                }
            }
            
            $arrTemp[$row['item_no']][$row['posting_date']] += $row['quantity'];
            $arrTotal[$row['posting_date']] += $row['quantity'];
            $arrTemp[$row['item_no']]['uom'] = $row['uom'];
        }
        foreach($arrTemp as $item_no => $row){
            $result[] = array_merge([$item_no], array_values($row));
        }
        $result[] = array_merge(['TOTAL'], array_values($arrTotal));
        $result[] = array_merge(['Container'], array_values($arrNumberContainer));
        
        
        return [$arrMonthYear, $result];
    }
    public function findItemNo($item_no, $limit = 10){
        $sqlQuery = 'SELECT No_'
                . ' FROM item'
                . ' WHERE No_ LIKE :item_no'
                . ' LIMIT :limit';
        $sqlCommand = Yii::$app->db->createCommand($sqlQuery);
        $sqlCommand->bindValue(':item_no',$item_no . '%');
        $sqlCommand->bindValue(':limit',$limit);
        $sqlResult = $sqlCommand->queryAll();
        return array_column($sqlResult, 'No_');
    }
    
    public function getPeriod($dateFrom, $dateTo){
        $result = [];
        $dateFrom = (new DateTime($dateFrom));
        $dateTo = (new DateTime($dateTo));
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($dateFrom, $interval, $dateTo);
        
        foreach ($period as $dt) {
            $key = $dt->format('n/Y');
            $first_date = $dt->format('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+1 month', strtotime($first_date)));
            $end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_date)));
            $result[$key] = [$first_date, $end_date];
        }
        /*
        $dateFrom = (new DateTime($dateFrom))->modify('first day of this month');
        $dateTo = (new DateTime($dateTo))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($dateFrom, $interval, $dateTo);
        
        foreach ($period as $dt) {
            $key = $dt->format('n/Y');
            $first_date = $dt->modify('first day of this month')->format("d/n/Y");
//            var_dump($first_date);
            $end_date = $dt->modify('last day of this month')->format("d/n/Y");
//            var_dump($end_date);
            $result[$key] = [$first_date, $end_date];
        }*/
        return $result;
    }
    public function getTotalContainer(){
        $select = 'SELECT * FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Trailing Sales Orders Setup];';
        $sqlCommand = Yii::$app->dbMS->createCommand($select);
//        var_dump($sqlCommand);die();
        $arrItems = $sqlCommand->queryAll();
        var_dump($arrItems);die();
    }
    public function getSummaryData(array $parameter){
        $itemNo = $parameter['item_no'];
        $itemCat = $parameter['item_cat'];
        $location = $parameter['location'];
        $dateFrom = $parameter['date_from'];
        $dateTo = $parameter['date_to'];
        $not_include_component = $parameter['not_include_component'];
        
        $resultData = [];
        $select = 'SELECT ile.[Item No_] AS item_no';
        
        $where = ' WHERE ile.[Item No_]<>\'\''
                . ' AND ile.[Entry Type] IN (1,3,5,6) AND ile.[Quantity]<0'
                . ' AND ile.[Item No_] LIKE COALESCE(:item_no, ile.[Item No_])'
                . ' AND ile.[Item Category Code] = COALESCE(:item_cat, ile.[Item Category Code])'
                . ' AND ile.[Location Code] = COALESCE(:location_code, ile.[Location Code])'
                . ' AND ile.[Posting Date] BETWEEN :date_from'
                . ' AND :date_to';
        if($not_include_component == 1){
            $arrComponent = Component::find()->asArray()->All();
            $arrComponent = array_column($arrComponent, 'item_no');
            $where = $where . ' AND ile.[Item No_] NOT IN (\'' . implode('\',\'', $arrComponent). '\')';
        }
        $subQueryTemp = 'SELECT SUM (Quantity) * (-1) ' 
                . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry]';
        
        $subWhere = ' WHERE ile.[Item No_] = [Item No_]' 
                . ' AND [Item No_]<>\'\''
                . ' AND [Location Code] = COALESCE(:location_code, [Location Code])'
                . ' AND [Entry Type] IN (1,3,5,6) AND [Quantity]<0';
        
        $arrPeriod = $this->getPeriod($dateFrom, $dateTo);
        $arrMonthYear = [];
        $resultHeader[0][] = 'Item No'; 
        foreach($arrPeriod as $monthYear => $period){
            $select .= ',(' . $subQueryTemp 
                    . $subWhere . " AND [Posting Date] BETWEEN '$period[0]' AND '$period[1]') AS '$monthYear'";
            $resultHeader[0][] = $monthYear;
            $arrMonthYear[] = $monthYear;
        }
        $numberContainerModel = new NumberContainer();
        $numberContainer = $numberContainerModel->getNumContWoKey($arrMonthYear, $parameter['location']);
        $resultHeader[0][] = 'Unit of Measure';
        $select .= ' ,[Unit of Measure Code] as uom';
        $mainQuery = $select
                . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Ledger Entry] ile'
                .  $where
                . ' GROUP BY ile.[Item No_], ile.[Unit of Measure Code]';
        
        $sqlCommand = Yii::$app->dbMS->createCommand($mainQuery);
        $sqlCommand->bindValue(':item_no',$itemNo);
        $sqlCommand->bindValue(':item_cat',$itemCat);
        $sqlCommand->bindValue(':location_code',$location);
        $sqlCommand->bindValue(':date_from',$dateFrom);
        $sqlCommand->bindValue(':date_to',$dateTo);
        $sqlResult = $sqlCommand->queryAll();
        //convert PCS to Metric
        if($parameter['pcs_metric'] == 1){
            $arrItemNo = array_column($sqlResult, 'item_no');
            $sqlItems = 'SELECT No_ AS item_no, Metre23' 
                        . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item]'
                        . ' WHERE No_ IN (\'' . implode('\',\'', $arrItemNo) . '\')';
            $sqlItemsCommand = Yii::$app->dbMS->createCommand($sqlItems);
            $arrItems = $sqlItemsCommand->queryAll(\PDO::FETCH_KEY_PAIR);
            $i = 0;
            foreach($sqlResult as $row){
                if($row['uom'] == 'PCS' & $arrItems[$row['item_no']] > 0){
                    foreach($arrMonthYear as $monthYear){
                        $sqlResult[$i][$monthYear] = $sqlResult[$i][$monthYear] * $arrItems[$row['item_no']];
                    }
                    $sqlResult[$i]['uom'] = 'M3';
                }
                $i++;
            }
        }
        
        $arrayTotal['item_no'] = 'Total';
        foreach($arrPeriod as $monthYear => $period){
            $arrayTotal[$monthYear] = array_sum(array_column($sqlResult, $monthYear));
        }
        $sqlResult = array_merge($sqlResult, [$arrayTotal]);
        
        for($i = 0; $i < count($sqlResult); $i++){
            $resultData[] = array_values($sqlResult[$i]);
        }
        
        $resultData = array_merge($resultHeader, $resultData);
        $numberContainer = array_merge(['Number Container'], $numberContainer);
        return array_merge($resultData, [$numberContainer]);
    }
}
