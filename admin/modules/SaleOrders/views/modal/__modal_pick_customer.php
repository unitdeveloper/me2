<style>
/* #ewPickCustomer .modal-dialog  {width:80%;}*/
@media screen and (min-width: 768px) {
    #ewPickCustomer .modal-dialog  {width:80%;}
    #ewPickCustomer .modal-body  {max-height: calc(100vh - 210px);overflow-y: auto;}
} 

</style>
<!-- <?=__FILE__?> -->
<div id="ewPickCustomer" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title ew-title-pic-cust"><?=Yii::t('common','Select Customer') ?></h4>
      </div>
      <div class="Smooth-Ajax">
        <div class="modal-body">


        <!--row -->
        <div class="row">
          <div class="col-sm-4">
           
          <div class="input-group">
            <input type="text" name="q" class="form-control" id='ew-search-text' placeholder="<?=Yii::t('common','Search');?>..."/>
            <span class="input-group-btn">
              <button type='button' name='search' id='ew-search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
          </div>
            
          </div>
        </div>
        <!--/.row -->

        <div class="ew-Pick-Customer">Loading ....
          
        </div>
        </div>
      </div>

      <div class="modal-footer" >

          <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off" aria-hidden="true"></i>   <?=Yii::t('common','Close');?></button>    


      </div>
    </div>
    
  </div>

  
</div>
