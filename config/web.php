<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'user' => [
            'class' => dektrium\user\Module::class,
            'modelMap' => [
                'User' => 'app\models\User',
            ],
            'controllerMap' => [
                'security' => 'app\controllers\user\AdminController',
                'registration' => 'app\controllers\user\RegistrationController'
            ],
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin']
        ],
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
        ],
    ],

    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2X3_D9WbFmsbUeY60lywY_RNR7Ko6mrd',
        ],
        /* 'cache' => [
            'class' => 'yii\caching\FileCache',
        ], */
        'user' => [
            'class' => yii\web\User::class,
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => false,
            'transport' => [
                'scheme' => 'smtp',
                'host' => $params['mailer']['host'],
                'username' => $params['mailer']['username'],
                'password' => $params['mailer']['password'],
                'port' => $params['mailer']['port'],
                //'dsn' => $params['mailer']['dsn'],
            ],
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
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'user/login' => 'user/security/login',
                'user/register' => 'user/registration/register',
                'user/registration/validate-referral-code' => 'user/registration/validate-referral-code',
                'packages' => 'package/index',
                'packages/<action:\w+>' => 'package/<action>',
                'packages/<action:\w+>/<id:\d+>' => 'package/<action>',
                'countries' => 'country/index',
                'countries/<action:\w+>' => 'country/<action>',
                'countries/<action:\w+>/<id:\d+>' => 'country/<action>',
                'states' => 'states/index',
                'states/<action:\w+>' => 'states/<action>',
                'states/<action:\w+>/<id:\d+>' => 'states/<action>',
                'service-charges' => 'service-charges/index',
                'service-charges/<action:\w+>' => 'service-charges/<action>',
                'service-charges/<action:\w+>/<id:\d+>' => 'service-charges/<action>',
                'roi-plan' => 'roi-plan/index',
                'roi-plan/<action:\w+>' => 'roi-plan/<action>',
                'roi-plan/<action:\w+>/<id:\d+>' => 'roi-plan/<action>',
                
                // Admin Customer & Income routes
                'admin/customer' => 'user/security/customer',
                'admin/referred-team' => 'user/security/referred-team',
                'admin/level-team' => 'user/security/level-team',
                'admin/income' => 'user/security/income',
                
                // Customer CRUD routes
                'customer' => 'customer/index',
                'customer/<action:\w+>' => 'customer/<action>',
                'customer/<action:\w+>/<id:\d+>' => 'customer/<action>',
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@app/views/user'
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'models' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],
    ],
    'as access' => [
        'class' => yii\filters\AccessControl::class,
        'except' => ['user/login', 'user/security/login', 'user/register', 'user/registration/register', 'user/registration/validate-referral-code', 'site/login', 'site/error'], // no login required here
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
        'denyCallback' => function ($rule, $action) {
            return Yii::$app->response->redirect(['user/login']);
        },
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'giiant-model' => [
                'class' => 'schmunk42\giiant\generators\model\Generator',
                'templates' => [
                    'default' => '@vendor/schmunk42/yii2-giiant/src/generators/model/default',
                ]
            ],
            'giiant-crud' => [
                'class' => 'schmunk42\giiant\generators\crud\Generator',
                'templates' => [
                    'default' => '@vendor/schmunk42/yii2-giiant/src/generators/crud/default',
                ]
            ],
        ],
    ];
}

return $config;
