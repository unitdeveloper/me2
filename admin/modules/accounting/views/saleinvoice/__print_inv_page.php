<?php
use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
?>

<?php 

    // Set line amount per page.
    $PerPage        = 15;
    $HeaderHeight   = 285 - Yii::$app->session->get('toppage');
    Yii::$app->session->set('vat',$model->include_vat);
    $AllData = $dataProvider->getTotalCount();
    $data           = $dataProvider->models;
    $data           = array_chunk($data, $PerPage);


    $ix             = 0;

    for ($i=0; $i < count($data); $i++) { 
        if($ix > 1 ){
            $brackpage = '<pagebreak />';
        }else {
            $brackpage = NULL;
        }

        $td = $brackpage.'<div style="height:'.$HeaderHeight.'px;"></div>';
        $td.= '<div class="body">';
        $td.= '<table border="0" cellpadding="0" cellspacing="0"  width="100%"  style="width: 100%;">';
        $td.= '<tbody>';

        foreach ($data[$i] as $saleinvline) {
            
            if($saleinvline->itemstb->No!='1^x'){
                $ix++;
            }
            
            $discount = number_format($saleinvline->line_discount);
            if($discount==0) $discount ='';

            // if(Yii::$app->session->get('vat')==1){ // Include Vat.
            //     $unitprice = $saleinvline->unit_price;
            //     $amount = $saleinvline->quantity * $saleinvline->unit_price;
            // }else  {    // Exclude Vat.
            //     $unitprice = $saleinvline->unit_price;
            //     $amount = $saleinvline->quantity * $saleinvline->unit_price;
            // } 

            $td.= '<tr>';
                $xx      =   $ix.'.';
                if($saleinvline->itemstb->No=='1^x') $xx = ''; 

                $td.= '<td class="item item-count" align="center" valign="top"><b>'.$xx.'</b> </td>';
            
                if($saleinvline->item=='1414'){ // ข้อความ
                    if(($saleinvline->code_no_=='1^x') || ($saleinvline->code_no_=='...')){
                        $td.= '<td class="item item-code" valign="top"> </td>';
                    }else{
                        $td.= '<td class="item item-code" valign="top"><b>'.$saleinvline->code_no_.'</b></td>';
                    }
                }else{                    
                    $td.= '<td class="item item-code" valign="top"><b>'.$saleinvline->crossreference->no.'</b></td>';
                }

                if($saleinvline->code_desc_==''){                  
                    $td.= '<td class="item item-desc" valign="top"><b>'.$saleinvline->crossreference->desc.'</b></td>';
                }else {                    
                    $td.= '<td class="item item-desc" valign="top"><b>'.$saleinvline->code_desc_.'</b></td>';
                }    
                // $name = $saleinvline->items->getItemCustomer((Object)[
                //             'search'    => '', 
                //             'customer'  => $saleinvline->orderNo->cust_no_,
                //             'qty'       => 0,
                //             'price'     => 0,
                //             'discount'  => 0
                //         ]);

                // $td.= '<td class="item item-desc" valign="top">'.$name['desc'].'</td>';      
                $measure    = $saleinvline->unitofmeasures 
                                ? $saleinvline->unitofmeasures->UnitCode      // ใช้ใน inv line 
                                : $saleinvline->defaultMeasures->code;   // ใช้ใน item (default)

                $quantity = number_format($saleinvline->quantity).' ' .$measure;
                  
                //if($saleinvline->code_no_=='1^x') $quantity = '';

                $td.= '<td class="item item-measure" align="right" valign="top">
                            <b>'.$quantity.'</b>
                        </td>';   

                $price    =   number_format($saleinvline->unit_price,2);
                //if($saleinvline->code_no_=='1^x') $price = ''; 

                $td.= '<td class="item item-price" align="right" valign="top">
                            <b>'.$price.'</b>
                       </td>';

                 
                       
                if($saleinvline->code_no_=='1^x') $discount = ''; 
                       
                $td.= '<td class="item item-discount" align="right" valign="top">
                        <b>'.($discount != 0 ? $discount .'%' : ' ').'</b> 
                       </td>';


                $total      = $saleinvline->quantity * $saleinvline->unit_price;
                $discount   = ($saleinvline->line_discount / 100) * $total;
                $LineTotal  = ($total) - $discount;

                $sumTotal      =   number_format($LineTotal,2);
                //if($saleinvline->code_no_=='1^x') $sumTotal = '';        
                $td.= '<td class="item item-amount" align="right" valign="top">
                        <b>'.$sumTotal.'</b>
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
 
 