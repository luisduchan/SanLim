<?php

namespace backend\modules\scorecard\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class LazboyScoreCardForm extends Model {

    public $dateFrom;
    public $dateTo;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                [
                    [
                        ['dateFrom', 'dateTo'], 'required'
                    ],
        ];
    }

}
