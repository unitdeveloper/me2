<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;
$Bahttext           = new FunctionBahttext();
$Fnc                = new FunctionSaleOrder();
$HeightContent      = '885px';
$Font_size_Content  = '16px';
$Company            = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();
// ไม่ได้เลือก Sales People
// หากไม่ได้เลือกลูกค้า
if(empty($model->sales_people)) return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/update/', 'id' => $model->id]));
if(empty($model->customer_id)) return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/update/', 'id' => $model->id])); 
?>

<!DOCTYPE html>
<html lang="en">
  <head></head>
  <title><?=$model->no?></title>
<body>
    <table  border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 5px;">
        <tr>
            <td valign="bottom" >
                <?PHP if($model->vat_percent != 0): ?>
                <img src="<?=$Company->logoViewer; ?>" style="width: 100px;">
            <?php endif; ?>
            </td>
            <td valign="bottom" align="center" style="font-size: 12px;">                
                <div class="col-sm-12" >
                <?PHP if($model->vat_percent != 0): ?>
                    <h4 style="font-size: 18px;"><?=$Company->name; ?></h4><br>
                     <span style="font-size: 14px;"><?=$Company->name_en; ?></span> <br>
                    <div style="font-size: 14px;">
                      <?=$Company->vat_address; ?>  อ.<?=$Company->vat_city; ?> จ.<?=$Company->vat_location; ?> <?=$Company->postcode; ?>
                      <span style="font-size: 2px;"></span><br>
                      <?=$Company->phone; ?> <?=$Company->fax; ?> <?=$Company->mobile; ?><br> 
                    </div> 
                <?php endif; ?>
              </div>
            </td>
            <td align="center">
                <table style="width:100%; border: 1px solid #000; padding:10px; background-color: #ccc;" border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td><span>ใบสั่งขาย <br>Sale Order <br></span> </td>
                    </tr>                    
                </table>
            </td>
        </tr>
        <tr>
            <td valign="bottom" colspan="2" width="500">
                <div class="row" >
                <?PHP if($model->vat_percent != 0): ?>
                  <div class="col-sm-12" style="margin-top: 50px;">
                  เลขประจำตัวผู้เสียภาษี <?=$Company->vat_register; ?> <span style="margin-left: 30px; font-size: 14px; font-weight: bold;"><?=$Company->headofficetb->data_char; ?></span></div>
                <?php endif; ?>    
                </div>

            </td>
            <td valign="top">
                
                <table style="width:100%; border: 1px solid #000; font-size: 10px;" border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td align="center" style="border-bottom: 1px solid #000; width: 50px; height: 40px;">
                            เลขที่<br>
                            No.<br>
                        </td>
                        <td align="center" style="border-bottom: 1px solid #000; border-left: 1px solid #000; width: 80px;">
                            <?= $model->no ?> 
                            </td>
                    </tr>
                    <tr >
                        <td align="center" style=" height: 40px;">
                        วันที่<br>
                        Date.<br>
                        </td>
                        <td align="center" style="border-left: 1px solid #000;  ">
                            <?= date('d / m / Y',strtotime($model->order_date)) ?> 
                        </td>
                    </tr>    
                </table>

            </td>
        </tr>
    </table>
    <table style="width:100%; font-size: 30px;" border="0" cellpadding="0" cellspacing="0" >
        
        
        <tr>
            <td valign="top" colspan="3">
                <table width="100%"   border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px;  height: 250px;">
                            <p style="margin-top: 0px;">
                               <?php 
                                     
                                

                                    if($model->customer->province!='')
                                    {
                                        $findProvince   = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;

                                        if( strpos( $model->ship_address, $findProvince )) {

                                            str_replace($model->ship_address, 'จ.'.$model->customer->provincetb->PROVINCE_NAME, 'จ.'.$model->customer->provincetb->PROVINCE_NAME);
                                        }else {

                                            $model->ship_address = $model->ship_address.' '.$findProvince;

                                        }

                                        
                                    }


                                    if($model->customer->postcode!='')
                                    {
                                        $findPost   = $model->customer->postcode;

                                        if( strpos( $model->ship_address, $findPost )) {

                                            str_replace($model->ship_address, $model->customer->postcode, $model->customer->postcode);
                                        }else {
                                            $model->ship_address = $model->ship_address.' '.$findPost;

                                        }
                                    } 
                                   


    
                                ?>     

                                ชื่อลูกค้า : <?= $model->customer->name ?><br> <br>
                                ที่อยู่ : <?= wordwrap($model->ship_address, 150, "<br/>\r\n") ?> <br>
                                
                                
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
                            <?PHP if($model->vat_percent != 0): ?>
                            เลขประจำตัวผู้เสียภาษี : <?= $model->customer->vat_regis ?>  <?=$headeroffice ?>  <br>
                            <?php endif; ?>
                            
                            โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?>
                                 
                        </td>
                     
                        <td  valign="top" style="border: 1px solid #000;">
                   
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr > 
                                    <td style="text-align: left; width: 100px;  font-size: 25px;   padding:5px 0 0 5px; border-right: 1px solid #000; height: 95px;">
                                        พนักงานขาย <br>
                                        SALESMAN<br>
                                    </td>
                                    <td style="text-align: center; width: 350px; font-size: 25px; padding:5px 0 0 5px; ">
                                        <?= $model->sales->code ?> - <?= $model->sales->name ?> <?= $model->sales->surname ?>
                                    </td>
                                </tr>
                                <tr  > 
                                    <td style="text-align: left; width: 320px;  font-size: 25px; padding:5px 0 0 5px; border-top: 1px solid #000;border-right: 1px solid #000; height: 95px;">
                                        เงื่อนไขการชำระเงิน <br>
                                        TERM OF PAYMENT <br>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 25px; padding:5px 0 0 5px; border-top: 1px solid #000;">
                                        <?php if($model->payment_term!='0') echo $model->payment_term.' วัน';  ?> 
                                    </td>
                                </tr>
                                <tr > 
                                    <td style="text-align: left; width: 100px;  font-size: 25px; padding:5px 0 0 5px; border-top: 1px solid #000;border-right: 1px solid #000; height: 95px;">
                                        กำหนดชำระ <br>
                                        DUE DATE<br>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 25px; padding:5px 0 0 5px; border-top: 1px solid #000;">
                                        <?= $model->paymentdue ?>
                                    </td>
                                </tr>
                                <tr> 
                                    <td style="text-align: left; width: 100px;  font-size: 25px; padding:5px 0 0 5px; border-top: 1px solid #000;border-right: 1px solid #000; height: 95px;">
                                    <p style="margin-top: 3px;">
                                        ใบสั่งซื้อ เลขที่ <br>
                                        PO.NO.<br></p>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 15pt; border-top: 1px solid #000;">
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
            <td valign="top" colspan="3" style="height: <?=$HeightContent ?>; border-left: 1px solid 000; border-right: 1px solid 000;">

                <table class="layout" border="0" cellpadding="0" cellspacing="0"  width="100%"   >
                    <thead>
                        <tr >
                            <td class="thead" style="width:40px; height: 80px; text-align: center; border: 1px solid #000; font-size: 28px; ">ลำดับ</td>
                            <td class="thead"  style="width:130px; text-align: center; border: 1px solid #000;  font-size: 28px;">รหัสสินค้า</td>
                            <td class="thead"  style="width:298px; text-align: center; border: 1px solid #000;  font-size: 28px;">รายการ</td>
                            <td class="thead"  style="width:100px; text-align: center; border: 1px solid #000;  font-size: 28px;">จำนวน</td>
                            <td class="thead"  style="width:100px; text-align: center; border: 1px solid #000;  font-size: 28px;">ราคาต่อหน่วย</td>
                            <td class="thead"  style="width:80px; text-align: center; border: 1px solid #000;  font-size: 28px;">ส่วนลด</td>
                            <td class="thead"  style="width:130px; text-align: center; border: 1px solid #000;  font-size: 28px;">จำนวนเงิน</td>      
                        </tr >
                    </thead>
                    <tbody >  
                        <?php 

                            Yii::$app->session->set('vat',$model->include_vat);

                            $td = '';
                            $i =0;
                            foreach ($dataProvider->models as $saleline) {
                                $i++;

                                
                                $discount = $saleline->line_discount;
                                

                                if(Yii::$app->session->get('vat')==1) // Include Vat.
                                {
                                    $unitprice = $saleline->unit_price;
                                    $amount = $saleline->quantity * $saleline->unit_price;
                                }else  {    // Exclude Vat.
                                    $unitprice = $saleline->unit_price;
                                    $amount = $saleline->quantity * $saleline->unit_price;
                                } 

                                $td.= '<tr>';
                                    $td.= '<td class="layout" align="center" style="border-bottom: 1px solid #ccc; height:60px; font-size:28px;">'.$i.'</td>';
                                    $td.= '<td class="layout" style="padding-left:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.$saleline->items->master_code.'</td>';

                                    if($saleline->description==''){
                                      
                                         $td.= '<td class="layout" style="padding-left:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.$saleline->items->description_th.'</td>';
                                      }else {
                                        
                                         $td.= '<td class="layout" style="padding-left:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.$saleline->description.'</td>';
                                      }

                                   




                                    $td.= '<td class="layout" align="center" style="padding-left:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.$saleline->quantity.' '.$saleline->items->UnitOfMeasure.'</td>';   


                                                              
                                    $td.= '<td class="layout" align="right" style="padding-right:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.number_format($unitprice,2).'</td>';

                                    $td.= '<td class="layout" align="right" style="padding-right:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.$discount.'</td>';


                                    $td.= '<td class="layout" align="right" style="padding-right:5px; border-bottom: 1px solid #ccc; font-size:28px;">'.number_format($amount,2).'</td>';
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

                              
                              

                              if($model->discount != 0)
                              { 
                                $rowspan = '3';
                              }else {
                                $rowspan = '2';
                              }
                        ?>
                    </tbody>
                       
                    </tr>

                    
                </table>
                  


            </td>

        </tr>
        <tr>
            <td colspan="3" style=" border-left: 1px solid 000; border-right: 1px solid 000; border-bottom:  1px solid 000;">

                <table border="0" cellpadding="0" cellspacing="0"  width="100%">
                    <tr>
                        <td valign="top" colspan="4" rowspan="<?=$rowspan?>" style="border-top: 1px solid #000; padding:5px; font-size: 26px;" >
                            หมายเหตุ :<br>
                            REMARK<br><br>
                            <div style="color:red;">
                                &nbsp;&nbsp;<?=$model->remark ?>
                            </div>
                        </td>

                        <td colspan="2" style="width:160px; border-left: 1px solid #000; border-top: 1px solid #000; padding:5px; font-size: 25px;">
                            ราคารวม <br>
                            NET TOTAL <br>
                          <br>
                           
                        </td>
                        <td align="right" style="width:130px; padding-right:5px; border-left: 1px solid #000; border-top: 1px solid #000; font-size: 25px;" >
                            <?= number_format($BeforeDisc,2) ?>
                        </td>
                       
                    </tr>

                    <?php if($model->discount != 0): // ถ้ามีส่วนลด ?>
                    <tr>
                        <td   style="border-left: 1px solid #000; padding:5px; font-size: 25px;" valign="bottom"> 

                            หลังหักส่วนลด  
                        </td>
                        <td align="right" valign="bottom" style="font-size: 25px;" >
                             
                            <p><?= number_format($model->discount,2) ?> </p>
                            
                        </td>

                        <td align="right" style="padding-right:5px; border-left: 1px solid #000; font-size: 25px; width: 180px;" >
                            <p><?= number_format($subtotal,2) ?> </p>
                        </td>
                       
                    </tr>
                    <?php endif; ?>


                    <tr>
                        <td   style="border-left: 1px solid #000; padding:5px; font-size: 25px;" valign="bottom"> 

                            ราคารวม <br>

                            ภาษีมูลค่าเพิ่ม VAT<br>

                        </td>
                        <td align="right" valign="bottom" style="font-size: 25px;" ><?= $vat ?> % </td>

                        <td align="right" style="padding-right:5px; border-left: 1px solid #000; font-size: 25px; width: 180px;" >
                            <?php if($model->include_vat == 0): // Vat ใน ?>
                            <p><?= number_format($subtotal - $InCVat,2) ?> </p>
                            <?php endif; ?>

                           

                            <?= number_format($InCVat,2) ?>
                        </td>
                       
                    </tr>


                    <tr>
                        <td align="center" style="background-color: #ccc; border-top: 1px solid #000; border-right: 1px solid #000; font-size: 25px; height: 80px;" > 
                           <b> บาท <br>
                            BAT <br></b>
                        </td>
                         <td colspan="3" style="padding-left:5px; background-color: #ccc; border-top: 1px solid #000; font-size: 25px;">
                            (<?= $Bahttext->ThaiBaht($total) ?>)
                         </td>
                         <td colspan="2" style="border-right: 1px solid #000; padding-left: 5px; font-size: 25px; background-color: #000; font-weight: bold; color:#FFF; border-top: 1px solid #000;">
                          จำนวนเงินรวมทั้งสิน <br>
                                  GRAND TOTAL <br>
                                </td> 
                        <td align="right" style="padding-right:5px; background-color: #ccc; border-top: 1px solid #000; font-size: 25px;">
                            <?= number_format($total,2) ?>                            
                        </td>
                    </tr>

                </table>
                       
            </td>
        </tr>
       
    </table>
    
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 5px;">
        <tr>    
            <td >
                <p style="font-size: 11px;"><br>
                <?PHP if($model->vat_percent != 0): ?>
                   - หากมีปัญหาเกี่ยวกับตัวสินค้า โปรดติดต่อกลับทางบริษัทฯ ภายใน 7 วัน<br>
                   - กรณีชำระด้วยเช็คโปรดสั่งจ่าย และขีดคร่อมในนาม บริษัท จีโนล กรุ๊ป ซีที อิเล็คทริคฟิเคชั่น จำกัด เท่านั้น<br>
                   &nbsp;&nbsp;และการชำระเงินจะสมบูรณ์ต่อเมื่อเช็คนั้นเรียกเก็บจากธนาคารได้ครบถ้วนแล้ว<br>
                   - กรณีโอนเงิน โอนในนาม บริษัท จีโนล กรุ๊ป ซีที อิเล็คทริคฟิเคชั่น จำกัด ธนาคารกสิกรไทย สาขาถนนเศรษฐกิจ 1 บัญชีเลขที่ 464-1-02799-0
                <?php endif; ?>
                </p>
                
            </td>
        </tr>

        <tr>    
            <td >

                    <table width="100%" border="0" cellpadding="0">
                        <tr>
                            <td width="400">
                                <?PHP if($model->vat_percent != 0): ?>
                                    <div  style="padding-top: 5px;">
                                        <span style="font-size: 12PX; text-align: center;">
                                        <p style="padding: 2px;">จ่ายชำระเงิน (...) เงินโอน  (...) เงินสด (...) เช็คเลขที่................. </p>
                                        <p>ลงวันที่...................ธนาคาร........................สาขา................ </p>
                                        <p>จำนวนเงิน.................... บาท (...........................................)</p>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td align="center">
                                 <br>
                                
                                     
                                    <?php
                                        if($model->sales->sign_img!=''){
                                            echo '<img src="images/sign/'.$model->sales->sign_img.'" height="32px">';
                                        }else {
                                            echo '<br><br><br>';
                                        }
                                    ?>
                                    
                                    ...........................................
                                      <p style="font-size: 10px;">
                                      ผู้สั่งขายสินค้า<br>
                                      AUTHORIZED SIGNATURE

                                      </p>
                                      </p>
                                </span>  
                                
                            </td>
                            <td align="center">
                                <br>
                                <div >
                                    <br><br>
                                    ...........................................
                                      <p style="font-size: 10px;">
                                      ผู้จัดสินค้า<br>
                                      AUTHORIZED SIGNATURE

                                      </p>
                                      </p>
                                </div>  
                            </td>
                            <td align="center">
                                <br>
                                <div >
                                    <br><br>
                                     ...........................................
                                      <p style="font-size: 10px;">
                                      ผู้รับมอบอำนาจ<br>
                                      AUTHORIZED SIGNATURE

                                      </p>
                                      </p>
                                </div>  
                            </td>






                        </tr>

                    </table>
                     
            </td>
        </tr>
    </table>  
<!-- <pagebreak /> -->
    




  </body>
</html>
 