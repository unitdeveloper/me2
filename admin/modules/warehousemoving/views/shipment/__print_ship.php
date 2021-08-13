<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;



use common\models\Company;



//$Company = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);

$Company = Company::findOne($model->comp_id);
 
$shipName   = $model->ship_name;
$address    = $model->ship_address;
$phone      = $model->phone == '' ? $model->customer->phone : $model->phone;
?>





<!DOCTYPE html>
<html lang="en">
  <head></head>
 
<body>
    <p style="position: absolute; right: 70px; font-size: 29px;"><?=$model->Description ?></p><br><br>
    <table   border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 5px;">
        <tr>
            <td valign="bottom" ><img src="<?=$Company->logoViewer; ?>" style="width: 70px;">
                <br>
                <?=$Company->name; ?><br>
                <?=$Company->name_en; ?><br>
                <?=$Company->vat_address; ?>  อ.<?=$Company->vat_city; ?> จ.<?=$Company->vat_location; ?> <?=$Company->postcode; ?><br>
                <?=$Company->phone; ?> <?=$Company->fax; ?> <?=$Company->mobile; ?>
                <br>
                <br>
                <p style="font-size: 24px; text-align: center;"> กรุณาส่ง</p>
            </td>
            <td align="center" valign="top" style=" font-size: 16pt; ">
                <p>  เลขที่ อ้างอิง</p>
                <p>  No. : <?= $model->SourceDoc ?></p>
                <p> <barcode code="<?= $model->SourceDoc ?>" type="C128B" /></p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <table style="width:150px; border: 1px solid #000;" border="0" cellpadding="0" cellspacing="0"></tr></table>
            </td>
        </tr>
    </table>
    <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top" colspan="3">
                <table width="100%"   border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px; font-size: 52px; height: 350px;">
                            <span style="margin-top: 0px; ">
                                <u <?=(strlen($shipName) > 60)? 'style="font-size:45px;"' : ' '?> > 
                                    <?= $shipName == '' ? $model->customer->name : $shipName ?>
                                </u>
                                <br>
                                ที่อยู่ :   <?=$address == '' ? $model->customer->address : $address ?> <br>                              
                                    <?=$phone !='' ? "โทร : {$phone}" : '' ?>  
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>
    </table>
<br><br>
<?php
  if($model->remark =='') $model->remark = '(ระวังสินค้าแตกง่าย)';
?>
<div style="font-size: 36px; position: absolute; left:70px; top: 515px;"> <?=$model->comment;?> </div>
<div style="font-size: 36px; position: absolute; left:70px; top: 1055px;"> <?=$model->comment;?> </div>
<div style="font-size: 36px; position: absolute; right:70px; top: 500px; color: red;">  <?=$model->remark;?> </div>
<div style="font-size: 36px; position: absolute; right:70px; top: 1040px;  color: red;"> <?=$model->remark;?> </div>

<div  style="position:absolute; left:0px; top:50%; width:100%; height:5px; border-top:1px dashed #ccc;"  ></div>

<p style="position: absolute; right: 70px; font-size: 29px; top: 545px;"><?=$model->Description ?></p>
    <table   border="0" cellpadding="0" cellspacing="0" style="width:100%;">
        <tr>
            <td valign="bottom" ><img src="<?=$Company->logoViewer; ?>" style="width: 70px;">
                <br>
                <?=$Company->name; ?><br>
                <?=$Company->name_en; ?><br>
                <?=$Company->vat_address; ?>  อ.<?=$Company->vat_city; ?> จ.<?=$Company->vat_location; ?> <?=$Company->postcode; ?><br>
                <?=$Company->phone; ?> <?=$Company->fax; ?> <?=$Company->mobile; ?>
                <br>
                <br>
                <p style="font-size: 24px; text-align: center;"> กรุณาส่ง</p>
            </td>

            <td>
                <td align="center" valign="top" style=" font-size: 16pt; ">
                    <p>  เลขที่ อ้างอิง</p>
                    <p>  No. : <?= $model->SourceDoc ?></p>
                    <p> <barcode code="<?= $model->SourceDoc ?>" type="C128B" /></p>
                </td>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <table style="width:150px; border: 1px solid #000;" border="0" cellpadding="0" cellspacing="0"></tr></table>
            </td>
        </tr>
    </table>
    <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top" colspan="3">
                <table width="100%"   border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px; font-size: 52px; height: 350px;">
                            <span style="margin-top: 0px;">
                                <u <?=(strlen($shipName) > 60)? 'style="font-size:45px;"' : ' '?> > 
                                    <?= $shipName == '' ? $model->customer->name : $shipName ?>
                                </u>
                                <br>
                                ที่อยู่ :   <?=$address == '' ? $model->customer->address : $address ?> <br>                              
                                        <?=$phone !='' ? "โทร : {$phone}" : '' ?>   
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>
    </table>
  </body>
</html>
<?php /* $this->render('__print_pack', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) */ ?>