<?php

namespace backend\modules\moutput\assets;

use yii\web\AssetBundle;

class MouputAsset extends AssetBundle {

    // the alias to your assets folder in your file system
    public $sourcePath = '@moutput-assets';
    // finally your files..
//    public $css = [
//        'css/first-css-file.css',
//        'css/second-css-file.css',
//    ];
    public $js = [
        'js/moutput.js',
    ];
    // that are the dependecies, for makeing your Asset bundle work with Yii2 framework
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];

}
