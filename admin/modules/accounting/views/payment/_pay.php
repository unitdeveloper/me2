<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\BankList;
use kartik\widgets\DatePicker;

?>
<style>
    .readio-group input[name="type"] {
        /* display:none; */
    }
</style>
<div class="modal fade" id="modal-pay-now">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= Yii::t('common','Payment method')?></h4>
            </div>
            <div class="modal-body">
                <div class="row readio-group">
                    <div class="col-xs-3 text-center">
                        <label class="btn btn-success-ew btn-flat" for="btn-cache"><i class="fas fa-dollar-sign"></i> เงินสด </label> <br />
                        <input type="radio" name="type" id="btn-cache" value="Cash" checked/>
                    </div>
                    <div class="col-xs-3 text-center">
                        <label class="btn btn-info-ew btn-flat" for="btn-cheque"><i class="fas fa-receipt"></i> เช็ค </label> <br />
                        <input type="radio" name="type" id="btn-cheque" value="Cheque"/>
                    </div>
                    <div class="col-xs-3 text-center">                        
                        <label class="btn btn-warning-ew btn-flat" for="btn-transfer"><i class="fas fa-random"></i> โอนเงิน </label> <br />
                        <input type="radio" name="type" id="btn-transfer" value="ATM"/>
                    </div>
                    <div class="col-xs-3 text-center">                        
                        <label class="btn btn-primary-ew btn-flat" for="btn-credit"><i class="fas fa-credit-card"></i> บัตรเครดิต </label> <br />
                        <input type="radio" name="type" id="btn-credit" value="Credit"/>
                    </div>
                </div>

                <div class="row"> <hr /> </div>
                <div class="row">                    
                    <div class="col-xs-3"><?= Yii::t('common','Payment Date')?></div>
                    <div class="col-xs-9 font-roboto"> 
                    <?php 
                        echo DatePicker::widget([
                            'name'      => 'payment_date',
                            'value'     => date('Y-m-d'),        
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format'    => 'yyyy-mm-dd'
                            ],
                            'pluginEvents' => [
                                "hide" => "function(e) { $('body').find('input[name=\"amount\"]').focus(); }",
                            ],
                            'options'   => ['autocomplete' => 'off']                            
                        ]);
                    ?>
                    </div>
                </div>
                
                <div class="row">                    
                    <div class="col-xs-3 mt-10"><?= Yii::t('common','Amount')?></div>
                    <div class="col-xs-9 mt-10 has-success"><input type="text" value="" name="amount" class="form-control font-roboto "></div>
                </div>

                <div class="row bank-zone" style="display:none;">                    
                    <div class="col-xs-3 mt-10"><?= Yii::t('common','Bank')?></div>
                    <div class="col-xs-9 mt-10">
                        <?= Html::dropDownList('bank-id', null,
                            ArrayHelper::map(
                                BankList::find()
                                ->orderBy(['name' => SORT_ASC])
                                ->all(),'id','name'),
                                ['class' => 'form-control select-bank']
                            ) 
	                ?>
                    </div>
                </div>

                <div class="row cheque-zone" style="display:none;">                    
                    <div class="col-xs-3 mt-10 text-cheque"><?= Yii::t('common','Cheque ID')?></div>
                    <div class="col-xs-9 mt-10">
                        <input type="text" value="000-0-00000-0" name="cheque_id" class="form-control font-roboto">
                    </div>
                </div>

                <div class="row cheque-date-zone" style="display:none;">                    
                    <div class="col-xs-3 mt-10"><?= Yii::t('common','Cheque date')?></div>
                    <div class="col-xs-9 mt-10 font-roboto">
                        <?php 
                            echo DatePicker::widget([
                                'name'      => 'check_date',
                                'value'     => date('Y-m-d'),        
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format'    => 'yyyy-mm-dd'
                                ],
                                'options'   => ['autocomplete' => 'off']                            
                            ]);
                        ?>
                    </div>
                </div>

                <div class="row credit-zone" style="display:none;">                    
                    <div class="col-xs-3 mt-10"><?= Yii::t('common','CVC')?></div>
                    <div class="col-xs-3 mt-10 has-warning">
                        <input type="text" value="000" name="cvc" class="form-control font-roboto">
                    </div>
                </div>

                <div class="row remark-zone">                    
                    <div class="col-xs-3 mt-10"><?= Yii::t('common','Remark')?></div>
                    <div class="col-xs-9 mt-10 has-primary">
                        <textarea class="form-control" name="remark" id="remark"></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-success btn-save-payment"><i class="fa fa-save"></i> <?= Yii::t('common','Save')?></button>
            </div>
        </div>
    </div>
