
<?php
        
        
        use common\models\SetupSysMenu;
        use admin\modules\apps_rules\models\SysRuleModels;
        
        
        $Policy = SetupSysMenu::findOne(3);
        //$PoliWh = explode(',',$Policy->rules_id);
        //$PoliWh = SysRuleModels::getPolicy('Data Access','warehousemoving','report','common','menu');
        
        $myRule = \Yii::$app->session->get('Rules');
        
        dmstr\widgets\Menu::$iconClassPrefix = ''; 
        
        ?>
                
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                        'items' => [
                            ['label' => Yii::t('common','POS'), 'options' => ['class' => 'header']],
                            [
                                'label' => Yii::t('common','POS-Home'),
                                'icon' => 'fas fa-home',
                                'url' => ['/SaleOrders/event/index'],
                                'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/event/index']),
                            ],
                            [
                                'label' => Yii::t('common','POS-Sale'),
                                'icon' => 'fas fa-cart-plus',
                                'url' => ['/SaleOrders/event/create'],
                                'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/event/create']),
                            ],
                            [
                                'label' => Yii::t('common','POS-History'),
                                'icon' => 'fas fa-history',
                                'url' => ['/SaleOrders/event/sale-line'],
                                'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/event/sale-line']),
                            ],
                            [
                                'label' => Yii::t('common','Product'),
                                'icon' => 'fas fa-cubes',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('common','Product'),
                                        'icon' => 'fa fa-cube',
                                        'url' => ['/SaleOrders/event/items'],                                        
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'SaleOrders/event/items',
                                            'items/items/index',
                                            'items/items/create',
                                            'items/items/view',
                                            'items/items/update',
                                            ]),
                                    ],
                                    [
                                        'label' => Yii::t('common','Adjust Stock'),
                                        'icon' => 'fa fa-cubes',
                                        'url' => ['/warehousemoving/adjust/index'],                                        
                                        'active' => in_array(\Yii::$app->controller->Route,[
                                            'warehousemoving/adjust/index',
                                            'warehousemoving/adjust/create',
                                            'warehousemoving/adjust/update'
                                            ]),
                                    ],
                                    [
                                        'label' => Yii::t('common','Barcode Print'),
                                        'icon' => 'fa fa-barcode',
                                        'url' => ['/SaleOrders/event/barcode-print-all'],                                        
                                        'active' => in_array(\Yii::$app->controller->Route,['SaleOrders/event/barcode-print-all']),
                                    ],
                                ]
                                 
                            ],
                            
        
                             
                        ],
        
                         
                    ]
                ) ?>