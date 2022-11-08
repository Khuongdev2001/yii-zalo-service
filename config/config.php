<?php

/*
 * Created on Thu Feb 22 2018
 * By Heru Arief Wijaya
 * Copyright (c) 2018 belajararief.com
 * This is configuration of your microfw. 
 * Put your database and other configuration here.
 * Don't forget to use different id for better microfw management
 */

$env = require(__DIR__ . '/env.php');
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'micro-app',
    // the basePath of the application will be the `micro-app` directory
    'basePath' => dirname(__DIR__),
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\v1',
        ],
    ],
    // this is where the application will find all controllers
    'controllerNamespace' => 'app\controllers',
    // set an alias to enable autoloading of classes from the 'micro' namespace
    'aliases' => [
        '@app' => __DIR__ . '/../',
    ],
    'components' => [
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<alias:\w+>' => 'site/<alias>',
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfCookie' => false,
        ],
        'db' => $db,
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'zalo' => [
                    'class' => app\components\authclient\clients\ZaloService::class,
                    'appId' => env("ZALO_APP_ID"),
                    'secretKey' => env("ZALO_Secret_Key"),
                    'returnUrl' => "https://5eca-2402-800-63e0-460e-88b5-d8de-cb27-f10e.ngrok.io/zalo/request-permission?authclient=zalo",
                    'validateAuthState' => false,
                    'tableAccessToken' => 'service_oauth'
                ]
            ],
        ],
        'zalo' => [
            'class' => app\components\zalo\ZaloComponent::class,
            'authClientId' => 'authClientCollection',
            'zaloId' => 'zalo',
            'tableAccessToken' => 'service_oauth'
        ]
    ],
    'params' => $params,
];
