<?php
use admin\modules\SaleOrders\models\FunctionSaleOrder;
//header('Content-type: application/json;charset=utf-8');

$FincSale = new FunctionSaleOrder();
?>
<!-- _modal_pickitem -->
<style>
    @media only screen and (max-width: 480px) {
        .touch .modal .modal-body {
            max-height: none; 
        }
    }
    @media (max-width: 425px) {
        ._radio{
            width:100%;
            text-align:left;
            margin:0px 0px 5px 0px;
        }
        
    }
</style>
<div>

    <div class="row">
        <div class="col-sm-3 text-center" >
            <img class="img-responsive ew-itemset-pic" src="images/icon/load.gif"  >
            
        </div>
        <div class="col-sm-9" >
            <div class=" ">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="ew-getItem-Set">
                            <?= $FincSale->getItemSetLoad(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
    <div class="box box-success renders-box" style="margin-top:15px; display:none;">
        <div class="box-header with-border">
            <h4><label><div class="ew-render-itemno" id="ew-render-itemno"></div></label></h4>                    
        </div>
        <div id="ItemName"></div>
        <div class=" ">
            <input type="hidden" class="form-control" id="itemno" value="<?=$_POST['param']['itemno'];?>" >
            <input type="hidden" class="form-control" id="itemset" value="<?=$_POST['param']['itemset'];?>" >            
        </div>
        <div class="box-body">
            <div class="ew-render-item"></div> 
            <div class="" style="margin-top:10px;"><?=Yii::t('common','Stock')?> <i class="fas fa-archive"></i> : 
                <span class="text-amount" style="padding: 0px 5px 0px 5px;">  </span>
            </div>    
        </div>
        <div class="box-footer">
            <div class="row">
                <div class="col-xs-6">
                    <label for="inputEmail3" class="col-sm-6 control-label" ><?=Yii::t('common','Quantity')?></label>
                    <div class="col-sm-6">
                        <input type="number" step=any pattern="[0-9.]*" class="form-control text-right" id="ew-amount" value="1">
                    </div>
                </div>
                <div class="col-xs-6">
                    <label for="inputEmail3" class="col-sm-6 control-label"><?=Yii::t('common','Price')?></label>
                    <div class="col-sm-6">
                        <input type="number" step=any pattern="[0-9.]*" class="form-control text-right" id="ew-price" value="100" >
                    </div>
                </div>
            </div>
        </div>
    <!-- /.box-body -->
    </div>
        
</div>
