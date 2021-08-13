<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;

$Bahttext = new FunctionBahttext();
$Fnc = new FunctionSaleOrder();




$HeightContent              = '805px';
$Font_size_Content          = '16px';


$Company = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

 

?>
 
 
          
          
 

<!DOCTYPE html>
<html lang="en">
  <head>
 
</head>

<body>
    <table   border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 5px;">
        <tr>
            <td valign="bottom" >
                 
                 
             
            </td>
            <td valign="bottom" align="center" style="font-size: 12px;">
                
                <div class="col-sm-12" >
                
                     
                
              </div>

            </td>

        </tr>
        <tr>
            <td valign="bottom" colspan="2" width="500">
                <div class="row" >
               
                  
                 
                </div>

            </td>
            <td valign="top">
                <!-- ส่วนของเลขที่และวันที่เอกสาร -->
                <table style="width:100%; font-size: 14px; margin-top: 80px;" border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td align="center" style="width: 50px;"></td>
                        <td align="center" style=" width: 80px;">
                            <?= $model->no_ ?> 
                            </td>
                    </tr>
                    <tr >
                        <td align="center" style=" height: 35px; "></td>
                        <td align="center" valign="bottom" style="border: 0px solid #ccc;">
                            <?= date('d / m / Y',strtotime($model->order_date)) ?> 
                        </td>
                    </tr>    
                </table>
            </td>
        </tr>
    </table>
    <table style="width:100%; font-size: 28px" border="0" cellpadding="0" cellspacing="0" >
        
        
        <tr>
            <td valign="top" colspan="3">
                <table  style="margin-top: 15px;" width="100%"   border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td valign="top"  style="width:900px; padding: 8px 8px 8px;  height: 200px;">
                            <!-- <p style="margin-top: -100px;"> -->
                               <?php 
                                     
                              
                                    if($model->customer->province!='')
                                    {
                                        $findProvince   = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;

                                        if( strpos( $model->cust_address, $findProvince )) {

                                            str_replace($model->cust_address, 'จ.'.$model->customer->provincetb->PROVINCE_NAME, 'จ.'.$model->customer->provincetb->PROVINCE_NAME);
                                        }else {

                                            $model->cust_address = $model->cust_address.' '.$findProvince;

                                        }

                                        
                                    }


                                    if($model->customer->postcode!='')
                                    {
                                        $findPost   = $model->customer->postcode;

                                        if( strpos( $model->cust_address, $findPost )) {

                                            str_replace($model->cust_address, $model->customer->postcode, $model->customer->postcode);
                                        }else {
                                            $model->cust_address = $model->cust_address.' '.$findPost;

                                        }
                                    } 
                                   


    
                                ?>     

                                ชื่อลูกค้า : <?= $model->customer->name ?><br> <br>
                                ที่อยู่ : <?= wordwrap($model->cust_address, 150, "<br/>\r\n") ?> <br>
                                
                                
                            </p>

                            <br>
                            <br>

                            <?php 

                                if($model->customer->headoffice == 1 ){
                                    $headeroffice =  ' สำนักงานใหญ่';
                                }else {
                                    $headeroffice =  NULL;
                                }

                            ?>
                            
                            เลขประจำตัวผู้เสียภาษี : <?= $model->customer->vat_regis ?>  <?=$headeroffice ?>  <br>
                             
                            
                            โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?>
                                 
                        </td>
                     
                        <td  valign="top" style="height: 380px; border: 1px solid #fff;">
                   
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr > 
                                    <td style="text-align: left; width: 100px;  font-size: 10px;; height: 95px;">
                                       <!--  พนักงานขาย  --><br>
                                        <!-- SALESMAN --><br>
                                    </td>
                                    <td style="text-align: left; width: 400px; font-size: 25px; ">
                                        <?= $model->sales->code ?> - <?= $model->sales->name ?> <?= $model->sales->surname ?>
                                    </td>
                                </tr>
                                <tr  > 
                                    <td style="text-align: left; width: 320px;  font-size: 25px; padding:5px 0 0 5px; height: 65px;">
                                        <!-- เงื่อนไขการชำระะเงิน  --><br>
                                        <!-- TERM OF PAYMENT --> <br>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 25px; ">
                                        <?php 
                                            if($model->payment_term == '0'){
                                                echo Yii::t('common','Cash');  
                                            }else {
                                                echo $model->payment_term.' วัน';
                                            }
                                        ?> 

                                    </td>
                                </tr>
                                <tr > 
                                    <td style="text-align: left; width: 100px;  font-size: 25px; padding:5px 0 0 5px;height: 65px;">
                                        <!-- กำหนดชำระ --> <br>
                                        <!-- DUE DATE --><br>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 25px; padding:5px 0 0 5px;">
                                        <?= $model->paymentdue ?>
                                    </td>
                                </tr>
                                <tr> 
                                    <td style="text-align: left; width: 100px;  font-size: 25px; padding:5px 0 0 5px;  height: 65px;">
                                    <p style="margin-top: 3px;">
                                        <!-- ใบสั่งซื้อ เลขที่  --><br>
                                       <!--  PO.NO. --><br></p>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 15pt; ">
                                        <?= $model->ext_document ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                       
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>
        <tr>
            <td valign="top" colspan="3" style="height: <?=$HeightContent ?>; border: 1px solid #fff;">

                <table class="layout" border="0" cellpadding="0" cellspacing="0"  width="100%" style="margin-top:-60px;margin-right: 50px; margin-left: 15px;"  >
                    <thead>
                        <tr >
                            <td class="thead" style="width:75px; height: 38px; font-size: 25px;"><!-- ลำดับ --></td>
                            <td class="thead"  style="width:220px; font-size: 25px;"><!-- รหัสสินค้า --></td>
                            <td class="thead"  style="width:px; font-size: 25px;"><!-- รายการ --></td>
                            <td class="thead"  style="width:100px; font-size: 25px;"><!-- จำนวน --></td>
                            <td class="thead"  style="width:150px; font-size: 25px;"><!-- ราคาต่อหน่วย --></td>
                            <td class="thead"  style="width:290px; font-size: 25px;"><!-- ส่วนลด --></td>
                            <td class="thead"  style="width:100px; font-size: 25px;"><!-- จำนวนเงิน --></td>      
                        </tr >
                    </thead>
                    <tbody >  
                        <?php 

                            Yii::$app->session->set('vat',$model->include_vat);

                            $td = '';
                            $i =0;
                            foreach ($dataProvider->models as $saleinvline) {
                                $i++;

                                
                                $discount = number_format($saleinvline->line_discount);
                                if($discount==0) $discount ='';

                                if(Yii::$app->session->get('vat')==1) // Include Vat.
                                {
                                    $unitprice = $saleinvline->unit_price;
                                    $amount = $saleinvline->quantity * $saleinvline->unit_price;
                                }else  {    // Exclude Vat.
                                    $unitprice = $saleinvline->unit_price;
                                    $amount = $saleinvline->quantity * $saleinvline->unit_price;
                                } 

                                $td.= '<tr>';
                                    $td.= '<td class="layout" align="center" style="height:60px; font-size:25px;">'.$i.'</td>';
                                    $td.= '<td class="layout" style="padding-left:5px; font-size:25px;">'.$saleinvline->itemstb->master_code.'</td>';

                                    if($saleinvline->code_desc_==''){
                                      
                                         $td.= '<td class="layout" style="padding-left:5px; font-size:25px;">'.$saleinvline->itemstb->description_th.'</td>';
                                      }else {
                                        
                                         $td.= '<td class="layout" style="padding-left:5px; font-size:25px;">'.$saleinvline->code_desc_.'</td>';
                                      }

                                   




                                    $td.= '<td class="layout" align="right" style="padding-left:5px; font-size:25px;">
                                            '.number_format($saleinvline->quantity,2).' '.$saleinvline->itemstb->UnitOfMeasure.'
                                          </td>';   


                                                              
                                    $td.= '<td class="layout" align="right" style="padding-right:5px; border-bottom: 0px solid #ccc; font-size:25px;">
                                            '.number_format($unitprice,2).'
                                           </td>';

                                    $td.= '<td class="layout" align="right" style="padding-right:5px; border-bottom: 0px solid #ccc; font-size:25px;">
                                            '.$discount.'
                                           </td>';


                                    $td.= '<td class="layout" align="right" style="padding-right:5px; border-bottom: 0px solid #ccc; font-size:25px;">
                                            '.number_format($amount,2).'
                                           </td>';
                                $td.= '</tr>';
                            }
                            
                            echo $td;

    


                              // $vat      = $model->vat_percent; 
                              // $subtotal = $Fnc->getTotalSaleOrderExvat($dataProvider->models,Yii::$app->session->get('vat'));
                              // $InCVat   = ($subtotal * $vat )/ 100;
                              // $Discount = $model->discount;

                              // $BeforDis = $InCVat + $subtotal;
                              // $total    = ($InCVat + $subtotal) - $Discount;

                              $vat          = $model->vat_percent; 
                              $BeforeDisc   = $Fnc->getTotalSaleOrder($dataProvider->models);
                              
                              $Discount     = $model->discount;

                              // หักส่วนลด (ก่อน vat)
                              $subtotal     = $BeforeDisc - $Discount ;
                              

                              if($model->include_vat == 1){ 

                                // Vat นอก
                                

                                $InCVat   = ($subtotal * $vat )/ 100;
                                
                                $total    = ($InCVat + $subtotal);
                              }else {

                                // Vat ใน

                                 

                                $InCVat   = $subtotal - ($subtotal / 1.07);
                                
                                $total    = $subtotal;
                              }

                              
                              
 
                        ?>
                    </tbody>
                       
                    </tr>

                    
                </table>
                  


            </td>

        </tr>
        <tr>
            <td colspan="3">

                <table style="margin-top: 10px;" border="0" cellpadding="0" cellspacing="0"  width="100%">
                    <tr>
                        <td valign="top" colspan="4" rowspan="3" style="padding:5px; font-size: 28px;" >
                           
                            <div style="">
                                &nbsp;&nbsp;<?=$model->remark ?>
                            </div>
                        </td>

                        <td colspan="2" style="padding:5px; font-size: 25px; border: 1px solid #fff;text-align: right;">
                            รวมเป็นเงิน<br>
                            <!-- NET TOTAL  --><br>
                           
                        </td>
                        <td align="right" style="padding-right:50px; font-size: 25px;" >
                            <?= number_format($BeforeDisc,2) ?>
    
                        </td> 
                       
                    </tr>

                    
                    <tr>
                        <td   style="padding:5px; font-size: 25px;  border: 1px solid #fff;" valign="bottom"> 

                            <!-- หลังหักส่วนลด  --> 
                        </td>
                        <td align="right" valign="top" style="font-size: 25px;  border: 1px solid #fff;" >
                             
                             <p>ส่วนลด</p>
                             
                        </td>

                        <td align="right" style="padding-right:50px;font-size: 25px; border: 1px solid #fff;" >
                            <p style="height: 25px;"> 
                                <?= number_format($model->discount,2) ?> 
                             </p>
                            <p><?= number_format($subtotal,2) ?> </p>
                        </td>
                       
                    </tr>
                    


                    <tr>
                        <td   style="padding:0px; font-size: 25px;" valign="bottom"> 

                           <!--  ราคารวม  <br>-->

                            <!-- ภาษีมูลค่าเพิ่ม VAT <br>-->

                        </td>

                        <td align="right" valign="bottom" style="font-size: 25px; border: 1px solid #fff;" ><?= $vat ?> % </td>

                        <td align="right" valign="bottom" style="padding-right:50px;font-size: 25px;padding-top: 10px; border: 1px solid #fff; height: 75px;" >
                            
                            <?php if($model->include_vat == 0): // Vat ใน ?>

                            <p><?= number_format($subtotal - $InCVat,2) ?> </p>

                            <?php endif; ?>

                            <?= number_format($InCVat,2) ?>

                        </td>
                       
                    </tr>


                    <tr>
                        <td align="center" style="font-size: 25px;" >
                        </td>
                         <td colspan="3" style="padding-left:5px; font-size: 25px; height: 98px; border: 1px solid #fff;">
                            (<?= $Bahttext->ThaiBaht(abs($total)) ?>)
                         </td>
                         <td colspan="2" style="padding-left: 5px; font-size: 25px; font-weight: bold; color:#fff;">
                          <!-- จำนวนเงินรวมทั้งสิน --> <br>
                                  <!-- GRAND TOTAL  --><br>
                                </td> 
                        <td align="right" style="width:300px; padding-right:50px; font-size: 25px; margin-top: 5px; border: 1px solid #fff;">
                            <?= number_format($total,2) ?>                            
                        </td>
                    </tr>

                </table>
                       
            </td>
        </tr>
       
    </table>
    
     
<!-- <pagebreak /> -->
    




  </body>
</html>
 