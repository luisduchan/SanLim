<?php

namespace backend\modules\overview\assets;

use yii\web\AssetBundle;

class OverviewAsset extends AssetBundle {

    // the alias to your assets folder in your file system 
    public $sourcePath = '@overview-assets';
    // finally your files..  
//    public $css = [
//        'css/first-css-file.css',
//        'css/second-css-file.css',
//    ];
    public $js = [
        'js/overview.js',
    ];
    // that are the dependecies, for makeing your Asset bundle work with Yii2 framework 
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
