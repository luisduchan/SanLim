<?php

namespace backend\modules\customerpo\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class NeedPlanF extends Model {

    public $group;
//    public $customer;
    public $date_type;
    public $not_delay;
    public $not_scheduled;
    public $group_by_product_group;
    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
        [
            [['group','date_type'], 'required'],
            [['not_delay', 'not_scheduled', 'group_by_product_group'],'safe',]
        ];
    }

}
