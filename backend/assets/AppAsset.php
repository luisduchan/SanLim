<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
//    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $css = [
//        'css/bootstrap.min.css',
        'css/red-pace-theme-flash.css',
//        'css/red-pace-theme-flash.css',
        'css/site.css',
    ];
    public $js = [
        'js/pace.js',
//        'js/grouped-categories.js',
//        'js/exporting.js',
//        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css',
//        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
