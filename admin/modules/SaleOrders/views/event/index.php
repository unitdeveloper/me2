<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Event Headers');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-event-header-index" ng-init="Title='<?=$this->title?>'" ng-controller="indexController">
  <?php if(Yii::$app->session->hasFlash('alert')):?>
      <?= \yii\bootstrap\Alert::widget([
      'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
      'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
      ])?>
  <?php endif; ?>
 
 
  <div class="row margin-top">
    <div class="col-md-6">
      <div class="row">
        <div class="col-sm-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-3">
                  <h2><i class="fa  fa-file-text-o fa-2x text-info" aria-hidden="true"></i></h2>
                </div>
                <div class="col-xs-9 text-right ">
                <?=Yii::t('common','จำนวนบิลวันนี้')?>
                  <u class="text-primary"><h2 ng-bind="countDocument|number"></h2></u>                  
                </div>
              </div>
            </div>
            <div class="panel-footer " style="background-color:rgb(70,148,78); color:#fff; height:40px;">
              <!-- <i class="fa fa-search-plus" aria-hidden="true"></i> Detail -->
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="panel panel-default pointer"  ng-click="detailAmount($event)">
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-3">
                  <h2><i class="fa fa-line-chart fa-2x text-danger" aria-hidden="true"></i></h2>
                </div>
                <div class="col-xs-9 text-right">
                  <?=Yii::t('common','ยอดขายวันนี้')?>
                  <u class="text-primary"><h2 ng-bind="sumTotal|number"></h2></u>                  
                </div>
              </div>
            </div>
            <div class="panel-footer" style="background-color:rgb(69,131,177); color:#fff;  height:40px;">
              <!-- <i class="fa fa-search-plus" aria-hidden="true"></i> Detail -->
            </div>
          </div>
        </div>

      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="panel panel-default">
            <!-- <div class="panel-heading">
              <span class="hidden-xs"></span><?=Yii::t('common','Item Group');?>
            </div> -->
            <div class="panel-body panel-promo">
              <div class="row">
                <div id="pieChartItemgroup"></div>
              </div>
            </div>
            <div class="panel-footer" style="background-color:rgb(158,208,154); height:100px; ">
              <div class="row">
                <div class="col-xs-4 text-center"><div class="menu"><p>ดูรายงานตามลูกค้า</p> <i class="fas fa-chart-line fa-3x"></i></div></div>
                <div class="col-xs-4 text-center"><div class="menu"><p>ดูรายงานตามจำนวน</p> <i class="fas fa-chart-bar fa-3x"></i></div></div>
                <div class="col-xs-4 text-center"><div class="menu"><p>ดูรายงานทั้งหมด</p> <i class="fas fa-list fa-3x"></i></div></div>
              </div>              
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php Pjax::begin(); ?>
          <?= GridView::widget([
          'dataProvider' => $dataProvider,
          //'filterModel' => $searchModel,
          'tableOptions' => ['class' => 'table table-bordered table-hover pos-list'],          
          // 'rowOptions' => function($model){
          //     return ['class' => $model->status=='closed' ? 'bg-success  ' : 'bg-warning   '];
          // },
          //'layout' => '{items} {pager}',
          'summary' => '',
          'columns' => [
              [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'bg-green number-head-text'],                 
              ],
              
              [
                'attribute' => 'no',
                'label' => Yii::t('common','No'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'bg-orange search-textbox' ],
                //'contentOptions' => ['class' => 'editBill pointer'],
                'value' => function($model){
                  if($model->status=='closed'){
                    $status = 'btn-success btn-flat';
                  }else {
                    $status = 'btn-warning btn-flat';
                  }
                  $html = '<div class="col-sm-4">
                              <div class="row">
                                <div class="col-xs-6 text-right">'.number_format($model->balance,2).'</div>
                                <div class="col-xs-6 text-right"> <small>'.$model->no.' </small></div>
                              </div>
                            </div>';   
                  $html.= '<div class="col-sm-4 hidden-xs"> '.date('d-m-Y',strtotime($model->order_date)).' <small class="hidden-sm">'.date('H:i:s',strtotime($model->create_date)).'</small></div>';
                                
             
                  

                  //$html.= "<div class=\"pull-right\"><label class=\"label $status\">$model->status</label></div>";
                  $html.= '<div class="col-sm-4 text-right">';
                    $html.= Html::a($model->status, ['update', 'id' => $model->id], [
                      'class' => 'btn '.$status.' btn-xs pull-right btn-flat',
                      'style' => 'width:50px; margin-left:2px;'
                    ]);
                    if(($model->balance<=0) || ($model->status!='closed')){
                      $html.= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                          'class' => 'btn btn-danger-ew btn-xs pull-right btn-flat',
                          'data' => [
                              'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                              'method' => 'post',
                          ],
                      ]);
                    }
                  $html.= '</div>';

                 
                  return $html;
                }
              ],
              
              // [
              //   'format' => 'raw',
              //   'headerOptions' => ['class' => 'bg-gray'],
              //   'contentOptions' => ['class' => 'text-right'],
              //   'value' => function($model){
              //     if($model->status=='Open'){
              //       return Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
              //           'class' => 'btn btn-danger-ew',
              //           'data' => [
              //               'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
              //               'method' => 'post',
              //           ],
              //       ]);
              //     }else{
              //       return ' ';
              //     }
              //   }
              // ]
          ],
          'pager' => [
            'options'=>['class'=>'pagination pull-right'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=>Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>Yii::t('common','next'),    // Set CSS class for the "next" page button
            'prevPageCssClass'=>Yii::t('common','prev'),    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>Yii::t('common','first'),    // Set CSS class for the "first" page button
            'lastPageCssClass'=>Yii::t('common','last'),    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],
      ]); ?>
      <?php Pjax::end(); ?>

      
      <div class="col-xs-12 panel-footer" style="background-color:rgb(158,208,154); height:100px; margin-top:-10px;">
        <div class="row">
          <div class="col-xs-4 text-center menu-graph"><div class="menu"><p>จำนวนบิลวันก่อนหน้า</p> <label class="label label-box text-black">12,3456</label></div></div>
          <div class="col-xs-4 text-center menu-graph"><div class="menu"><p>ยอดขายวันก่อนหน้า</p> <label class="label label-box text-black">12,3456</label></div></div>
          <div class="col-xs-4 text-center menu-graph"><div class="menu"><p>รายละเอียดทั้งหมด</p> <label class="label label-box text-black">12,3456</label></div></div>
        </div>              
      </div>
      

    </div>
    
  </div>

  
  <?php if(Yii::$app->session->get('Rules')['comp_id']==1){ // GINOLR ONLY ?>
    <div class="row">
      <div class="col-sm-10 col-xs-12">
      <a href="index.php?r=SaleOrders/event/index&brand=ALL" class="btn  mode <?php 
        if(isset($_GET['brand'])){
          if($_GET['brand'] == 'ALL'){
            echo 'Active btn-primary';
          }else {
            echo 'btn-default'; 
          } 
        }else {
          echo 'Active btn-primary';
        }; 
      ?>" data-key="ALL"><img src="images/company/all.png" style="height:20px;"> All</a> 
       <a href="index.php?r=SaleOrders/event/index&brand=GINOLR" class="btn  mode <?php 
        if(isset($_GET['brand'])){
          if($_GET['brand'] == 'GINOLR'){
            echo 'Active btn-primary';
          }else {
            echo 'btn-default'; 
          } 
        }else {
          echo 'btn-default'; 
        }; 
      ?>" data-key="GINOLR"><img src="images/company/ginolr.png" style="height:20px;"> Ginolr</a> 
       <a href="index.php?r=SaleOrders/event/index&brand=ENCOM" class="btn mode <?php 
          if(isset($_GET['brand'])){
            if($_GET['brand'] == 'ENCOM'){
              echo 'Active btn-primary';
            }else {
              echo 'btn-default'; 
            } 
          }else {
            echo 'btn-default'; 
          }; 
        ?>" data-key="ENCOM"><img src="images/company/encom.png" style="height:20px;"> ENCOM</a>
      </div>
      <div class="col-sm-2 hidden-xs">
        <div style="margin-top:-20px;"><h3><i ng-bind="brandText"></i></h3></div>
      </div>
    </div>
  <?php } ?>
             
     
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading" style="background-color:#2a2a2a; color:#fff; border-bottom:0px;">
            <h3 class="panel-title"><?=Yii::t('common','Date Filter')?></h3>
            <div style="position:absolute; right:20px; top:10px;">
              <span class="label label-primary">From : </span><span class="label label-warning" ng-bind="formDate | date:'longDate'"></span>
              <span class="label label-primary"> To : </span><span class="label label-warning" ng-bind="toDate | date:'longDate'"></span>
              <span class="label label-danger" ng-bind="((((((toDate - formDate)) /1000)/60)/60)/24) +1 | number:0"><?= Yii::t('common','Day')?></span>
            </div>
          </div>
          <div class="panel-body" style="height:200px; background-color:#2a2a2a; color:#fff;">
            <div class="row">
              <my-chart ></my-chart>
            </div>
          </div>
          <div class="panel-footer" style="background-color:#6394ff; color:#fff;">
            <div class="row">
              <div class="col-xs-8">
                <h4>Sales Report</h4>
              </div>
              <div class="col-xs-4 text-right">
                <h5 id="sumTotal" data="{{sumTotal|number}}" ng-bind="sumTotal|number"></h5>
              </div>
            </div>              
          </div>
        </div>
      </div>
    </div>         



    <div class="row">

      <div class="col-lg-10 col-md-9 col-sm-8">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><?=Yii::t('common','Sale Top')?> 10</h3>
              </div>
              <div class="panel-body">                 
                <div id="pieChartdiv"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><?=Yii::t('common','Best seller')?> <?=Yii::t('common','Top')?> 20</h3>
              </div>
              <div class="panel-body">                
                <div id="barChartdiv"></div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="modal fade " id="itemInfoModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-green-ew">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id=""><i class="fa fa-info"></i> <?=Yii::t('common','Item infomation')?></h4>
            </div>
            <div class="modal-body">
              <div class="item-info-body"></div>
              <div class="box box-primary">
                <div class="box-body">
                  <div class="col-sm-3">
                    <label><?=Yii::t('common','Color')?></label>
                    <div class="input-group">
                      <span class="btn input-group-addon">
                        <i class="fa fa-crosshairs" aria-hidden="true"></i>
                      </span>
                      <input type="text"  class="form-control colorpicker" aria-invalid="false">
                      <span id="ew-modal-pick-cust" class="btn btn-success input-group-addon save-color">
                        <i class="fa fa-floppy-o text-aqua" aria-hidden="true" style="cursor:pointer;"> <?=Yii::t('common','Save')?></i>
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-9">
                       
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off"></i> Close</button>

            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-md-3 col-sm-4">

          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><?=Yii::t('common','Options')?></h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-12">
                  <button type="button" name="button" class="btn btn-info sort-by-amount full-width active" disabled><i class="fa fa-sort-numeric-asc"></i> <?=Yii::t('common','Sort by amount')?></button>
                </div>
                <div class="col-sm-12 margin-top">
                  <button type="button" name="button" class="btn btn-warning sort-by-sales  full-width"><i class="fa fa-usd"></i> <?=Yii::t('common','Sort by sales')?></button>
                </div>
              </div>

            </div>

          </div>


          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">All Items</h3>
            </div>            
            <div class="panel-body" style="font-size:9px; ">
                <div ng-repeat="item in items" class="row">
                  <div style="min-height:40px;" class="pointer item-info" data-key="{{item.no}}" data-color="{{item.color}}" data-group="{{item.group}}">
                    
                    <div class="col-xs-3">
                      <div class="box-color pull-left text-center" style="background-color:{{item.color}}; padding:3px;">{{item.qty}}</div>
                    </div>
                    <div class="col-xs-9">
                      <span ng-style="item.group && {'color':'blue'}"> {{item.name}} </span>                     
                    </div>                     
                    
                  </div>
                </div>
            </div>
            <div class="panel-footer" style="font-size:9px;">
              <div><?=Yii::t('common','Text')?></div>
              <div style="color:blue"><?=Yii::t('common','Blue')?> : <?=Yii::t('common','Has group')?></div>
              <div style="color:#999"><?=Yii::t('common','Gray')?> : <?=Yii::t('common','Ungroup')?></div>
            </div>
          </div>

      </div>
    </div>

