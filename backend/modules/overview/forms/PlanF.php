<?php

namespace backend\modules\overview\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PlanF extends Model {

    public $item_no;
    public $date_from;
    public $date_to;
    public $pcs_metric;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                    [
                        [
                            ['date_from', 'date_to'],
                        'required'
                    ],
                        [
                            [
                            'item_no', 'date_from', 'date_to'
                        ],
                        'safe',
                    ]
        ];
    }

}
