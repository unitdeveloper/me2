<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
 
 
 
use common\models\Company;
 




$HeightContent              = '950px';
$Font_size_Content          = '16px';

$Company = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

if(empty($model->sales_people)) return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/update/', 'id' => $model->id]));

?>
 
 
          
          
 

<!DOCTYPE html>
<html lang="en">
  <head>
 
</head>

<body>
    <table   border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 5px;">
        <tr>
            <td valign="bottom" ><img src="<?=$Company->logoViewer; ?>" style="width: 80px;">
                
            <br>
             <?=$Company->name; ?><br>
             <?=$Company->name_en; ?><br>
             <?=$Company->vat_address; ?>  อ.<?=$Company->vat_city; ?> จ.<?=$Company->vat_location; ?> <?=$Company->postcode; ?><br>
             <?=$Company->phone; ?> <?=$Company->fax; ?> <?=$Company->mobile; ?>
             <br>
            <br>
            <br>

            <h1 style="font-size: 25px; text-align: center;"> กรุณาส่งสินค้าที่</h1>
            </td>


            <td>
                <td align="center" valign="top" style=" font-size: 10pt; ">
                    <h4 style="right: 0px;"><?=$model->transport ?></h4><br><br>
                    <p>  เลขที่ อ้างอิง</p>
                    <p>  No. : <?= $model->no ?></p>
                    <p> <barcode code="<?= $model->no ?>" type="C128B" /></p>
                </td>
            </td>

        </tr>
        <tr>

            <td valign="top">
                
                <table style="width:150px; border: 1px solid #000;" border="0" cellpadding="0" cellspacing="0">

                    </tr>
  
                </table>

            </td>
        </tr>
    </table>
    <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
        
        
        <tr>
            <td valign="top" colspan="3">
                <table width="100%"   border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px; font-size: 32px; height: 350px;">
                            <p style="margin-top: 0px;">
                                ชื่อลูกค้า : <?= $model->customer->name ?><br> <br>
                                ที่อยู่ : <?= $model->ship_address ?><br>
                            </p>

                            <br>

                            <?php 

                                if($model->customer->headoffice == 1 ){
                                    $headeroffice =  ' สำนักงานใหญ่';
                                }else {
                                    $headeroffice =  NULL;
                                }

                            ?>


                            
                            โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?>
                               
                        </td>

                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>                
    </table>
<br>


        <table   border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 5px;">
        <tr>
            <td valign="bottom" ><img src="<?=$Company->logoViewer; ?>" style="width: 80px;"> 
            <br>
             <?=$Company->name; ?><br>
             <?=$Company->name_en; ?><br>
             <?=$Company->vat_address; ?>  อ.<?=$Company->vat_city; ?> จ.<?=$Company->vat_location; ?> <?=$Company->postcode; ?><br>
             <?=$Company->phone; ?> <?=$Company->fax; ?> <?=$Company->mobile; ?>
             <br>
<br>
<br>

            <h1 style="font-size: 25px; text-align: center;"> กรุณาส่งสินค้าที่</h1>
            </td>

            <td>
                <td align="center" valign="top" style=" font-size: 10pt; ">
                    <h4 style="right: 0px;"><?=$model->transport ?></h4><br><br>
                    <p>  เลขที่ อ้างอิง</p>
                    <p>  No. : <?= $model->no ?></p>
                    <p> <barcode code="<?= $model->no ?>" type="C128B" /></p>
                </td>
            </td>

        </tr>
        <tr>

            <td valign="top">
                
                <table style="width:150px; border: 1px solid #000;" border="0" cellpadding="0" cellspacing="0">

                    </tr>
  
                </table>

            </td>
        </tr>
    </table>
    <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
        
        
        <tr>
            <td valign="top" colspan="3">
                <table width="100%"   border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px; font-size: 32px; height: 350px;">
                            <p style="margin-top: 0px;">
                                ชื่อลูกค้า : <?= $model->customer->name ?><br> <br>
                                ที่อยู่ : <?= $model->sale_address ?><br>
                            </p>

                            <br>


                            <?php 

                                if($model->customer->headoffice == 1 ){
                                    $headeroffice =  ' สำนักงานใหญ่';
                                }else {
                                    $headeroffice =  NULL;
                                }

                            ?>


                            
                            โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?>
                                 
                        </td>

                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>                
    </table>







  </body>
</html>
 