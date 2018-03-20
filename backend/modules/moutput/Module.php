<?php

namespace backend\modules\moutput;

/**
 * moutput module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\moutput\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setAliases([
            'moutput-assets' => __DIR__ . '/assets'
        ]);
        // custom initialization code goes here
    }

}
