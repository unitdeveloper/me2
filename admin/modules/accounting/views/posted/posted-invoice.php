<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;
use kartik\widgets\DatePicker;

use kartik\grid\GridView;
//use yii\grid\GridView;

use common\models\Items;

$Bahttext = new FunctionBahttext();
$Fnc = new FunctionSaleOrder();
/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */

$this->title = $model->no_;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_no_, 'url' => ['view', 'id' => $model->document_no_]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?> 

<div class="sale-invoice-header-update" ng-init="Title='<?=$model->no_?>'" style="font-family: saraban;">     
    <div class="panel <?=($model->creditnote)? 'panel-warning' : 'panel-default'; ?>">
        <!-- Invoice Header  -->
        <div class="panel-heading">
            <div class="row">
                    <div class="col-xs-12" >
                    <div class="pull-right">
                    <?php if($model->no_ == ''): ?>
                        <a href="#" id="ew-get-source" class="btn btn-primary"  data-toggle="modal" data-target="#ew-modal-source"><i class="fa fa-search-plus"></i> <?=Yii::t('common','Get source document') ?></a>
                    <?php else:  ?>                         
                        <h4>เลขที่ :  <?=$model->no_?>  </h4> 
                    <?php endif; ?>
                    </div>
                    <div><h4><i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i> ใบกำกับภาษี / ใบส่งสินค้า</h4></div>
                </div>
            </div>
        </div>
        <div class="panel-body" >
            <?php $form = ActiveForm::begin(['id' => $model->id]); ?>
            <div class="row">
                <div class="col-sm-4"></div>
            </div>
            
            <div class="row">
                <div class="col-sm-offset-6 col-md-offset-7 col-lg-offset-8">
                    
                    <?php if($model->creditnote){ ?>
                        <?php 
                        $total = 0;
                        foreach ($model->creditnote as $key => $value) {
                        
                            echo '<div class="col-md-12"  style="margin-bottom:20px;">   
                                                        
                                        '.Html::a('<div class="pull-left"><i class="fab fa-centercode fa-spin text-orange"></i> ใบลดหนี้ เลขที่ : '.$value->no_ .'</div>
                                        <div class="pull-right">'.number_format($value->sumLine,2).'</div>' , 
                                        ['/accounting/credit-note/view', 'id' => base64_encode($value->id),'no' => $value->no_], 
                                        [
                                            'class' => 'btn btn-danger-ew btn-flat',  
                                            'style' => 'width:100%;'                      
                                        ]).'                                                                  
                                </div>';
                            $total += $value->sumLine;     
                        }

                            $colorStatus = (abs($total)==abs($model->sumLine))? 'btn-success' : 'btn-warning' ;

                            echo '<div class="col-md-12 text-right"  style="margin-bottom:20px;">
                                    <span class="btn '.$colorStatus.'  btn-flat" style="width:70%;">
                                    <div class="pull-left">'.Yii::t("common","Total").'</div>
                                    <div class="pull-right ">'.number_format($total,2).'</div></span> 
                                  </div>';
                        ?>
                    
                    <?php } ?>
                      
                </div>
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="cust_code"><?=Yii::t('common','Customer Code')?></label>
                            <a href="?r=customers%2Fcustomer%2Fview&id=<?=$model->cust_no_?>" target="_blank" class="form-control" id="cust_code" ><?=$model->cust_code?> </a>
                            <?php // $form->field($model, 'cust_code')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                             
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true,'readonly' => 'readonly', 'id' => 'cust_name']) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'cust_address')->textInput(['readonly' => 'readonly']) ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'district')->textInput(['disabled' => true,'value' => $model->customer->locations->tumbol]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'city')->textInput(['disabled' => true,'value' => $model->customer->locations->amphur]) ?>
                        </div>

                        <div class="col-sm-6">
                            <?= $form->field($model, 'province')->textInput(['disabled' => true,'value' => $model->customer->locations->province]) ?>
                        </div>

                        <div class="col-sm-6">
                            <?= $form->field($model, 'postcode')->textInput(['disabled' => true,'value' => $model->customer->locations->zipcode]) ?>
                        </div>
                    </div>  
                </div>   
                <div class="col-sm-4">   
                    <?php                      
                        $Sales = \common\models\SalesPeople::find()
                            ->where(['comp_id'      => Yii::$app->session->get('Rules')['comp_id']])
                            ->andWhere(['status'    => 1])
                            ->orderBy(['code'       => SORT_ASC])
                            ->all();

                        $salespeople = arrayHelper::map($Sales,'id', function ($element) {
                            return '['.$element->code .']  ' .$element->name.' '.$element->surname;
                        });

                        echo $form->field($model, 'sale_id') ->dropDownList($salespeople,
                            [
                                'class' => 'sale_id',
                                //'style' => 'margin-top: 25px;',
                                'prompt'=> '- เลือก Sales -',
                            ]
                        )->label(Yii::t('common','Sales'));
                    ?>                                     
                    <?php // $form->field($model, 'sales_people')->textInput(['maxlength' => true,'readonly' => 'readonly','value' => $model->sales ? $model->sales->name : '']) ?>
                    
                </div> 
                <div class="col-sm-2">
                    <?php //form->field($model, 'posting_date')->textInput(['value' => date('Y-m-d',strtotime($model->posting_date))]) ?>
                    <?= $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                          'options' => [
                              'placeholder' => Yii::t('common','Posting Date').'...', 
                              'value'       => date('Y-m-d',strtotime($model->posting_date))
                            ],
                          //'value'   => date('Y-m-d',strtotime($model->posting_date)),
                          'type'    => DatePicker::TYPE_COMPONENT_APPEND,
                          'removeButton'    => false,
                          'pluginOptions'   => [
                              'format'      => 'yyyy-mm-dd',
                              'autoclose'   => true,
                          ]
                    ]); ?> 
                    <?= $form->field($model, 'order_date')->textInput(['readonly' => 'readonly']) ?>
                    <?= $form->field($model, 'ship_date')->textInput(['readonly' => 'readonly']) ?>
                    <?= $form->field($model, 'payment_term')->dropDownList([
                        '0'=> Yii::t('common','Cash'),
                        '7'=> '7 '.Yii::t('common','Day'),
                        '15' => '15 '.Yii::t('common','Day'),
                        '30' => '30 '.Yii::t('common','Day'),
                        '45' => '45 '.Yii::t('common','Day'),
                        '60' => '60 '.Yii::t('common','Day'),
                        '90' => '90 '.Yii::t('common','Day'),
                      ]) 
                    ?>
                </div>
                <div class="col-sm-2">

                    <?= $form->field($model, 'document_no_')->textInput([
                            'maxlength' => true,
                            'readonly' => 'readonly', 
                            'value' => ($model->shipments)? $model->shipments->DocumentNo : ' '
                        ])->label(Yii::t('common','Shipment No.')) ?>

                    <div class="form-group field-rcinvoiceheader-order_id">
                        <label class="control-label" for="rcinvoiceheader-order_id"><?=Yii::t('common','Sale Order')?></label>
                        <div class="form-control"><?= $model->saleOrder ? Html::a($model->saleOrder->no,['/SaleOrders/saleorder/view','id' => $model->saleOrder->id],['target' => '_blank']) : ' '; ?></div>
                        <div class="help-block"></div>
                    </div>
                 
                    <?= $form->field($model, 'doc_type')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                    <?= $form->field($model, 'paymentdue')->widget(DatePicker::classname(), [
                          'options' => ['placeholder' => Yii::t('common','Due date').'...','style' => 'background-color:rgba(239, 186, 180, 0.78);'],
                          'value' => $model->paymentdue,
                          'type' => DatePicker::TYPE_COMPONENT_APPEND,
                          'removeButton' => false,
                          'pluginOptions' => [
                              'format' => 'yyyy-mm-dd',
                              'autoclose'=>true
                          ]
                      ])->label(Yii::t('common','Due date')); ?>
                </div>
            </div>
            <div class="table render-invoice-line" style="position: relative;">
                 <!-- Render Hear               -->
                 <div class="blink text-center" style="height:200px;"> </div>
            </div>            
            <!--  /. Invoice Line  -->
            <div class="row">
                <div class="col-md-7">
                    <p class="text-muted well well-sm no-shadow"> <?php echo  $Bahttext->ThaiBaht(abs($model->sumtotals->total)) ?>   </p>
                         
                        <div class="mt-10">
                            <label><?=Yii::t('common','Remark')?></label>
                            <textarea class="form-control" rows="3" id="remark" ><?=$model->remark?></textarea>
                        </div>

                        <div class="my-10">
                            <textarea class="form-control update-comment" rows="7" ><?=$model->comments?></textarea>
                        </div>
                    
                        <div class="hidden">          
                            <img src="images/credit/visa.png" alt="Visa">
                            <img src="images/credit/mastercard.png" alt="Mastercard">
                            <img src="images/credit/american-express.png" alt="American Express">
                            <img src="images/credit/paypal2.png" alt="Paypal">
                    </div>
                </div>
                <div class="col-md-5">
                    
                    <div class="panel panel-default">                                 
                        <div class="table-responsive" style="">
                        
                            <table class="table">             
                                 
                                <tr>
                                    <th class="bg-success"><?=Yii::t('common','Before Discount')?>:</th>
                                    <td class="bg-success before-discount" align="right"><?=number_format($model->sumtotals->sumline,2) ?></td>
                                </tr>
                                <tr>
                                    <th>
                                        <div class="pull-left" style="margin-right:3px;"><?=Yii::t('common','Discount')?></div> 
                                        <div><?= $form->field($model, 'percent_discount')
                                            ->textInput([
                                                'readonly' => false, 
                                                'value' => $model->percent_discount * 1 ,
                                                'data-name' => 'percent_discount',
                                                'class' => 'text-right form-control', 
                                                'style' => 'width:80px;'
                                            ])
                                            ->label(false) ?> 
                                        </div>
                                    </th>
                                    <td align="right">
                                        <input type="text" value="<?=number_format($model->discount,2) ?>" class="form-control text-right discount" style="width:100px;" readonly />       
                                        <?php /* $form->field($model, 'discount')
                                        ->textInput([
                                            'readonly' => true, 
                                            'value' => number_format($model->discount,2) ,
                                            'data-name' => 'discount',
                                            'class' => 'text-right form-control', 
                                            'style' => 'width:100px;'
                                        ])
                                        ->label(false) */ ?>
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <th  class="bg-green" style="width:50%"><?=Yii::t('common','Subtotal')?>:</th>
                                    <td  class="bg-green" align="right"><div class="subtotal"><?= number_format($model->sumtotals->subtotal,2) ?></div></td>
                                </tr>
                                <?php if($model->include_vat == 0): // Vat ใน ?>
                                <tr>
                                    <th> ก่อนรวมภาษี </th> 
                                    <td align="right"><?= number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th >
                                        <?=Yii::t('common','Vat')?> (<span class="vat"><?=$model->sumtotals->vat?>%</span>)
                                        
                                        <div class="ew-vat-choice" >
                                            <?= Html::dropDownList('vat_percent', null,arrayHelper::map(\common\models\VatType::find()->all(),'vat_value', 'name'),
                                                                            [
                                                                                'class'=>'form-control',
                                                                                //'prompt' => Yii::t('common','Every one'),
                                                                                'options' => [                        
                                                                                    $model->sumtotals->vat => ['selected' => 'selected']
                                                                                ],
                                                                                'style'     => 'max-width: 80px;'
                                                                            ] );?>
                                        </div>
                                    
                                    </th> 
                                    <td  align="right">
                                        <span class="inc-vat-val"><?= number_format($model->sumtotals->incvat,2) ?></span>
                                        <div class="inc-vat" style="<?= ($model->sumtotals->vat > 0 ? '' : 'display:none;') ?>">
                                            <?= Html::dropDownList('include_vat',null,[
                                                '0' => Yii::t('common','Include Vat'),
                                                '1' => Yii::t('common','Exclude Vat')
                                                ],[
                                                    'options' => [                        
                                                        $model->include_vat => ['selected' => 'selected']
                                                    ],
                                                    'style' => 'max-width: 120px;'
                                                ]);
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr >
                                    <th><?=Yii::t('common','Grand total')?>:</th>
                                    <td align="right" class="grand-total"><?= number_format($model->sumtotals->total,2) ?> </td>
                                </tr>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>



            <div class="row button-zone" style="position: fixed;
                                bottom: -20px;
                                border-top: 1px solid #ccc;                                 
                                background-color: rgba(239, 239, 239, 0.9);
                                padding: 10px 10px 15px 10px;
                                right: 0px;
                                text-align: right;
                                z-index: 1000;
                                left: -15px;"
                                >
                <div class="col-sm-6 text-left">
                    <div style="margin-left: 15px;">
                        <?php if(!$model->creditnote): ?>
                                    
                            <?=Html::a('<i class="fas fa-undo"></i> '.Yii::t('common','Credit Note').' (CN)','#modal-cn',
                                [
                                    'class'         => 'btn btn-warning  ',                
                                    'data-toggle'   => "modal",
                                    
                                ]) ?>
                            
                        <?php else : ?>
                            
                        
                            <?php 
                                $getSumLine = 0;
                                foreach ($model->creditnote as $key => $value) {     
                                    $getSumLine += $value->sumLine;                            
                                }
                                if(abs($getSumLine) < abs($model->sumLine)) { 
                                    echo  Html::a('<i class="fas fa-undo"></i> '.Yii::t('common','Credit Note').' (CN) .','#modal-cn',
                                                [
                                                    'class'         => 'btn btn-warning',                
                                                    'data-toggle'   => "modal",                                            
                                                ]);
                            } ?>
                        <?php endif; ?>   

                        

                        <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => base64_encode($model->id)], [
                            'class' => 'btn btn-danger-ew',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]) ?>

                    <?php 
                    if($model->revenue==0){
                        echo '<button type="button" class="btn-cancel-invoice btn btn-default-ew"><i class="fas fa-ban "></i> '.Yii::t("common","Cancel Document").'</button>';
                    }else{
                        echo '<button type="button" class="btn-open-invoice btn btn-primary-ew"><i class="fas fa-check-circle"></i> '.Yii::t("common","Enabled").'</button>';
                    }

                    ?>

                    <?=Html::a('<i class="fas fa-file-excel"></i> '.Yii::t('common','Download Excel'),[
                                '/accounting/print/export',
                                'id' => $model->id,
                                'status' => $model->status,
                                'vat'   => $model->vat_percent
                                ],
                                [
                                    'class' => 'btn btn-success ew-print-preview',
                                    'style' => 'margin-left: 20px; margin-right: 20px;',          
                                    'target'  => '_blank',              
                                ]) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group pull-right">                    
                            <?=Html::a('<i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Print'),['print-inv', 'id' => base64_encode($model->id),'footer'=>'1'],
                                [
                                    'class' => 'btn btn-info',
                                    'target' => '_blank',
                                ]) ?>                     
                    </div> 

                </div>
            </div>



            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

 
