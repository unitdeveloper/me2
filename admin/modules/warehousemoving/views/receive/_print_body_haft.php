 

 
<?php

$Yii            = 'Yii';
$subStr         = 50;

// Set line amount per page.
$PerPage         = 10;
$HeaderHeight    = 220 - Yii::$app->session->get('toppage');
$AllData         = $dataProvider->getTotalCount();
$data            = $dataProvider->models;
$data            = array_chunk($data, $PerPage);
$postingDate     = date('d/m/Y', strtotime($model->PostingDate));

$address         = $company->fullAddress->address;

$vendorName      = $model->vendor? $model->vendor->name : '';
$vendorAddr      = $model->vendor? $model->vendor->address : '';

   $ix              = 0;
   for ($i=0; $i < count($data); $i++) {
       if($ix > 1 ){
           $brackpage = '<pagebreak />';
       }else {
           $brackpage = NULL;
       }
       $td =
<<<HTML
 
<div style="position:absolute; top:10mm; left:15px; width:90%; ">
    <div style="text-align:left; ">
        <h5><b>$vendorName<b></h5> 
        <div style="font-size:11px;">$vendorAddr</div> 
    </div>
</div>

 
<div style="position:absolute; top:10mm;  width:90%; display:none;">
    <div style="text-align:left; ">
        <h5><b>$company->name<b></h5>
        <div style="font-size:11px;">$company->name_en</div>
        <div style="font-size:11px;">$address</div>
        <div style="font-size:11px;"> $company->phone  $company->fax  $company->mobile</div>
    </div>
</div>

<div style="border-top:1px dashed #000; width:100%; position:absolute; top:36mm; "></div>
 

<div style="position:absolute; top:8mm; right:20px; width:200px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
        <tr>
            <td colspan="2" align="right" style="height:40px;"><h4><b>ใบรับสินค้า<b></h4></td>
        </tr>
        <tr>
            <td>เลขที่เอกสาร</td>
            <td width="100">$model->DocumentNo</td>
        </tr>
        <tr>
            <td>วันที่รับสินค้า</td>
            <td>$postingDate</td>
        </tr>
        <tr>
            <td style="height:40px;">เลขที่ใบสั่งซื้อ  </td>
            <td><b>$model->SourceDoc</b></td>
        </tr>
    </table>
</div>
 
<div style="position:absolute; top:35mm; width:100%;">
    <table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
    <thead>
        <tr>
            <th style=" height:40px;" align="center">ลับดับ</th>
            <th style=" " colspan="2" align="center">รายละเอียด</th>
            <th style=" " align="right">จำนวน</th> 
            <th style=" " align="center">หน่วย </th> 
            <th style=" " align="center">คลังสินค้า</th> 
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
                        $td.= '<td align="center"  >'.($line->items->id== 1414 ? '' : ($line->locations ? $line->locations->code : '')).'</td>';
                $td.= '</tr>';
            }

       }

       echo $td;
   }
?>
        </tbody>
    </table>
</div>
<div style="width:100%; position:absolute; top:110mm;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
    <tr>
        <td align="center" style="padding: 10px;">
            <p>(...........................................)<br>
            </p>
            <p><br>วันที่.........../............/...............<br><br>
            </p>
            <p style=" ">ผู้รับสินค้า<br>
            </p>
        </td>
        <td align="center" style="padding: 10px;">
            <p>(...........................................)<br><br>วันที่.........../............/...............<br><br>
            </p>
            <p style=" ">ผู้ตรวจสอบ<br>
            </p>
        </td>
        <td align="center" style="padding: 10px;">
            <p>(...........................................)<br>
            </p>
            <p><br>วันที่.........../............/...............<br><br>
            </p>
            <p style=" ">ฝ่ายบัญชี<br>
            </p>
        </td>
    </tr>
    </tbody>
    </table>
</div>
 
<div style="border-top:1px dashed #fff; width:100%; position:absolute; top:145mm; "></div>
 
 