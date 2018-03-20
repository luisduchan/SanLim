<?php

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'admin', // required
        'log',
        'backend\components\Settings'],
    'modules' => [
        'auth' => [
            'class' => 'backend\modules\auth\Module',
        ],
        'po' => [
            'class' => 'backend\modules\po\Module',
        ],
        'overview' => [
            'class' => 'app\modules\overview\Module',
        ],
        'common' => [
            'class' => 'app\modules\common\Module',
        ],
        'crm' => [
            'class' => 'backend\modules\crm\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'customerpo' => [
            'class' => 'backend\modules\customerpo\Module',
        ],
        'scorecard' => [
            'class' => 'backend\modules\scorecard\Module',
        ],
        'item' => [
            'class' => 'backend\modules\item\Module',
        ],
        'schedule' => [
            'class' => 'backend\modules\schedule\Module',
        ],
        'document' => [
            'class' => 'backend\modules\document\Module',
        ],
        'so' => [
            'class' => 'backend\modules\so\Module',
        ],
        'moutput' => [
            'class' => 'backend\modules\moutput\Module',
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
        ],
        'blanketpo' => [
            'class' => 'backend\modules\blanketpo\Module',
        ],  
        'ashley' => [
            'class' => 'backend\modules\ashley\Module',
        ],      
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    //'cachePath' => '@runtime/Smarty/cache',
                ],
            ],
        ],
    /*
      'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
      ],
      ],
     */
//        'view' => [
//            'theme' => [
//                'pathMap' => [
//                    '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
//                ],
//            ],
//        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            '*',
            // 'site/*',
            // 'admin/*',
            // 'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
    ],
    'aliases' => [
        '@mdm/admin' => '@vendor/mdmsoft/yii2-admin',
    ],
    'params' => $params,
];