<!-- MODAL -->
<div class="modal fade" id="modal-cn"  ng-controller="creditnoteCtrl" style="font-family: saraban;">
    <div class="modal-dialog modal-hd box-shadow ">
        <div class="modal-content ">
            <?php $form = ActiveForm::begin([
                    'id' => 'credit-note'
                ]); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="fas fa-table"></i> <?=Yii::t('common','Credit Note')?></h4>
                </div>
                <div class="modal-body">
                    <h4>ยืนยันการยกเลิกบิล</h4>
                    <small>ระบบจะสร้างใบลดหนี้ให้อัตโนมัติ</small>
                    <?= $form->field($model, 'id')->textInput(['type' => 'hidden'])->label(false) ?>                       
                    <div class="row">                        
                        <div class="col-sm-6">
                            <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true,'readonly' => 'readonly'])->label(Yii::t('common','Customer')) ?>
                        </div>                  
                        
                        <div class="col-sm-3">
                            <?php //$model->posting_date = date('Y-m-d',strtotime($model->posting_date));?>
                            <?php // $form->field($model, 'posting_date')->textInput(['type' => 'date']) ?>     

                            <?= $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                                'options' => [
                                    'placeholder' => 'Enter Order date ...',
                                    'id'    => 'postingdate'
                                ],
                                'value' => date('Y-m-d'),
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'autoclose'=>true,
                                    'remove' => false
                                ]
                            ]); ?>      
                        </div>
                        <div class="col-sm-3">
                            <?php $model->no_ = $doc_no; ?>
                            <?= $form->field($model, 'no_')->textInput(['maxlength' => true,'class' => 'form-control text-right']) ?>                            
                        </div>
                    </div>
                     

                    <?=  GridView::widget([
                            'dataProvider'=> $dataProvider,
                            'summary' => false,
                            //'tableOptions'=>['class' => 'table table-bordered'],
                            'rowOptions'=>function($model){
                                if($model->referCreditNote){
                                    return ['class' => 'credit-note-content', 'data-id' => $model->item];
                                }else{
                                    return ['class' => 'credit-note-content info', 'data-id' => $model->item];
                                }
                                
                            },
                            'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                            'striped'=>false, 
                            'columns' => [
                                [
                                    //'class' => 'yii\grid\SerialColumn',
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'pointer','ng-click' => "checked(\$event)"],
                                    'value' => function($model,$key,$index){
                                        $i =  $index+1;
                                        if($model->referCreditNote){
                                            $html = '<div class="pull-left"> <input ng-click="checked($event)" type="checkbox" data="'.$model->id.'" name="chk[]" value="'.$model->id.'"/></div><div class="pull-right">'.$i.'</div>';
                                        }else {
                                            $html = '<div class="pull-left"> <input ng-click="checked($event)" type="checkbox" checked="checked" data="'.$model->id.'" name="chk[]" value="'.$model->id.'"/></div><div class="pull-right">'.$i.'</div>';
                                        }
                                         
                                        return $html;
                                    },
                                    'header' => '<input type="checkbox" ng-model="selectedAll" ng:click="checkAll($event)" id="check-all" />
                                                    <label class="pointer" for="check-all"> '.Yii::t("common","Check All").' </label>',
                                ],
                                [
                                    'format' => 'html',
                                    'label' => Yii::t('common','Item'),
                                    'contentOptions' => ['ng-click'=>"checked(\$event)",'class' => 'pointer'],
                                    'value' => function($model){
                                        if($model->type=='Item')
                                        {
                                            $code = $model->crossreference->no;
                                        }else {
                                            $code = 'G/L Number';
                                        } 
                                                               
                                        $color = '';
                                        if($model->status=='delete') $color = 'text-red';
                                        if($model->item=='1414') $color = 'text-orange';
                    
                                        $html = '<div class="'.$color.'">'.$code.'</div>';
                    
                                        // ถ้าเป็นข้อความ
                                        // 
                                        if($model->item=='1414'){
                                            if($model->code_no_=='1^x'){
                                                $html = '<div class="'.$color.'"> </div>';
                                            }else if($model->code_no_==' '){
                                                $html = '<div class="'.$color.'"> </div>';
                                            }else {
                                                $html = '<div class="'.$color.'">'.$model->code_no_.'</div>';
                                            }
                                            
                                        } 
                                        return $html;
                                    },
                                    
                                ],
                                [
                                    'label' => Yii::t('common','Name'),
                                    'contentOptions' => ['class' => ' '],
                                    'value' => function($model){
                                        return $model->code_desc_;
                                    },                     
                                ],

                                [
                                    'label' => Yii::t('common','Quantity'),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'text-right','style' => 'min-width:120px; width:120px;'],
                                    'contentOptions' => ['class' => 'text-right  '],
                                    'value' => function($model){
                                       // return number_format($model->quantity,2);
                                       if($model->referCreditNote){
                                            $disable = 'disabled="disabled"';
                                       }else{
                                            $disable = null;
                                       }

                                        return '<input type="number"  step=any onclick="$(this).select();"  '.$disable.' value="'.number_format($model->quantity, 0, '.', '').'" 
                                                    class="form-control money text-right text-line update-quantity" 
                                                    name="quantity[]" autocomplete="off">';
                                    },                     
                                ],

                                [
                                    //'label' => Yii::t('common','Receive All'),
                                    'format' => 'raw',
                                    'headerOptions' => ['style' => 'width:92px;'],
                                    'contentOptions' => ['class' => 'text-center'],
                                    'value' => function($model){
                                        return '<input type="checkbox" '.($model->item != 1414 ? 'checked' : 'disabled').' class="receive-checked" name="receive['.$model->id.']" value="'.$model->quantity.'"/>';
                                    },
                                    'header' => '<input type="checkbox" ng-model="receivedAll" ng:click="receiveAll($event)" id="receive-all"  />
                                                    <label class="pointer" for="receive-all"> '.Yii::t('common','Receive All').' </label>',
                                ],

                                [
                                    'label' => Yii::t('common','Unit Price'),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'text-right','style' => 'min-width:120px; width:120px;'],
                                    'contentOptions' => ['class' => 'text-right pointer text-red'],
                                    'value' => function($model){
                                        //return '-'.number_format($model->unit_price,2);
                                        if($model->referCreditNote){
                                            $disable = 'disabled="disabled"';
                                        }else{
                                                $disable = null;
                                        }
                                        return '<input type="number"  step=any onclick="$(this).select();" '.$disable.'
                                        value="'.number_format($model->unit_price, 2, '.', '').'" 
                                        class="form-control text-right money text-line update-unit_price" 
                                        name="unit_price[]">';
                                    },                     
                                ],               
                                [
                                    'label' => Yii::t('common','Amount'),
                                    'headerOptions' => ['class' => 'text-right','style' => 'min-width:100px;'],
                                    'contentOptions' => function($model){
                                        return [
                                            'class'     => 'text-right pointer line-amount text-red',
                                            'ng-click'  =>"checked(\$event)",
                                            'data'      => ($model->quantity * $model->unit_price) - $model->line_discount,
                                        ];
                                    },
                                    'value' => function($model){
                                        return number_format(($model->quantity * $model->unit_price)- $model->line_discount,2);
                                    },
                                    
                                     
                                ], 
                            ],
                            'responsiveWrap' => false,
                        ]);
                    ?>
                    <div class="row">
                        <div class="col-md-8">
                         <?= $form->field($model, 'remark')->textarea(['rows'=>'3','placeholder' => 'ระบุเหตุผลที่ยกเลิกบิล'])->label(Yii::t('common','Remark')) ?>                
                        </div>                        
                        <div class="col-md-4">
                            <div class="panel  ">                                                         
                                <div class="table-responsive" style="">
                                    <table class="table">                                           
                                        <tr>
                                            <th style="width:50%"><?=Yii::t('common','Subtotal')?> :</th>
                                            <td class="text-right text-red subtotal-modal"> <?= number_format($model->sumtotals->subtotal,2) ?></td>
                                        </tr>                                
                                    </table>
                                </div> 
                            </div>
                        </div>
                    </div>

                    
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="button" class="btn btn-default-ew btn-flat" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common','Cancel')?></button>                    
                    <?= Html::a('<i class="fas fa-gavel"></i> '.Yii::t('common', 'Confirm'), ['credit-note', 'id' => base64_encode($model->id)], [
                            'class' => 'btn btn-success-ew btn-flat',
                            'ng-click' => 'confirmCn($event)',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to post Credit Note?'),
                                'method' => 'post',
                            ],
                        ]) ?>                    
                    
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJsFile('js/accounting/postedController.js?v=4.11.19.2');?>

