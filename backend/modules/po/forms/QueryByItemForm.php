<?php

namespace backend\modules\po\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class QueryByItemForm extends Model {

    public $item_nos;
    public $item_no;
    public $description;
    public $group_by_item_group;
    public $customers;
    public $date_from;
    public $date_to;
    public $date_type;
    public $unit_quantity;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                [
                    [
                      ['date_from',
                            'date_to',],'required'
                    ],
                    [

                        [
                            'item_no',
                            'item_nos',
                            'description',
                            'customers',
                            'group_by_item_group',

                            'date_type',
                            'unit_quantity'
                        ],
                        'safe',
                    ]
        ];
    }

}
