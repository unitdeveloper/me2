<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
 
//use yii\grid\GridView;
//use yii\widgets\Pjax;
use common\models\Province;
use yii\helpers\ArrayHelper;

use common\models\SalesPeople;

use kartik\export\ExportMenu;
use kartik\widgets\SwitchInput;
use admin\modules\apps_rules\models\SysRuleModels;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\customers\models\SearchCustomer */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Customers');
$this->params['breadcrumbs'][] = $this->title;

 
// use kartik\cmenu\ContextMenu;
// $items = [
//     ['label'=>'Action', 'url'=>'#'],
//     ['label'=>'Another action', 'url'=>'#'],
//     ['label'=>'Something else here', 'url'=>'#'],
//     '<li class="divider"></li>',
//     ['label'=>'Separated link', 'url'=>'#'],
// ];

// ContextMenu::begin(['items'=>$items]);
// echo '<span class="kv-context">Right click here.</span>';
// ContextMenu::end();

 


        $gridColumns = [
            

                //['class' => 'yii\grid\SerialColumn'],
                //['class' => '\kartik\grid\RadioColumn'], 
 
                [
                    'attribute' => 'code',
                    'headerOptions' => ['class' => ' ','style' => 'width:100px;'],
                    'contentOptions' => ['style' => 'font-family: saraban, roboto;'],
                    'format' => 'raw',
                    'value' => function ($model) { 
                        $html = Html::a($model->code, ['view', 'id' => $model->id]);
                        $html.= $model->child 
                                    ? '<div class="text-gray text-right"><small>Parent : '.(
                                        $model->childOff != null 
                                            ? $model->childOff->code 
                                            : ''
                                        ).' </small></div>' 
                                    : '';

                        return $html;
                    },
                ],
                
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) { 
    
                        $html = '<div>'.Html::a($model->name, ['view', 'id' => $model->id],['style' => 'font-size: medium;']).'</div>';
                        $html.= '<small>'.$model->address.'</small>';
                        $html.= '<div style="margin-top:15px;">'.($model->contact? '<i class="far fa-user-circle"></i> '.$model->contact : null).'</div>';                        
                        $model->phone = str_replace(" ",",",$model->phone);
                        $mobiles = explode(",",$model->phone);
                        foreach ($mobiles as $mobile) {
                            if($mobile)
                            $html.= '<div><i class="fas fa-mobile-alt"></i> '.($mobile ? $mobile : '--').'</div>';
                        }                        
                        return $html;
                    },
                ],
                //'province.zone.name',
                
                [
                    'attribute' => 'province', 
                    'label' => Yii::t('common','Province'),
                    'value' => function($model){
                        $Province = '';
                        if($model->province!='') $Province = $model->provincetb->PROVINCE_NAME;
                        return $Province;
    
                    }
                ],
                [
                    'attribute' => 'region', 
                    //'label' => Yii::t('common','ภาค'),
                    'value' => 'provincetb.zone.name',
                    'filter'=>ArrayHelper::map(\common\models\Zone::find()->asArray()->all(), 'id', 'name'),
                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('common','Show All')],
                ],
                //'owner_sales',
    
                [
                    'attribute' => 'owner_sales',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'max-width:150px; overflow-x:auto;'],
                    'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                    'value' => function ($model) { 
                        
                        if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->count()>0)
                        {
                            $sales = SalesPeople::find()
                            ->where(['code' => explode(',',$model->owner_sales)])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->all();
                            $salpeople = '';
                            foreach ($sales as $people) {
                                $salpeople.= '<span>['.$people->code.'] '.$people->name.'</span> <br />'."\r\n"; 
                            }
                            return $salpeople;
    
                        }else {
                            return '-';
                        }
                    },
                    'filter' => ArrayHelper::map(\common\models\SalesPeople::find()
                                                    ->where(['status'=> 1])
                                                    ->andWhere(['comp_id' => \Yii::$app->session->get('Rules')['comp_id']])
                                                    ->orderBy(['code' => SORT_ASC])
                                                    ->all(), 
                                                    'code',
                                                    function($model){ return '['.$model->code.'] '.$model->name. ' '.$model->surname; }
                    ),
                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('common','Every one')],
                ],
                // [
                //     'attribute' => 'phone',
                //     'contentOptions' => ['style' => 'max-width:100px; overflow-x:auto;'],
                //     'value' => 'phone'
                // ],
                
                //'credit_limit',
                // [
                //     'attribute' => 'credit_limit',
                //     'format' => 'raw',
                //     'contentOptions' => ['class' => 'text-right'],
                //     'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                //     'value' => function ($model) { 
                          
                //          return number_format($model->credit_limit);
                //     },
                // ],
    
                // [
                //     //'attribute' => 'balance',
                //     'label' => Yii::t('common','Balance'),
                //     'format' => 'raw',
                //     'headerOptions' => ['class' => 'no-print text-right','style' => 'min-width:100px;'],
                //     'filterOptions' => ['class' => 'no-print'],
                //     'contentOptions' => ['class' => 'no-print text-right'],
                //     'footerOptions' => ['class' => 'no-print'],
                //     'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                //     'value' => function ($model) { 
                //          return number_format($model->balance);
                //     },
                // ],
    

                // [
                //     'attribute' => 'suspend',
                //     'label' => Yii::t('common','Suspending'),
                //     'format' => 'raw',
                //     'headerOptions' => ['class' => 'no-print','style' => 'min-width:100px;'],
                //     'filterOptions' => ['class' => 'no-print'],
                //     'contentOptions' => ['class' => 'text-center no-print'],
                //     'footerOptions' => ['class' => 'no-print'],
                //     'value' => function($model){
                //         $html = '<i class="fas fa-circle text-green"></i>';
                //         if ($model->suspend==1){
                //             $html = '<i class="fas fa-circle text-red"></i>';
                //         }
                //         return $html;
                //     },
                //     'filter' => Html::activeDropDownList($searchModel,'suspend',
                //     [
                //         '0' => Yii::t('common','Enable'),
                //         '1' => Yii::t('common','Suspend'),
                //     ],
                //     [                        
                //         'class' => 'form-control hidden-xs',
                //         'prompt' => Yii::t('common','All'),
                //     ]),
                // ],
    
                [
                    'attribute' => 'status',
                    'headerOptions' => ['class' => 'no-print'],
                    'filterOptions' => ['class' => 'no-print'],
                    'contentOptions' => ['class' => 'no-print'],
                    'footerOptions' => ['class' => 'no-print'],
                    'format' => 'raw',
                    'value' => function($model){
                        $data = SwitchInput::widget([
                            'name'          => 'status',
                            'value'         => $model->status,
                            'pluginOptions' => [
                                'onColor'   => 'success',
                                'offColor'  => 'danger',
                                'size'      => 'mini',
                                'onText'    => 'Show',
                                'offText'   => 'hide'
                             
                            ]
                        ]);
                        return $data;
                    },
                    'filter' => Html::activeDropDownList($searchModel,'status',
                    [
                        '0' => Yii::t('common','Hide'),
                        '1' => Yii::t('common','Show'),
                    ],
                    [                        
                        'class' => 'form-control hidden-xs',
                        'prompt' => Yii::t('common','Show All'),
                    ]),
                ],

            
            
    
            
        ];

