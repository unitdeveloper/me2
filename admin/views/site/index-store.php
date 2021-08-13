<?php
use yii\helpers\Html;
?>
<div class="content">
    <div class="row">
        <div class="col-sm-12"><h3><?=Yii::t('common','Stock Movement')?></h3> </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="col-sm-3 col-xs-6">
                <?=Html::a('
                <img src="images/icon/adjust-.png" class="img-responsive" style="max-width:150px;" />
                <div>'.Yii::t('common','Stock Adjust').'</div>
                ',['/warehousemoving/stock'],['class' => 'btn btn-primary'])?>
            </div>
            <div class="col-sm-3 col-xs-6">
                <?=Html::a('
                    <img src="images/icon/chart.png" class="img-responsive" style="max-width:150px;" />
                    <div>'.Yii::t('common','Stock Report').'</div>
                    ',['/warehousemoving/stock-report'],['class' => 'btn btn-warning'])?>
            </div>
        </div>
    </div>
</div>
<?=$this->render('widget')?>
