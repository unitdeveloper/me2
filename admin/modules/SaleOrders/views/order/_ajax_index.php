<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
?>

<?php 
        $gridColumns = [

              ['class' => 'yii\grid\SerialColumn'],
              // [
              //   'class' => 'yii\grid\ActionColumn',
              //   'options'=>['style'=>'width:150px;'],
              //   'buttonOptions'=>['class'=>'btn btn-default'],
              //   'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
              // ],
              //'id',
              // [
              //   'label' => Yii::t('common','Order date'),
              //   'value' => function($model){
              //     $Header = \common\models\SaleHeader::find()->where(['no' => $model->order_no])->one();

              //     return $Header->order_date;
              //   }
              // ],
              //'orderNo.order_date',
              'orderNo.sales_people',
              [ 
                'label' => Yii::t('common','Order date'),
                'value' => function($model)
                {
                  $link  = $model->orderNo->order_date;

                  if($model->orderNo->update_date != ''){
                      $link = date('d-m-Y',strtotime($model->orderNo->update_date));
                  }

                  return $link;
                }
              ],
              //'order_no',
              [
                //'attribute' => 'quantity',
                'label' => Yii::t('common','No'),
                'value' => function($model){
                  return $model->order_no;
                }
              ],
              //'type',
              'itemstb.master_code',
              'itemstb.description_th',
              //'quantity',
              [
                //'attribute' => 'quantity',
                'label' => Yii::t('common','Quantity'),
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function($model)
                {
                  return $model->quantity *1;
                }
              ],
              [
                //'attribute' => 'unit_price',
                'label' => Yii::t('common','Unit Price'),
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function($model)
                {
                  return $model->unit_price *1;
                }
              ],
              // 'unit_measure',
              //'unit_price',
              [ 
                'label' => Yii::t('common','Amount'),
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function($model)
                {
                  return $model->quantity * $model->unit_price;
                }
              ],

        ];



?>

<?= GridView::widget([
          'dataProvider' => $dataProvider,
          
          // 'panel'=>[
          //         'before'=>' '
          // ],

          //'filterModel' => $searchModel,
          'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>'First',   // Set the label for the "first" page button
            'lastPageLabel'=>'Last',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],
          'options' => ['class' => 'table-responsive'],
          'columns' => $gridColumns,
              
              
          
      ]); ?>


<div>
  <?php
        // echo ExportMenu::widget([
        //         'dataProvider' => $dataProvider,
        //         'columns' => $gridColumns,
        //         'columnSelectorOptions'=>[
        //             'label' => 'Columns',
        //             'class' => 'btn btn-success-ew'
        //         ],
        //         'fontAwesome' => true,
        //         'dropdownOptions' => [
        //             'label' => 'Export All',
        //             'class' => 'btn btn-primary-ew'
        //         ],
        //         'exportConfig' => [
        //             ExportMenu::FORMAT_HTML => false,
        //             ExportMenu::FORMAT_PDF => false,

        //         ],
        //         'styleOptions' => [
        //             ExportMenu::FORMAT_PDF => [
        //                 'font' => [
        //                      'family' => ['THSarabunNew','garuda'],
        //                         'bold' => true,
        //                         'color' => [
        //                              'argb' => 'FFFFFFFF',
        //                      ],
        //                 ],
        //             ],
        //         ],
        //     ]); 
        ?> 

</div>   