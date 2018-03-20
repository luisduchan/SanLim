<?php

namespace backend\modules\common\models;

use Yii;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Destination extends \yii\db\ActiveRecord {

    public function init() {
        
    }

    public function getDestination($name) {
        $sql = 'SELECT id'
                . ' FROM destination'
                . ' WHERE name = :name';
        $sqlCommand = Yii::$app->db->createCommand($sql);
        $sqlCommand->bindValue(':name', $name);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult[0]['id'];
        }
        return FALSE;
    }

}