<?php
$id     = base64_encode($model->id);  
$Yii    = 'Yii';
$js =<<<JS


const getInvLine = (id) => {
    let loading = `<div class="loading-div text-primary" style="position: 
                                                    absolute;z-index: 10;
                                                    background: rgba(204, 204, 204, 0.3);
                                                    width: 100%;
                                                    height: 100%;text-align: center; 
                                                    padding: 10% 0px 0px 0px;" >
                        <i class="fa fa-refresh fa-spin fa-3x  "></i> <br /> Loading
                    </div>`;
    $('body').find('.render-invoice-line').prepend(loading)
    $.ajax({ 
        url:"?r=accounting/posted/get-invoice-line",
        type: 'POST', 
        data: {id:id},
        async:true,
        success:function(getData){              
           $('.render-invoice-line').html(getData);
        }
    });
}


let totalChange = (data) => {

    let beforeDiscount  = parseFloat(data.total.sumline);
    let discount        = parseFloat(data.total.discount);
    let sumline         = parseFloat(data.total.sumline);
    let subtotal        = parseFloat(data.total.subtotal);
    let total           = parseFloat(data.total.total);
    let incvat          = parseFloat(data.total.incvat);

    $('body').find('.before-discount').html(number_format(beforeDiscount.toFixed(2)));
    $('body').find('.discount').val(number_format(discount.toFixed(2)));
    $('body').find('.subtotal').html(number_format(subtotal.toFixed(2)));
    $('body').find('.inc-vat-val').html(number_format(incvat.toFixed(2)));
    $('body').find('.grand-total').html(number_format(total.toFixed(2)));


}





 $(document).ready(function(){
     //$('a.ew-bt-app-home').attr('href','index.php?r=accounting%2Fposted%2Fcredit-note-list');
     setTimeout(() => {
        $('.btn-app-print').show();
        $('.btn-app-print').attr('href', '?r=accounting%2Fposted%2Fprint-inv&id={$id}&footer=1');
        $('.btn-app-print').attr('style', 'visibility: visible;')
     }, 100);

 
    getInvLine('{$model->id}');
     
 });





 $('body').on('change','input.update-quantity',function(){
    sumLine($(this));
 })
 $('body').on('change','input.update-unit_price',function(){
    
    sumLine($(this));
 })

 function sumLine(row){
    var price   = row.closest('tr').find('input.update-unit_price').val();
    var qty     = row.closest('tr').find('input.update-quantity').val();
    row.closest('tr').find('.line-amount').attr('data',qty*price).html(number_format((qty*price).toFixed(2)));
    row.closest('tr').find('input.receive-checked').val(qty);
    sumTotal();
 }
 
 function sumTotal(){
    var obj = $('tr.credit-note-content');
    var total = 0;
    $.each(obj,function(key,model){
        if($(model).find('input[type="checkbox"]').is(":checked")){
            total += $(model).children('td.line-amount').attr('data') * 1;
        }
        
    });
    
     $('.subtotal-modal').html(number_format(total.toFixed(2)));
 }

 const updateInvoice = (obj, callback) => {
    fetch("?r=accounting/posted/posted-invoice-update", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);
    }).catch(error => {
        console.log(error);
    });
 }

 (function($, window, document, undefined) {
  $("#rcinvoiceheader-payment_term").on("change", function() {
    var today   = $('#rcinvoiceheader-posting_date').val();
    var date    = new Date(today),
        days    = parseInt($("#rcinvoiceheader-payment_term").val(), 10);

    if (!isNaN(date.getTime())) {
        date.setDate(date.getDate() + days);
        $('input[id="rcinvoiceheader-paymentdue"]').val(date.toInputFormat());

        fetch("?r=accounting/posted/posted-invoice-update", {
            method: "POST",
            body: JSON.stringify({id:`{$model->id}`,term:days, due:date.toInputFormat() }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            setTimeout(()=>{
                if(response.status===200){
                    swal(response.message, response.suggestion, "success");
                }else{
                    swal(response.message, response.suggestion, "warning");
                }
            },500);            
        })
        .catch(error => {
            console.log(error);
        });

    } else {
      alert("Invalid Date");
    }
  });

  $("#rcinvoiceheader-posting_date").on("change", function() {
    var today   = $('#rcinvoiceheader-posting_date').val();
    var date    = new Date(today),
        days    = parseInt($("#rcinvoiceheader-payment_term").val(), 10);

    if (!isNaN(date.getTime())) {
    
        fetch("?r=accounting/posted/posted-invoice-update", {
            method: "POST",
            body: JSON.stringify({id:`{$model->id}`,term:days, posting:date.toInputFormat() }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            setTimeout(()=>{
                if(response.status===200){
                    swal(response.message, response.suggestion, "success");
                }else{
                    swal(response.message, response.suggestion, "warning");
                }
            },500);
        })
        .catch(error => {
            console.log(error);
        });

    } else {
      alert("Invalid Date");
    }
  });

  //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
  Date.prototype.toInputFormat = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    return (
      yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0])
    ); // padding
  };
})(jQuery, this, document);
 
