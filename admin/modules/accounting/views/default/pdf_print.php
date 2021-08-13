<div style="position:absolute; 
top:16px; 
left:-6px;
background:url('images/50.png');
background-repeat: no-repeat;
background-size: 147mm 797;
width: 148.02mm;
height:100%;
<?=Yii::$app->request->get('nobg') ? 'display:none;' : ''?>
"> </div>

<div style="position: absolute; top: 42px; left:419px; width:100%; " > <!-- เล่มที่ -->
    <?=$model->book_id?>
</div>

<div style="position: absolute; top: 42px; left:475px;" width:100%; > <!-- เลขที่ -->
    <?=$model->book_no?>
</div>

 
<div style="position: absolute; top: 80px; left:23px;" > <!-- ชื่อบริษัท -->
    <?php $Comp_branch = $Company->headofficetb->data == 1 ? '('.$Company->headofficetb->data_char.')' : NULL; ?>
    <?=$Company->name. ' '.$Comp_branch?>
</div>
 
<div style="position: absolute; top: 89px; left:327px;  " > <!-- เลขที่ผู้เสียภาษี -->
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="12" align="center"><?=$vat_regis['0']?></td>    
            <td width="12" align="center"><?=$vat_regis['1']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis['2']?></td>  
            <td width="12" align="center"><?=$vat_regis['3']?></td>  
            <td width="12" align="center"><?=$vat_regis['4']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis['5']?></td>              
            <td width="12" align="center"><?=$vat_regis['6']?></td>  
            <td width="12" align="center"><?=$vat_regis['7']?></td>  
            <td width="12" align="center"><?=$vat_regis['8']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis['9']?></td>  
            <td width="12" align="center"><?=$vat_regis['10']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis['11']?></td>  
            <td width="12" align="center"><?=$vat_regis['12']?></td>  
        </tr>  
    </table>
</div>

<div style="position: absolute; top: 108px; left:35px;" ><!-- ที่อยู่บริษัท -->
    <?=$Company->fullAddress->address?>
</div>
 

<div style="position: absolute; top: 228px; left:25px;" ><!-- ชื่อ ลูกค้า -->
    <?php // $branch = $vendors->headofficetb->data == 1 ? '('.$vendors->headofficetb->data_char.')' : NULL; ?>
    <?=$model->vendor_name; ?>
</div>
<div style="position: absolute; top: 238px; left:327px;  " > <!-- ผู้ถูกหักฯ  :  เลขที่ผู้เสียภาษี -->
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="12" align="center"><?=$vat_regis_vendor['0']?></td>    
            <td width="12" align="center"><?=$vat_regis_vendor['1']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis_vendor['2']?></td>  
            <td width="12" align="center"><?=$vat_regis_vendor['3']?></td>  
            <td width="12" align="center"><?=$vat_regis_vendor['4']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis_vendor['5']?></td>              
            <td width="12" align="center"><?=$vat_regis_vendor['6']?></td>  
            <td width="12" align="center"><?=$vat_regis_vendor['7']?></td>  
            <td width="12" align="center"><?=$vat_regis_vendor['8']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis_vendor['9']?></td>  
            <td width="12" align="center"><?=$vat_regis_vendor['10']?></td>  
            <td width="13" align="center"> </td>  
            <td width="12" align="center"><?=$vat_regis_vendor['11']?></td>  
            <td width="12" align="center"><?=$vat_regis_vendor['12']?></td>  
        </tr>  
    </table>
</div>

<div style="position: absolute; top: 255px; left:35px;" ><!-- ที่อยู่ ลูกค้า -->
    <?=$model->vendor_address?>
</div>
 
<?php 
    $docType        = explode(',',$model->choice_substitute);
