<?php
use admin\modules\Itemset\models\FunctionItemset;
//header('Content-type: application/json;charset=utf-8');

$FincSale = new FunctionItemset();
?>
<!-- _modal_pickitem -->
<div ng-app>

<div class="row">
    <div class="col-sm-4 text-center" >
        <img class="img-responsive ew-itemset-pic" src="images/icon/load.gif" style="max-width:300px;">
        <h3><label><div class="ew-render-itemno" id="ew-render-itemno"></div></label></h3>
    </div>
    <div class="col-sm-8" >
        <div class=" ">
            <div class=" ">
                <div class="ew-getItem-Set">
                <?= $FincSale->getItemSetLoad(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box box-warning">
    <div class="box-header with-border">
        <div id="ItemName"></div><input type="hidden" class="form-control" id="itemno" value="<?=$_POST['param']['itemno'];?>" >
        <input type="hidden" class="form-control" id="itemset" value="<?=$_POST['param']['itemset'];?>" >
        <h3 class="box-title"><label><div class="ew-render-item"></div> </label></h3>
        <div class="row">
            <div class="col-md-6"><?=Yii::t('common','Remaining')?> : <span class="text-amount">  </span></div>
        </div>
    </div>
    <div class="box-body">
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
