<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;

use common\models\SaleInvoiceLine;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Invoice Headers');
$this->params['breadcrumbs'][] = $this->title;
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-invoice-header-index" style="font-family: saraban;" ng-init="Title='<?=$this->title?>'">
<div class="row">
    <div class="col-xs-12 text-left">
        <div class="pull-right">
            <?php
                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                       
                        [
                    
                            'label'         => Yii::t('common','Date'),
                            'value' => function($model){                   
                                return date('Y-m-d', strtotime($model->posting_date));
                            }
            
                        ],
                        'no_',
                        [
                            'label'         => Yii::t('common','Customer Code'),
                            'value' => function($model){
                                return $model->customer 
                                        ? ($model->customer->code)
                                        : '';
                            }
                        ],
                        [
                            'label'         => Yii::t('common','Customer name'),
                            'value' => function($model){
                                return $model->customer 
                                        ? $model->customer->name
                                        : '';
                            }
                        ],
                        [
                    
                            'label' => Yii::t('common','Before Vat'),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'font-roboto text-right'],
                            'value' => function($model){                   
                                return number_format($model->sumtotals->before,2);
                            }
            
                        ],
                        [
                    
                            'label' => Yii::t('common','Vat'),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'font-roboto text-right'],
                            'value' => function($model){                   
                                return number_format($model->sumtotals->incvat,2);
                            }
            
                        ],
                        [
                            
                            'label' => Yii::t('common','Balance'),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'font-roboto text-right'],
                            'value' => function($model){                   
                                return number_format($model->sumtotals->total,2);
                            }
            
                        ],
                    ],
                    'columnSelectorOptions'=>[
                        'label' => ' ',
                        'class' => 'btn btn-default'
                    ],
                    'fontAwesome' => true,
                    'dropdownOptions' => [
                        'label' => 'Export All',
                        'class' => 'btn btn-default'
                    ],
                    
                ]); 
            ?>
        </div>
        <div class="pull-right">
            <?=$this->render('@admin/modules/SaleOrders/views/reserve/_script_inv_list')?>
        </div>
    </div>  
</div>
<h3>รายการบิลที่ยังไม่ Post (ยังไม่บันทึกเป็นรายได้)</h3>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table'],
        'columns' => [
            [
                'headerOptions' => [
                    'style' => 'width:20px;'
                ],
                'class' => 'yii\grid\SerialColumn'
            ],

            //'id',
            //'no_',
            //'posting_date:date',
            // [
            //   'attribute' => 'posting_date',
            //     'label' => Yii::t('common','Posting date'),
            //     'format' => 'raw',
            //     'value' => function($model){
            //         return date('Y-m-d',strtotime($model->posting_date));

            //     }
            // ],
            [
                'attribute' => 'posting_date',
                'label' => Yii::t('common','Posting Date'),
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs','style' => 'width:100px;'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'font-roboto','style' => 'min-width:50px;max-width:100;'],
                'value' => function($model){
                    $date =  ($model->posting_date)? $model->posting_date : ' ';
                    return date('Y-m-d',strtotime($date));
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'posting_date',
                    'convertFormat' => true,
                    'options'   => [
                        'autocompleate' => 'off',
                        'class' => 'form-control'
                    ],
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                        ],                                
                    ],
                    
                ]),
            ],
            [
                'attribute' => 'no_',
                'label' => Yii::t('common','Document No'),
                'headerOptions' => ['style' => 'width:110px;'],
                'contentOptions' => ['class' => 'font-roboto'],
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->no_,['update','id' => $model->id],['data' => $model->id]);

                }

            ],
            //'cust_no_',
            [
                'attribute' => 'cust_no_',
                'label' => Yii::t('common','Customer'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a(wordwrap($model->cust_name_, 100, "<br/>\n", false),['update','id' => $model->id],['data' => $model->id]);

                }

            ],
            // [
            //     'attribute' => 'cust_no_',
            //     'label' => Yii::t('common','Address'),
            //     'format' => 'raw',
            //     'value' => function($model){
            //         return wordwrap($model->cust_address, 50, "<br/>\n", false);
            //     }

            // ],
            //'cust_name_',
            //'cust_address:ntext',
            // 'cust_address2:ntext',
            // 'posting_date',
            // 'order_date',
            // 'ship_date',
            // 'cust_code',
            // 'sales_people',
            // 'document_no_',
            [
                
                'label' => Yii::t('common','Before Vat'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'font-roboto text-right'],
                'value' => function($model){                   
                    return number_format($model->sumtotals->before,2);
                }

            ],
            [
                
                'label' => Yii::t('common','Vat'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'font-roboto text-right'],
                'value' => function($model){                   
                    return number_format($model->sumtotals->incvat,2);
                }

            ],
            [
                
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'font-roboto text-right'],
                'value' => function($model){                   
                    return number_format($model->sumtotals->total,2);
                }

            ],


            

            //['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
            'lastPageLabel'=>'<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
