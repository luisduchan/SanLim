<?php

namespace common\modules\sanlim\models;

use Yii;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class ViewCommon extends \yii\db\ActiveRecord {

    public static function pageTotal($provider, $colName) {
        $total = 0;
        foreach ($provider->allModels as $key => $val){
            $total += $val[$colName];
        }
        return round($total, 3);
    }

}
