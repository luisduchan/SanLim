<?php

namespace backend\modules\scorecard\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class WsiScoreCardForm extends Model {

    public $reportGroup;
    public $baseOnCofirmShipDate;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return
                [
                    [
                        ['reportGroup'], 'required',

                    ],[['baseOnCofirmShipDate'], 'safe']
        ];
    }

}
