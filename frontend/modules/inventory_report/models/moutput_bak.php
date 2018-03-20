<?php

namespace app\modules\inventory_report\models;

use Yii;
use yii\db\Query;
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
                        . ' SUM(b.Quantity) AS quantity,'
                        . ' SUM(a.[Metre23] * b.Quantity) AS metric';
        $this->group_by = ' GROUP BY b.[Location Code],'
                        .' cast(DATEPART(mm, [Posting Date]) as varchar ) + \'/\' +cast(DATEPART(yyyy, [Posting Date]) as varchar),'
                        .' a.[No_]';
       
        $sqlQuery = new Query();
        $sqlQuery->select($this->select)
                ->from($this->from)
                ->where($this->where)
                ->groupBy($this->group_by);
        
        $sqlCommand = $sqlQuery->createCommand();
        $sqlCommand->bindValue(':item_no',$item_no);
        $sqlCommand->bindValue(':item_cat',$item_cat);
        $sqlCommand->bindValue(':location_code',$location);
        $sqlCommand->bindValue(':date_from',$date_from);
        $sqlCommand->bindValue(':date_to',$date_to);
        
        return $sqlCommand->queryAll(Yii::$app->dbMS);
    }
    
}