</div><!-- End Controller -->

<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()],'type'=>'text/javascript'];?>
<!-- Resources amcharts-->
<?php $this->registerJsFile('https://www.amcharts.com/lib/3/amcharts.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php $this->registerJsFile('https://www.amcharts.com/lib/3/serial.js',$Options);?>
<?php $this->registerJsFile('https://www.amcharts.com/lib/3/themes/black.js',$Options);?>
<?php $this->registerJsFile('https://www.amcharts.com/lib/3/pie.js',$Options);?>
<?php $this->registerJsFile('https://www.amcharts.com/lib/3/themes/light.js',$Options);?>

<?php $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',$Options);?>


<?php $this->registerJsFile('@web/js/saleorders/eventController.js?v=3.03.10',$Options);?>
<?php $this->registerJsFile('@web/js/saleorders/dashboardController.js?v=2.12.21',$Options);?>

<?php $this->registerJsFile('https://adminlte.io/themes/AdminLTE/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',$Options);?>
<?php $this->registerCssFile('https://adminlte.io/themes/AdminLTE/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css');?>

<?php $this->registerCssFile('@web/css/sale-pos-style.css');?>
<?php
$Url = Url::to(['/SaleOrders/event/update']);
$js =<<<JS
$(document).on('ready pjax:success', function() {
  onLoad();
});
$(document).ready(function(){
  onLoad();
})
function onLoad(){
  var template_menu = '<div class="right-menu-top hidden-sm hidden-xs"><div class="col-xs-8 text"></div><div class="col-xs-4 quantity"></div></div>';
  $('.right-menu-widget').html(template_menu)
  $('.right-menu-widget div.right-menu-top div.text').text('ยอดขายรวมทั้งหมด');
  $('.right-menu-widget div.right-menu-top div.quantity').text($('#sumTotal').attr('data'));
  $('.right-menu-widget .right-menu-top').show();
  // setTimeout(function(){   
  //   $('table.pos-list >  thead').attr('style','visibility: visible;'); 
  //   $('table.pos-list >  thead').show(2000);
  // }, 2000);
  // setTimeout(function(){
  //   $('.right-menu-widget .right-menu-top').show("slide", { direction: "left" }, 1000);
  // }, 3000);
  
  $('.search-textbox').html('<div class="row">'+
                              '<div class="col-xs-4 text-find text-right">ค้นหาเอกสาร </div>'+
                              '<div class="col-xs-8">'+
                                '<div class="has-warning has-feedback">'+
                                  '<input type="text" class="form-control" name="EventSearch[no]">'+
                                  '<span class="glyphicon glyphicon-search form-control-feedback"></span>'+
                                '</div>'+
                              '</div>'+
                            '</div>');
  $('.number-head-text').html('<div class="text-head">#</div>');
}

  $('body').on('click','td.editBill',function (e) {
    var id = $(this).closest('tr').data('key');
    console.log($(this).data('key'));
    location.href = "{$Url}&id=" + id;
  });

  $(function () {
    //Colorpicker
    $('.colorpicker').colorpicker();

  });

  $('body').on('click','.save-color',function(){
    updateItems('color',$('.colorpicker').attr('data-no'),$('.colorpicker').val());
  })

  $('body').on('change','input.ajax-update',function(){
    updateItems($(this).attr('name'),$(this).attr('data-key'),$(this).val());
  })

  function updateItems(field,id,value){
    $.ajax({
      url:'index.php?r=items/ajax/update&id='+id+'&field='+field,
      method:'POST',
      data:{id:id,value:value},
      success:function(getData){
        console.log(getData);
      }
    })
  }

  $('body').on('change','.colorpicker',function(){
    $(this).attr('style','background-color:'+$(this).val());
  })

  $('body').on('click','.item-info',function(){
    var itemNo = $(this).attr('data-key');
    var color  = $(this).attr('data-color');
    var group  = $(this).attr('data-group');
    $('.item-info-body').html('');
    $('#itemInfoModal').modal('show');
    $.ajax({
      url:'index.php?r=items/items/view-modal&id='+itemNo,
      method:'GET',
      success:function(getData){
         $('.item-info-body').html(getData);
         $('.sale-summary a').attr('href','index.php?r=SaleOrders/event/sale-line&No='+itemNo).attr('target','_blank');
         $('.colorpicker').val(color).attr('data-no',itemNo).attr('style','background-color:'+color);
         $('input[name="group_chart"]').val(group).attr('data-key',itemNo);
      }
    })
  })
JS;

$this->registerJs($js,\yii\web\View::POS_END);
?>

 