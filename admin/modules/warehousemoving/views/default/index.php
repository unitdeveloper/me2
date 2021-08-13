<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Shipment');
$this->params['breadcrumbs'][] = $this->title;

function utf8_strlen($string) {
    $c = strlen($string); $l = 0;
    for ($i = 0; $i < $c; ++$i)
    if ((ord($string[$i]) & 0xC0) != 0x80) ++$l;
    return $l;
}

?>
<style >

.fade-in-onload{
  display: none;
}
 
.panel-promo{
  height:260px;
  /*height:250px;
  overflow-y:auto;
  overflow-x:hidden;
  background-repeat: no-repeat;
  background-size:250px;*/
}
.panel-promo img{
  -webkit-filter: drop-shadow(5px 20px 20px #222 );
  filter: drop-shadow(5px 20px 20px #222);
  margin:-40px 0px 0px -10px;

}


#chartdiv {
  width		: 108%;
  height		: 170px;
  font-size	: 11px;
  margin:-10px 0px 0px -10px !important;


}

#dateRemain {
  width: 100%;
  height: 250px;
  font-size: 11px;
}

.viewShip{
  height: 50px;
}
 
input.no-border{
  background-color: transparent !important;
}

@media only screen and (max-width: 500px) {
     #dateRemain text {
       font-size: 30px;
     }
}


#barcode{
  max-width: 200px;
  padding:0px 5px 0px 0px;
  font: 400 14px/100% 'Roboto', sans-serif;
}

ng-barcode, .ng-barcode, div[ng-barcode] 
{
  display: block;
  height: 35px;
  margin: 1px auto;
}

.print{
  display:none;
  /* width:794px; */
  /* height:29.7cm */
  /* width:794px; height:1122px , width:21cm; height:29.7cm A4*/
}
.need{
  color:blue;
}

@media print {

  body {
    visibility:hidden;
  }

  
  .print  
  {

    display: block !important;
    visibility:visible !important;      
    overflow: visible;
    margin-top:0 !important;
    padding:0 !important;
    left:0 !important;
    top:0 !important;
    width:685px !important;
    /* height:1021px !important; */
    font-size:11px;
    

  }

  .qty{
    width:80px;
  }

 

  .content-render{
    display:none;
  }
  #barcode span{
    font-size:10px;
  }

  .print img{
    float:left;
  }
  
  .child-code{
    font-size:8px !important; 
    color:#444  !important;
    margin-left:5px  !important;
     
  }

  .child-code_{
    font-size:8px !important; 
    color:#444  !important;
     
  }

  @page{
    margin-left: 0px !important;
    margin-right: 0px !important;
    margin-top: 0px !important;
    margin-bottom: 0px !important;

    }

}
</style>
<?= $this->render('@admin/modules/items/views/items/barcode-print-all') ?>
 
<div class="sale-event-header-index" ng-init="Title='<?=$this->title?>';" ng-controller="shipmentCtrl">
  <?php if(Yii::$app->session->hasFlash('alert')):?>
      <?= \yii\bootstrap\Alert::widget([
      'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
      'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
      ])?>
  <?php endif; ?>