?>
<div style="position: absolute; top: 290px; left:0px;" >
    <table border="0" cellpadding="0" cellspacing="0" style="margin:0px 18px 0px 2px; width:100%;">
        <tr><!-- ลำดับที่ แถว 1-->
            <td align="left" style="padding-left:50px; padding-top:2px;"> <?=$model->no?> </td>
            <td width="87" align="left"><?=in_array(1,$docType) ? 'X' : '' ;?></td>
            <td width="105" align="left"><?=in_array(2,$docType) ? 'X' : '' ;?></td>
            <td width="105" align="left"><?=in_array(3,$docType) ? 'X' : '' ;?></td>
            <td width="87" align="left"><?=in_array(4,$docType) ? 'X' : '' ;?></td>
        </tr>

        <tr><!-- ลำดับที่ แถว 2-->
            <td align="left" > </td>
            <td align="left"><?=in_array(5,$docType) ? 'X' : '' ;?></td>
            <td align="left"><?=in_array(6,$docType) ? 'X' : '' ;?></td>
            <td align="left"><?=in_array(7,$docType) ? 'X' : '' ;?></td>
            <td align="left"> </td>
        </tr>  
    </table>
</div>



<?php $rowHeight = 17.8; ?>

<?php 
    $total      = 0;
    $total_dot  = 0;
 
 
    $Bahttext           = new \admin\models\FunctionBahttext();
   

    $real_total         = 0;

    $totalVatAmount     = 0;
    $totalVatAmount_dot = 0;

    $rows = [];
    foreach ($query as $key => $value) {

        //list($amount,$amount_dec)   = explode('.',  $value->wht_amount);
        //list($wht_vat,$wht_vat_dec) = explode('.',  $value->wht_vat_amount);

         
        $amount         = floor($value->wht_amount);      
        $amount_dec     = number_format($value->wht_amount - $amount, 2, '.', '');   

        $wht_vat        = floor($value->wht_vat_amount);      
        $wht_vat_dec    = number_format($value->wht_vat_amount - $wht_vat , 2, '.', '');   

        $rows[$value->wht_id] = (Object)[
            'id' => $value->wht_id,
            'other' => $value->wht_other,
            'amount' => $amount,
            'amount_dot' => $amount_dec != 0 ? ($amount_dec * 100) : '',
            'vat' => $wht_vat,
            'vat_dot' => $wht_vat_dec != 0 ? ($wht_vat_dec * 100) : '',
            'date' => date('d / m / y',strtotime($model->wht_date.' + 543 Years')),
            //'date' => date('d / m',strtotime($model->wht_date)).' / 62'
        ];

        $real_total+= $value->wht_vat_amount;
        $total+= (int)$value->wht_amount;
        $total_dot+= $amount_dec * 100;

        $totalVatAmount+= (int)$value->wht_vat_amount;
        $totalVatAmount_dot+= $wht_vat_dec * 100;
        
    }


    $ThaiBaht = $Bahttext->ThaiBaht($real_total);
  
?>
 
<div style="position: absolute; top: 315px; left:0px;  " >
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-left:2px;margin-right:20px;">
        <tr>
            <th height="35" width="260"></th>
            <th width="65"></th>
            <th width="77"></th>
            <th width="28"></th>
            <th width="77"></th>
            <th width="28"></th>
        </tr>
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
        </tr>  
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
        </tr> 

        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>   
        </tr>  
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>    
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>   
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>   
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>   
        </tr> 
        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>   
        </tr> 

        <tr>
            <td align="center" height="<?=$rowHeight?>"> </td>    
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
            <td align="center"> </td>  
        </tr> 
        
        <tr>
            <td align="center" height="10"> </td>    
            <td align="center"  style="padding-top:-10px;"> <?=isset($rows[12]) ? $rows[12]->date : ''; ?> </td>  
            <td align="center"  style="padding-top:-10px;"> <?=isset($rows[12]) ? $rows[12]->amount : ''; ?> </td>  
            <td align="center"  style="padding-top:-10px;"> <?=isset($rows[12]) ? $rows[12]->amount_dot : ''; ?></td>  
            <td align="center"  style="padding-top:-10px;"> <?=isset($rows[12]) ? $rows[12]->vat : ''; ?></td>  
            <td align="center"  style="padding-top:-10px;"> <?=isset($rows[12]) ? $rows[12]->vat_dot : ''; ?></td>  
        </tr> 
        
        
        <tr>
            <td align="left" height="40" style="padding-left:60px; padding-top:-20px;"> <?=isset($rows[13]) ? $rows[13]->other : ''; ?> </td>    
            <td align="center"> <?=isset($rows[13]) ? $rows[13]->date : ''; ?></td>  
            <td align="center"> <?=isset($rows[13]) ? number_format($rows[13]->amount) : ''; ?></td>  
            <td align="center"> <?=isset($rows[13]) ? $rows[13]->amount_dot : ''; ?></td>  
            <td align="center"> <?=isset($rows[13]) ? number_format($rows[13]->vat) : ''; ?></td>  
            <td align="center"> <?=isset($rows[13]) ? $rows[13]->vat_dot : ''; ?></td>  
        </tr>        
    </table>
