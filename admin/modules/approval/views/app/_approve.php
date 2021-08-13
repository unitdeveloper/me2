<?php
use common\models\Cheque;

$MoreCheque = Cheque::find()->where(['source_id' => $cheque->source_id])->andwhere(['not', ['source_id' => null]]);
$sumBl      = $MoreCheque->sum('balance');

?>
<style type="text/css">
    body {
        font-family: 'Kanit', sans-serif !important;
    }
    .text-lg{
        font-size: 19px !important;
        font-weight: bold;
    }
    .bg-content-inDialog{
        position: absolute;
        width:80px;
        min-height: 100px;
        background-size: 80px auto;
        background-repeat:no-repeat;
        background-position:right top;
        opacity: 0.1;
        right: 10px;
        top: 55px;
    }
    .bank-bg-well{
        position: absolute;
        width:80px;
        min-height: 100px;
        background-size: 80px auto;
        background-repeat:no-repeat;
        background-position:right top;
        opacity: 0.1;
        right: 25px;
        top: 10px;
    }

    .cheque-info{
        /*font-family: courierthaimono;*/
        
    }
</style>

<div class="row cheque-info">
        <div class="col-md-6"  >
            <div class="panel panel-success" style="position: relative;">
                <div class="panel-heading"><i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Payment')?> : <?=Yii::t('common',$cheque->type)?></div>
                <div class="panel-body" >
                    <div class="bg-content-inDialog" style="background-image:url(uploads/<?=$cheque->banklist->imageFile?>);"></div>
                    <div class="row">
                        <?php if($cheque->customer->credit_limit=='') $cheque->customer->credit_limit = 0; ?>
                        <label class="col-xs-5 col-sm-3"><?=Yii::t('common','Balance')?></label><div class="col-xs-7 col-sm-9 text-lg"> <?=number_format($sumBl,2);?></div>
                    </div> 
                    <div class="row">
                        
                        <label class="col-xs-5 col-sm-3"><?=Yii::t('common','Bank')?></label><div class="col-xs-7 col-sm-9">
                        <?=Yii::t('common',$cheque->banklist->name)?> </div>
                        <div class="col-xs-12"><hr></div>
                    </div> 
                    
                    <?php if($cheque->type=='Cheque') : ?>
                        <div class="row">
                            
                            <?php if($cheque->type!='Cheque') $cheque->bank_id = NULL; ?>
                            <label class="col-xs-5 col-sm-3"><?=Yii::t('common','Bank ID')?></label><div class="col-xs-7 col-sm-9"> <?=$cheque->bank_id;?></div>
                        </div> 
                    <?php endif; ?>
                     
                    <!-- <div class="row">
                         
                        <label class="col-xs-5 col-sm-3"><?=Yii::t('common','Date')?></label><div class="col-xs-7 col-sm-9">
                         <?=$cheque->posting_date;?> <?=date('H:i',strtotime($cheque->transfer_time));?></div>
                    </div> 
 -->
                    <?php if($cheque->remark!='') : ?>
                        <div class="row">
                             
                            <label class="col-xs-5 col-sm-3"><?=Yii::t('common','Remark')?></label><div class="col-xs-7 col-sm-9">
                             <?=$cheque->remark;?> </div>
                        </div> 
                    <?php endif; ?>


                    <?php if($cheque->type=='ATM') : ?>
                        <div class="row">
                             

                            <label class="col-xs-12"><?=Yii::t('common','To account')?></label>
                            <div class="col-xs-12">
                                <div class="well" >
                                     <div><?=$cheque->bankaccount->name;?> </div>
                                     <div><?=$cheque->bankaccount->bank_no;?> </div>
                                     <div><?=$cheque->posting_date;?> <?=date('H:i',strtotime($cheque->transfer_time));?> </div>
                                     <div class="bank-bg-well" style="background-image:url(uploads/<?=$cheque->bankaccount->banklist->imageFile?>);"></div>
                                </div>
                            </div>
                        </div> 
                    <?php endif; ?>

                </div>
            </div>

            
            <div class="panel panel-info">
                <div class="panel-heading">
                <a href="index.php?r=customers/customer/view&id=<?=$cheque->cust_no_?>" target="_blank"><i class="fa fa-address-card-o" aria-hidden="true"></i> <?=$cheque->customer->code?></a></div>
                <div class="panel-body">
                    <div class="row">   
                       <div class="col-xs-12"> 
                        <?=$cheque->customer->name?>
                            
                        </div>
                        <div class="col-xs-12 text-right"> <i class="fa fa-map-marker" aria-hidden="true"></i> 
                        <?php if($cheque->customer->province!='') echo $cheque->customer->provincetb->PROVINCE_NAME; ?>
                            
                        </div>
                    </div> 
                    <div class="row"><hr></div>
                    <?php 
                                
                        if($cheque->customer->credit_limit > 1000000){
                            $credit_limit = number_format($cheque->customer->credit_limit/1000000,3).' (m)'; 
                        }else {
                            $credit_limit = number_format($cheque->customer->credit_limit,2);
                        } 



                        $SumBalance = 0;

                        $SaleHeader = \common\models\SaleHeader::find()
                        ->where(['customer_id' => $cheque->cust_no_])
                        ->andWhere(['or',
                                    ['status'=>'Shiped'],
                                    ['status' => 'Checking'],
                                    ['status' => 'Invoiced'],
                                    ['status' => 'Pre-Cancel']
                                ]);

                        $SumBalance = $SaleHeader->sum('balance');






                        $Available  = 0;

                        // ยังไม่ Approve
                        $NotApprove   =  \common\models\Approval::find()->where(['comp_id'=>Yii::$app->request->get('comp_id')])->all();
                        $NappId  = array();
                        foreach ($NotApprove as $value) {
                            $NappId[] = ''.$value->source_id.'';
                        }


                        $allCheque  = \common\models\Cheque::find()->where(['cust_no_' => $cheque->cust_no_])->andWhere(['NOT IN','id',$NappId]);

                        $TotalRc    = $allCheque->sum('balance'); 



                        // Approve แล้ว
                        $Approved   =  \common\models\Approval::find()->where(['comp_id'=>Yii::$app->request->get('comp_id')])->all();
                        $appId  = array();
                        foreach ($Approved as $value) {
                            $appId[] = ''.$value->source_id.'';
                        }
                        $allChequeApp  = \common\models\Cheque::find()->where(['cust_no_' => $cheque->cust_no_])->andWhere(['IN','cheque.id',$appId]);

                        $TotalRcApp    = $allChequeApp->sum('balance'); 
                      

                        // ขาย ลบจ่าย(ที่ approve แล้ว)
                        $PayIn      = $SumBalance - $TotalRcApp;




                        // มี Invoice อะไรบ้าง
                        // แต่ละ Invoice  มีมูลค่าเท่าไหร่
                        $PostedInvoice  = \common\models\RcInvoiceHeader::find()->where(['cust_no_' => $cheque->cust_no_])->all();
                        $SumPostedInv   = 0;
                        foreach ($PostedInvoice as $key => $value) {

                            //var_dump($value->no_);
                            $SumPostedInv  += \admin\modules\accounting\models\FunctionAccounting::getTotalBalance($value,'RcInvoiceLine');
                        }
                        

                        // $TotalInv       = $PostedInvoice->sum('');
                        //$Usage      = $cheque->customer->credit_limit - $SumBalance;

                        // $Available  = $cheque->customer->credit_limit - $TotalRc;

                        // if($Available > 1000000){
                        //     $Available = number_format($Available/1000000,3).' (m)'; 
                        // }else {
                        //     $Available = number_format($Available,2);
                        // } 
                        $CusInfo = \common\models\Customer::Info($cheque);

                    ?>
                    
                    <fieldset>
                        <legend><h5><i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Credit')?></h5></legend>
                        <div class="row">
                            
                            <div class="col-xs-5"><?=Yii::t('common','Limit')?></div>
                            <div class="col-xs-7 text-right"><?=$credit_limit;?></div>
                        </div> 
                        <div class="row">
                            
                            <div class="col-xs-7"><?=Yii::t('common','Usage')?></div>
                            <div class="col-xs-5 text-right"><?=number_format($PayIn,2);?></div>
                        </div> 

                        <div class="row">
                             
                            <div class="col-xs-5"><?=Yii::t('common','Available')?></div>
                            <div class="col-xs-7 text-right"> 
                                <div class="<?=$cheque->customer->credit_limit - $PayIn < 0 ? 'text-red' : 'text-success'; ?>"><?=number_format($cheque->customer->credit_limit - $PayIn,2);?></div>
                            </div>
                        </div>
 
                        

                        
                    </fieldset>
                    <div class="row"><hr></div>
                    <fieldset>
                        <legend><h5><i class="fa fa-usd" aria-hidden="true"></i> <?=Yii::t('common','Payment')?></h5></legend>
                        <div class="row">
                            
                            <div class="col-xs-6"><?=Yii::t('common','ยอดหนี้')?></div>
                            <div class="col-xs-6 text-right"> 
                                <div class=""><?=number_format($SumPostedInv,2);?></div>
                            </div> 
                        </div>
                        <div class="row">
                             
                            <div class="col-xs-7"><?=Yii::t('common','ยอดที่ชำระ')?></div>
                            <div class="col-xs-5 text-right"><?=number_format($TotalRcApp,2);?></div>
                        </div>  
                        <div class="row">
                            
                            <div class="col-xs-6"><?=Yii::t('common','ยอดค้างชำระ')?></div>
                            <div class="col-xs-6 text-right"> 
                                <div class="<?=$SumPostedInv - $TotalRcApp < 0 ? 'text-red' : 'text-success'; ?>"><?=number_format($SumPostedInv - $TotalRcApp,2);?></div>
                            </div> 
                        </div>

                        <div class="row">
                            
                            <div class="col-xs-7"><?=Yii::t('common','ยอดเครดิตคงเหลือ')?></div>
                            <div class="col-xs-5 text-right"> 
                                <div class="<?=$cheque->customer->credit_limit - ($SumPostedInv - $TotalRcApp) < 0 ? 'text-red' : 'text-success'; ?>">
                                    <?=number_format($cheque->customer->credit_limit - ($SumPostedInv - $TotalRcApp),2);?>
                                </div>
                            </div> 
                        </div>
                    </fieldset>

                </div>
            </div>   

            

            
            
        </div>



        <div class="col-md-6">
            <?php
                $EachCheque = $MoreCheque->all();
                foreach ($EachCheque as $key => $cheque) {

            ?>

            <div class="panel panel-warning">
                <div class="panel-heading"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?=Yii::t('common','Invoice')?></div>
                <div class="panel-body">
                    <div class="row">
                        <?php

                        $Total      = 0;
                        $SaleTotal  = 0;

                        $ext_doc    = ''; 
                        $ext_bal    = 0;

                        $diff       = 0;
                        $OrderID    = 0;

                        $sign = '<span style="color:green;"> + </span>';

                        
                            

                            if($cheque->apply_to_status=='Open'){

                                $Invoice = \common\models\SaleInvoiceHeader::findOne($cheque->apply_to);
                                $Total = \admin\modules\accounting\models\FunctionAccounting::getTotalBalance($Invoice,'SaleInvoiceLine');

                            }else{

                                $Invoice = \common\models\RcInvoiceHeader::findOne($cheque->apply_to);
                                $Total = \admin\modules\accounting\models\FunctionAccounting::getTotalBalance($Invoice,'RcInvoiceLine');

                            }

                                
                               

                                $SaleOrder = \common\models\SaleHeader::find()->where(['id' => $Invoice->order_id]);

                                if($SaleOrder->exists()){
                                    $Order = $SaleOrder->one();

                                    $SaleTotal = \admin\modules\SaleOrders\models\FunctionSaleOrder::getTotalBalance($Order);
                                    $ext_doc = $Order->no; 
                                    $ext_bal = $SaleTotal; 

                                    $OrderID = $Order->id;

                                    
                                } 

                                // เก็บค่า Sale Order ลบ Invoice 
                                $diff = $SaleTotal - $Total;


                                // Sale Order 
                                $inv = '<div class="col-xs-12"><h5>'.Yii::t('common','Sale Order').'</h5></div>
                                        <div class="col-xs-8 text-left">
                                            <a href="index.php?r=SaleOrders/saleorder/print-page&id='.$OrderID.'&footer=1" target="_blank">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> '.$ext_doc.'</a></div>
                                        <div class="col-xs-4 text-right">'.number_format($ext_bal,2).'</div> ';




                                $inv.= '<div class="col-xs-12"><hr></div>';  

                                // Invoice
                                if($cheque->apply_to_status=='Open'){

                                    $urlAcc = "index.php?r=accounting%2Fsaleinvoice%2Fprint-inv-page&id=".$Invoice->id;
                                    $fileColor = 'text-warning';
                                }else{
                                    $urlAcc = "index.php?r=accounting%2Fposted%2Fprint-inv&id=".base64_encode($Invoice->id);
                                    $fileColor = 'text-success';
                                }
                                $inv.= '<div class="col-xs-12"><h5>'.Yii::t('common','Invoice').'</h5></div>
                                        <div class="col-xs-8 text-left">
                                            <a href="'.$urlAcc.'&footer=1" target="_blank">
                                            <i class="fa fa-file-pdf-o '.$fileColor.'" aria-hidden="true"></i> '.$Invoice->no_.'</a></div>
                                        <div class="col-xs-4 text-right">'.number_format($Total,2).'</div>';



                                // แสดงใบรับที่เคยรับใน Invoice นี้
                                $NewCheque = \common\models\Cheque::find()->where(['apply_to' => $Invoice->id]);
                                $SumCheque = $NewCheque->sum('balance');
                                 
                                
                                if($SumCheque > 0){

                                    $inv.= '<div class="col-xs-12" style="margin-top:10px;">'.Yii::t('common','Receipt').'</div>';

                                    $CheckAll = $NewCheque->all();
                                    foreach ($CheckAll as $key => $ck) {

                                        $lineColor = NULL;

                                        if($ck->id == $cheque->id) $lineColor = 'text-primary';

                                        $inv.= '<div class="col-xs-6 text-right '.$lineColor.'">
                                                    <span class="hidden-xs">'.Yii::t('common',$ck->type).'</span> 
                                                    '.Yii::t('common',$ck->post_date_cheque).' 
                                                    <span class="hidden-xs">'.date('H:i',strtotime($cheque->transfer_time)).'</span> 
                                                </div>                                                
                                                <div class="col-xs-6 text-right '.$lineColor.'">'.number_format($ck->balance,2).'</div>
                                                 ';
                                         
                                     } 

                                    $inv.= '<div class="col-xs-6 text-left" style="margin-top:10px;">'.Yii::t('common','Total Receipt').'</div>
                                            <div class="col-xs-6 text-right" style="margin-top:10px;"><span style="border-bottom:4px double #ccc; max-width:100%;">'.number_format(abs($SumCheque),2).'</span></div>';  

                                    

                                } 



        

                                // ถ้า Sale Order ลบ Invoice ไม่เท่ากับ 0 ให้แสดงผลต่าง
                                if($diff!=0){

                                    if($Total < $SaleTotal) $sign = '<span style="color:red;"> - </span>';
                                    $inv.= '<div class="col-xs-12"><hr></div>';  

                                    $inv.= '<div class="col-xs-12" style="color:red;">'.Yii::t('common','Sale Order').' VS '.Yii::t('common','Invoice').'</div>
                                            <div class="col-xs-6 text-left">'.Yii::t('common','Different').'</div>
                                            <div class="col-xs-6 text-right">'.$sign.number_format(abs($diff),2).'</div>';

                                } 

                                // ถ้ายอดรับเงิน(รวม) เกิน Invoice
                                if($SumCheque > $Total){
                                    $inv.= ' 
                                            <div class="col-xs-6 text-left">'.Yii::t('common','Over').'</div>
                                            <div class="col-xs-6 text-right">'.number_format(abs($SumCheque - $Total),2).'</div>';
                                }


                                

                                echo $inv;
                            

                                # code...
                         
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
<script>
    
window.addEventListener('touchstart', function() {
  // the user touched the screen!
  //alert('-_-');
});
</script>


