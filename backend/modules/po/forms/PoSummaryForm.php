<?php

namespace backend\modules\po\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PoSummaryForm extends Model {

    public $item_no;
    public $purchaser;
    public $vendor;
    public $date_type;
    public $date_from;
    public $date_to;
    public $pcs_metric;
    public $po_status;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                    [
                        [
                            ['date_from', 'date_to'],
                        'required'
                    ],
                        [
                            [
                            'item_no', 'purchaser', 'vendor', 'date_from',
                            'date_to', 'pcs_metric','date_type','po_status'
                        ],
                        'safe',
                    ]
        ];
    }

}
