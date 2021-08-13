<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Planning\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Setup Safety Stock');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row" style="background-color:#ffe8eb; height:50px; padding-top:10px; margin-top:-15px; border-bottom:1px solid #ccc; margin-bottom:15px;">
    <div class="col-xs-8">
        <div class="input-group hidden-sm hidden-md hidden-lg">
            <input id="search" class="form-control" type="text" placeholder="<?=Yii::t('common','Filter')?>..." />
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
        </div> 
    </div>
    <div class="col-xs-4  text-right"><?=Html::a('<i class="far fa-eye"></i> '.Yii::t('common','Preview'),['safety-stock'],['class' => 'btn btn-info'])?></div>
</div>
<div class="modal fade" id="modal-customize" data-key="0">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <label><?=Yii::t('common','Name')?></label>
                <input type="text" name="name" readonly class="form-control" />

                <div class="row">
                    <div class="col-xs-4">                
                        <label class="mt-10"><?=Yii::t('common','Safety Stock')?></label>
                        <input type="number" name="safety"  class="form-control text-right" autocomplete="off" style="background-color: #f2ffc0;" />
                    </div>

                    <div class="col-xs-4">
                        <label class="mt-10"><?=Yii::t('common','Reorder Point')?></label>
                        <input type="number" name="reorder" class="form-control  text-right"  autocomplete="off" style="background-color: #c0f0ff;"/>
                    </div>

                    <div class="col-xs-4">
                        <label class="mt-10"><?=Yii::t('common','Mininum Stock')?></label>
                        <input type="number" name="minimum" class="form-control  text-right"  autocomplete="off" style="background-color: #edc0ff;" />
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-xs-8 img"></div>
                    <div class="col-xs-4">
                        <label class="mt-10"><?=Yii::t('common','Stock')?></label>
                        <input type="text" readonly name="stock" class="form-control text-right" style="background-color: #ffd089;" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-primary save-change"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
            </div>
        </div>
    </div>
</div>

    
     
    <div class="render-table table-responsive"></div>
     
</div>


<?= $this->render('_script') ?>