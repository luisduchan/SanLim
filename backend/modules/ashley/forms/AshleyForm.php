<?php
namespace backend\modules\ashley\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class AshleyForm extends Model {

    public $customer;
    public $blanketNo;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return[
            [['customer'],'required'],
            [['blanketNo'], 'required']
        ];
    }

}