?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<style>
    .pagination {
        margin: 0px;
    }
    .form-group {
        margin-bottom: 0px;
    }
    @media print {
        .no-print, .no-print *{
            display: none !important;
        }
    }
</style>

<div class="customer-index" ng-init="Title='<?=$this->title;?>'">
    <div class="panel-heading" style="position: relative; height:55px;">
        <?php  if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){ ?>
            <div class="row"><?= Html::a('<i class="fa fa-random"></i> '.Yii::t("common","Move Customer"),['/customers/move/index'],['class' => 'btn btn-info btn-flat'])?></div>
        <?php } ?>

        <div style="position: absolute;right:0px; top:10px;">
        <?php
            echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                            'columns' => [
                                
                                [
                                    'attribute' => 'code', 
                                    'label' => Yii::t('common','Code'),
                                    'format' => 'raw',
                                    'value' => function($model){
                                        return $model->code? $model->code : '';
                                    }
                                ],
                                //'name',
                                [
                                    'attribute' => 'name', 
                                    'label' => Yii::t('common','Company'),
                                    'format' => 'raw',
                                    'value' => function($model){
                                        return $model->name? $model->name : '';
                                    }
                                ],
                                [
                                    'label' => Yii::t('common','Address'),
                                    'format' => 'raw',
                                    'value' => function($model){
                                        return $model->getAddress()['address'];
                                    }
                                ],
                                'phone',
                                [
                                    'attribute' => 'province', 
                                    'label' => Yii::t('common','Province'),
                                    'format' => 'raw',
                                    'value' => function($model){
                                        $Province = '';
                                        if($model->province!='') $Province = $model->provincetb->PROVINCE_NAME;
                                        return $Province;
                    
                                    }
                                ],
                                //'owner_sales',
                                [
                                    'attribute' => 'region', 
                                    'value' => 'provincetb.zone.name'                              
                                ],
                                [
                                    'attribute' => 'owner_sales',
                                    'format' => 'raw',
                                    'value' => function ($model) { 
                                        
                                        if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->count()>0)
                                        {
                                            $sales = SalesPeople::find()
                                            ->where(['code' => explode(',',$model->owner_sales)])
                                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            ->all();
                                            $salpeople = '';
                                            foreach ($sales as $people) {
                                                $salpeople.= '['.$people->code.'] '.$people->name."\r\n"; 
                                            }
                                            return $salpeople;
                    
                                        }else {
                                            return '-';
                                        }
                                    },
                                ],
                                
                                
                    
                                
                        ],
                        'columnSelectorOptions'=>[
                            'label' => Yii::t('common','Columns'),
                            'class' => 'btn btn-success-ew'
                        ],
                        'fontAwesome' => true,
                        'dropdownOptions' => [
                            'label' => Yii::t('common','Export All'),
                            'class' => 'btn btn-primary-ew'
                        ],
                        'exportConfig' => [
                            ExportMenu::FORMAT_HTML => false,
                        ],
                        'styleOptions' => [
                            ExportMenu::FORMAT_PDF => [
                                'font' => [
                                    'family' => ['garuda'],
                                        //'bold' => true,
                                        'color' => [
                                            'argb' => 'FFFFFFFF',
                                    ],
                                ],
                            ],
                        ],
                        'filename' => Yii::t('common','Customer'),
                        //'encoding' => 'utf8',
                    ]);
        ?>
        </div>
    </div>
  <?php
