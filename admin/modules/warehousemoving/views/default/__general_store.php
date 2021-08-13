<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
?>
<?= GridView::widget([
      'dataProvider' => $dataProvider,
      //'filterModel' => $searchModel,
      'tableOptions' => ['class' => 'table table-bordered   grid-content'],
      'rowOptions' => function($model){
          return [
            'class' => (($model->confirm*1) > 0)? ' ' : 'bg-pink',
          ];
      },
      'columns' => [
          [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class' => 'bg-green'],
            'contentOptions' => function($model){
              return ['class' => (($model->confirm*1) > 0)? 'bg-gray serial-column' : 'bg-yellow blink serial-column',];
            }
          ],

          [
              'attribute' => 'head_date',
              'label' => Yii::t('common','Date'),
              'format' => 'raw',
              'headerOptions' => ['class' => 'hidden-xs bg-green'],
              'filterOptions' => ['class' => 'hidden-xs'],
              'contentOptions' => function($model){
                return ['class' => 'hidden-xs pointer','style' => 'position:relative;', 'ng-click' => 'openModal($event)', 'data-no' => $model->no, 'data-key' => $model->id];
              },            
              'value' => function ($model) {
                  $link = '<div>'.Yii::t('common','Date').' : '.date('d/m/Y',strtotime($model->order_date)).'</div>'."\r";
                  if($model->update_date != ''){
                      $link.= '<div>'.Yii::t('common','Time').' : '.date('H:i:s',strtotime($model->update_date)).'</div>'."\r";
                  }else {
                      $link.= '<div>'.Yii::t('common','Time').' : '.date('H:i:s',strtotime($model->create_date)).'</div>'."\r";
                  }
                  return $link;
              },
          ],

          [
              'attribute' => 'no',
              'format' => 'raw',
              'headerOptions' => ['class' => 'bg-green'],
              'contentOptions' => function($model){
                return ['class' => 'text-info pointer','style' => 'position:relative;', 'ng-click' => 'openModal($event)', 'data-no' => $model->no, 'data-key' => $model->id];
              },
              'value' => function($model){

                  // ตัดตัวอักษร ถ้ามากกว่า 35 ตัว
                  $count_char = utf8_strlen($model->customer['name']);
                  if($count_char >=32 )
                  {
                      $cust_name = iconv_substr($model->customer['name'],0,32,'UTF-8').'...';
                  }else {
                      $cust_name = $model->customer['name'];
                  }


                  if($model->vat_type==1)
                  {
                      $vat_color =  'text-success';
                  }else {
                      $vat_color =  'text-primary';
                  }
                  if(date('Ymd') == date('Ymd', strtotime($model->create_date )))
                  {
                      $Showdate = date('H:i',strtotime($model->create_date));
                  }else {
                      $Showdate = date('d/m/Y',strtotime($model->create_date));
                  }


                  // $cus = Html::a('<div class="text-customer-info">
                  //                   '.$cust_name.'
                  //                 </div>'."\r",
                  //                 ['view', 'id' => $model->id]);
                  //
                  // $cus.= Html::a('<div class="'.$vat_color.' text-order-number">
                  //                   '.$model->no.'
                  //                 </div>'."\r",
                  //               ['view', 'id' => $model->id]);
                  $cus = '<div class="text-customer-info">
                                    '.$cust_name.'
                                  </div>';

                  $cus.= '<div class="'.$vat_color.' text-order-number">
                                    '.$model->no.'
                                  </div>';


                  $cus.= '<div class="hidden-sm hidden-md hidden-lg text-right" style="position:absolute; right:15px; top:10px; color:#ccc;">
                            <div class="text-aqua text-balance">
                              <span  style="background-color:#fff; padding-left:5px;padding-right:5px;">'.number_format($model->balance,2).'</span>
                            </div>
                            <small class="hidden-sm hidden-md hidden-lg " style="padding-left:5px;padding-right:5px;">  '.$Showdate.'</small>
                          </div>'."\r";
                  //$cus.= '<div>PO : '.$model->ext_document.'</div>';

                  $Fnc = new FunctionSaleOrder();

                  $JobStatus = $Fnc->OrderStatus($model);

                  $cus.= '<div class="hidden-sm hidden-md hidden-lg text-ship-status">'.Yii::t('common',$JobStatus).'</div>'."\r";

                  if($model->status=='Shiped')
                  {
                      $cus.='<div class="hidden-sm hidden-md hidden-lg text-ship-status">
                              <i class="fa fa-calendar" aria-hidden="true"></i> '.date('d/m/Y',strtotime($model->ship_date)).'
                            </div>'."\r";
                  }

                  //return Html::a(Yii::t('common',$JobStatus),['view', 'id' => $model->id]);
                  return $cus;
              },
          ],
        //   [
        //       'attribute' => 'sales_people',
        //       'format' => 'raw',
        //       'filterOptions' => ['class' => 'hidden-xs'],
        //       'headerOptions' => ['class' => 'hidden-xs bg-green'],
        //       'contentOptions' => function($model){
        //         return ['class' => 'hidden-xs pointer','style' => 'position:relative;', 'ng-click' => 'openModal($event)', 'data-no' => $model->no, 'data-key' => $model->id];
        //       },
        //       'value' => function($model){
        //           return '<div id="sale-name"><p>'.$model->sales['name'].' '. $model->sales['surname'].'</p></div>';
        //       }
        //   ],

          [
              //'attribute' => 'status',
              'format' => 'raw',
              'filterOptions' => ['class' => 'hidden-xs'],
              'headerOptions' => ['class' => 'hidden-xs bg-green'],
              'contentOptions' => function($model){
                return ['class' => 'hidden-xs pointer','style' => 'position:relative;', 'ng-click' => 'openModal($event)', 'data-no' => $model->no, 'data-key' => $model->id];
              },            
              'value' => function($model){

                $confirmed = '';
                $waitting  = '';

                if(($model->confirm*1) > 0){
                  $status = '<div class="pull-left status"><i class="fa fa-check-square-o text-success"></i> '.Yii::t('common','Confirmed').'</div>';
                  $status.= '<div class="pull-right" style="color:#ccc;font-family:tahoma; font-size:10px;">'.date('Y-m-d H:i',strtotime($model->confirm_date)).'</div>';
                }else {
                  $status = '<div class="status"><i class="fa fa-square text-danger"></i> '.Yii::t('common','Waitting confirm').'</div>';
                }

                $Html = '<div class="col-xs-12">'.$status.'</div>';

                return $Html;

              },

          ],



      ],
      'pager' => [
        'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
        'prevPageLabel' => '«',   // Set the label for the "previous" page button
        'nextPageLabel' => '»',   // Set the label for the "next" page button
        'firstPageLabel'=>Yii::t('common','First'),   // Set the label for the "first" page button
        'lastPageLabel'=>Yii::t('common','Last'),    // Set the label for the "last" page button
        'nextPageCssClass'=>Yii::t('common','next'),    // Set CSS class for the "next" page button
        'prevPageCssClass'=>Yii::t('common','prev'),    // Set CSS class for the "previous" page button
        'firstPageCssClass'=>Yii::t('common','first'),    // Set CSS class for the "first" page button
        'lastPageCssClass'=>Yii::t('common','last'),    // Set CSS class for the "last" page button
        'maxButtonCount'=>6,    // Set maximum number of page buttons that can be displayed
        ],
  ]); ?>

