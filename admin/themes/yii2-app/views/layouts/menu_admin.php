<?php
use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;



$Policy = SetupSysMenu::findOne(3);
//$PoliWh = explode(',',$Policy->rules_id);
$PoliWh = SysRuleModels::getPolicy('Data Access','warehousemoving','report','common','menu');

$myRule = \Yii::$app->session->get('Rules');

dmstr\widgets\Menu::$iconClassPrefix = ''; 
?>
<style>
.ew-data-approve,
.ew-data-approval{
    display:none;
}
</style>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => Yii::t('common','Main Menu'), 'options' => ['class' => 'header']],

                    [
                        'label' => Yii::t('common','Management'),
                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Management','report','common','menu')),
                        'icon' => 'fa fa-universal-access', 
                        'labelCount' => 1,
                        'labelCountClass' => 'label label-danger ew-data-approve',                       
                        // 'options' => [
                        //                     'class' => '',
                        //                     'id' => 'ew-alert-management',
                        //                     'ew-url' => 'Url',
                        //                     ],
                        'url' => '#',
                        'items' => [
                                [
                                    'label' => Yii::t('common','Approval'),
                                    'icon' => 'far fa-list-alt',
                                    'url' => ['/Management/approval/index'],     
                                    'labelCount' => 1,
                                    'labelCountClass' => 'label label-danger ew-data-approval',                                 
                                    'active' => in_array(\Yii::$app->controller->Route,[
                                        'Management/approval/index',
                                        'Management/approval/view',
                                        'Management/approval/approval',
                                        'Management/approval/approve',
                                        'Management/approval/reject'
                                        ]),
                                ],
                                [
                                    'label' => Yii::t('common','Approve Receipt'),
                                    'icon' => 'fa fa-credit-card',
                                    'url' => ['/Management/approve'],
                                    'labelCount' => 1,
                                    'labelCountClass' => 'label label-danger ew-data-approve',  
                                    // 'options' => [
                                    //         'class' => '',
                                    //         'id' => 'ew-alert-management-approve',
                                    //         'ew-url' => 'Url',
                                    //         ],
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
                        'label' => Yii::t('common','Financial'),
                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Finance','report','common','menu')),
                        'icon' => 'fas fa-university',                        
                        'url' => '#',
                        'items' => [

                            [
                                'label' => Yii::t('common','Receive Money'),
                                'icon' => 'fas fa-hand-holding-usd',
                                'url' => '#',
                                'items' => [

                                    [
                                        'label' => Yii::t('common','For General Store'),
                                        'icon' => 'fas fa-store-alt text-yellow',
                                        'url' => ['/Management/financial/cheque'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'Management/financial/cheque'
                                        ])
                                    ],
                                    [
                                        'label' => Yii::t('common','For Moderntrade'),
                                        'icon' => 'fab fa-fort-awesome text-aqua',
                                        'url' => ['/accounting/cheque/index-ajax'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/cheque/index-ajax',
                                            'accounting/cheque/index-ajax-detail',
                                        ])
                                    ],
                                ]
                                
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
                            ],

                            [
                                'label' => Yii::t('common','Payable'),
                                'icon' => 'fas fa-credit-card text-info',
                                'url' => ['/financial/payable/index'],
                                'active' => in_array(\Yii::$app->controller->Route,['financial/payable/index']),
                            ],
                                
                            [
                                'label' => Yii::t('common','Tax invoice report'),
                                'icon' => 'glyphicon glyphicon-blackboard',
                                'url' => '#',
                                'items' => [

                                                [
                                                    'label' => Yii::t('common','Sort by number'),
                                                    'icon' => 'fa fa-sort-numeric-asc',
                                                    'url' => ['/Management/financial/tax-invoice'],
                                                    'active' => in_array(\Yii::$app->controller->Route,['Management/financial/tax-invoice']),
                                                ],
                                                [
                                                    'label' => Yii::t('common','Sort by customer'),
                                                    'icon' => 'fa fa-sort-alpha-asc',
                                                    'url' => ['/Management/financial/sale-cash'],
                                                    'active' => in_array(\Yii::$app->controller->Route,['Management/financial/sale-cash']),
                                                ],
                                                [
                                                'label' => Yii::t('common','Posted Invoice No Detail'),
                                                'icon' => 'fa fa-sort-alpha-asc',
                                                'url' => ['/Management/financial/sale-cash-no-detail'],
                                                'active' => in_array(\Yii::$app->controller->Route,['Management/financial/sale-cash-no-detail']),
                                            ],
                                    ]

                            ],

                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Report Setting'), 'icon' => 'fa fa-user-md', 'url' => '#',],

                                ],
                            ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => ['/accounting/report/main'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'accounting/report/bank-list',
                                    'accounting/report/main',
                                    'accounting/cheque/index',
                                    'accounting/default/50tw'
                                ])
                                    // 'items' => [
                                    //     [
                                    //         'label' => Yii::t('common','Cash Receipt'),
                                    //         'icon' => 'fas fa-money-check-alt text-aqua',
                                    //         'url' => ['/accounting/report'],
                                    //         'active' => in_array(\Yii::$app->controller->Route,[
                                    //             'accounting/report/',
                                    //             'accounting/report/index',
                                    //             'accounting/report/update',
                                    //             'accounting/report/create',
                                    //             ]),
                                    //     ],

                                    //     [
                                    //         'label' => Yii::t('common','Cash Receipt Journals'),
                                    //         'icon' => 'fas fa-suitcase',
                                    //         'url' => ['/accounting/cheque'],
                                    //         'active' => in_array(\Yii::$app->controller->Route,[
                                    //             'accounting/cheque/',
                                    //             'accounting/cheque/index',
                                    //             'accounting/cheque/update',
                                    //             'accounting/cheque/create',
                                    //             ]),
                                    //     ],
                                        

                                    // ],
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
                                'icon' => 'fa fa-paypal text-yellow',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Vendor'),
                                        'icon' => 'far fa-id-card',
                                        'url' => ['/accounting/vendor/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/vendor/index',
                                            'accounting/vendor/create',
                                            'accounting/vendor/update',
                                            'accounting/vendor/view',
                                            ])
                                    ],
                                    [
                                        'label' => Yii::t('common','Payable Invoice'),
                                        'icon' => 'far fa-file-alt',
                                        'url' => ['/accounting/payable/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/payable/index',
                                            'accounting/payable/create',
                                            'accounting/payable/update',
                                            'accounting/payable/view',
                                            'accounting/payable/print',
                                            ])
                                    ],
                                    [
                                        'label' => Yii::t('common','Payment'),
                                        'icon' => 'far fa-credit-card text-green',
                                        'url' => ['/accounting/payment/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/payment/index',
                                            'accounting/payment/create',
                                            'accounting/payment/update',
                                            'accounting/payment/view',
                                            'accounting/payment/print',
                                            ])
                                    ],                                    
                                    // ['label' => Yii::t('common','Credit Note'), 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    // ['label' => Yii::t('common','Payment Journals'), 'icon' => 'fa fa-circle-o', 'url' => '#',],



                                ],

                            ],
                            [
                                'label' => Yii::t('common','Receivables'),
                                'icon' => 'fa fa-calculator text-aqua',
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

                                    [
                                        'label' => Yii::t('common','Invoice Cross Company'),
                                        'icon' => 'fa fa-file-text-o text-yellow',
                                        'url' => ['/SaleOrders/saleorder/index-list'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/saleorder/index-list',
                                        ]),
                                        
                                    ],

                                    [
                                        'label' => Yii::t('common','Credit Note'), 
                                        'icon' => 'fas fa-credit-card', 
                                        'url' => ['/accounting/credit-note/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'accounting/credit-note/index',
                                            'accounting/credit-note/view',
                                            'accounting/credit-note/update',
                                            'accounting/posted/view-credit'
                                            ]),
                                    ],
                                    
                                    [
                                        'label' => Yii::t('common','Report'),
                                        'icon' => 'fa fa-bar-chart',
                                        'url' => '#',
                                            'items' => [
                                                [
                                                    'label' => Yii::t('common','Report Invoice'),
                                                    'icon' => 'far fa-calendar-alt',
                                                    'url' => ['/accounting/rcreport/index'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/rcreport/index',
                                                        ]),
                                                ],
                                                [
                                                    'label' => Yii::t('common','Posted Invoice'),
                                                    'icon' => 'fa fa-book',
                                                    'url' => ['/accounting/posted/index'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/posted/index',
                                                        'accounting/posted/posted',                                                                                
                                                        'accounting/posted/posted-invoice'                               
                                                        ])
                                                ],
                                                [
                                                    'label' => Yii::t('common','Sales tax report'),
                                                    'icon' => 'fa fa-file-pdf-o',
                                                    'url' => ['/accounting/rcreport/tax-invoice'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/rcreport/tax-invoice',
                                                    ]),
                                                ],

                                                [
                                                    'label' => Yii::t('common','Invoice report'),
                                                    'icon' => 'fa fa-file-pdf-o',
                                                    'url' => ['/accounting/rcreport/invoice'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/rcreport/invoice',
                                                    ]),
                                                ],

                                                [
                                                    'label' => Yii::t('common','All Invoice report'),
                                                    'icon' => 'fa fa-file-pdf-o',
                                                    'url' => ['/accounting/rcreport/all-invoice'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/rcreport/all-invoice'
                                                    ]),
                                                ],


                                                [
                                                    'label' => Yii::t('common','Order List'),
                                                    'icon' => 'fa fa-file-pdf-o',
                                                    'url' => ['/accounting/rcreport/all-invoice-mobile'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/rcreport/all-invoice-mobile'
                                                    ]),
                                                ],


                                                [
                                                    'label' => Yii::t('common','Customer This Years'),
                                                    'icon' => 'far fa-address-card',
                                                    'url' => ['/accounting/report/customer-sale'],
                                                    'active' => in_array(\Yii::$app->controller->Route,[
                                                        'accounting/report/customer-sale',
                                                    ]),
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
                        'label' => Yii::t('common','Customer'),
                        //'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Main Menu','saleorder','Customer','Customer-Access')),
                        'icon' => 'fa fa-address-card-o',
                        'items' => [
                            [
                                'label' => Yii::t('common','Customers'),                                
                                'icon' => 'fa fa-address-card-o',
                                'url' => ['/customers/customer/readonly'],
                                
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'customers/customer/readonly'
                                ]),
                            ],   
                            [
                                'label' => Yii::t('common','Customers List'),                                
                                'icon' => 'fa fa-address-card-o',
                                'url' => ['/customers/customer/index'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'customers/customer/',
                                    'customers/customer/index',
                                    'customers/customer/update',
                                    'customers/customer/view',
                                    'customers/customer/create',
                                ]),
                            ],
                            [
                                'label' => Yii::t('common','Customer Group'),                                
                                'icon' => 'fa fa-address-card-o',
                                'url' => ['/customers/groups/index'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'customers/groups/',
                                    'customers/groups/index',
                                    'customers/groups/update',
                                    'customers/groups/view',
                                    'customers/groups/create',
                                    ]),
                            ],

                            [
                                'label' => Yii::t('common','Responsible'),                                
                                'icon' => 'fa fa-address-card-o',
                                'url' => ['/customers/responsible/index'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'customers/responsible/',
                                    'customers/responsible/index',
                                    'customers/responsible/update',
                                    'customers/responsible/view',
                                    'customers/responsible/create',
                                    ]),
                            ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart text-aqua',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Customer item sale'),                                
                                        'icon' => 'fa fa-cube',
                                        'url' => ['/customers/customer/item-sale'],                                        
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'customers/customer/item-sale'
                                        ]),
                                    ],   
                                    [
                                        'label' => Yii::t('common','Customer item sale Show cost'),                                
                                        'icon' => 'fa fa-cube text-red',
                                        'url' => ['/customers/customer/item-sale-cost'],                                        
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'customers/customer/item-sale-cost'
                                        ]),
                                    ],   
                                     
                                ]
                            ],
                        ]
                    ],


                    [
                        'label' => Yii::t('common','Sales & Marketing'),
                        'icon' => 'fas fa-chart-line',
                        'url' => '#',
                        'template'=>'<a href="{url}">{icon} {label}<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i>
                            <small class="label pull-right bg-red" id="alert-menu-marketing"></small></span>                            
                            </a>',
                        'labelCount' => 1,
                        'labelCountClass' => 'label label-warning ew-alert-saleorder',      
                        // 'options' => [
                        //                     'class' => '',
                        //                     'id' => 'ew-alert-salemain',
                        //                     'ew-url' => 'Url',
                        //                     ],
                        'items' => [
                            [
                                'label' => Yii::t('common','Sale Person'),

                                'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2 || Yii::$app->session->get('Rules')['rules_id']==4),
                                'icon' => 'fa fa-user-circle-o',
                                'url' => ['/salepeople/people'],
                                'active' => in_array(\Yii::$app->controller->id,['people','create','view'])
                            ],
                            // [
                            //     'label' => Yii::t('common','Customer'),
                            //     'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Main Menu','saleorder','Customer','Customer-Access')),
                            //     //'visible' => in_array(Yii::$app->session->get('Rules')['rules_id'],['1','3','4','7']),
                            //     'icon' => 'fa fa-address-card-o',
                            //     'url' => ['/customers/customer'],
                            //     'active' => in_array(\Yii::$app->controller->id,['customer','create','view'])
                            // ],
                            //['label' => 'Quotation', 'icon' => 'fa fa-quora', 'url' => ['#'],],
                            [
                                'label' => Yii::t('common','Sale Quotation'),
                                'icon' => 'fas fa-hand-holding-usd text-aqua',
                                'url' => ['/SaleOrders/quotation'],                              
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'SaleOrders/quotation/',
                                    'SaleOrders/quotation/index',
                                    'SaleOrders/quotation/update',
                                    'SaleOrders/quotation/view',
                                    'SaleOrders/quotation/create',
                                    ]),
                            ],
                            [
                                'label' => Yii::t('common','Sale Order'),
                                'icon' => 'fa fa-usd text-yellow',
                                'url' => ['/SaleOrders/saleorder', 'uid'=> \Yii::$app->user->identity->id],
                                'labelCount' => 1,
                                'labelCountClass' => 'label label-warning ew-alert-saleorder',      
                                // 'options' => [
                                //             'class' => '',
                                //             'id' => 'ew-alert-saleorder',
                                //             'ew-url' => 'Url',
                                //             ],
                                'active' => in_array(\Yii::$app->controller->id,['saleorder'])

                            ],
                            [
                                'label' => Yii::t('common','Department store'),
                                'icon' => 'fab fa-fort-awesome text-green',
                                'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','saleorder','actionCreate','modern-trade')),
                                'items' => [   
                                    [
                                        'label' => Yii::t('common','Reserve'),
                                        'icon' => 'fab fa-creative-commons-share text-green',
                                        'url' => ['/SaleOrders/reserve/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/reserve/create',
                                            'SaleOrders/reserve/index',
                                            'SaleOrders/reserve/order'
                                        ])
                                    ],                                 
                                    [
                                        'label' => Yii::t('common','Orders'),
                                        'icon' => 'fab fa-creative-commons-share text-aqua',
                                        'url' => ['/SaleOrders/wizard/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/wizard/create',
                                            'SaleOrders/wizard/index',
                                            'SaleOrders/wizard/order'
                                        ])
                                    ],
                                    [
                                        'label' => Yii::t('common','Backwards Order'),
                                        'icon' => 'fas fa-history text-orange',
                                        'url' => ['/SaleOrders/backwards/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/backwards/index',
                                            'SaleOrders/backwards/create'
                                        ])
                                    ],
                                    
                                ]
                                
                            ],

                            [
                                'label' => Yii::t('common','Sale Return'),
                                'icon' => 'fa fa-random text-red',
                                'url' => ['/SaleOrders/return'],
                                'labelCount' => 1,
                                'labelCountClass' => 'label label-warning ew-alert-saleorder',                                     
                                'active' => in_array(\Yii::$app->controller->id,['return'])
                            ],

                            [
                                'label' => Yii::t('common','POS'),
                                'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','order','common','event')),
                                'icon' => 'fas fa-qrcode',
                                'url' => ['/SaleOrders/event'],   
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'SaleOrders/event/',
                                    'SaleOrders/event/index',
                                    'SaleOrders/event/update',
                                    'SaleOrders/event/create',
                                    'SaleOrders/event/sale-line',
                                    'SaleOrders/event/barcode-print',
                                    ]),
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
                                        'label' => Yii::t('common','Daily Report'),
                                        'icon' => 'fa fa-file-pdf-o text-red',
                                        'url' => ['/SaleOrders/report/daily'],
                                    ],
                                    
                                    [
                                        'label' => Yii::t('common','Sales Report'),
                                        'icon' => 'fa fa-file-pdf-o text-red',
                                        'url' => ['/SaleOrders/report/report-daily'],
                                    ],

                                    [
                                        'label' => Yii::t('common','Posted Invoice No Detail'),
                                        'icon' => 'fa fa-sort-alpha-asc text-yellow',
                                        'url' => ['/SaleOrders/report/sale-cash-no-detail'],
                                        'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/report/sale-cash-no-detail']),
                                    ],

                                    [
                                        'label' => Yii::t('common','Best Sale'),
                                        'icon' => 'fa fa-truck text-green',
                                        'url' => ['/SaleOrders/report/best-sale'],
                                        'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/report/best-sale']),
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

                                    [
                                        'label' => Yii::t('common','Product Report'),
                                        'icon' => 'fa fa-list-ol',
                                        'url' => ['/items/report/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,['items/report/index']),
                                        'visible' => (Yii::$app->session->get('Rules')['rules_id']===1),
                                    ],

                                    [
                                        'label' => Yii::t('common','Sale Order Tracking'),
                                        'icon' => 'fa fa-code text-aqua',
                                        'url' => ['/SaleOrders/report/order-tracking'],
                                        'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/report/order-tracking']),
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
                                        'url' => ['/SaleOrders/promotions/index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/promotions/index',
                                            'SaleOrders/promotions/create',
                                            'SaleOrders/promotions/update',
                                            'SaleOrders/promotions/view',
                                            'SaleOrders/promotions-item-group/index',
                                            'SaleOrders/promotions-item-group/create',
                                            'SaleOrders/promotions-item-group/update',
                                            'SaleOrders/promotions-item-group/view',
                                            ])


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
                        'label' => Yii::t('common','Planing'),
                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Management','report','common','menu')),
                        'icon' => 'fa fa-universal-access', 
                        'labelCount' => 1,
                        'labelCountClass' => 'label label-danger ew-data-approve', 
                        'url' => '#',
                        'items' => [
                                
                                [
                                    'label' => Yii::t('common','Main'),
                                    'icon' => 'fa fa-credit-card',
                                    'url' => ['/Planning/default'],
                                    'labelCount' => 1,
                                    'labelCountClass' => 'label label-danger ew-data-approve', 
                                    'active' => in_array(\Yii::$app->controller->Route,[
                                        'Planning/default/index'
                                    ]),
                                ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Safety Stock'),
                                        'icon'  => 'fa fa-credit-card',
                                        'url'   => ['/Planning/planning/safety-stock'],
                                        'active'=> in_array(\Yii::$app->controller->Route,[
                                            'Planning/planning/safety-stock'
                                        ]),
                                    ],
                                    [
                                        'label' => Yii::t('common','Stock By Customer'),
                                        'icon'  => 'fa fa-credit-card',
                                        'url'   => ['/Planning/planning/stock-invoice-by-customer'],
                                        'active'=> in_array(\Yii::$app->controller->Route,[
                                            'Planning/planning/stock-invoice-by-customer'
                                        ]),
                                    ]
                                ],
                            ],



                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Planning'), 
                                        'icon'  => 'fa fa-user-md', 
                                        'url'   => '#'
                                    ],
                                    [
                                        'label' => Yii::t('common','Safety Stock'),
                                        'icon'  => 'fa fa-credit-card',
                                        'url'   => ['/Planning/planning/index'],
                                        'active'=> in_array(\Yii::$app->controller->Route,[
                                            'Planning/planning/index'
                                        ]),
                                    ]
                                ],
                            ],
                        ],
                    ],

                    [
                        'label' => Yii::t('common','Purchase'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2),
                        'icon' => 'fas fa-shopping-cart',
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
                                  'vendors/vendors/update',
                                ]),
                            ],
                            [
                                'label' => Yii::t('common','Purchase Request'),
                                'icon' => 'fas fa-bullhorn text-aqua',
                                'url' => ['/Purchase/req'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                            'Purchase/req/view',
                                            'Purchase/req/index',
                                            'Purchase/req/create',
                                            'Purchase/req/update',
                                            'Purchase/req/receive',
                                            ]),

                            ],
                            [
                                'label' => Yii::t('common','Purchase Order'),
                                'icon' => 'fas fa-shopping-cart text-yellow',
                                'url' => ['/Purchase/order/indexs'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                            'Purchase/default/index',
                                            'Purchase/order/view',
                                            'Purchase/order/index',
                                            'Purchase/order/create',
                                            'Purchase/order/update',
                                            'Purchase/order/receive',  

                                            'Purchase/purchase-line/view',
                                            'Purchase/purchase-line/index',
                                            'Purchase/purchase-line/create',
                                            'Purchase/purchase-line/update',
                                            'Purchase/purchase-line/receive'                                          
                                            ]),
                                            

                            ],
                             
                            [
                                'label' => Yii::t('common','Project Control'),
                                'icon' => 'fas fa-tasks text-green',
                                'url' => ['/Purchase/project/index',],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                            'Purchase/project/index',
                                            'Purchase/project/create',
                                            'Purchase/project/view',
                                            'Purchase/project/update',
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
                                      'url' => ['/Purchase/order/print-editable-index'],
                                      'active' => in_array(\Yii::$app->controller->Route,[
                                                  'Purchase/order/print-options',
                                                  'Purchase/order/print-editable',
                                                  'Purchase/order/print-editable-index',
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
                        'label' => Yii::t('common','Items'),
                        'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Items','report','common','menu')),
                        'icon' => 'fas fa-archive',
                        'url' => '#',
                        'items' => [

                            [
                                'label' => Yii::t('common','Barcode'),                                            
                                'icon' => 'fa fa-barcode text-aqua',                               
                                'url' => ['/items/items/barcode-print'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/items/barcode-print'
                                    ]
                                )
                            ],

                            
                            [
                                'label' => Yii::t('common','Stock'),
                                'icon' => 'far fa-list-alt text-orange',
                                'url' => ['/items/stock/index'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/stock/index']
                                )
                            ],
                            [
                                'label' => Yii::t('common','Consumption'),
                                'icon' => 'fa fa-cubes',                                
                                'url' => ['/items/stock/index-monthly'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/stock/index-monthly']
                                )
                            ],
                            [
                                'label' => Yii::t('common','Inventory evaluate'),
                                'icon' => 'fa fa-cubes',                                
                                'url' => ['/items/stock/monthly'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/stock/monthly']
                                )
                            ],
                            [
                                'label' => Yii::t('common','Stock by invoice'),
                                'icon' => 'fa fa-cubes text-red',     
                                'visible' => in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','Items','report','actionIndex','menu-planning')),                           
                                'url' => ['/items/stock/stock-by-invoice'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/stock/stock-by-invoice']
                                )
                            ],
                            //
                            [
                                'label' => Yii::t('common','Items'),                                            
                                'icon' => 'fa fa-th text-aqua',        
                                'url' => ['/items/items/indexs'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/items/index',
                                    'items/items/read-only'
                                    ]
                                )
                            ],

                           

                            [
                                'label' => Yii::t('common','Items List'),                                            
                                'icon' => 'fa fa-th text-yellow',   
                                'url' => ['/items/items/list'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'items/items/list'
                                    ]
                                )
                            ],

                            [
                                'label' => Yii::t('common','Items FG'),
                                'icon' => 'fas fa-box-open text-green',
                                'url' => ['/warehousemoving/item/finish-goods'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/item/finish-goods']
                                )
                            ],
                            [
                                'label' => Yii::t('common','Item RM'),
                                'icon' => 'fas fa-boxes text-black',
                                'url' => ['/warehousemoving/item'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/item/index',
                                    'warehousemoving/item/create',
                                    'warehousemoving/item/update',
                                    'warehousemoving/item/view']
                                )
                            ],
                            
                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [                                    
                                    [
                                        'label' => Yii::t('common','Item Group'),
                                        'icon' => 'fa fa-sitemap text-aqua',
                                        'url' => ['/itemgroup/itemgroup'],
                                        'active' => in_array(\Yii::$app->controller->id,['itemgroup']),
                                    ], 
                                    [
                                        'label' => Yii::t('common','Unit Of Measure'),
                                        'icon' => 'fa fa-file-text-o text-red',
                                        'url' => ['/measure/measure'],
                                        'active' => in_array(\Yii::$app->controller->id,['measure']),
                                    ],
                                                                                                         
                                    [
                                        'label' => Yii::t('common','Item Set'),
                                        'icon' => 'fab fa-buromobelexperte',
                                        'url' => ['/Itemset/itemset'],
                                        'active' => in_array(\Yii::$app->controller->id,['itemset']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Property'), 
                                        'icon' => 'far fa-list-alt', 
                                        'url' => ['/property/property'],
                                        'active' => in_array(\Yii::$app->controller->id,['property']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Item Category'),
                                        'icon' => 'fas fa-dna',
                                        'url' => ['/itemcategory/category'],
                                        'active' => in_array(\Yii::$app->controller->id,['category']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Production BOM'),
                                        'icon' => 'fa fa-link text-aqua',
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


                                            [
                                                'label' => Yii::t('common','Item Craft'),
                                                'icon' => 'fa fa-file-text-o',
                                                'url' => '#',
                                                'items' => [
                                                            [
                                                                'label' => Yii::t('common','Item List'),
                                                                'icon' => 'fa fa-file-text-o',
                                                                'url' => ['/items/item-craft'],
                                                                'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'items/item-craft/index'
                                                                    ]
                                                                )
                                                            ],
                                                            
                                                    ],
                                            ],
                                        ],
                                    ],
                                     
                                ],
                            ],
                        ]
                    ],


                    [
                        'label' => Yii::t('common','Warehouse'),
                        'visible' => in_array(Yii::$app->session->get('Rules')['rules_id'],$PoliWh),
                        'icon' => 'fas fa-warehouse',
                        'url' => '#',
                        'items' => [                          
                            [
                                'label' => Yii::t('common','Item Journal'),
                                'icon' => 'fab fa-leanpub text-aqua',
                                'url' => '#',
                                'items' => [

                                    [
                                        'label' => Yii::t('common','Sale Return'),
                                        'icon' => 'fa fa-random text-red',
                                        'encodeLabels' => false,
                                        'url' => ['/SaleOrders/return'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/return/index',
                                            'SaleOrders/return/create',
                                            'SaleOrders/return/update',                                           
                                            'SaleOrders/return/view'])
                                    ],

                                    // [
                                    //     'label' => Yii::t('common','Item Journal'),
                                    //     'icon' => 'fas fa-dolly-flatbed text-red',
                                    //     'encodeLabels' => false,
                                    //     'url' => ['/warehousemoving/journal'],
                                    //     'active' => in_array(\Yii::$app->controller->Route,[
                                    //         'warehousemoving/journal/index',
                                    //         'warehousemoving/journal/create',
                                    //         'warehousemoving/journal/update',
                                    //         'warehousemoving/journal/journal-line-list',
                                    //         'warehousemoving/journal/view'])
                                    // ],

                                    // [
                                    //     'label' => Yii::t('common','Item Reclass Journal'),
                                    //     'icon' => 'fas fa-people-carry text-yellow',
                                    //     'encodeLabels' => false,
                                    //     'url' => ['/warehousemoving/reclass-journal'],
                                    //     'active' => in_array(\Yii::$app->controller->Route,[
                                    //         'warehousemoving/reclass-journal/index',
                                    //         'warehousemoving/reclass-journal/create',
                                    //         'warehousemoving/reclass-journal/update',
                                    //         'warehousemoving/reclass-journal/journal-line-list',
                                    //         'warehousemoving/reclass-journal/view'])
                                    // ],
                                ]
                            ],

                            [
                                'label' => Yii::t('common','Item Adjust'),
                                'icon' => 'fa fa-balance-scale text-red',
                                'encodeLabels' => false,
                                'url' => '#',
                                //'url' => ['/warehousemoving/adjust'],
                                // 'active' => in_array(\Yii::$app->controller->Route,[
                                //     'warehousemoving/adjust/index',
                                //     'warehousemoving/adjust/create',
                                //     'warehousemoving/adjust/update',
                                //     'warehousemoving/adjust/view']),

                                'items' => [

                                    [
                                        'label' => Yii::t('common','Stock Adjust'),
                                        'icon' => 'fas fa-dolly-flatbed text-aqua',
                                        'encodeLabels' => false,
                                        'url' => ['/warehousemoving/stock'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/stock/index'
                                        ]),
                                    ],

                                    [
                                        'label' => Yii::t('common','Stock Report'),
                                        'icon' => 'fas fa-bar-chart text-success',
                                        'encodeLabels' => false,
                                        'url' => ['/warehousemoving/stock-report'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/stock-report/index',
                                        ]),
                                    ],
                                    [
                                        'label' => Yii::t('common','Group Setting'),
                                        'icon' => 'fas fa-th-large text-warning',
                                        'encodeLabels' => false,
                                        'url' => ['/warehousemoving/stock-report/setting'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/stock-report/setting',
                                            'warehousemoving/stock-report/add-item'
                                        ]),
                                    ],

                                    // [
                                    //     'label' => Yii::t('common','Item Adjust'),
                                    //     'icon' => 'fas fa-dolly-flatbed text-red',
                                    //     'encodeLabels' => false,
                                    //     'url' => ['/warehousemoving/adjust'],
                                    //     'active' => in_array(\Yii::$app->controller->Route,[
                                    //         'warehousemoving/adjust/index',
                                    //         'warehousemoving/adjust/create',
                                    //         'warehousemoving/adjust/update',
                                    //         'warehousemoving/adjust/view']),
                                    // ],
                                ]
                            ],

                            [
                                'label' => Yii::t('common','Product Receive'),
                                'icon' => 'fas fa-hands',
                                'url' => ['/warehousemoving/receive/index'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/receive/index'   
                                ])                            
                            ],
                            [
                                'label' => Yii::t('common','Delivery Confirm'),
                                'icon' => 'fa fa-truck text-info',
                                'url' => ['/warehousemoving'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'warehousemoving/default/index',
                                    'warehousemoving/default/view',
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
                                        'url' => ['/warehousemoving/inventory'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/inventory/index',
                                        ])
                                    ],
                                    [
                                        'label' => Yii::t('common','Movement'),
                                        'icon' => 'fa fa-file-pdf-o',
                                        'url' => ['/warehousemoving/warehouse'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/warehouse/index',
                                            'warehousemoving/warehouse/',
                                            'warehousemoving/warehouse/shipment',
                                            'warehousemoving/warehouse/shipline',
                                            'warehousemoving/warehouse/index-filter'
                                            ])
                                    ],
                                   
                                    ['label' => Yii::t('common','สินค้าคงคลัง'), 'icon' => 'fa fa-file-pdf-o', 'url' => '#',],
                                    //['label' => Yii::t('common','รายงานการรับ สินค้า'), 'icon' => 'fa fa-file-pdf-o', 'url' => '#',],
                                    [
                                        'label' => Yii::t('common','RECEIVE/SHIPMENT'),
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
                                        'label' => Yii::t('common','Locations'),
                                        'icon' => 'fa fa-th-large',
                                        'url' => ['/location/location'],
                                        'active' => in_array(\Yii::$app->controller->id,['location'])
                                    ],

                                ],
                            ],
                        ],
                    ],                   

                    [
                        'label' => Yii::t('common','Production'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2 ),
                        'icon' => 'fa fa-industry',
                        'url' => '#',
                        'items' => [                           

                            [
                                'label' => Yii::t('common','Execution'),
                                'icon' => 'fa fa-cubes',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('common','Prod. Order'), 'icon' => 'fa fa-tasks', 'url' => ['/Manufacturing/production/index']],
                                    ['label' => Yii::t('common','Output'), 'icon' => 'fa fa-archive',  'url' => ['/Manufacturing/production/output']],
                                    ['label' => Yii::t('common','Consumption'), 'icon' => 'fa fa-free-code-camp', 'url' => '#',],
                                ],
                            ],

                            [
                                'label' => Yii::t('common','Report'),
                                'icon' => 'fa fa-bar-chart',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Prod. Report'), 
                                        'icon' => 'fa fa-file-pdf-o', 
                                        'url' => '#',
                                    ],
                                    

                                ],
                            ],

                            [
                                'label' => Yii::t('common','Setup'),
                                'icon' => 'fa fa-cog',
                                'url' => '#',
                                'items' => [
                                     
                                    
                                ],
                            ],
                        ],
                    ],



                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],                   
                    [
                        'label' => Yii::t('common','Setup'),
                        'icon' => 'fa fa-cogs',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('common','Company'),
                                'icon' => 'fa fa-building-o',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Company Info'),
                                        'icon' => 'fas fa-home',
                                        'url' => ['/company/company'],
                                        'active' => in_array(\Yii::$app->controller->id,['company']),
                                    ],
                                    [
                                        'label' => Yii::t('common','Department'),
                                        'icon' => 'fa fa-sitemap',
                                        'url' => ['/apps_rules/setup'],
                                        'active' => in_array(\Yii::$app->controller->id,['setup']),
                                    ],
    
                                    [
                                        'label' => Yii::t('common','Employee'),
                                        'icon' => 'fas fa-users',
                                        'url' => ['/apps_rules/rules'],
                                        'active' => in_array(\Yii::$app->controller->id,['rules']),
                                    ],
    
                                ],    
                            ],
                            [
                                'label' => Yii::t('common','Application'),
                                'icon' => 'fa fa-android',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Printer'),
                                        'icon' => 'fa fa-print text-aqua',
                                        'url' => ['/setting/printer-index'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'setting/index',
                                            'setting/printer-editable',
                                            'setting/printer-index',
                                            ]),
            
                                            
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
                                ]
                            ],
                            
                            

                        ],
                    ],
                    [
                        'label' => Yii::t('common','Administrator'),
                        'visible' => (Yii::$app->session->get('Rules')['rules_id']==1 || Yii::$app->session->get('Rules')['rules_id']==2 ),
                        'icon' => 'fa fa-desktop',
                        'url' => '#',
                        'items' => [                            
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
                                                        'label' => Yii::t('common','Language'),
                                                        'icon' => 'fa fa-flag text-aqua',
                                                        'url' => ['/language'],
                                                        'active' => in_array(\Yii::$app->controller->Route,[
                                                                    'language/index',
                                                                    'language/create',
                                                                    'language/update',
                                                                    'language/view'])
                                                    ],
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



                                    //['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
                                    //['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
                                    [
                                        'label' => 'Configuration', 
                                        'icon' => 'fa fa-cog', 
                                        'url' => ['/config'],
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'install/default/index',
                                            'config/default/index',
                                            'Management/report/inv-fixed',
                                            'express/default/index'
                                        ])
                                    ],
                                    
                                    ['label' => 'Admin', 'icon' => 'fa fa-dashboard', 'url' => ['/admin'],],
                                    // [
                                    //     'label' => 'phpMyAdmin',
                                    //     'icon' => 'fa fa-dashboard',
                                    //     'url' => '/phpmyadmin',
                                    //     'template'=> '<a href="{url}" target="_blank">{icon}{label}</a>',
                                    // ],

                                ],
                            ],
                            [
                                'label' => Yii::t('common','Users'),
                                'icon' => 'fas fa-users',
                                'url' => ['/users/users/index'],
                                'active' => in_array(\Yii::$app->controller->Route,[
                                    'users/users/index',
                                    'users/users/create',
                                    'users/users/update',
                                    'users/users/view',
                                   ])
                            ],

                        ],
                    ],


                    
                ],
                

            ]
        ) ?>