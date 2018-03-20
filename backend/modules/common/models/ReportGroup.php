<?php

namespace backend\modules\common\models;

use Yii;
use yii\db\ActiveRecord;;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class ReportGroup extends ActiveRecord {
    
    public function init() {
        
    }
    public static function getDb()
    {
        // use the "db2" application component
        return \Yii::$app->dbMS;  
    }
    public static function tableName()
    {
        return '[SAN LIM FURNITURE VIETNAM LTD$Report Group Setup]';
    }
    public function getPeriod($groupdCodes){
        $result = ReportGroup::find()
                ->select('MIN([Begin Date]) as "Begin Date",MAX([End Date]) as "End Date"')
                ->where(['Code' => $groupdCodes])->one();
        if ($result) {
            return [$result->{'Begin Date'},$result->{'End Date'}];
        }
        return FALSE;
    }
    public function getValueKey() {
        $result = [];
        $sql = 'SELECT Code,Code '
                . 'FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Report Group Setup]'
                . 'ORDER BY [Begin Date];';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
//            var_dump($sqlResult);die();
            foreach ($sqlResult as $row) {
                $result[$row['Code']] = $row['Code'];
            }
            return $result;
        }
        return FALSE;
    }

    public function getAllI() {
        $sql = 'SELECT Code, Description, [Begin Date], [End Date]'
                . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Report Group Setup]';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
//        $sqlCommand->bindValue(':name', $name);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return FALSE;
    }

    public function getGroup($code) {
        $sql = 'SELECT Code code, Description description, [Begin Date] start_date, [End Date] end_date'
                . ' FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Report Group Setup]'
                . ' WHERE Code = :code';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':code', $code);
        $sqlResult = $sqlCommand->queryOne();
        if ($sqlResult) {
            return $sqlResult;
        }
        return FALSE;
    }

}