$layout = <<< HTML
<div class="pull-right">
    {summary}
</div>
{custom}
<div class="clearfix"></div>
{items}
<div class="row">
    <div class="pull-right">
        <div class="col-xs-12">
        {pager}
        </div>
    </div>
</div>
HTML;
?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'options' => ['class' => 'table-responsive'],
         
        'tableOptions' => ['class' => 'table  table-bordered', 'style' => 'font-family: saraban, roboto;'],
        'responsiveWrap' => false,
        'pjax'=>false,
        // 'rowOptions'=>function($model){
        //                     //if($model->genbus_postinggroup == '01') return ['class' => 'success'];
        //                     if($model->genbus_postinggroup == '02'){
        //                         return ['class' => 'info viewCustomer'];
        //                     }else {
        //                         return ['class' => 'viewCustomer'];
        //                     }
        //             },
        'columns' => $gridColumns,
        'resizableColumns'=>true,
        'layout' => $layout,
        'replaceTags' => [
            '{custom}' => function($widget) {
                // you could call other widgets/custom code here
                if ($widget->panel === false) {
                    return '';
                } else {
                    return '';
                }
            }
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],

    ]); ?>


</div>

 
 <?php 
$js=<<<JS


  $('input[name="status"]').on('switchChange.bootstrapSwitch', function (event, state) {
    let id      = $(this).closest('tr').attr('data-key');
    let value   = state;
    $.ajax({
        url:'index.php?r=customers/ajax/update-status&id='+id,
        type:'POST',
        data:{param:{id:id,val:value}},
        dataType:'JSON',
        success:function(response){

            if(response.status===200){
                $.notify({
                    // options
                    icon: 'far fa-save',
                    message: 'Saved',                         
                },{
                    // settings
                    type: 'success',
                    delay: 1500,
                    z_index:3000,
                    placement: {
                        from: "top",
                        align: "center"
                    }
                });
            }
            console.log(response);
        }
    })
  });
   
JS;
$this->registerJs($js,\yii\web\View::POS_END,'JS');
?>

<?php
/*
$this->registerJs("
    $('body').on('click','tr.viewCustomer',function (e) {
        var id = $(this).data('key');
        location.href = '" . Url::to(['/customers/customer/view']) . "&id=' + id;
    });
");
*/