$('body').on('change','#rcinvoiceheader-paymentdue',function(){

    let days = parseInt($("#rcinvoiceheader-payment_term").val(), 10);

    fetch("?r=accounting/posted/posted-invoice-update", {
        method: "POST",
        body: JSON.stringify({id:`{$model->id}`,term:days, due: $('#rcinvoiceheader-paymentdue').val() }),
        headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        setTimeout(()=>{
            if(response.status===200){
                swal(response.message, response.suggestion, "success");
            }else{
                swal(response.message, response.suggestion, "warning");
            }
        },700);
        
    }).catch(error => {
        console.log(error);
    });
})

$('body').on('change','.update-comment',function(){

    let comment = $(this).val();

    fetch("?r=accounting/posted/posted-invoice-comment-update", {
        method: "POST",
        body: JSON.stringify({id:`{$model->id}`,comment:comment}),
        headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        setTimeout(()=>{
            if(response.status===200){
                $.notify({
                    // options
                    icon: "fas fa-check-circle",
                    message: response.message 
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: "success",
                    delay: 3000,
                    z_index: 3000
                });  
                //swal(response.message, response.suggestion, "success");
            }else{
                swal(response.message, response.suggestion, "warning");
            }
        },700);
        
    }).catch(error => {
        console.log(error);
    });
})

