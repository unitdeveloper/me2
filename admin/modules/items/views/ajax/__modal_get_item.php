<!-- Modal ewGetItemModal-->
<div id="ewGetItemModal" class="modal modal-full fade" role="dialog" >
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close ew-inc-close-pic-item" >&times;</button>
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
              <button type='button' name='search' id='ew-search-items-btn' class="btn btn-default btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
          </div>
            
          </div>
        </div>
        <!--/.row -->

        <div class="ew-Pick-Inc-Item">
          <div class="ewTimeout"><i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i><div class="blink"> Loading .... </div></div>
      <script type="text/javascript">
        setTimeout(function(){ $('.ewTimeout').html('<span style="color:red"><?=Yii::t('common','Server not responding.')?>...</span>');}, 10000);
      </script> 
          
        </div>
        <div class="ew-render-after-created"></div>
        </div>
      </div>
    
      <div class="modal-footer" >

    <button type="button" class="btn btn-default-ew pull-left ew-inc-close-pic-item" ><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>    

    <a type="button" class="btn btn-default-ew ew-pick-item-to-inc-line"> <i class="fa fa-check" aria-hidden="true"></i>  <?=Yii::t('common','Select')?></a>

      </div>
    </div>
    
  </div>

  
</div> 
<div class="Xtest"></div>