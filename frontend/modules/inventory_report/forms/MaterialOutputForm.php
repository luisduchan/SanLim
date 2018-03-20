<?php

namespace app\modules\inventory_report\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class MaterialOutputForm extends Model {

    public $location;
    public $item_no;
    public $item_cat;
    public $date_from;
    public $date_to;
    public $detail_info;
    public $generate_chart;
    public $chart_gorup_by_location;
    public $pcs_metric;
    public $chart_total_line;
    public $not_include_component;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // name, email, subject and body are required
                [
                    ['date_from', 'date_to'],
                'required'
            ],
                [
                    [
                    'item_no', 'location', 'item_no', 'date_from',
                    'date_to', 'item_cat', 'detail_info', 'generate_chart',
                    'chart_gorup_by_location', 'pcs_metric', 'chart_total_line',
                    'not_include_component'
                ],
                'safe'
            ]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels() {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

}