<div class="fade-in-onload"  ng-init="workdate='<?=Yii::$app->session->get('workdate')?> <?=Yii::$app->session->get('worktime')?>'; myInput='112233555'" style="font-family: roboto; font-size: 15px;">


  <div class="print">
    <div class="row">
      <div class="col-xs-12" style="overflow:auto;">
        <h4  ng-bind-html="modalTitle"></h4>
        <div class="row ">
          <div class="col-xs-12 pull-right ">
              <div id="barcode">
                  <ng-barcode
                      ng-barcode-input="{{barcode}}"
                      ng-barcode-code="code39"
                      ng-barcode-color="#000"
                      ng-barcode-background="#fff">
                  </ng-barcode>
                  
                  <span>{{barcode}}</span>
              </div>
              
              <div>
                
                <script>                 
                  // var genBarcode = function(keyCode){

                  //     JsBarcode("#barcode0", keyCode+"",{
                  //       format:"code128",
                  //       displayValue:true,
                  //       fontSize:10,
                  //       height:30,
                  //     });

                  //   };
                    //setTimeout(repeat,500);
                    
                </script>
              </div>
          </div>
        </div>
        <table class="table table-bordered" style="height:100%;">


          <thead>
            <tr >
              <th style="width:25px;">#</th>
              <th style="width:50px;"><?=Yii::t('common','Image')?></th>
              <th style="width:140px;"><?=Yii::t('common','Code')?></th>
              <th ><?=Yii::t('common','Name')?></th>
              <th class="qty_per text-center"><?=Yii::t('common','Quantity per unit')?></th>
              <th class="qty text-right"><?=Yii::t('common','Quantity')?></th>
              <th ><?=Yii::t('common','Unit')?></th>                
            </tr>
          </thead>


          <tbody>
          
            <tr ng-repeat-start="model in printList" >
              <td ng-init="rowId=$index + 1">{{rowId}}</td>
              <td><img src="{{model.img}}" alt="" class="img-responsive" ng-click="zoomImg()" ></td>
              <td >
                <div class="">
                  <span style="color:#cfcfcf"></span>{{model.code}}
                </div>
                <div class="">
                  <span style="color:#cfcfcf"></span>{{model.barcode}}
                </div>
              </td>
              <td >{{model.desc_th}}</td>
              <td  class="text-center qty_per">{{model.qty_per | number}}</td>
              <td  class="text-right">{{model.qtyprint |number}}</td>
              <td >{{model.unit}}</td>                
            </tr>



              <tr  ng-repeat-start="rm in model.child" class="item-child">
                <td ></td>            
                <td class="text-center" ng-init="subRowId=rowId +'.'+($index + 1)">{{subRowId}}</td>
                <td><img src="{{rm.img}}" class="img-responsive" style="width:30px;"> <span class="child-code">{{rm.code}}</span></td>             
                <td><div>{{rm.desc}}</div></td>
                <td class="text-right qty_per">{{rm.qtyprint | number}}</td>
                <td class="text-right">{{rm.qtyprint*model.qtyprint | number}}</td>
                <td>{{rm.unit}}</td>
              </tr>  




                <tr ng-repeat-start="subrm in rm.child" class="item-child">
                  <td></td>            
                  <td class="text-center" ng-init="subRowId2=subRowId +'.'+($index + 1)"></td>
                  <td>
                    <div class="row">
                      <div class="col-xs-4" ><div style="width:30px; margin-right:10px; font-size:9px;">{{subRowId2}}</div></div>
                      <div class="col-xs-8" style="border-left:1px solid #ccc; ">
                        <div class="row">
                          
                          <div class="col-xs-12 text-left"><img src="{{subrm.img}}" class="img-responsive" style="width:30px;"></div>
                          <div class="col-xs-12 text-left child-code">{{subrm.code}}</div>
                        </div>
                      </div>
                    </div>
                  </td>             
                  <td><div>{{subrm.desc}}</div></td>
                  <td class="text-right qty_per">{{subrm.qtyprint | number}}</td>
                  <td class="text-right">{{subrm.qtyprint*(rm.qtyprint*model.qtyprint) | number}}</td>
                  <td>{{subrm.unit}}</td>
                </tr>   
                




                  <tr  ng-repeat-start="subrm2 in subrm.child" class="item-child">
                    <td></td>            
                    <td class="text-center" ng-init="subRowId3=subRowId2 +'.'+($index + 1)"></td>
                    <td>
                      <div class="pull-right" style="width:86px; border-left:1px solid #ccc; padding-left:10px; height:100%;">
                        <div class="row">
                          <div class="col-xs-6 text-left" style="font-size:9px;">{{subRowId3}}</div>
                          <div class="col-xs-6"><img src="{{subrm2.img}}" class="img-responsive" > </div>

                        </div>  
                      </div>
                    </td>             
                    <td>                   
                      <div>{{subrm2.desc}}</div>
                      <span class="child-code_">{{subrm2.code}}</span>
                    </td>
                    <td class="text-right qty_per">{{subrm2.qtyprint | number}}</td>
                    <td class="text-right">{{subrm2.qtyprint*(subrm.qtyprint*(rm.qtyprint*model.qtyprint)) | number}}</td>
                    <td>{{subrm2.unit}}</td>
                  </tr> 




                      <tr ng-if="subrm2.status == true" ng-repeat-start="(key,bom) in subrm2.child track by $index" class="item-child" style="font-size:9px;">
                        <td></td>            
                        <td class="text-center" ng-init="subRowId4=subRowId3 +'.'+($index + 1)"></td>
                        <td></td>             
                        <td> 
                          <div class="pull-left" style="width:85px; border-right:1px solid #ccc; padding-right:10px; margin-right:5px;">{{subRowId4}}
                            <img src="{{bom.img}}" class="img-responsive" style="width:30px;"> 
                          </div>                  
                          <div>{{bom.desc}}</div>
                          <span class="child-code_">{{bom.code}}</span>
                        </td>
                        <td class="text-right qty_per">{{bom.qtyprint | number}}</td>
                        <td class="text-right">{{bom.qtyprint*(subrm2.qtyprint*(subrm.qtyprint*(rm.qtyprint*model.qtyprint))) | number}}</td>
                        <td>{{bom.unit}}</td>
                      </tr>   
                      <tr ng-repeat-end></tr>     



                      

                  <tr ng-repeat-end></tr>     

                <tr ng-repeat-end></tr>     

              <tr ng-repeat-end></tr>     

            <tr ng-repeat-end></tr>
          </tbody>
          <!-- <tbody ng-include="'repeat_data'"></tbody> -->
        </table>
      </div>
    </div>
  </div>
  

  <script type="text/ng-template" id="repeat_data"> 
    <tr>
      <td colspan="7">
        <ul>
          <li ng-repeat="bom in itemList">
            
            <img src="{{bom.img}}" alt="" class="img-responsive" style="width:20px;" >
            <div>{{bom.desc_th}} {{bom.desc}} </div>
            <div>{{bom.code}} </div>

            <div ng-switch on="bom.child.length > 0">
              <div ng-switch-when="true">
                <div ng-init="itemList = bom.child;" ng-include="'repeat_data'"></div>  
              </div>
            </div>

          </li>
        </ul>
      </td>
    </tr>
  </script> 


  
  <div class="content-render">
    <div class="row hidden">
      <div class="col-sm-offset-8">
        <div class="col-sm-12">  
          <?=Yii::t('common','Scan barcode')?>
          <div class="input-group">     
          <span class="input-group-addon"><i class="fa fa-search"></i></span>  
            <input type="text" class="form-control scanBarcode"   ng-model="scanBarcode" ng-keyup="$event.keyCode == 13 ? openModal($event) : null" placeholder="|| |||| |||| || ||||||">
            <span class="input-group-addon"  ><i class="fa fa-barcode"></i></span>
          </div>
        </div>
        
      </div>
    </div>


  
   <div class="row">
      <div class="col-sm-12">
        <div class="box box-solid">
          <div class="box-header with-border">
            <i class="fa fa-text-width"></i>

            <h3 class="box-title">ร้านค้า / General Store</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <?= $this->render('__general_store',['dataProvider' => $dataProvider]) ?>
          </div>
          <!-- /.box-body -->
        </div>        
      </div>
      
      <div class="col-sm-6 hidden">        
        <div class="box box-solid">
          <div class="box-header with-border">
            <i class="fa fa-text-width"></i>

            <h3 class="box-title">ห้าง / Modern Trade</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <?php // $this->render('__modern_trade',['dataProvider' => $moderntrade]) ?>
          </div>
          <!-- /.box-body -->
        </div>  
      </div>
   </div>



    <!-- Modal -->
    <div id="shipModal" class="modal modal-full fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-green-ew">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" ng-bind-html="modalTitle"></h4>
          </div>
          <div class="modal-body table-responsive">
            
            <form class="Shipment" data-key="{{orderId}}" data-no="{{orderNo}}" data-cust="{{custId}}">
            
              <table class="table table-hover table-bordered">
                <thead>
                  <tr class="bg-gray">
                    <th style="width:25px;">#</th>
                    <th style="width:50px;"><?=Yii::t('common','Image')?></th>
                    <th style="max-width:80px;"><?=Yii::t('common','Code')?></th>
                    <th style="min-width:300px;"><?=Yii::t('common','Name')?></th>
                    <th class="text-right"><?=Yii::t('common','Remain')?></th>
                    <th class="text-right bg-yellow" style="width:100px;"><?=Yii::t('common','Need')?></th>   
                    <th class="text-right bg-red" style="width:125px;" title="จำนวนที่จะตัดสต๊อก"><?=Yii::t('common','Quantity Must Produce')?></th>
                    <th><?=Yii::t('common','Unit Of Measure')?></th>
                    <th style="max-width:65px;">
                      <input type="checkbox" ng-model="selectedAll" ng:click="checkAll($event)" id="check-all"/>
                      <label class="pointer" for="check-all"> <?=Yii::t('common','Check All')?></label>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="model in itemList"  class="pointer" style="height:50px;">
                    <td>{{$index +1}}</td>
                    <td><img src="{{model.img}}" alt="" class="img-responsive" ng-click="zoomImg()" style="max-width:50px;"></td>
                    <td ng-click="checked(model,$event)">{{model.code}}</td>
                    <td ng-click="checked(model,$event)" style="max-width:80px;">{{model.desc_th}}</td>

                    <td ng-click="checked(model,$event)" class="text-right"   style="position:relative;"> 

                      <div ng-class="{ 
                            'text-orange'   : model.stock < 0,
                            'text-green'  : model.stock > 0,
                            }" >
                        <span style="position:absolute; right:10px; bottom:10px; z-index:1;"> </span>
                        {{model.stock | number}}
                      </div>
                      
                    </td>

                    <td ng-click="checked(model,$event)" class="text-right bg-warning" style="position:relative; color:blue;">
                      <span style="position:absolute; right:10px; bottom:10px; z-index:1;"> </span>                    
                      <input class="need text-right no-border form-control" type="text" name="{{model.id}}" ng-model="model.need" style="width:100px; float:right;" readonly="readonly" />
                    </td>       

                    <td  class="text-right bg-gray" ng-init="output=(model.qty - model.inven)" style="position:relative;">
                      
                      <div ng-if="model.type == 'Produce'">
                        <span style="position:absolute; left:10px; bottom:10px; z-index:1;"><i class="fas fa-wrench" style="opacity: 0.1;"></i></span>
        
                      
                        <div ng-if="model.qty > model.inven" >
                          <div ng-if="model.inven >= 0" >
                            <input class="output text-right form-control" type="number" name="{{model.id}}" ng-model="model.qty" style="width:100px; float:right;" ng-change="updateOutput(model)" readonly="readonly" />
                          </div>
                          <div ng-if="model.inven < 0" ng-init="output= model.qty">
                            <input class="output text-right form-control" type="number" name="{{model.id}}" ng-model="model.qty" style="width:100px; float:right;"  ng-change="updateOutput(model)" readonly="readonly"/>
                          </div>
                        </div>

                        <div ng-if="model.qty <= model.inven" ng-init="output = 0">
                          <input class="output text-right form-control" type="number" name="{{model.id}}" ng-model="model.qty" style="width:100px; float:right;" ng-change="updateOutput(model)" />
                        </div>
                      </div>

                      <div ng-if="model.type == null "> <?=Yii::t('common','Not set Replenishment')?> </div>

                      <div ng-if="model.type == 'Purchase'" ng-init="output = 0">
                        <input class="output text-right no-border form-control" type="number" name="{{model.id}}" ng-model="output" style="width:100px; float:right;" readonly="readonly" />
                      </div>


                    </td>

                    <td ng-click="checked(model,$event)">{{model.unit}}</td>

                    <td ng-click="checked(model,$event)" style="position:relative;" >
                      <input type="checkbox" ng-checked="model.selected" data-key="{{model.id}}"/>  <?=Yii::t('common','Check')?>
                      <small style="position:absolute; color:#bababa; left:25px; bottom:15px;"> {{model.time | date:'HH:mm:ss'}} </small>
                    </td>
                    
                  </tr>
                </tbody>
              </table>

              <div class="row">
                <div class="col-sm-12 text-right" ng-if="itemList[0].confirm <= 0">
                  <button type="button" name="button" class="btn btn-default btn-confirm" disabled ng-click="confirmCheckList($event)"><i class="fa fa-check"></i> <?=Yii::t('common','Confirm')?></button>
                  <!-- <button type="button" name="button" class="btn btn-default btn-confirm" disabled ng-click="postShipment($event)"><i class="fa fa-check"></i> <?=Yii::t('common','Confirm')?></button> -->
                </div>
                <div class="col-sm-12 text-right text-orange" ng-if="itemList[0].confirm > 0">เอกสารถูก <?=Yii::t('common','Confirmed')?> ไปแล้ว</div>
              </div>

            </form>

          </div>
          <div class="modal-footer" >
          
            <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>
            
            <div class="center-block" style="width:400px; margin: 0 auto !important;">

              <button type="button" class="btn btn-info-ew btn-flat hidden" style="color:#fff;" ng-click="nomalPrint()">
              <i class="fa fa-print text-info" aria-hidden="true"></i> <?=Yii::t('common','Print')?></button>

              <button type="button" class="btn btn-info-ew btn-flat hidden" style="color:#fff;" ng-click="bomPrint()">
              <i class="fa fa-sitemap text-warning" aria-hidden="true"></i> <?=Yii::t('common','BOM')?></button>

              <a class="btn btn-info-ew btn-flat hidden" style="color:#fff;" href="index.php?r=customers/customer/print-ship&id={{custId}}" target="_blank">
              <i class="fa fa-cube text-danger" aria-hidden="true"></i> <?=Yii::t('common','Print-Label')?></a>

              <a class="btn btn-info-ew btn-flat hidden" style="color:#fff;" href="index.php?r=SaleOrders/saleorder/print-page&id={{orderId}}&footer=1" target="_blank">
              <i class="fa fa-file-text text-info" aria-hidden="true"></i> <?=Yii::t('common','Sale Order')?></a>

            </div>
          
          </div>
        </div>

      </div>
    </div>


  </div>
