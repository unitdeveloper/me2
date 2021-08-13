<div id="ewSaleInvoiceModal" class="modal modal-full fade" role="dialog" >
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close close-inv-modal"  >&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Sale Invoice')?></h4>
      </div>
      <div class="Smooth-Ajax">
        <div class="modal-body ew-render-create-invlice" >
          <div class="SaleLine">    
             <?php  #echo $this->render('../saleinv/_sale_inv',['dataProvider' => $dataProvider,'model' => $model]); ?>
          </div>
        </div>
      </div>  
      <div class="modal-footer">
        <button type="button" class="btn btn-default-ew pull-left close-inv-modal"  ><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>    
      </div>  
    </div>   
  </div>
</div>





 

 
