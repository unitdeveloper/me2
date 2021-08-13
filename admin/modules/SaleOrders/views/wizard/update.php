<?php
use yii\helpers\Html;
?>
<div class="row">
    <section>
        <div class="wizard">
            <div class="wizard-inner wizard-4">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="<?=Yii::t('common','Select Customer')?>">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-user"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="<?=Yii::t('common','Upload')?>">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-open-file"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="<?=Yii::t('common','Tax Invoice')?>">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-print"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="<?=Yii::t('common','Complete')?>">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-ok"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            
            
            <div class="tab-content col-xs-12 ">
                <div class="tab-pane active text-center " role="tabpanel" id="step1">
                    <h3 class="mb-10"><?=Yii::t('common','Select Customer')?></h3>
                    <a class="btn btn-warning my-10" data-toggle="modal" href='#modal-pick-customer-wizard'><i class="fas fa-tasks"></i> <?=Yii::t('common','Select Customer')?></a>
                    <div class="row mt-10">
                        <div class="col-sm-12">
                            <div class="mt-10"><h4><label></label> <span class="cust-code"></span></h4></div>
                            <div class="mt-10"><h4><label></label> <span class="cust-name"></span></h4></div>
                        </div>
                    </div>

                    <ul class="list-inline pull-right">
                        <li><?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Home'),['/SaleOrders/wizard/index'],['class' => 'btn btn-default-ew text-gray'])?></li>
                        <li><button type="button"  class="btn btn-success-ew text-green next-step"><?=Yii::t('common','Next')?> <i class="fas fa-step-forward"></i></button></li>
                    </ul>
                    
                </div>
                <div class="tab-pane render-sale-line" role="tabpanel" id="step2">            
                    <?=$this->render('_sale_line',[
                        'model' => $model, 
                        'text' => $text,
                        'page' => $page
                    ])?>
                    <ul class="list-inline pull-right">
                        <li><?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Home'),['/SaleOrders/wizard/index'],['class' => 'btn btn-default-ew text-gray'])?></li>
                        <li><button type="button" class="btn btn-info-ew text-aqua prev-step"><i class="fas fa-step-backward"></i> <?=Yii::t('common','Back')?> </button></li>
                        <li><button type="button" id="btn-create-sale-line" class="btn btn-warning-ew text-warning next-step create-sale-line"><?=Yii::t('common','Next')?> <i class="fas fa-step-forward"></i></button></li>
                    </ul>
                </div>
                <div class="tab-pane" role="tabpanel" id="step3">
                    <h3>Document</h3>
                     
                    <div class="panel-group" id="accordion" style="margin-bottom: 150px;">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1"><?=Yii::t('common','Sale Order')?></a>
                                </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse in">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div  style="border:1px solid #ccc; min-height: 70px;">
                                                <h4 class="ml-5"><?=Yii::t('common','Sale order')?> : <i class="fas fa-print"></i> <?= Html::a('',
                                                ['/SaleOrders/saleorder/print', ['id' => '', 'footer' => 1]],
                                                ['class' => 'SALEORDER-NUMBER', 'target' => '_blank']) ?></h4>
                                                <h4 class="ml-5"><?=Yii::t('common','Customer')?> : [<span class="cust-code"></span>]  <span class="cust-name"></span></h4>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div  style="border:1px solid #ccc; min-height: 70px;">
                                                <h4 class="ml-5">
                                                    <?=Yii::t('common','Tax Invoice')?> : <i class="fas fa-print"></i> <?= Html::a('',
                                                                                    ['/accounting/posted/posted-invoice', ['id' => '']],
                                                                                    ['class' => 'INVOICE-NUMBER', 'target' => '_blank']) ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="renders-editable">Loading</div>      
                                    <div class="row">
                                        <div class="col-sm-4 pull-right">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="text-right"><?=Yii::t('common','Sum total')?></td>
                                                    <td id="sum-total" class="text-right">0</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"><?=Yii::t('common','Vat')?> 7%</td>
                                                    <td id="sum-vat" class="text-right">0</td>
                                                </tr>
                                                <tr class="bg-gray">
                                                    <td class="text-right"><?=Yii::t('common','Grand total')?></td>
                                                    <td id="grand-total" class="text-right">0</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>                         
                                
                            </div>
                        </div>
                         
                    </div> 
                    <ul class="list-inline pull-right">
                        <li><?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Home'),['/SaleOrders/wizard/index'],['class' => 'btn btn-default-ew text-gray'])?></li>
                        <li><button type="button" class="btn btn-info-ew text-aqua prev-step"><i class="fas fa-step-backward"></i> <?=Yii::t('common','Back')?></button></li>   
                        <li><button type="button" class="btn btn-success-ew text-green btn-info-full next-step"><?=Yii::t('common','Next')?> <i class="fas fa-step-forward"></i></button></li>                      
                    </ul>
                </div>
                <div class="tab-pane" role="tabpanel" id="complete">
                    <h3><?=Yii::t('common','Tax Invoice')?></h3>
                    <p>This is step 3</p>
                    <ul class="list-inline pull-right">
                        <li><?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Home'),['/SaleOrders/wizard/index'],['class' => 'btn btn-default-ew text-gray'])?></li>
                        <li><button type="button" class="btn btn-info-ew text-aqua prev-step"><i class="fas fa-step-backward"></i> <?=Yii::t('common','Back')?></button></li>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
             
        </div>
    </section>
</div>


<div class="modal fade " id="modal-pick-customer-wizard" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Customer')?></h4>
            </div>
           
            <div class="modal-body">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-6 pull-right">
                        <form name="search">
                            <div class="input-group"  >
                                <input type="text" name="search" class="form-control" autocomplate="off" placeholder="<?=Yii::t('common','Search')?>" />                 
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" id="renderCustomer"></div>
                </div>
            </div>
             
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <i class="fas fa-power-off"></i> Close</button>
                 
            </div>
        </div>
    </div>
</div>


<?php $this->registerCssFile('css/wizard.css?v=4',['rel' => 'stylesheet','type' => 'text/css']);?>
<?php $this->registerJsFile('@web/js/saleorders/wizard.js?v=4.03.20',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/saleorders/import.js?v=4.03.20',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/saleorders/editable.js?v=4.03.20',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

 