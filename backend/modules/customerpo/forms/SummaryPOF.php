<?php

namespace backend\modules\customerpo\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class SummaryPOF extends Model {

    public $reportGroup;
    public $customer;
    public $date_type;
    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
        [
            [['reportGroup','date_type'], 'required'],
            [['customer'],'safe',]
        ];
    }

}