$('body').on('change', '#rcinvoiceheader-sale_id', function(){
    let sale    = $(this).val();
    $('.button-zone').fadeOut();
    $('.btn-app-print').fadeOut();
    updateInvoice({id:`{$model->id}`, sale:sale}, res => {
        if(res.status===200){
            swal(res.message, res.suggestion, "success");
            $('.button-zone').fadeIn();
            $('.btn-app-print').fadeIn();
        }else{
            swal(res.message, res.suggestion, "warning");
        }
    })
});

$('body').on('change', 'textarea#remark', function(){
    let remark    = $(this).val();
    $('.button-zone').fadeOut();
    $('.btn-app-print').fadeOut();
    updateInvoice({id:`{$model->id}`, remark:remark}, res => {
        if(res.status===200){
            //swal(res.message, res.suggestion, "success");
            $.notify({
                    // options
                    icon: "fas fa-check-circle",
                    message: res.message 
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: "success",
                    delay: 3000,
                    z_index: 3000
                }); 
            $('.button-zone').fadeIn();
            $('.btn-app-print').fadeIn();
            totalChange(res);
        }else{
            swal(res.message, res.suggestion, "warning");
        }
    })
});

 

$('body').on('change', 'select[name="vat_percent"]', function(){
    if(parseInt($(this).val()) > 0){
        $('.inc-vat').show();
    }else{
        $('.inc-vat').hide();
    }

    let vat_percent = $(this).val();

    updateInvoice({id:`{$model->id}`, vat_percent:vat_percent}, res => {
        if(res.status===200){
            //swal(res.message, res.suggestion, "success");
            $.notify({
                    // options
                    icon: "fas fa-check-circle",
                    message: res.message 
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: "success",
                    delay: 3000,
                    z_index: 3000
                }); 
            $('.button-zone').fadeIn();
            $('.btn-app-print').fadeIn();
            totalChange(res);
        }else{
            swal(res.message, res.suggestion, "warning");
            return false;
        }
    })
})

