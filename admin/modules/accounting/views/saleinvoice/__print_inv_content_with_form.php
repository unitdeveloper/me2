 
 <?php



use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;


$subStr         = 50;

if(isset($_GET['substr']))      $subStr     = $_GET['substr'];
 
?>
 
          
 
 

 
<?php 


    // Set line amount per page.
    $PerPage        = 10;
    $HeaderHeight   = 210 - Yii::$app->session->get('toppage');




    Yii::$app->session->set('vat',$model->include_vat);


    $AllData = $dataProvider->getTotalCount();
    

    $data = $dataProvider->models;
    $data = array_chunk($data, $PerPage);


    $ix = 0;

    for ($i=0; $i < count($data); $i++) { 
            
        if($ix > 1 ){
            $brackpage = '<pagebreak />';
        }else {
            $brackpage = NULL;
        }


        $td = $brackpage.'<div style="height:'.$HeaderHeight.'px; "></div>';
        $td.= '<div class="body">';
        $td.= '<table border="0" cellpadding="0" cellspacing="0"  width="100%"  style="width: 100%;">';
        $td.= '<thead>';
        $td.= ' <tr>';
        $td.= '     <th style="font-size:12px; height:40px; padding-left:-5px;">ลับดับ</th>';
        $td.= '     <th style="font-size:12px;" colspan="2">'.Yii::t('common','Description').'</th>';
        $td.= '     <th style="font-size:12px; padding-right:-5mm;" align="center">'.Yii::t('common','Quantity').'</th>';
        $td.= '     <th style="font-size:12px; padding-right:-5mm;" align="center">'.Yii::t('common','Unit Price').'</th>';
        $td.= '     <th ></th>';
        $td.= '     <th style="font-size:12px; padding-right:-7mm;" align="center">'.Yii::t('common','Amount').'</th>';
        $td.= ' </tr>';
        $td.= '</thead>';
        $td.= '<tbody>';

        

        

        foreach ($data[$i] as $saleinvline) {
            
            $ix++;
            
            $discount   = $saleinvline->line_discount > 0 ? (number_format($saleinvline->line_discount).'%') : '';
        

            $unitprice  = $saleinvline->unit_price;
            $amount     = ($saleinvline->quantity * $saleinvline->unit_price) - (($saleinvline->quantity * $saleinvline->unit_price) * ($saleinvline->line_discount/100)); 

            $td.= '<tr>';
                $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';
                

                $itemCode = $saleinvline->crossreference->no;

                //$td.= '<td class="item item-code" valign="top">'.$itemCode.'</td>';

                if($saleinvline->item=='1414'){ // ข้อความ
                    if(($saleinvline->code_no_=='1^x') || ($saleinvline->code_no_=='...')){
                        $td.= '<td valign="top" class="item item-code"> </td>';
                    }else{
                        $td.= '<td valign="top" class="item item-code">'.$saleinvline->code_no_.'</td>';
                    }
                    
                }else{                    
                    $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';
                }

                if($saleinvline->code_desc_==''){
                  
                     $td.= '<td valign="top" class="item item-desc">'.mb_substr($saleinvline->items->description_th, 0,$subStr).'</td>';
                  }else {
                    
                     $td.= '<td valign="top" class="item item-desc">'.mb_substr($saleinvline->code_desc_,0,$subStr).'</td>';
                  }

            
                $quantity = number_format($saleinvline->quantity).' '.$saleinvline->measurement;
                  
                //if($saleinvline->code_no_=='1^x') $quantity = '';

                $td.= '<td valign="top" class="item item-measure" align="right" >
                        '.$quantity.'
                      </td>';   


                $price      =   number_format($unitprice,2);
                //if($saleinvline->code_no_=='1^x') $price = ''; 
                                          
                $td.= '<td valign="top" class="item item-price" align="right">
                        '.($amount != 0 ? $price : 0).'
                       </td>';

                $td.= '<td valign="top" class="item-discount" align="right" >'.$discount.'</td>';


                $td.= '<td valign="top" class="item item-amount" align="right">
                        '.number_format($amount,2).'
                       </td>';
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
 
 