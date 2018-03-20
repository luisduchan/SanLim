<?php

namespace backend\modules\overview\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class CusPOReport extends Model {

    public $customer;
    public $date_from;
    public $date_to;
    public $date_type;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                    [
                        [
                            ['date_from', 'date_to','date_type'],
                        'required'
                    ],
                        [
                            [
                            'customer'
                        ],
                        'safe',
                    ]
        ];
    }

}
