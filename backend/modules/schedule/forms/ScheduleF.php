<?php

namespace backend\modules\schedule\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ScheduleF extends Model {

    public $reportGroup;
    public $customer;
    public $downLoad;
//    public $date_from;
//    public $date_to;
//    public $pcs_metric;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                    [
                        [
                            ['reportGroup'],'required'
                    ],
                        [
                            [
                            'customer',
                                'downLoad',
                        ],
                        'safe',
                    ]
        ];
    }

}
