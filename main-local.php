<?php
defined('URL') or define('URL_BACKEND', '172.16.1.199/sl/backend/web/index.php?r=');
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=sl',
            'username' => 'root',
            'password' => '@thienhoa89',
            'charset' => 'utf8',
        ],
//        'dbMS' => [
//            'class' => 'yii\db\Connection',
//            'dsn' => 'sqlsrv:server=172.16.1.88;Database=SanlimDatabase;APP=sl_ext_prog;ConnectionPooling=false',
//            'username' => 'csharpsl',
//            'password' => 'csharpsl@123456',
//            'charset' => 'utf8',
//            'attributes' => [PDO::ATTR_STRINGIFY_FETCHES=>FALSE,]
//        ],
        'dbMS' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:server=172.16.1.88;Database=SanlimDatabase',
            'username' => 'hoaht',
            'password' => 'hoaht123456',
            'charset' => 'utf8',
            'attributes' => [PDO::ATTR_STRINGIFY_FETCHES=>FALSE,]
        ],
        'dbMS_NW' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:server=172.16.1.88;Database=Northwind',
            'username' => 'hoaht',
            'password' => 'hoaht123456',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'hoahuadev@gmail.com',
                'password' => '24thien11',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];
