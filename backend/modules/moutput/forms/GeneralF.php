<?php

namespace backend\modules\moutput\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class GeneralF extends Model {

//    public $reportGroup;
    public $date_from;
    public $date_to;
    public $item_no;
    public $range;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return[
            [
                [
                    'date_from',
                    'date_to',
                    'item_no',
                ],
                'required',
            ],
           [
               ['range'], 'safe'
           ]
        ];
    }

}
