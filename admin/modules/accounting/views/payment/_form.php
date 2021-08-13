<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

?>
<style>

.list-inline{
    position: fixed;
    bottom: -10px;
    border-top: 1px solid #ccc;
    width: 100%;
    background-color: #eee;
    padding: 10px 10px 15px 10px;
    right: 0px;    
    text-align: center;
}

.progress-inv{
    height:3px;
    width:100%;
    background:#6db5ff;
    margin-top:-15px;
}

.delete-row:hover{
    background:#fff4f4;
}

.tb-rows{
    border-top:2px solid #63ffff !important;
}
</style>
<div class="row">
    <div class="progress-inv"></div>
</div> 
<?php $form = ActiveForm::begin([
                'id'=>'payable-invoice',
                'options' => ['data-key' => $model->id]
                ]); ?>
            
    <div class="row content-button-zone" style="display:none;">
        <div class="col-md-8 col-sm-6"></div>
        <div class="col-md-4 col-sm-6 text-right">
            <div class="form-group">                        
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save data') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save data'), 
                [
                    'class' => $model->isNewRecord ? 'btn btn-info-ew' : 'btn btn-primary-ew'
                ]) ?>
                <span class="btn btn-danger-ew ew-confirm-post"><i class="fa fa-server" aria-hidden="true"></i> <?=Yii::t('common','Post');?></span>
            </div>
        </div>
    </div>

    <div class="content-step-vendor" style="display:none;">
        <div class="row" style="margin-top:150px;">
            <div class="col-xs-12 text-center" >
                <h3 class="mb-10"><?=Yii::t('common','Select Vendors')?></h3>
                <a class="btn btn-warning my-10" data-toggle="modal" href='#modal-pick-vendors-wizard'><i class="fas fa-tasks"></i> <?=Yii::t('common','Select Vendors')?></a>
                <div class="row">
                    <div class="col-sm-12  mt-10">
                        <div class="mt-10"><h4><label></label> <a href="#" target="_blank" class="vendor-code"></a></h4></div>
                        <div class="mt-10"><h4><label></label> <span class="vendor-name"></span></h4></div>
                    </div>
                    <div class="col-sm-12 text-center mt-10 hidden">
                        <select class="form-control" style="max-width:150px; margin:auto;" name="payment_term">
                                <option value='0'> <?=Yii::t('common','Cash')?></option>
                                <option value='7'> 7 <?=Yii::t('common','Day')?></option>
                                <option value='15' >  15  <?=Yii::t('common','Day')?></option>
                                <option value='30' >  30  <?=Yii::t('common','Day')?></option>
                                <option value='45' >  45  <?=Yii::t('common','Day')?></option>
                                <option value='60' >  60 <?=Yii::t('common','Day')?></option>
                                <option value='90' >  90  <?=Yii::t('common','Day')?></option>
                        </select>
                        
                    </div>
                </div>
                <ul class="list-inline pull-right">
                    <li><?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Home'),['/accounting/payment/index'],['class' => 'btn btn-default-ew text-gray'])?></li>
                    <li><button type="button"  class="btn btn-info-ew next-step next-to-create-line"><?=Yii::t('common','Next')?> <i class="fas fa-step-forward"></i></button></li>
                </ul>
            </div>
        </div>     
    </div>
    <div class="content-step-edit" style="display:none;">

        <div class="row">
            <div class="col-xs-12 my-10 text-right">
                <div class="btn btn-primary open-modal-get-source"><i class="fas fa-search"></i> <?= Yii::t('common','Get Purchase Order')?></div>                 
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3 my-10 pull-right">     
                <label><?= Yii::t('common','Invoice Number')?></label>            
                <input type="text" class="form-control" value="" name="no" />
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3 my-10 pull-right">     
                <label><?= Yii::t('common','Invoice Date')?></label>            
                <?php 
                    echo DatePicker::widget([
                        'name'      => 'inv_date',
                        'value'     => date('Y-m-d'),        
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format'    => 'yyyy-mm-dd'
                        ],
                        'options'   => ['autocomplete' => 'off']                            
                    ]);
                ?>
            </div>
            <div class="col-xs-3 my-10 pull-right">     
                <label><?= Yii::t('common','External Document')?></label>            
                <input type="text" name="ext_document" class="form-control"/>
            </div>
        </div>
                
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive" id="Payment-Line"></div>
                <?= $this->render('_total')?>
                <ul class="list-inline pull-right">                    
                     
                    <li><button type="button"  class="btn btn-warning-ew prev-step back-to-pick-vendor"><i class="fas fa-step-backward"></i> <?=Yii::t('common','Vendor')?> </button></li>
                    <li><button type="button"  class="btn btn-success-ew next-step save-this-page"> <i class="fas fa-save"></i>  <?=Yii::t('common','Save')?></button></li>
                    <li><button type="button"  class="btn btn-info-ew next-step next-to-finish"><?=Yii::t('common','Payment')?> <i class="fas fa-step-forward"></i> </button></li>
                    <div class="pull-right hidden" style="padding-top:3px;">
                        <span class="grandTotalPayment" style="color:#27ff00; font-size:18px;"></span>
                    </div>
                </ul>
            </div>
        </div>

    </div>

    <div class="content-step-success" style="display:none;">
        <div class="row">
            <div class="col-xs-12 my-10 text-right">
                <div class="btn btn-primary">No : <span class="payment-no"></span></div>
            </div>
        </div>
                
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive" id="Payment-results"></div>
                <ul class="list-inline pull-right">                    
                    <li><?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Home'),['/accounting/payment/index'],['class' => 'btn btn-default-ew text-gray'])?></li>
                    <li><button type="button"  class="btn btn-info-ew prev-step next-to-create-line"><i class="fas fa-step-backward"></i> <?=Yii::t('common','Back')?> </button></li>
                    <li>
                        <div class="btn-group dropup">
                            <button type="button" class="btn btn-success-ew"><i class="fas fa-print"></i> <?=Yii::t('common','Print')?> </button>
                            <button type="button" class="btn btn-success-ew dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#" class="save-exit"><i class="fas fa-save"></i> <?=Yii::t('common','Save & Exit')?></a></li>
                                <li><a href="#" class="Clear">Another action</a></li>
                                <li><a href="#">Something else here</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Separated link</a></li>
                            </ul>
                        </div>
                    </li> 
                </ul>
            </div>
        </div>           

    </div>

<?php ActiveForm::end(); ?>


<div class="modal fade" id="modal-get-source">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>                 
            </div>
        </div>
    </div>
</div>
 

<?= $this->render('_pay')?>
<?= $this->render('_script_js', ['model' => $model])?>
<?= $this->render('_pick_vendors', ['model' => $model])?>
<?= $this->render('_script_getsource', ['model' => $model])?>
<?php 
//http://numeraljs.com/ 
//$this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
 ?>
