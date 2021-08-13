<?php

use yii\helpers\Html;

use kartik\widgets\ActiveForm;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

//use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sale Line');
$this->params['breadcrumbs'][] = $this->title;



 
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

      [ 
        'attribute' => 'order_date',
        'label' => Yii::t('common','Order date'),
        'value' => function($model)
        {
          $link  = $model->orderNo->order_date;

          // if($model->orderNo->update_date != ''){
          //     $link = date('d-m-Y',strtotime($model->orderNo->update_date));
          // }

          return $link;
        }
      ],
      //'orderNo.sales_people',
      //'orderNo.customer.name',
       
      //'order_no',
      [
        'attribute' => 'order_no',
        'label' => Yii::t('common','No'),
        'format' => 'raw',
        'value' => function($model){
          return $html = Html::a($model->order_no,['/SaleOrders/saleorder/view','id' => $model->sourcedoc],['target' => '_blank']);
        }
      ],
      [
        //'attribute' => 'quantity',
        'label' => Yii::t('common','Vat'),
        'value' => function($model){
          if($model->orderNo->vat_type==1){
            $vat = 'Vat';
          }else{
            $vat = 'No Vat';
          }
          return $vat;
        }
      ],
      //'type',
      //'itemstb.master_code',
      [
        'label' => Yii::t('common','Items'),
        'format' => 'raw',
        'value' => function($model){
            $html = Html::a($model->items->master_code,['/items/items/view-only','id' => $model->items->id],['target' => '_blank']);
            if($model->items->ProductionBom != 0){
              $html = Html::a($model->items->master_code,['/Manufacturing/prodbom/view-only','id' => $model->items->ProductionBom],['target' => '_blank']);
            }
          return $html;
        }
      ],
      //'itemstb.description_th',
      [
        'attribute' => 'description',
        'label' => Yii::t('common','Product Name'),
        'format' => 'raw',
        'contentOptions' => [
                'style'=>'max-width:180px; min-height:100px; overflow: auto; word-wrap: break-word;'
        ],
        'value' => function($model){
          return $model->description ? $model->description : $model->items->description_th;
        }
      ],
      //'quantity',
      [
        //'attribute' => 'quantity',
        'label' => Yii::t('common','Quantity'),
        'contentOptions' => ['class' => 'text-right'],
        'headerOptions' => ['class' => 'text-right'],
        'value' => function($model){
          return $model->quantity *1;
        }
      ],
      [
        //'attribute' => 'unit_price',
        'label' => Yii::t('common','Unit Price'),
        'contentOptions' => ['class' => 'text-right'],
        'headerOptions' => ['class' => 'text-right'],
        'value' => function($model){
          return $model->unit_price *1;
        }
      ],
      // 'unit_measure',
      // 'unit_price',
      [ 
        'label' => Yii::t('common','Amount'),
        'contentOptions' => ['class' => 'text-right'],
        'headerOptions' => ['class' => 'text-right'],
        'value' => function($model){
          return $model->quantity * $model->unit_price;
        }
      ]
];

