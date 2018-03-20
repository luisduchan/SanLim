<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '35jwQLQRw6EwkzFxzBhjDzKBaGPvE0lZ',
        ],

//        'assetManager' => [
//            'class' => 'yii\web\AssetManager',
//            'forceCopy' => true,
//        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1','::1','192.168.31.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // 'allowedIPs' => ['*'],
    ];


}
// var_dump(\Yii::$app->getRequest()->getUserIP());
return $config;
