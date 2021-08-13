<?php 
//$item = Items::findOne();


?> 
<div id="ewItemInfoModal" class="modal fade" role="dialog" >
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header bg-green-ew">
        <button type="button" class="close close-ewItemInfoModal" >&times;</button>
        <h4 class="modal-title">Product Infomation</h4>
      </div>
      <div class="Smooth-Ajax">
      <div class="modal-body ew-item-info-body" >

        <div class="ew-render-item-info">
          
           
        </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default close-ewItemInfoModal pull-left" ><i class="fa fa-power-off" aria-hidden="true"></i>  <?=Yii::t('common','Close');?></button>

        <button type="button" name="Select" id="PickToSaleLine" class="btn btn-success-ew btn-lg" ><i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Select')?></button>
      </div>
    </div>

  </div>
</div>


