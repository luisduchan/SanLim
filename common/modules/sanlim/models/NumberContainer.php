<?php

namespace common\modules\sanlim\models;

use Yii;

/**
 * This is the model class for table "number_container".
 *
 * @property integer $id
 * @property double $number_container
 * @property string $date
 * @property string $month_year
 * @property string $location_code
 */
class NumberContainer extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'number_container';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['number_container'], 'number'],
                [['date'], 'safe'],
                [['month_year', 'location_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'number_container' => 'Number Container',
            'date' => 'Date',
            'month_year' => 'Month Year',
            'location_code' => 'Location Code',
        ];
    }
    
    public function getNumberContainer($arrMonthYear, $locationCode = NULL) {
        $arrMonthYearInPut = substr(str_repeat(',?', count($arrMonthYear)), 1);
        $sqlQuery = 'SELECT month_year, SUM(number_container)'
            . ' FROM `number_container` ' 
            . ' WHERE month_year IN (\'' . implode('\',\'', $arrMonthYear) . '\') ';
        if(!is_null($locationCode)){
            $sqlQuery = $sqlQuery . " AND location_code = '$locationCode'";
        }
        $sqlQuery = $sqlQuery . ' GROUP BY month_year';

        $sqlCommand = Yii::$app->db->createCommand($sqlQuery);
        $result = $sqlCommand->queryAll(\PDO::FETCH_KEY_PAIR);
        foreach($arrMonthYear as $monthYear){
            if(!isset($result[$monthYear])){
                $result[$monthYear] = 0;
            }
        }
        return $result;
    }
    
    public function getNumContWoKey($arrMonthYear, $locationCode = NULL) {
        $result = [];
        $numCont = $this->getNumberContainer($arrMonthYear, $locationCode);
        foreach($arrMonthYear as $monthYear){
            $result[] = round($numCont[$monthYear],3);
        }
        return $result;
    }
}
