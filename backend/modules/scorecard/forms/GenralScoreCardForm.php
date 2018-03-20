<?php

namespace backend\modules\scorecard\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class GenralScoreCardForm extends Model {

    public $reportGroup;
    public $customer;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return[
            [
                ['reportGroup', 'customer'],
                'required',
            ],
        ];
    }

}
