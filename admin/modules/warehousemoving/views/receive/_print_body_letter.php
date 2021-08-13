 

 
<?php

$Yii            = 'Yii';
$subStr         = 50;

// Set line amount per page.
$PerPage         = 10;
$HeaderHeight    = 220 - Yii::$app->session->get('toppage');
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
    <table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
    <thead>
        <tr>
        <th style="font-size:12px; height:40px; padding-left:-5px;">ลับดับ</th>
        <th style="font-size:12px;" colspan="2">รายละเอียด</th>
        <th style="font-size:12px;" align="right">จำนวน</th> 
        <th style="font-size:12px;" align="center">หน่วย </th> 
        <th style="font-size:12px;" align="center">คลังสินค้า</th> 
        </tr>
    </thead>
    <tbody>
HTML;
       foreach ($data[$i] as $line) {

            if($line->items->id== 1414 && $line->Quantity < 0){

            }else{
                $ix++;
        
                $td.= '<tr>';
                    $td.= '<td valign="top" class="" align="center" >'.$ix.'. </td>';
                    $itemCode      =   $line->item ? $line->ItemNo : $line->items->master_code;
                    if($line->items->id== 1414) $itemCode = '';
                        $td.= '<td valign="top" class="" >'.$itemCode.'</td>';                
                        $td.= '<td valign="top" class="" >'.($line->Description ? mb_substr($line->Description,0,$subStr) :  mb_substr($line->items->description_th, 0,$subStr)).'</td>';                 
                        $td.= '<td valign="top" class="" align="right" >'.number_format($line->Quantity * 1).'</td>';
                        $td.= '<td valign="top" class="" align="center" >
                                '.($line->unitofmeasures != null ? $line->unitofmeasures->UnitCode : ' ').'
                                </td>';
                        $td.= '<td align="center" style="width:80px;">'.($line->items->id== 1414 ? '' : ($line->locations ? $line->locations->code : '')).'</td>';
                $td.= '</tr>';
            }

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