?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="row" ng-init="Title='<?= Html::encode($this->title) ?>'">
  <div class="col-xs-12">
    <div class=" ">
      <div class=" ">
         
        <?php $form = ActiveForm::begin(['id' => 'saleline-search','method' => 'GET']); ?>
        <div class="row-" style="margin-bottom: 10px;">

          <div class="col-sm-offset-2 col-md-offset-4">

            <div class="row">

              <div class="col-sm-5 col-md-6">  
                <?php

                $startDate  = '';
                $endDate    = '';

                if(isset($_GET['fil_from_date']))    
                {
                  if($_GET['fil_from_date']!='') $startDate   = date('d-m-Y',strtotime($_GET['fil_from_date']));
                }


                if(isset($_GET['fil_to_date'])){      
                  if($_GET['fil_to_date']!='') $endDate     = date('d-m-Y',strtotime($_GET['fil_to_date']));

                }

                echo DatePicker::widget([
                    'name' => 'fil_from_date',
                    'value' => $startDate,
                    'type' => DatePicker::TYPE_RANGE,
                    'name2' => 'fil_to_date',
                    'value2' => $endDate,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy'
                    ],
                    'options' => [
                      'autocomplete' => 'off'
                    ],
                    'options2' => [
                      'autocomplete' => 'off'
                    ],
                    'pluginEvents' => [
                        //"changeDate" => "function(e) { ReloadSearch(); }",
                    ],
                ]);

                ?>
              </div>
              <div class="col-sm-3 col-md-2 ">
                <select name="vat_type" class="form-control ">
                    <option value="" <?=(@$_GET['vat_type']==1)? 'selected': '';?>><?=Yii::t('common','All')?></option>
                    <option value="1" <?=(@$_GET['vat_type']==1)? 'selected': '';?>>Vat</option>
                    <option value="2" <?=(@$_GET['vat_type']==2)? 'selected': '';?>>No Vat</option>
                </select>
              </div>
              <div class="col-sm-4 col-md-4"> 
                              
                  <div class="box-tools">
                    <div class="input-group  "  >
                      <input type="text" name="table_search" class="form-control pull-right" placeholder="<?=Yii::t('common','Search');?>">

                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                  
              </div>
            </div>
          </div><!-- /.col-sm-offset-6 -->
        </div><!-- /.row -->
         <?php ActiveForm::end(); ?>
      </div>
      <!-- /.box-header -->
      <div class="box-body no-padding ew-render-saleorder-linelist">
            <div class="row">
              <div class="col-sm-12 ">
                <?php
                  echo ExportMenu::widget([
                      'dataProvider' =>  $dataProvider,
                      'columns' => $gridColumns,
                      'columnSelectorOptions'=>[
                          'label' => 'Columns',
                          'class' => 'btn btn-success-ew'
                      ],
                      'fontAwesome' => true,
                      'dropdownOptions' => [
                          'label' => 'Export All',
                          'class' => 'btn btn-primary-ew'
                      ],
                      'exportConfig' => [
                          ExportMenu::FORMAT_HTML => false,
                          ExportMenu::FORMAT_PDF => false,

                      ],
                      'styleOptions' => [
                          ExportMenu::FORMAT_PDF => [
                              'font' => [
                                   'family' => ['THSarabunNew','garuda'],
                                      'bold' => true,
                                      'color' => [
                                           'argb' => 'FFFFFFFF',
                                   ],
                              ],
                          ],
                      ],
                      'target' => ExportMenu::TARGET_BLANK,
                  ]); 
                ?> 
              </div>
            </div> 

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
                        'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
                        'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
                        'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
                        'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
                        'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
                        'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
                        'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
                        ],
                      'options' => ['class' => 'table-responsive font-roboto'],
                      'columns' => $gridColumns,
                      'pjax'=>false,    
                      'responsiveWrap' => false, // Disable Mobile responsive    
                      
                  ]); ?>



       
              
      </div>


      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>

<script>

  $(document).ready(function(){
    $('a.ew-bt-app-new').hide();
    $('#w1-togdata-page').attr('title',' ');
  });
  function ReloadSearch()
  {
    //console.log($('input[name="fil_from_date"]').val()+' - '+ $('input[name="fil_to_date"]').val());

    var $dateSearch = $.trim($('input[name="table_search"]').val());

    var $date1      = $('input[name="fil_from_date"]').val();
    var $date2      = $('input[name="fil_to_date"]').val();

    $('.ew-render-saleorder-linelist').hide();
    $('.ew-render-saleorder-linelist').html('<div class="text-center" style="margin-top:50px;">\r\n'+
            '<i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>\r\n'+
            '<div class="blink"> Loading .... </div></div>\r\n');


    

    $('.ew-render-saleorder-linelist').slideToggle('slow',function(){
      setTimeout(function(){ 
         
        $.ajax({ 

            url:"index.php?r=SaleOrders/order",
            type: "GET", 
            data: {fil_from_date:$date1,fil_to_date:$date2,textSearch:$dateSearch},
            async:false,
            success:function(getData){
                 
                 //$('.ew-render-saleorder-linelist').html(getData); 
                
            }
        }); 
    }, 300);
    });
    
  }


  $('body').on('change','input[name="table_search"],input[name="fil_to_date"],input[name="fil_from_date"]',function(){

    // var sTrim = $.trim($('input[name="table_search"]').val());
    // $('input[name="table_search"]').val(sTrim);
    
    // var sTrim = $.trim($('input[name="table_search"]').val());
    // if(sTrim==''){
    //   $('input[name="table_search"]').focus();
    // }else {
    //   ReloadSearch();
    // } 

  });

  $('body').on('click','button.s-click',function(){

    $('form[id="saleline-search"]').submit();

    // var sTrim = $.trim($('input[name="table_search"]').val());
    // if(sTrim==''){
    //   $('input[name="table_search"]').focus();
    // }else {
    //   ReloadSearch();
    // }
    
  });


  $('body').on('keyup keypress','input[name="table_search"]', function(e) {

    // Disable form submit on enter.
      // var keyCode = e.keyCode || e.which;
      // if (keyCode === 13) { 
      //   e.preventDefault();
      //   ReloadSearch();
      //   //return false;
      // } 
    });

</script>
 