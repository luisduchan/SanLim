<?php

namespace backend\modules\moutput\models;

use Yii;

/**
 * This is the model class for table "material_out_detail".
 *
 * @property integer $id
 * @property string $division_code
 * @property string $workcenter_code
 * @property string $machine_code
 * @property string $item_code
 * @property string $document_no
 * @property double $used_mass
 * @property string $uom
 * @property string $create_date
 * @property string $last_update
 */
class MaterialOutDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_out_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['used_mass'], 'number'],
            [['create_date', 'last_update'], 'safe'],
            [['division_code', 'workcenter_code', 'machine_code', 'item_code', 'document_no', 'uom'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_code' => 'Division Code',
            'workcenter_code' => 'Workcenter Code',
            'machine_code' => 'Machine Code',
            'item_code' => 'Item Code',
            'document_no' => 'Document No',
            'used_mass' => 'Used Mass',
            'uom' => 'Uom',
            'create_date' => 'Create Date',
            'last_update' => 'Last Update',
        ];
    }
}
