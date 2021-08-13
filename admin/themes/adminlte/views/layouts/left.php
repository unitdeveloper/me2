<?php
use yii\helpers\Html;
use richardfan\widget\JSRegister;

use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;


$Policy = SetupSysMenu::findOne(3);
//$PoliWh = explode(',',$Policy->rules_id);
$PoliWh = SysRuleModels::getPolicy('Data Access','warehousemoving','report','common','menu');

$myRule = \Yii::$app->session->get('Rules');

$user = Yii::$app->user->identity;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
            <?= Html::img($user->profile->getAvatarUrl(24), [
                'class' => 'img-responsive',
                'alt' => $user->username,
            ]) ?>
                <!-- <img src="../../avatar/avatar.png" class="img-circle" alt="User Image"/> -->
            </div>
            <div class="pull-left info">
                <p><?= $Profile->name ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form id="search" action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="all-search" class="form-control ew-all-search" placeholder="<?=Yii::t('common','Search...')?>"/>
              <span class="input-group-btn">
                <button type='button' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
            <div class="ew-speech-ai"></div>
        </form>

        <!-- /.search form -->
        <?php dmstr\widgets\Menu::$iconClassPrefix = ''; ?>
        <?= dmstr\widgets\Menu::widget(
            [
                //'options' => ['class' => 'sidebar-menu'],
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => Yii::t('common','Main Menu'), 'options' => ['class' => 'header']],

                    [
                        'label' => Yii::t('common','Management'),
                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Management','report','common','menu')),
                        'icon' => 'fa fa-universal-access',
                        'options' => [
                                            'class' => '',
                                            'id' => 'ew-alert-management',
                                            'ew-url' => 'Url',
                                            ],
                        'url' => '#',
                        'items' => [
                                [
                                    'label' => Yii::t('common','Approve Receipt'),
                                    'icon' => 'fa fa-credit-card',
                                    'url' => ['/Management/approve'],
                                    'options' => [
                                            'class' => '',
                                            'id' => 'ew-alert-management-approve',
                                            'ew-url' => 'Url',
                                            ],
                                    'active' => in_array(\Yii::$app->controller->Route,['Management/approve/index']),
                                ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Approved'),
                                        'icon' => 'fa fa-credit-card',
                                        'url' => ['/Management/report/approved'],
                                        'active' => in_array(\Yii::$app->controller->Route,['Management/report/approved']),
                                    ],
                                    // [
                                    //     'label' => Yii::t('common','Sale Cash'),
                                    //     'icon' => 'fa fa-table',
                                    //     'url' => '#',
                                    //     //'active' => in_array(\Yii::$app->controller->Route,['Management/report/sale-cash']),

                                    // ],
                                    // [
                                    //     'label' => Yii::t('common','Invoice'),
                                    //     'icon' => 'fa fa-money',
                                    //     'url' => ['/Management/report/invoice'],
                                    //     'active' => in_array(\Yii::$app->controller->Route,['Management/report/invoice']),
                                    // ],


                                ],
                            ],



                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Report Setting'), 'icon' => 'fa fa-user-md', 'url' => '#',],

                                ],
                            ],
                        ],
                    ],

                    [
                        'label' => Yii::t('common','Service Module'),
                        'visible' => (Yii::$app->user->identity->id==1),
                        'icon' => 'fa fa-handshake-o',
                        'url' => '#',
                        'items' => [

                        	 [
                                'label' => Yii::t('common','Engineer Info'),
                                'icon' => 'fa fa-user-secret',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Engineer Name'), 'icon' => 'fa fa-user-md', 'url' => ['/engineer/engineer'],],
                                    ['label' => Yii::t('common','Repair History'), 'icon' => 'fa fa-bar-chart', 'url' => '#',],


                                ],
                            ],



                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Engineer Type'), 'icon' => 'fa fa-user-md', 'url' => ['/engineertype/engineertype'],],

                                ],
                            ],
                        ],
                    ],

                    [
                        'label' => Yii::t('common','Financial'),
                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Finance','report','common','menu')),
                        'icon' => 'fa fa-line-chart',
                        'url' => '#',
                        'items' => [

                                    [
                                        'label' => Yii::t('common','Receive Money'),
                                        'icon' => 'fa fa-credit-card',
                                        'url' => ['/Management/financial/cheque'],
                                        'active' => in_array(\Yii::$app->controller->Route,['Management/financial/cheque']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Sale Cash'),
                                        'icon' => 'fa fa-table',
                                        'url' => ['/Management/financial/sale-cash'],
                                        'active' => in_array(\Yii::$app->controller->Route,['Management/financial/sale-cash']),

                                    ],




                                    [
                                        'label' => Yii::t('common','Setup'),
                                        'icon' => 'fa fa-cog',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => Yii::t('common','Report Setting'), 'icon' => 'fa fa-user-md', 'url' => '#',],

                                        ],
                                    ],
                        ],
                    ],


                    [
                        'label' => Yii::t('common','Accounting'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2),
                        'icon' => 'fa fa-money',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('common','Payables'),
                                'icon' => 'fa fa-paypal',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Invoice'),
                                        'icon' => 'fa fa-circle-o',
                                        'url' => '#',
                                        //'active' => in_array(\Yii::$app->controller->id,['saleinvheader'])
                                    ],
                                    ['label' => Yii::t('common','Credit Note'), 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    ['label' => Yii::t('common','Payment Journals'), 'icon' => 'fa fa-circle-o', 'url' => '#',],



                                ],

                            ],
                            [
                                'label' => Yii::t('common','Receivables'),
                                'icon' => 'fa fa-calculator',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Customer'),
                                        'visible' => in_array(!Yii::$app->session->get('Rules')['rules_id'],['1','3']),
                                        'icon' => 'fa fa-address-card-o',
                                        'url' => ['/customers/customer'],
                                        //'active' => in_array(\Yii::$app->controller->Route,['/customers/customer']),
                                        'active' => in_array(\Yii::$app->controller->id,['customer','create','view'])
                                    ],
                                    [
                                        'label' => Yii::t('common','Billing Note'),
                                        'icon' => 'fa fa-btc',
                                        'url' => ['/accounting/billing/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/billing/index',
                                            'accounting/billing/create',
                                            'accounting/billing/update',
                                            'accounting/billing/view',


                                        ]),
                                        //'active' => in_array(\Yii::$app->controller->id,['salebilling'])
                                    ],

                                    [
                                        'label' => Yii::t('common','Tax Invoice/Receipt'),
                                        'icon' => 'fa fa-file-text-o',
                                        'url' => ['/accounting/saleinvoice'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/saleinvoice/',
                                            'accounting/saleinvoice/index',
                                            'accounting/saleinvoice/update',
                                            'accounting/saleinvoice/create',
                                            ]),
                                        //'active' => in_array(\Yii::$app->controller->id,['saleinvoice'])
                                    ],

                                    ['label' => Yii::t('common','Credit Memos'), 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    [
                                        'label' => Yii::t('common','Cash Receipt Journals'),
                                        'icon' => 'fa fa-circle-o',
                                        'url' => ['/accounting/cheque'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/cheque/',
                                            'accounting/cheque/index',
                                            'accounting/cheque/update',
                                            'accounting/cheque/create',
                                            ]),
                                    ],
                                    [
                                        'label' => Yii::t('common','Report'),
                                        'icon' => 'fa fa-bar-chart',
                                        'url' => '#',
                                            'items' => [
                                                [
                                                    'label' => Yii::t('common','Sales tax report'),
                                                    'icon' => 'fa fa-file-pdf-o',
                                                    'url' => ['/accounting/rcreport/sale-tax'],
                                                    'active' => in_array(\Yii::$app->controller->id,['rcreport'])
                                                ],
                                                [
                                                    'label' => Yii::t('common','Sales report'),
                                                    'icon' => 'fa fa-book',
                                                    'url' => ['/accounting/rcreport/sale-report'],
                                                    'active' => in_array(\Yii::$app->controller->id,['rcreport'])
                                                ],

                                                [
                                                    'label' => Yii::t('common','Posted Invoice'),
                                                    'icon' => 'fa fa-book',
                                                    'url' => ['/accounting/posted/index'],
                                                    'active' => in_array(\Yii::$app->controller->id,['index','posted'])
                                                ],

                                            ],
                                        ],
                                ],
                            ],
                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Account Schedules'), 'icon' => 'fa fa-file-pdf-o', 'url' => '#',],
                                    ['label' => Yii::t('common','สมุดรายวัน'), 'icon' => 'fa fa-book', 'url' => '#',],
                                    [
                                        'label' => 'งบการเงิน',
                                        'icon' => 'fa fa-calculator',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => Yii::t('common','งบดุล'), 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                            ['label' => Yii::t('common','งลกำไรขาดทุน'), 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Chart of accounts'),
                                        'icon' => 'fa fa-sliders',
                                        'url' => ['/accounting/chart-of-account/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/chart-of-account/index',
                                            'accounting/chart-of-account/create',
                                            'accounting/chart-of-account/view',
                                            'accounting/chart-of-account/update'])
                                    ],

                                    //['label' => Yii::t('common','Budget'), 'icon' => 'fa fa-pencil-square-o', 'url' => '#',],

                                    [
                                        'label' => Yii::t('common','Bank Account'),
                                        'icon' => 'fa fa-university',
                                        'url' => ['/accounting/bank-account'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/bank-account/index',
                                            'accounting/bank-account/create',
                                            'accounting/bank-account/view',
                                            'accounting/bank-account/update',
                                            'accounting/bank-list/index',
                                            'accounting/bank-list/create',
                                            'accounting/bank-list/view',
                                            'accounting/bank-list/update'])
                                    ],

                                    [
                                        'label' => Yii::t('common','Vat Type'),
                                        'icon' => 'fa fa-tags',
                                        'url' => ['/vattype/vattype'],
                                        'active' => in_array(\Yii::$app->controller->id,['vattype','create','view'])
                                    ],
                                ],
                            ],
                        ],
                    ],


                    [
                        'label' => Yii::t('common','Sales & Marketing'),
                        'icon' => 'fa fa-id-card-o',
                        'url' => '#',
                        'options' => [
                                            'class' => '',
                                            'id' => 'ew-alert-salemain',
                                            'ew-url' => 'Url',
                                            ],
                        'items' => [
                            [
                                'label' => Yii::t('common','Sale Person'),

                                'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2 || Yii::$app->session->get('Rules')['rules_id']==4),
                                'icon' => 'fa fa-user-circle-o',
                                'url' => ['/salepeople/people'],
                                'active' => in_array(\Yii::$app->controller->id,['people','create','view'])
                            ],
                            [
                                'label' => Yii::t('common','Customer'),
                                'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Main Menu','saleorder','Customer','Customer-Access')),
                                //'visible' => in_array(Yii::$app->session->get('Rules')['rules_id'],['1','3','4','7']),
                                'icon' => 'fa fa-address-card-o',
                                'url' => ['/customers/customer'],
                                'active' => in_array(\Yii::$app->controller->id,['customer','create','view'])
                            ],
                            //['label' => 'Quotation', 'icon' => 'fa fa-quora', 'url' => ['#'],],
                            [
                                'label' => Yii::t('common','Sale Order'),
                                'icon' => 'fa fa-usd',
                                'url' => ['/SaleOrders/saleorder'],
                                'options' => [
                                            'class' => '',
                                            'id' => 'ew-alert-saleorder',
                                            'ew-url' => 'Url',
                                            ],
                                'active' => in_array(\Yii::$app->controller->id,['saleorder'])
                            ],
                            [
                                'label' => Yii::t('common','Tax Invoice/Receipt'),
                                'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Main Menu','saleorder','SaleAdmin','Bill-For-Admin')),
                                //'visible' => in_array(\Yii::$app->session->get('Rules')['rules_id'],['4']),
                                'icon' => 'fa fa-file-text-o',
                                'url' => ['/accounting/saleinvoice'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'accounting/saleinvoice/',
                                    'accounting/saleinvoice/index',
                                    'accounting/saleinvoice/update',
                                    'accounting/saleinvoice/create',
                                    ]),

                            ],
                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Sales Report'),
                                        'icon' => 'fa fa-file-pdf-o',
                                        'url' => ['/SaleOrders/report/report-daily'],
                                    ],

                                    [
                                        'label' => Yii::t('common','Goods Sale'),
                                        'icon' => 'fa fa-truck',
                                        'url' => '#',
                                    ],

                                    [
                                        'label' => Yii::t('common','Sale Line'),
                                        'icon' => 'fa fa-list-ol',
                                        'url' => ['/SaleOrders/order'],
                                        //'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                                    ],

                                    [
                                        'label' => Yii::t('common','Posted Invoice'),
                                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Main Menu','saleorder','SaleAdmin','Bill-For-Admin')),
                                        //'visible' => in_array(\Yii::$app->session->get('Rules')['rules_id'],['4']),
                                        'icon' => 'fa fa-book',
                                        'url' => ['/accounting/posted/index'],
                                        'active' => in_array(\Yii::$app->controller->id,['index','posted'])
                                    ],

                                ],
                            ],
                            [
                                'label' => Yii::t('common','Setup'),
                                'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Sale Group'),
                                        'icon' => 'fa fa-users',
                                        'url' => ['/salepeople/salegroup/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                'salepeople/salegroup/index',
                                                'salepeople/salegroup/create',
                                                'salepeople/salegroup/update',
                                                'salepeople/salegroup/view'])
                                    ],
                                    [
                                        'label' => Yii::t('common','Promotion'),
                                        'icon' => 'fa fa-circle-o',
                                        'url' => '#',


                                    ],
                                    [
                                        'label' => Yii::t('common','Online Service'),
                                        'icon' => 'fa fa-mixcloud',
                                        'url' => '#',

                                    ],
                                ],
                            ],
                        ],
                    ],


                    [
                        'label' => Yii::t('common','Purchase'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2),
                        'icon' => 'fa fa-shopping-basket',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('common','Vendors'),
                                'icon' => 'fa fa-address-card-o',
                                'url' => ['/vendors/vendors'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                  'vendors/vendors/index',
                                  'vendors/vendors/create',
                                  'vendors/vendors/view',
                                  'vendors/vendors/update'
                                ]),
                            ],
                            [
                                'label' => Yii::t('common','Purchase Order'),
                                'icon' => 'fa fa-cc-visa',
                                'url' => ['/Purchase/order'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                            'Purchase/default/index',
                                            'Purchase/order/view',
                                            'Purchase/order/index',
                                            'Purchase/order/create',
                                            'Purchase/order/update',
                                            ]),

                            ],
                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    [
                                      'label' => Yii::t('common','Print Options'),
                                      'icon' => 'fa fa-print text-aqua',
                                      'url' => ['/Purchase/order/print-options'],
                                      'active' => in_array(\Yii::$app->controller->Route,[
                                                  'Purchase/order/print-options',
                                                  ]),

                                    ],
                                    [
                                      'label' => Yii::t('common','Currency'),
                                      'icon' => 'glyphicon glyphicon-random text-aqua',
                                      'url' => ['/Purchase/default/index',],
                                      'active' => in_array(\Yii::$app->controller->Route,[
                                                  'Purchase/order/Currency',
                                                  ]),

                                    ],
                                    // [
                                    //     'label' => 'Currency',
                                    //     'icon' => 'fa fa-usd',
                                    //     'url' => '#',
                                    //     'items' => [
                                    //         ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    //         ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    //     ],
                                    // ],
                                ],
                            ],
                        ],
                    ],


                    [
                        'label' => Yii::t('common','Warehouse'),
                        'visible' => in_array(Yii::$app->session->get('Rules')['rules_id'],$PoliWh),
                        'icon' => 'fa fa-th',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('common','Items'),
                                'icon' => 'fa fa-cube',
                                'url' => ['/warehousemoving/item'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/item/index',
                                    'warehousemoving/item/create',
                                    'warehousemoving/item/update',
                                    'warehousemoving/item/view'])
                            ],

                            [
                                'label' => Yii::t('common','Item Adjust'),
                                'icon' => 'fa fa-balance-scale',
                                'encodeLabels' => false,
                                'url' => ['/warehousemoving/adjust'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/adjust/index',
                                    'warehousemoving/adjust/create',
                                    'warehousemoving/adjust/update',
                                    'warehousemoving/adjust/view'])
                            ],

                            [
                                'label' => Yii::t('common','Receive'),
                                'icon' => 'fa fa-th',
                                'url' => '#',
                                // 'active' => in_array(\Yii::$app->controller->Route,[
                                //     'warehousemoving/default/index',
                                //     'warehousemoving/warehouse/index',
                                //     'warehousemoving/warehouse/',
                                //     'warehousemoving/warehouse/shipment',
                                //     'warehousemoving/warehouse/shipline'])
                                ],
                            [
                                'label' => Yii::t('common','Shipment'),
                                'icon' => 'fa fa-truck',
                                'url' => ['/warehousemoving'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/default/index',
                                    'warehousemoving/warehouse/shipment',
                                    'warehousemoving/warehouse/shipline'])
                            ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Stock Movement'),
                                        'icon' => 'fa fa-file-pdf-o',
                                        'url' => ['/warehousemoving/warehouse'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/default/index',
                                            'warehousemoving/warehouse/index',
                                            'warehousemoving/warehouse/',
                                            'warehousemoving/warehouse/shipment',
                                            'warehousemoving/warehouse/shipline'])
                                    ],
                                    ['label' => Yii::t('common','สินค้าคงคลัง'), 'icon' => 'fa fa-file-pdf-o', 'url' => '#',],
                                    ['label' => Yii::t('common','รายงานการรับ สินค้า'), 'icon' => 'fa fa-file-pdf-o', 'url' => '#',],
                                    [
                                        'label' => Yii::t('common','รายงานการส่ง สินค้า'),
                                        'icon' => 'fa fa-file-pdf-o',
                                        'url' => ['/warehousemoving/header/'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/header/index',
                                            'warehousemoving/header/view'
                                        ])
                                    ],

                                ],
                            ],
                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Location'),
                                        'icon' => 'fa fa-th-large',
                                        'url' => ['/location/location'],
                                        'active' => in_array(\Yii::$app->controller->id,['location'])
                                    ],

                                ],
                            ],
                        ],
                    ],

                    [
                        'label' => Yii::t('common','Manufacturing'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2 ),
                        'icon' => 'fa fa-industry',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('common','Items'),
                                'icon' => 'fa fa-cube',
                                'url' => ['/items/items'],
                                'active' => in_array(\Yii::$app->controller->id,['items'])
                            ],

                            [
                                'label' => Yii::t('common','Production BOM'),
                                'icon' => 'fa fa-link',
                                'url' =>  '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','KitBOM'),
                                        'icon' => 'fa fa-file-text-o',
                                        'url' => '#',
                                        'items' => [
                                                    [
                                                        'label' => Yii::t('common','KitBOM Header'),
                                                        'icon' => 'fa fa-file-text-o',
                                                        'url' => ['/Itemset/bomset'],
                                                        'active' => in_array(\Yii::$app->controller->id,['bomset']),
                                                    ],
                                                    [
                                                        'label' => Yii::t('common','KitBOM Line'),
                                                        'icon' => 'fa fa-braille',
                                                        'url' => ['/Manufacturing/bom'],
                                                        'active' => in_array(\Yii::$app->controller->id,['bom']),
                                                    ],
                                            ],
                                    ],


                                    [
                                        'label' => Yii::t('common','Product BOM'),
                                        'icon' => 'fa fa-file-text-o',
                                        'url' => '#',
                                        'items' => [
                                                    [
                                                        'label' => Yii::t('common','BOM Header'),
                                                        'icon' => 'fa fa-file-text-o',
                                                        'url' => ['/Manufacturing/prodbom/index'],
                                                        'active' => in_array(\Yii::$app->controller->id,['prodbom']),
                                                    ],
                                                    [
                                                        'label' => Yii::t('common','BOM Line'),
                                                        'icon' => 'fa fa-braille',
                                                        'url' => ['/Manufacturing/prodbomline'],
                                                        'active' => in_array(\Yii::$app->controller->id,['prodbomline']),
                                                    ],
                                            ],
                                    ],
                                ],
                            ],

                            [
                                'label' => Yii::t('common','Execution'),
                                'icon' => 'fa fa-cubes',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Prod. Order'), 'icon' => 'fa fa-tasks', 'url' =>  '#',],
                                    ['label' => Yii::t('common','Output'), 'icon' => 'fa fa-archive', 'url' =>  '#',],
                                    ['label' => Yii::t('common','Consumption'), 'icon' => 'fa fa-free-code-camp', 'url' => '#',],
                                ],
                            ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Prod. Report'), 'icon' => 'fa fa-file-pdf-o', 'url' => '#',],


                                ],
                            ],

                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Item Category'),
                                        'icon' => 'fa fa-sitemap',
                                        'url' => ['/itemcategory/category'],
                                        'active' => in_array(\Yii::$app->controller->id,['category']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Item Group'),
                                        'icon' => 'fa fa-sitemap',
                                        'url' => ['/itemgroup/itemgroup'],
                                        'active' => in_array(\Yii::$app->controller->id,['itemgroup']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Item Set'),
                                        'icon' => 'fa fa-sitemap',
                                        'url' => ['/Itemset/itemset'],
                                        'active' => in_array(\Yii::$app->controller->id,['itemset']),
                                    ],
                                    ['label' => Yii::t('common','Property'), 'icon' => 'fa fa-sitemap', 'url' => ['/property/property'],
                                        'active' => in_array(\Yii::$app->controller->id,['property']),],
                                    [
                                        'label' => Yii::t('common','Base Unit'),
                                        'icon' => 'fa fa-cube',
                                        'url' => '#',
                                        'items' => [
                                            //['label' => 'Item Unit Of Measure', 'icon' => 'fa fa-braille', 'url' => '#',],
                                            [
                                                'label' => Yii::t('common','Unit Of Measure'),
                                                'icon' => 'fa fa-file-text-o',
                                                'url' => ['/measure/measure'],
                                                'active' => in_array(\Yii::$app->controller->id,['measure']),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],



                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => Yii::t('common','Administrator'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2 ),
                        'icon' => 'fa fa-desktop',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('common','Company'),
                                'icon' => 'fa fa-building-o',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Company'),
                                        'icon' => 'fa fa-sitemap',
                                        'url' => ['/company/company'],
                                        'active' => in_array(\Yii::$app->controller->id,['company']),
                                    ],

                                    [
                                        'label' => Yii::t('common','Users'),
                                        'icon' => 'fa fa-user-circle',
                                        'url' => '#',
                                        //'active' => in_array(\Yii::$app->controller->id,['rules']),
                                         'items' => [
                                                [
                                                    'label' => Yii::t('common','Rules'),
                                                    'icon' => 'fa fa-sitemap',
                                                    'url' => ['/apps_rules/rules'],
                                                    'active' => in_array(\Yii::$app->controller->id,['rules']),
                                                ],
                                                [
                                                    'label' => Yii::t('common','Setup'),
                                                    'icon' => 'fa fa-sitemap',
                                                    'url' => ['/apps_rules/setup'],
                                                    'active' => in_array(\Yii::$app->controller->id,['setup']),
                                                ],
                                            ],

                                    ],

                                ],

                            ],
                            [
                                'label' => Yii::t('common','No Series'),
                                'icon' => 'fa fa-tag',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','No Series'),
                                        'icon' => 'fa fa-braille', 'url' => ['/series/index'],
                                        'active' => in_array(\Yii::$app->controller->id,['series']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Runing No Series'),
                                        'icon' => 'fa fa-file-text-o',
                                        'url' => ['/runingnoseries/runnose'],
                                        'active' => in_array(\Yii::$app->controller->id,['runnose']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Setup No Series'),
                                        'icon' => 'fa fa-braille',
                                        'url' => ['/setupnos/index'],
                                        'active' => in_array(\Yii::$app->controller->id,['setupnos']),
                                    ],

                                ],
                            ],
                            [
                                'label' => Yii::t('common','System Setup'),
                                'icon' => 'fa fa-wrench',
                                'url' => '#',
                                'visible' => (Yii::$app->user->identity->id==1),
                                'items' => [

                                    [
                                        'label' => Yii::t('common','Application'),
                                        'icon' => 'fa fa-android',
                                        'url' => '#',
                                        'items' => [
                                                    [
                                                        'label' => Yii::t('common','Business Type'),
                                                        'icon' => 'fa fa-fort-awesome',
                                                        'url' => ['/biz-type'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'biz-type/index',
                                                                    'biz-type/create',
                                                                    'biz-type/update',
                                                                    'biz-type/view'])
                                                    ],
                                                    [
                                                        'label' => Yii::t('common','Modules'),
                                                        'icon' => 'fa fa-codepen text-danger',
                                                        'url' => ['/module-app'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'module-app/index',
                                                                    'module-app/create',
                                                                    'module-app/update',
                                                                    'module-app/view'])
                                                    ],
                                        ]
                                    ],

                                        [
                                            'label' => Yii::t('common','Setup Policy'),
                                            'icon' => 'fa fa-cogs',
                                            'url' => '#',
                                            'items' => [
                                                    [
                                                    'label' => Yii::t('common','Policy'),
                                                    'icon' => 'fa fa-unlock text-warning',
                                                    'url' => ['/apps_rules/setupsys'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                            'apps_rules/setupsys/index',
                                                            'apps_rules/setupsys/create',
                                                            'apps_rules/setupsys/update',
                                                            'apps_rules/setupsys/view'])
                                                    ],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('common','Maps'),
                                        'icon' => 'fa fa-map',
                                        'url' => '#',
                                        'items' => [
                                                    [
                                                        'label' => Yii::t('common','Province'),
                                                        'icon' => 'fa fa-location-arrow',
                                                        'url' => ['/province'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'province/index',
                                                                    'province/create',
                                                                    'province/update',
                                                                    'province/view'])
                                                    ],
                                                    [
                                                        'label' => Yii::t('common','Amphur'),
                                                        'icon' => 'fa fa-map-marker',
                                                        'url' => ['/amphur'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'amphur/index',
                                                                    'amphur/create',
                                                                    'amphur/update',
                                                                    'amphur/view'])
                                                    ],
                                                    [
                                                        'label' => Yii::t('common','District'),
                                                        'icon' => 'fa fa-map-signs',
                                                        'url' => ['/district'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'district/index',
                                                                    'district/create',
                                                                    'district/update',
                                                                    'district/view'])
                                                    ],
                                                    [
                                                        'label' => Yii::t('common','Postcode'),
                                                        'icon' => 'fa fa-street-view',
                                                        'url' => ['/zipcode'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'zipcode/index',
                                                                    'zipcode/create',
                                                                    'zipcode/update',
                                                                    'zipcode/view'])
                                                    ],


                                        ],
                                    ],



                                    ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
                                    ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
                                    ['label' => 'Admin', 'icon' => 'fa fa-dashboard', 'url' => ['/admin'],],
                                    [
                                        'label' => 'phpMyAdmin',
                                        'icon' => 'fa fa-dashboard',
                                        'url' => '/phpmyadmin',
                                        'template'=> '<a href="{url}" target="_blank">{icon}{label}</a>',
                                    ],

                                ],
                            ],

                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