</div>



<?php 
    $otherChoice        = explode(',',$model->other_choice);
?>
<div style="position: absolute; top: 530px; left:95px;" ><?=in_array(0,$otherChoice) ? '_____' : '' ;?></div>
<div style="position: absolute; top: 542px; left:19px;" ><?=in_array(1,$otherChoice) ? '_________' : '' ;?></div>
<div style="position: absolute; top: 552px; left:140px;" ><?=in_array(2,$otherChoice) ? '______' : '' ;?></div>
<div style="position: absolute; top: 552px; left:175px;" ><?=in_array(3,$otherChoice) ? '____' : '' ;?></div>
<div style="position: absolute; top: 552px; left:200px;" ><?=in_array(4,$otherChoice) ? '_____' : '' ;?></div>
<div style="position: absolute; top: 552px; left:230px;" ><?=in_array(5,$otherChoice) ? '_____' : '' ;?></div>
<div style="position: absolute; top: 564px; left:19px;" ><?=in_array(6,$otherChoice) ? '____________' : '' ;?></div>


<div style="position: absolute; bottom: 142px; left:0px;  " >
    <table border="0"  cellpadding="0" cellspacing="0" width="100%" style="margin-left:5px;margin-right:20px;">
        <tfoot> 
            <tr>
                <th align="center" height="<?=$rowHeight?>" ></th>  
                <th width="66"> </th>
                <th width="77" align="center"><?=number_format($total)?></th>
                <th width="29" align="center"><?=$total_dot?></th>
                <th width="78" align="center"><?=number_format($totalVatAmount)?></th>
                <th width="28" align="center"><?=$totalVatAmount_dot?></th>
            </tr>
            <tr>
                <th align="left" colspan="6" height="30" style="padding-left:155px;"><?=$ThaiBaht?></th>  
            </tr>
        <tfoot> 
    </table>
</div>



<?php 

$payer          = explode(',',$model->choice_payer);

?>
 
<div style="position: absolute; bottom: 2px; left:13px; font-size:25px; " >
    <table border="0" cellpadding="0" cellspacing="0">
        <tr><th height="16" width="20" align="center"><b><?=in_array(0,$payer) ? '/' : '' ;?></b></th></tr>
        <tr><th height="16" align="center"><b><?=in_array(1,$payer) ? '/' : '' ;?></b></th></tr>
        <tr><th height="16" align="center"><b><?=in_array(2,$payer) ? '/' : '' ;?></b></th></tr>
        <tr><th height="16" align="center"><b><?=in_array(3,$payer) ? '/' : '' ;?></b></th></tr> 
    </table>
</div>


<div style="position: absolute;  bottom: 25px; left:180px; " ><!-- ลงชื่อ  -->
    <?=$model->user_name?>
</div>
 

 <?php 

    $month_arr=array(
        "01"=>"มกราคม",
        "02"=>"กุมภาพันธ์",
        "03"=>"มีนาคม",
        "04"=>"เมษายน",
        "05"=>"พฤษภาคม",
        "06"=>"มิถุนายน", 
        "07"=>"กรกฎาคม",
        "08"=>"สิงหาคม",
        "09"=>"กันยายน",
        "10"=>"ตุลาคม",
        "11"=>"พฤศจิกายน",
        "12"=>"ธันวาคม"                 
    );
    
    
    $m = date('m',strtotime($model->wht_date));

?>
<div style="position: absolute;  bottom: 5px; left:180px;" ><!-- วัน เดือน ปี  -->
    <?=date('d',strtotime($model->wht_date));?> <?=$month_arr[$m];?> <?=date('y',strtotime($model->wht_date.' + 543 Years'));?>
</div>
 