</div>

<?php
$Yii    = 'Yii';
$js     =<<<JS
 

const postToApi = (payment) => {
    let header  = localStorage.getItem('payment-header') ? JSON.parse(localStorage.getItem('payment-header')) : [];
    let itemList= localStorage.getItem('payment-line') ? JSON.parse(localStorage.getItem('payment-line')) : [];
    let vendors = localStorage.getItem('vendors') ? JSON.parse(localStorage.getItem('vendors')) : [];
    fetch("?r=accounting/payment/create-payment", {
        method: "POST",
        body: JSON.stringify({header:header, line:itemList, payment:payment, vendors:vendors}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);            
    })
    .catch(error => {
        console.log(error);
    });
}

const finishProcess = () => {    
    $('#modal-pay-now').modal('show');    
}


$('#modal-pay-now').on('hidden.bs.modal',function(){   
    setTimeout(() => {
        $('body').find('input[name="search-code"]').focus().select();
    }, 500);
    $('.content-step-vendor').hide();
    $('.content-step-edit').show();
    $('.content-step-success').hide();

    console.log('close modal');
});


$('body').on('click','.btn-save-payment', function(){
  $('.content-step-vendor').hide();
  $('.content-step-edit').hide();
  $('.content-step-success').show();

  let payment = {
    type: $('body').find('input[name="type"]:checked').val(),
    amount : $('body').find('input[name="amount"]').val(),
    bank: $('body').find('select[name="bank-id"]').val(),
    cheque: $('body').find('input[name="cheque_id"]').val(),
    cheque_date: $('body').find('input[name="check_date"]').val(),
    date: $('body').find('input[name="payment_date"]').val(),
    remark: $('body').find('input[name="remark"]').val(),
    cvc: $('body').find('input[name="cvc"]').val()
  };
  postToApi(payment);
});


$('body').on('change', 'input[name="amount"]', function(){
     
})


// Btn Zone Click
 

$('body').on('click', '#btn-cache', function(){
    $('.bank-zone').slideUp();
    $('.cheque-zone').slideUp();
    $('.credit-zone').slideUp();
    $('.cheque-date-zone').slideUp();
    setTimeout(() => {
        $('.select-bank').val(0);
    }, 800);
});

$('body').on('click', '#btn-cheque', function(){
    $('.bank-zone').slideDown();
    $('.cheque-zone').slideDown();
    $('.credit-zone').slideUp();
    $('.cheque-date-zone').slideDown();
    $('.text-cheque').text("{$Yii::t('common','Cheque ID')}");
});

$('body').on('click', '#btn-transfer', function(){
    $('.cheque-zone').slideDown();
    $('.bank-zone').slideDown();
    $('.credit-zone').slideUp();
    $('.cheque-date-zone').slideUp();
    $('.text-cheque').text("{$Yii::t('common','Bank ID')}");
});

$('body').on('click', '#btn-credit', function(){
    $('.cheque-zone').slideDown();
    $('.bank-zone').slideDown();
    $('.credit-zone').slideDown();
    $('.text-cheque').text("{$Yii::t('common','Credit Card')}");
    $('.cheque-date-zone').slideUp();
});

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
  