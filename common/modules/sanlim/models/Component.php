<?php

namespace common\modules\sanlim\models;

use Yii;

/**
 * This is the model class for table "component".
 *
 * @property string $item_no
 */
class Component extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'component';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['item_no'], 'required'],
                [['item_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'item_no' => 'Component No',
        ];
    }

}
