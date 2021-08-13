<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;



use common\models\Company;




$Company = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

  
?>






<!DOCTYPE html>
<html lang="en">
  <head>

</head>

<body>
    <p style="position: absolute; right: 70px; font-size: 29px;"><?=$model->transport ?></p><br><br>
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



            <td align="center" valign="top" style=" font-size: 16pt;">

                 
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
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px; font-size: 52px; height: 350px;">
                            <p style="margin-top: 0px;">
                                <u> <?= $model->name ?></u><br>
                                ที่อยู่ :  <?=$model->getAddress()['shipaddress']?>
                            </p>

                            <p> <?php if($model->phone!='') echo "โทร : {$model->phone}" ?> </p>

                        </td>

                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>
    </table>
<br><br>
<?php
  if($model->text_comment =='') $model->text_comment = '(ระวังสินค้าแตกง่าย)';
?>
<div style="font-size: 36px; position: absolute; left:70px; top: 515px;"> <?=$model->text_remark;?> </div>
<div style="font-size: 36px; position: absolute; left:70px; top: 1055px;"> <?=$model->text_remark;?> </div>
<div style="font-size: 36px; position: absolute; right:70px; top: 515px; color: red;">  <?=$model->text_comment;?> </div>
<div style="font-size: 36px; position: absolute; right:70px; top: 1055px;  color: red;"> <?=$model->text_comment;?> </div>



<p style="position: absolute; right: 70px; font-size: 29px; top: 565px;"><?=$model->transport ?></p>
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
                        <td valign="top"  style="width:900px; border: 1px solid #000; padding: 15px 15px 0 15px; font-size: 52px; height: 350px;">
                            <p style="margin-top: 0px;">
                                <u> <?= $model->name ?></u><br>
                                ที่อยู่ : <?=$model->getAddress()['shipaddress']?> 
                            </p>

                            <p> <?php if($model->phone!='') echo "โทร : {$model->phone}" ?> </p>

                        </td>

                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr>
    </table>






  </body>
</html>