</div>


 
<!-- Right Click -->
<style type="text/css">
  #contextMenu {
  position: absolute;
  display:none;
  z-index: 500;

 
}
    .dropdown-menu{
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        background-color: rgb(250,250,250);
    }
</style> 
<div id="contextMenu" class="dropdown clearfix" style="">
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px; ">

        <li>
            <a tabindex="-1" href="#Edit" class="RCmenuEdit"><i class="fa fa-pencil text-warning" aria-hidden="true"></i> <?=Yii::t('common','Edit')?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a tabindex="-1" href="index.php?r=accounting/saleinvoice/index"><i class="fa fa-refresh text-success" aria-hidden="true"></i> <?=Yii::t('common','Refresh')?></a>
        </li>



        <li><?= Html::a('<i class="fa fa-trash-o text-danger" aria-hidden="true"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => ''], [
            'class' => 'RCmenuDelete',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?></li>

        <li class="divider"></li>

        <li>
            <a tabindex="-1" href="#Detail" data='x' class="RCmenuDetail"><i class="fa fa-info text-info" aria-hidden="true"></i> <?=Yii::t('common','Detail')?></a>
        </li>

    </ul>
</div>
<?PHP
$js=<<<JS
  
    // Date Fillter
    // $(document).ready(function(){  
    //     var element = $('input[name="SaleinvoiceSearch[posting_date]"]');
    //     var template = '<div class="input-group date" data-provide="datepicker">'+
    //                         '<input type="text" class="form-control " name="SaleinvoiceSearch[posting_date]">'+
    //                         '<div class="input-group-addon">'+
    //                             '<span class="glyphicon glyphicon-th"></span>'+
    //                         '</div>'+
    //                     '</div>';
    //     element.parent('td').html(template);
    // })
    // /.Date Fillter


    $(function() {
      
      var contextMenu = $("#contextMenu");
      
      $("body").on("contextmenu", "td a", function(e) {

        contextMenu.css({
          display: "block",
          left: e.pageX,
          top: e.pageY
        });

        contextMenu.find('.RCmenuEdit').attr('href','index.php?r=accounting/saleinvoice/update&id='+$(this).attr('data'));

        contextMenu.find('.RCmenuDelete').attr('href','index.php?r=accounting/saleinvoice/delete&id='+$(this).attr('data'));

        contextMenu.find('.RCmenuDetail').attr('href','index.php?r=accounting/saleinvoice/view&id='+$(this).attr('data'));


        return false;
      });
      
      contextMenu.on("click", "a", function() {
         contextMenu.hide();
      });
      
    });


    $(document).click(function(e) {

      // check that your clicked
      // element has no id=info

      if(e.target.id != 'contextMenu') {
        $("#contextMenu").hide();


         
      }
 
      //if(e.target.id != 'contextmenu') {

        // var data = '<h4>' + $('.ew-inv-change').attr('ew-no_') + '<h4>';
     //    $('.ew-inv-change').html(data); 
     //    $('.ew-inv-change').attr('class','ew-inv-no');
      //}
    });
JS;
$this->registerJS($js);
?>
<!-- /.Right Click -->








 