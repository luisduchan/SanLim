<?php

namespace backend\modules\common\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use DateTime;
use backend\modules\common\models\ReportGroup;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class ArrayTool extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function converKeyValue($array, $keyColum, $valueColum) {
        if (empty($array)) {
            return [];
        }
        $result = [];
        for ($i = 0; $i < count($array); $i++) {
            $result[$array[$i][$keyColum]] = $array[$i][$valueColum];
        }
        return $result;
    }

    public static function converKeyValueAdv($array, $keyColum, $arrayColumn) {
        if (empty($array)) {
            return [];
        }
        $result = [];
        for ($i = 0; $i < count($array); $i++) {
            $row = $array[$i];
            foreach($arrayColumn as $column){
                $result[$row[$keyColum]][$column] = $row[$column];
            }
            
        }
        return $result;
    }


    public static function convertColumsToHeader($columLeft, $headerName, $valueName, $data) {
//        var_dump($headerName);die();
        if(!$data){
            return [[], [], []];
        }
        $headers = array_column($data, $headerName);
        $headers = array_unique($headers);
        sort($headers);

        $columns = array_column($data, $columLeft);
        $columns = array_unique($columns);
        $result = [];
        foreach ($columns as $value) {
            $result[$value][$columLeft] = $value;
        }
        foreach ($data as $row) {
            foreach ($headers as $header) {
                if (!isset($result[$row[$columLeft]][$header])) {
                    $result[$row[$columLeft]][$header] = 0;
                }
                if (!isset($result[$row[$columLeft]]['total'])) {
                    $result[$row[$columLeft]]['total'] = 0;
                }
                if ($row[$headerName] == $header) {
                    $result[$row[$columLeft]][$header] += round($row[$valueName], 2);
                    $result[$row[$columLeft]]['total'] += round($row[$valueName], 2);
                }
            }
        }
        return [$columns, $headers, $result];
    }
    //merge 2 array key value to 1 array with the first columns is key and other column is value of array 1 and 2
    public static function merge2array($arr1, $arr2, $firstColumn, $secondColumn, $thirdColumn) {
        if(!$arr1 && !$arr2){
            return [];
        }
        if(!$arr1){
            return $arr2;
        }elseif(!$arr2){
            return $arr1;
        }
        $key1 = array_keys($arr1);
        $key2 = array_keys($arr2);
        $keys = array_merge($key1, $key2);
        array_unique($keys);
        $result = [];
        $i = 0;
        foreach($keys as $key){
            $result[$key][$firstColumn] = $key;
            $result[$key][$secondColumn] = isset($arr1[$key]) ? array_sum(array_values($arr1[$key])) : NULL;
            $result[$key][$thirdColumn] = isset($arr2[$key]) ? $arr2[$key] : NULL;
            $i++;
        }
        return $result;
    }
    public static function convertColumsToHeaderCustom($columLeft, $headerName, $valueName, $data, $arrayColumDetail) {
//        var_dump($headerName);die();
        if(!$data){
            return [[], [], []];
        }
        $result = [];
        $headers = array_column($data, $headerName);
        $headers = array_unique($headers);
        sort($headers);

        for($i=0; $i<count($data); $i++){
            $key = $data[$i][$columLeft];
            $result[$key][$columLeft] = $key;
            foreach($arrayColumDetail as $columName){
                $result[$key][$columName] = $data[$i][$columName];
            }
        }
        foreach ($data as $row) {
            foreach ($headers as $header) {
                if (!isset($result[$row[$columLeft]][$header])) {
                    $result[$row[$columLeft]][$header] = 0;
                }
                if (!isset($result[$row[$columLeft]]['total'])) {
                    $result[$row[$columLeft]]['total'] = 0;
                }
                if ($row[$headerName] == $header) {
                    $result[$row[$columLeft]][$header] += round($row[$valueName], 2);
                    $result[$row[$columLeft]]['total'] += round($row[$valueName], 2);
                }
            }
        }
        return [$headers, $result];
    }

}
