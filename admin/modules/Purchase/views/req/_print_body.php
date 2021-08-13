<?php
use admin\models\FunctionBahttext;

$Yii            = 'Yii';
$subStr         = 150;
if(isset($_GET['substr']))      $subStr     = $_GET['substr'];

   // Set line amount per page.
   $PerPage         = $body->pagesize;
   $HeaderHeight    = $header->height - Yii::$app->session->get('toppage');
   $AllData         = $dataProvider->getTotalCount();
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
    <table border="0" cellpadding="0" cellspacing="0"  style="width: 100%;" class="table-body">
    <thead>
        <tr >
            <th class="border-start" style="font-size:12px; height:40px; padding-left:-5px;" align="center" rowspan="2">ลำดับ</th>
            <th class="border-start" style="font-size:12px;"  rowspan="2">{$Yii::t('common','Code')}</th>
            <th class="border-start" style="font-size:12px;"  rowspan="2">{$Yii::t('common','Description')}</th>

            <th class="border-start" style="font-size:12px;" colspan="2" align="center">{$Yii::t('common','Request')}</th>
        
            <th class="border-start" style="font-size:12px;" align="center">{$Yii::t('common','Required')}</th>

            <th class="border-start" style="font-size:12px;" align="center">{$Yii::t('common','Estimated')}</th>

            <th class="border-start" style="font-size:12px;" align="center">{$Yii::t('common','Total')}</th>
            
            

            <th class="border-end" style="font-size:12px;" colspan="3" align="center">{$Yii::t('common','PROCUREMENT NOTE')}</th>
        </tr>
        <tr>          
        
            <th class="border-bottom-start" style="font-size:12px;" align="right">{$Yii::t('common','Quantity')}</th>
            <th class="border-bottom-start" style="font-size:12px;" align="center">{$Yii::t('common','Unit')}</th>

            <th class="border-bottom-start" style="font-size:12px;" align="center">{$Yii::t('common','Date')}</th>

            <th class="border-bottom-start" style="font-size:12px;" align="center">{$Yii::t('common','Unit Price')}</th>            

            <th class="border-bottom-start" style="font-size:12px;" align="center">{$Yii::t('common','Price')}</th>


            <th class="border-bottom-start" style="font-size:12px; width:50px;" align="center">{$Yii::t('common','PO')}</th>
            <th class="border-bottom-start" style="font-size:12px; width:50px;" align="center">{$Yii::t('common','D-M-Y')}</th>
            <th class="border-bottom-end" style="font-size:12px; width:100px;" align="center">{$Yii::t('common','Vendor')}</th>

        </tr>
    </thead>
    <tbody>
HTML;
       foreach ($data[$i] as $line) {
            $ix++;
            $unitprice = $line->unitcost;
            $qty       = $line->quantity * 1;
            $amount    = $line->quantity * $line->unitcost;
            $td.= '<tr>';
                $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';
                $itemCode      =   $line->item == 1414 ? ' ' : $line->items_no;
                $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';
                if($line->description==''){
                    $td.= '<td valign="top" class="item item-desc">'.($line->items->description_th).'</td>';
                }else {
                    //$td.= '<td valign="top" class="item item-desc">'.mb_substr($line->description,0,$subStr).'</td>';
                    $td.= '<td valign="top" class="item item-desc">'.($line->description).'</td>';
                }
                $td.= '<td valign="top" class="item item-qty" align="right" >'.number_format($qty,2).' </td>';
                $td.= '<td valign="top" class="item item-measure" align="center">'.($line->unitofmeasures != null ? $line->unitofmeasures->UnitCode : ' ').'</td>';
                $td.= '<td valign="top" class="item item-date" align="center">'.$line->expected_date.'</td>';
                $td.= '<td valign="top" class="item item-price" align="right">'.number_format($unitprice,3).'</td>';
                $td.= '<td valign="top" class="item item-amount" align="right">'.number_format($amount,3).'</td>';


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
