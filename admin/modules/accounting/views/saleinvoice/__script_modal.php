<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<style>
@media screen and (min-width: 768px) {
    #ewPickCustomer .modal-dialog  {width:80%;}
    #ewPickCustomer .modal-body  {max-height: calc(100vh - 210px);overflow-y: auto;}
} 

</style>

<!-- Modal Getsource-->
<div id="ew-modal-source" class="modal  fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green">
        <button type="button" class="close close-modal" >&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Select Source')?>  </h4>
      </div>
      <?php $form = ActiveForm::begin(); ?>
      
      <div class="modal-body ew-source-body" style="">
        <div class="loading"></div> <p>Loading . . .</p>
      </div>       
      <div class="modal-footer">
        <a type="button" class="btn btn-default-ew pull-left  close-modal"><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></a>

        <a type="button" class="btn btn-default-ew ew-get-ship"> <i class="fa fa-check" aria-hidden="true"></i>  <?=Yii::t('common','Select')?></a>
        
      </div>
      <?php ActiveForm::end(); ?>
    </div>
    <!-- /. Modal content-->
  </div>
</div>
<!-- /.Modal Getsource-->






<!-- Modal ewPickCustomer-->
<div id="ewPickCustomer" class="modal fade" role="dialog" >
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close ew-inv-close-pic-cus" >&times;</button>
        <h4 class="modal-title ew-title-pic-cust"><?=Yii::t('common','Select Customer') ?></h4>
      </div>
      <div class="Smooth-Ajax">
        <div class="modal-body">


        <!--row -->
        <div class="row">
          <div class="col-sm-4">
           
          <div class="input-group margin">
            <input type="text" name="q" class="form-control" id='ew-search-cust-text' placeholder="<?=Yii::t('common','Search');?>..."/>
            <span class="input-group-btn">
              <button type='button' name='search' id='ew-search-cust-btn' class="btn btn-default-ew btn-flat"><i class="fa fa-search"></i></button>
            </span>
          </div>
            
          </div>
        </div>
        <!--/.row -->

        <div class="ew-Render-Pick-Inv-Customer">Loading ....
          
        </div>
        </div>
      </div>

      <div class="modal-footer" >

          <button type="button" class="btn btn-default-ew pull-left ew-inv-close-pic-cus" ><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>    


      </div>
    </div>
    
  </div>

  
</div>







<!-- Modal ewGetItemModal-->
<div id="ewGetItemModal" class="modal modal-full fade" role="dialog" >
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close ew-inv-close-pic-item" >&times;</button>
        <h4 class="modal-title ew-title-pic-cust"><?=Yii::t('common','Please choose a product that you want.') ?></h4>
      </div>
      <div class="Smooth-Ajax">
        <div class="modal-body">


        <!--row -->
        <div class="row">
          <div class="col-sm-4">
           
          <div class="input-group">
            <input type="text" name="q" class="form-control" id='ew-search-items-text' placeholder="<?=Yii::t('common','Search');?>..."/>
            <span class="input-group-btn">
              <button type='button' name='search' id='ew-search-items-btn' class="btn  btn-default-ew btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
          </div>
            
          </div>
        </div>
        <!--/.row -->

        <div class="ew-Pick-Inv-Item">
          <div class="ewTimeout"><i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i><div class="blink"> Loading .... </div></div>
      <script type="text/javascript">
        setTimeout(function(){ $('.ewTimeout').html('<span style="color:red"><?=Yii::t('common','Server not responding.')?>...</span>');}, 10000);
      </script> 
          
        </div>
        <div class="ew-render-after-created"></div>
        </div>
      </div>
    
      <div class="modal-footer" >

    <button type="button" class="btn btn-default-ew pull-left ew-inv-close-pic-item" ><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>    

    <a type="button" class="btn btn-default-ew ew-pick-item-to-inv-line"> <i class="fa fa-check" aria-hidden="true"></i>  <?=Yii::t('common','Select')?></a>

      </div>
    </div>
    
  </div>

  
</div> 
<div class="Xtest"></div>










<!-- Modal Add-series-->
<div id="ew-add-series" class="modal modal-full fade" role="dialog" data-backdrop="static" data-keyboard="true">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <div class="modal-header">
        
        <button type="button" class="close add-series-close-modal">&times;</button>

        <h4 class="modal-title pull-left" style="padding-right: 20px;"><?=Yii::t('common','Create number series')?></h4>

        <a href="#" class="link" id="modal-back" >< Back</a>
      </div>
      <ew class="ew-condition"></ew>
      <div class="modal-body ew-series-body"  >

        <div class="loading"></div> Loading...

      </div>

      <div class="modal-footer" >

        <button type="button" class="btn btn-default add-series-close-modal pull-left" >Close</button>

        <button type="button" class="btn btn-default ew-save-modal-common" onclick="" >Save</button>

        <div class="ew-menu-center text-center hidden-xs hidden-sm">

          <a href="#" class="link"><i class="fa fa-print" aria-hidden="true"></i> Print</a>

          <a href="#" class="link"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
         
        </div>

        
        
      </div>
    </div>

  </div>

</div>



 
<!-- Modal data-keyboard="false" data-backdrop="static"-->
<div id="RunNoSeries" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="data-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default  pull-left close-modal-cheque" data-dismiss="modal">
        <i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>    
      </div>
    </div>

  </div>
</div>

<!-- /.Modal Add-series-->
