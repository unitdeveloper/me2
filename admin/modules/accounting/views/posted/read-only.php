<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
 

<div class="sale-invoice-header-update" ng-init="Title='<?=$model->no_?>'">     
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
        <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>
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
                            <?= $form->field($model, 'cust_code')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
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
                    <?= $form->field($model, 'sales_people')->textInput(['maxlength' => true,'readonly' => 'readonly','value' => $model->sales->name]) ?>
                    <?= $form->field($model, 'discount')->textInput(['readonly' => 'readonly']) ?>
                </div> 
                <div class="col-sm-2">
                    <?= $form->field($model, 'posting_date')->textInput(['readonly' => 'readonly', 'value' => date('Y-m-d',strtotime($model->posting_date))]) ?>
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
                    ],['disabled' => true]) 
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
                    <?= $form->field($model, 'paymentdue')->textInput(['readonly' => 'readonly'])->label(Yii::t('common','Due date')); ?>
                </div>
            </div>
            <div class="table">
                <?php 
                $gridColumns = [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => Yii::t('common','Item'),
                            'format' => 'html',
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
                            'value' => function($model){
                                return $model->code_desc_;
                            },                     
                        ],
                        [
                            'label' => Yii::t('common','Quantity'),
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                return number_format($model->quantity,2);
                            },                     
                        ],
                        [
                            'label' => Yii::t('common','Unit Price'),
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                return number_format($model->unit_price,2);
                            },                     
                        ], 

                        [
                            'label' => Yii::t('common','Discount'),
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                return number_format($model->line_discount,2);
                            },                     
                        ], 

                        [
                            'label' => Yii::t('common','Amount'),
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                return number_format(($model->quantity * $model->unit_price)- $model->line_discount,2);
                            },
                            
                        ], 
                    ];
                ?>
                <?=  GridView::widget([
                    'dataProvider'=> $dataProvider,
                    //'filterModel' => $searchModel,
                    'summary' => false,
                    //'showFooter' => true,
                    'rowOptions' => function($model){
                        if($model->referCreditNote){
                            return ['class' => 'bg-danger'];
                        }
                    },
                    'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                    'columns' => $gridColumns,
                    //'responsive'=>true,
                    //'hover'=>true,
                    //'summary' => false,
                    'striped'=>false, 
                    'responsiveWrap' => false,
                ]);
                ?>                
            </div>            
            <!--  /. Invoice Line  -->
            <div class="row">
                <div class="col-md-8">
                    <p class="text-muted well well-sm no-shadow"> <?php echo  $Bahttext->ThaiBaht(abs($model->sumtotals->total)) ?>   </p>
                        <?php if($model->remark){ ?>
                        <div class="panel panel-default">
                            <div class="panel-body">
                            <?=$model->remark?>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="hidden">          
                            <img src="images/credit/visa.png" alt="Visa">
                            <img src="images/credit/mastercard.png" alt="Mastercard">
                            <img src="images/credit/american-express.png" alt="American Express">
                            <img src="images/credit/paypal2.png" alt="Paypal">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">                                 
                        <div class="table-responsive" style="">
                            <table class="table">             
                                <?php if($model->discount != 0): ?>
                                <tr>
                                    <th class="bg-success"><?=Yii::t('common','Before Discount')?>:</th>
                                    <td class="bg-success" align="right"><?=number_format($model->sumtotals->sumline,2) ?></td>
                                </tr>
                                <tr>
                                    <th><?=Yii::t('common','Discount')?>:</th>
                                    <td align="right"><?=$model->sumtotals->discount ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th  class="bg-green" style="width:50%"><?=Yii::t('common','Subtotal')?>:</th>
                                    <td  class="bg-green" align="right"><?= number_format($model->sumtotals->subtotal,2) ?></td>
                                </tr>
                                <?php if($model->include_vat == 0): // Vat ใน ?>
                                <tr>
                                    <th> ก่อนรวมภาษี </th> 
                                    <td align="right"><?= number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th ><?=Yii::t('common','Vat')?> (<?=$model->sumtotals->vat?>%)</th> 
                                    <td  align="right"><?= number_format($model->sumtotals->incvat,2) ?></td>
                                </tr>
                                <tr >
                                    <th><?=Yii::t('common','Grand total')?>:</th>
                                    <td align="right" ><?= number_format($model->sumtotals->total,2) ?> </td>
                                </tr>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>



           
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

 
<?php $this->registerJsFile('js/accounting/postedController.js?v=3.03.27');?>

<?php

$js =<<<JS
 $(document).ready(function(){
     //$('a.ew-bt-app-new').remove();
 })

 $('body').on('change','input.update-quantity',function(){
    sumLine($(this));
 })
 $('body').on('change','input.update-unit_price',function(){
    
    sumLine($(this));
 })

 function sumLine(row){
    var price   = row.closest('tr').find('input.update-unit_price').val();
    var qty     = row.closest('tr').find('input.update-quantity').val();
    row.closest('tr').find('.line-amount')
    .attr('data',qty*price)
    .html(number_format((qty*price).toFixed(2)));
     
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
 
JS;
$this->registerJS($js,\yii\web\View::POS_END);

?>