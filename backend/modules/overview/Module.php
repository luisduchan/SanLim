<?php

namespace app\modules\overview;

/**
 * overview module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\overview\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setAliases([ 
            'overview-assets' => __DIR__ . '/assets' 
        ]); 
        // custom initialization code goes here
    }
}
