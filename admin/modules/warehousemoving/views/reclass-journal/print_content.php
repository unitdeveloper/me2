<?php
use admin\models\FunctionBahttext;
$Yii            = 'Yii';
$subStr         = 50;
if(isset($_GET['substr']))      $subStr     = $_GET['substr'];

   // Set line amount per page.
   $PerPage         = 10;
   $HeaderHeight    = 220 - Yii::$app->session->get('toppage');
   $data            = $dataProvider->models;
   $data            = array_chunk($data, $PerPage);

   $ix              = 0;
   for ($i=0; $i < count($data); $i++) {
       if($ix > 1 ){
           $brackpage = '<pagebreak />';
       }else {
           $brackpage = NULL;
       }
       $td =
<<<HTML
{$brackpage }
 
<div class="body">
<table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
  <thead>
    <!-- <tr>
      <th colspan="3" style="border-bottom:1px solid #000;">สินค้า</th>
      <th colspan="2" style="border-bottom:1px solid #000;" align="center" >{$Yii::t('common','Location')}</th>
      <th colspan="2" style="border-bottom:1px solid #000;" align="center" >{$Yii::t('common','Infomation')}</th>
       
    </tr> -->
    <tr>
      <th style="font-size:12px; height:40px; padding-left:-5px;">ลับดับ</th>
      <th style="font-size:12px;" align="left" >{$Yii::t('common','Code')}</th>
      <th style="font-size:12px;"  align="left" >{$Yii::t('common','Description')}</th>
      <th style="font-size:12px; " align="center">{$Yii::t('common','Source Location')}</th>
      <th style="font-size:12px; " align="center">{$Yii::t('common','Destination Location')}</th>
      <th style="font-size:12px; " align="center">{$Yii::t('common','Quantity')}</th>
      <th style="font-size:12px; " align="left">{$Yii::t('common','Measure')}</th>
    </tr>
</thead>
<tbody>
HTML;
       foreach ($data[$i] as $line) {
           $ix++;
           $unitprice = $line->unit_price;
           $amount    = $line->Quantity * $line->unit_price;
           $td.= '<tr>';

                $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';

                $itemCode      =   $line->items->master_code;
                if($line->items->No=='1^x') $itemCode = '';

                $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';
               
                if($line->Description==''){
                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>';
                }else {
                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->Description,0,$subStr).'</td>';
                }

                $td.= ' <td valign="top" class="item item-measure" align="center" >
                            '.$line->locations->code.'
                        </td>';  
                
                $td.= ' <td valign="top" class="item item-measure" align="center" >
                            '.$line->tolocations->code.'
                        </td>';  

                $td.= ' <td valign="top" class="item item-measure" align="right" >
                            '.number_format($line->Quantity).'
                        </td>';

                $td.= ' <td valign="top" class="item item-measure" align="left" >
                            '.$line->items->UnitOfMeasure.'
                        </td>';


            //    $td.= '<td valign="top" class="item item-price" align="right">
            //            '.number_format($unitprice,2).'
            //           </td>';
            //    $td.= '<td valign="top" class="item item-amount" align="right">
            //            '.number_format($amount,2).'
            //           </td>';
           $td.= '</tr>';
       }
       $td.= '</tbody>';
       $td.= '</table>';
       $td.= '</div>';
       echo $td;
   }
?>
<!-- Footer on Last Page -->
<?php if(isset($_GET['footer'])): ?>
<?php if($_GET['footer'] == 1) : ?>
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" />

<?php endif; ?>
<?php endif; ?>
<!-- /. Footer on Last Page -->
