<?php

namespace backend\modules\common\models;

use Yii;
use backend\modules\common\models\ArrayTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Customer extends \yii\db\ActiveRecord {

    public function init() {
        
    }

    public function getCustomer($name) {
        $sql = 'SELECT id'
                . ' FROM customer'
                . ' WHERE name = :name';
        $sqlCommand = Yii::$app->db->createCommand($sql);
        $sqlCommand->bindValue(':name', $name);
        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return $sqlResult[0]['id'];
        }
        return FALSE;
    }
    public function getListCus() {
        $sql = 'SELECT *'
                . ' FROM customer';
        $sqlCommand = Yii::$app->db->createCommand($sql);
//        $sqlCommand->bindValue(':name', $name);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return FALSE;
    }
    public function getListCusKeyVal(){
        $cusotmerList = $this->getListCus();
//        var_dump($cusotmerList);die();
        return ArrayTool::converKeyValue($cusotmerList, 'code','name');
    }

}
