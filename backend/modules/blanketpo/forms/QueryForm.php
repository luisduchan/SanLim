<?php

namespace backend\modules\blanketpo\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class QueryForm extends Model {

    public $blanket_name;
    public $item_nos;
    public $customers;
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

            [
                'item_nos',
                'blanket_name',
                'customers',
                'date_from',
                'date_to',
                'date_type',
            ],
            'safe',
        ]
    ];
}

}