$('body').on('change', 'select[name="include_vat"]', function(){
    let inc_vat   = $(this).val();
    updateInvoice({id:`{$model->id}`, include_vat:inc_vat}, res => {
        if(res.status===200){
            //swal(res.message, res.suggestion, "success");
            $.notify({
                    // options
                    icon: "fas fa-check-circle",
                    message: res.message 
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: "success",
                    delay: 3000,
                    z_index: 3000
                }); 
            $('.button-zone').fadeIn();
            $('.btn-app-print').fadeIn();

            totalChange(res);
            //window.location="?r=accounting%2Fposted%2Fposted-invoice&id={$id}";
        }else{
            swal(res.message, res.suggestion, "warning");
            return false;
        }
    })
});


$('body').on('change', 'input#rcinvoiceheader-discount, input#rcinvoiceheader-percent_discount', function(){
    let value       = $(this).val();
    let name        = $(this).attr('data-name');

    updateInvoice({id:`{$model->id}`,name:name, value:value }, res => {
        if(res.status===200){
            
            $.notify({
                    // options
                    icon: "fas fa-check-circle",
                    message: res.message 
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: "success",
                    delay: 3000,
                    z_index: 3000
                }); 
            $('.button-zone').fadeIn();
            $('.btn-app-print').fadeIn();
            totalChange(res);
            //window.location="?r=accounting%2Fposted%2Fposted-invoice&id={$id}";

        }else{
            swal(res.message, res.suggestion, "warning");
            return false;
        }
    })
})

