<?php
use \kartik\datecontrol\Module;
use \kartik\mpdf\Pdf;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    //'name'=>'<i class="fa fa-google" aria-hidden="true"></i>INOLR (ERP)',
    //'name' => 'ERP ONLINE',
    'name' => '<img src="images/icon/ewinl.png">',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'admin\controllers',
    'bootstrap' => [
        'admin',
        'log',
        //'assetsAutoCompress',
        [
            'class' => 'common\components\LanguageSelector',
            'supportedLanguages' => ['en-EN','en-US', 'th-TH','la-LA', 'ch-CH','zh-CN', 'jp-JP'], //กำหนดรายการภาษาที่ support หรือใช้ได้
        ]
    ],
    'modules' => [
        'apps_rules' => [
            'class' => 'admin\modules\apps_rules\modules',
        ],
        'accounting' => [
            'class' => 'admin\modules\accounting\modules',
        ],
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => '@webroot/uploads/file',
            'uploadUrl' => '@web/uploads/file',
            'imageAllowExtensions'=>['jpg','png','gif']
        ],
        'images' => [ // ยังใช้งานไม่ได้
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => '@webroot/uploads/file',
            'uploadUrl' => '@web/uploads/file',
            'imageAllowExtensions'=>['jpg','png','gif']
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu',
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'dektrium\user\models\User',
                    'fullnameField' => 'profile.name',
                    'extraColumns' => [
                        [
                            'attribute' => 'user',
                            'label' => 'Full Name',
                            'value' => function($model, $key, $index, $column) {
                                return $model->profile->name;
                            },
                        ],
                        [
                            'attribute' => 'company',
                            'label' => 'Company Name',
                            'value' => function($model, $key, $index, $column) {
                                return $model->profile->company;
                            },
                        ],
                        #'searchClass' => 'dektrium\user\models\UserSearch'
                    ]
                    //เรียกใช้โมเดล user ของ dektrium
                ]
            ],


        ],
        'financial' => [
            'class' => 'admin\modules\financial\module',
        ],
        'Backup' => [
            'class' => 'admin\modules\Backup\modules',
        ],
        'config' => [
            'class' => 'admin\modules\config\module',
        ],
        'user' => [
            // following line will restrict access to profile, recovery, registration and settings controllers from backend
            //'as backend' => 'dektrium\user\filters\BackendFilter',
            'class' => 'dektrium\user\Module',
            'enableFlashMessages' => true,
            'enableRegistration' => false,
            'enableUnconfirmedLogin' => true,
            'enablePasswordRecovery' => false,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin']
        ],
        'vendors' => [
            'class' => 'admin\modules\vendors\Module',
        ],
        'location' => [
            'class' => 'admin\modules\location\module',
        ],
        'Management' => [
            'class' => 'admin\modules\Management\module',
        ],

        'measure' => [
            'class' => 'admin\modules\measure\Module',
        ],
        'Manufacturing' => [
            'class' => 'admin\modules\Manufacturing\modules',
        ],
        'ModuleApp' => [
            'class' => 'admin\modules\ModuleApp\module',
        ],
        'pdfjs' => [
            'class' => '\yii2assets\pdfjs\Module',
        ],
        'Purchase' => [
            'class' => 'admin\modules\Purchase\module',
        ],

        'Planning' => [
            'class' => 'admin\modules\Planning\module',
        ],
        
        'runingnoseries' => [
            'class' => 'admin\modules\runingnoseries\modules',
        ],

        'itemgroup' => [
            'class' => 'admin\modules\itemgroup\module',
        ],
        'items' => [
            'class' => 'admin\modules\items\module',
        ],
        'install' => [
            'class' => 'admin\modules\install\module',
        ],
        'Itemset' => [
            'class' => 'admin\modules\Itemset\module',
        ],

        'engineertype' => [
            'class' => 'admin\modules\engineertype\module',
        ],
        'engineer' => [
            'class' => 'admin\modules\engineer\module',
        ],

        'users' => [
            'class' => 'admin\modules\users\module',
        ],
        'property' => [
            'class' => 'admin\modules\property\module',
        ],
        'ItemHasProperty' => [
            'class' => 'admin\modules\ItemHasProperty\module',
        ],
        'company' => [
            'class' => 'admin\modules\company\module',
        ],
        'approval' => [
            'class' => 'admin\modules\approval\module',
        ],
        'salepeople' => [
            'class' => 'admin\modules\salepeople\modules',
        ],

        'tracking' => [
            'class' => 'admin\modules\tracking\modules',
        ],

        'customers' => [
            'class' => 'admin\modules\customers\module',
        ],

        'express' => [
            'class' => 'admin\modules\express\module',
        ],

        'itemcategory' => [
            'class' => 'admin\modules\itemcategory\module',
        ],
        'vattype' => [
            'class' => 'admin\modules\vattype\modules',
        ],

        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '*'] // adjust this to your needs
        ],

        'SaleOrders' => [
            'class' => 'admin\modules\SaleOrders\module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'pdf' => [
            'class' => Pdf::classname(),
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            // refer settings section for all configuration options
        ],

        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd-MM-yyyy',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss a',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // set your display timezone
            'displayTimezone' => 'Asia/Bangkok',

            // set your timezone for date saved to db
            'saveTimezone' => 'UTC',

        ],

        'warehousemoving' => [
            'class' => 'admin\modules\warehousemoving\modules',
        ],

    ],
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                   //'@app/views' => '@admin/themes/adminlte/views'
                   '@app/views' => '@admin/themes/yii2-app/views'
                   //'@app/views' => '@admin/themes/bt4/views'
                ],
            ],
        ],
        'urlManagerFrontend' => [
                'class' => 'yii\web\urlManager',
                'baseUrl' => $_SERVER['DOCUMENT_ROOT'],//'https://www.ewinl.com/',//i.e. $_SERVER['DOCUMENT_ROOT'] .'/yiiapp/web/'
                'enablePrettyUrl' => true,
                'showScriptName' => false,
        ],

        'request' => [
            'csrfParam' => 'eWin-backend',
            //'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'ewini7mq',
             
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
        ],
        'user' => [
            'identityCookie' => [
                'name'     => '_backendIdentity',
                // localhost
                'path'     => '/',

                // host
                //'path'     => '/backend/web',
                'httpOnly' => true,
            ],
        ],
        // 'session' => [
        //     'name' => 'BACKENDSESSID',
        //     'cookieParams' => [
        //         'httpOnly' => true,
        //         'path'     => '/',
        //         'timeout' => 3600*720, //session expire
        //     ],
        // ],
        /*
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],*/
        'redis'         => [
            'class'    => 'yii\redis\Connection',
            'hostname' => 'redis',
            'port'     => 6379,
            'database' => 0,
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => 'redis',
                'port' => 6379,
                'database' => 0,
            ]
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'redis',
                'port' => 6379,
                'database' => 0,
            ]
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-green',
                ],

                'dosamigos\google\maps\MapAsset' => [
                    'options' => [
                        'key' => 'AIzaSyDBtq3rsP3B3yhVboxVcRh8hwUAReVnHTk',// ใส่ API ตรงนี้ครับ
                        'language' => 'th',
                        'version' => '3.1.18'
                        ]
                ]
            ],

        ]
    ],
    'params' => $params,
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            //'site/*',
            //'admin/*',
            'line/*',
            'user/*',
            'site/logout',
            'vattype/*',
            'ajax/*',
            'install/default/*',
            'site/index',
            'users/validate/*',
            'api/api-login',
            'line-bot/robot',
            'mobile/*',
            'website/*'
            //'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
    ],
];