</div>
 
<?php $this->registerJsFile('js/warehouse/shipmentController.js?v=5.08.17');?>
<?php //$this->registerJsFile('@web/dist/JsBarcode.all.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>
<div class="loading" style="position:absolute; top:20%; left:50%; display:none; z-index:2000;">
    <i class="fab fa-react fa-spin fa-5x text-aqua"></i>
    <h3 class="text-red blink" style="margin-left:-10px;">Loading...</h3>
</div>

 
<?php

$js =<<<JS
 

  const CheckJob = (obj, callback) =>  {
    fetch("?r=warehousemoving/default/count-job", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);             
    })
    .catch(error => {
        console.log(error);
    });
  }

  // Start Notify
    var eventClick = function() {
        window.location.href = "?r=warehousemoving";
    };

    var myIcon = "images/blockdevice.png";

    const notifyNow = (obj) => {
    
      if(obj.qty > 0){    
        $("#easyNotify").easyNotify({
          title: "Confirm",
          options: {
            body: obj.qty + " Jobs",
            icon: myIcon,
            lang: "TH",
            onClick: eventClick
          }
        });
      }
            
    }

  // END Notify
 
    

    var timer;
    $(document).on('mousemove', function(e){
      clearInterval(timer);

      timer = setInterval(function() {
        location.reload();
      }, 300000);
    });



    $(document).ready(function(){


      CheckJob({limit:'false'}, res =>{
        notifyNow({qty:res.qty});
      });


      timer = setInterval(function() {
        
        CheckJob({limit:'false'}, res =>{
          notifyNow({qty:res.qty});
        });

        setTimeout(() => {
          location.reload();
        }, 1000);
        

      }, 300000);

      

      setTimeout(() => {
        $("body").addClass("sidebar-collapse").find(".user-panel").hide();  
        $('input.scanBarcode').val('').focus();            
      }, 50);

      setTimeout(() => { 
        $('body').find('.user-panel').hide(); 
        $('.fade-in-onload').fadeIn('slow');   
      }, 1500);

      setTimeout(() => { 
        $('.fade-in-onload').fadeIn('slow');   
      }, 1000);

    });


JS;

$this->registerJs($js,\yii\web\View::POS_END);

?>