const updateLine = (obj, callback) => {
    fetch("?r=accounting/posted/posted-invoice-update-line", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);
        if(response.status===200){            
             
        }else{
            swal(response.message, response.suggestion, "warning");
            return false;
        }
    }).catch(error => {
        console.log(error);
    });
 }
$('body').on('change', '.measure-change', function(){
    let measure = $(this).val();
    let id      = $(this).closest('tr').attr('data-key');
    let el      = $(this);

    updateLine({id:id, field:'measure', value:measure}, res => {
        if(res.status===200){  
            el.addClass('text-green');
        }
    })
});


$('body').on('change', '.quantity-change, .price-change', function(){
    let id      = '{$model->id}';
    let qty     = $(this).val();
    let line    = $(this).closest('tr').attr('data-key');
    let el      = $(this);
    let name    = $(this).attr("name");


    updateLine({id:line, field:name, value:qty}, res => {
        if(res.status===200){  
            el.addClass('text-green');
            getInvLine(id);
            totalChange(res);
        }
    })

    

})




const cancelDocument = (obj, callback) => {
  fetch("?r=accounting%2Fposted%2Fcancel-document", {
    method: "POST",
    body: JSON.stringify(obj),
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
    },
  })
  .then(res => res.json())
  .then(response => { callback(response); })
  .catch(e => { console.log(e); });
}


$('body').on('click', '.btn-cancel-invoice', function(){
  let id = parseInt("{$model->id}");
  let el = $(this);
  if(confirm("Cancel ?")){   
    
    cancelDocument({id:id, action:1}, res => {
      if(res.status===200){
        el.html('<i class="fas fa-check-circle"></i> '+ "{$Yii::t('common','Enabled')}");        
        el.removeClass('btn-cancel-invoice btn-default-ew');
        el.addClass('btn-open-invoice btn-primary-ew');
      }
    })
  }
});

$('body').on('click', '.btn-open-invoice', function(){
  let id = parseInt("{$model->id}");
  let el = $(this);
  if(confirm("Enabled ?")){    
    cancelDocument({id:id, action:0}, res => {
      if(res.status===200){
        el.html('<i class="fas fa-ban"></i> '+ "{$Yii::t('common','Cancel Document')}");        
        el.removeClass('btn-open-invoice btn-primary-ew');
        el.addClass('btn-cancel-invoice  btn-default-ew');
      }
    })
  }
})


JS;
$this->registerJS($js,\yii\web\View::POS_END);

?>