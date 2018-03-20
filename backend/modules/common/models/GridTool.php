<?php

namespace backend\modules\common\models;

use Yii;

class GridTool extends \yii\db\ActiveRecord {

    public static function pageTotal($provider, $colName) {
        $total = 0;
        foreach ($provider->allModels as $key => $val) {
            $total += $val[$colName];
        }
        $total = round($total, 3);
        return $total;
    }

}
