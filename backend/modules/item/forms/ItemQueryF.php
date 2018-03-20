<?php

namespace backend\modules\item\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ItemQueryF extends Model {

    public $itemNo;
    public $name;
    public $customer;
    public $numberPerPage;
    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
        [
            [['itemNo'], 'required'],
            [['name','customer','numberPerPage'],'safe',]
        ];
    }

}
