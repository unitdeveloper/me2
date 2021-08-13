<?=$this->render('_header')?>

<div class="text-center table-responsive" style="background:#ccc; padding:50px;" id="docx">

    <table class="table" id="export_table" 
        style="width:216mm; background:#fff; font-family: saraban; font-size:11px; color:#000;">
        <thead>
            <tr>
              
                <th width="30"></th>
                <th colspan="6" style="text-align:center;"> </th>
                <th align="right"> </th>
            </tr>
            <tr>
              
                <th width="30"></th>
                <th colspan="6" style="text-align:center;"> </th>
                <th align="right"> Page: 1/1</th>
            </tr>
        </thead>
        <thead>
            <tr >             
                <th colspan="3"  valign="top" style="text-align:left;" class="text-left">
                    <?=Yii::t('common','Customer Code')?> : <span class="cust-code">0000</span>
                </th>
                <th colspan="3" style="text-align:right; " class="text-right "> </th>
                <th colspan="2" style="text-align:center; " class="text-center inv-no">IV62120384</th>
            </tr>
            <tr >             
                <th colspan="3"  valign="top" style="  text-align:left;" class="text-left">
                    <?=Yii::t('common','Customer Name')?> : <span class="cust-name">---</span>
                </th>
                <th colspan="3" style="text-align:right; " class="text-right "> </th>
                <th colspan="2" style="text-align:center; " class="text-center order-date">27/12/62</th>
            </tr>
            <tr >             
                <th colspan="3" rowspan="2" valign="top" style="text-align:left;" class="text-left">
                    <?=Yii::t('common','Address')?> : <span class="cust-address">---</span><br />
                    เลขประจำตัวผู้เสียภาษี : <span class="vat-regis"></span>
                </th>
                <th colspan="3" style="text-align:right; " class="text-right "> </th>
                <th colspan="2" style="text-align:center; " class="text-center sale-name"> </th>
            </tr>

            <tr >             
                <th colspan="3" style="text-align:right; " class="text-right ">  </th>
                <th colspan="2" style="text-align:center; " class="text-center  payment-due "> </th>
            </tr>

            <tr >             
                <th colspan="3" valign="top" style="text-align:left;" class="text-left">
                    <?=Yii::t('common','Tel')?> : <span class="cust-tell"> 0000 00 </span>
                    <?=Yii::t('common','Fax')?> : <span class="cust-fax"> 0000 00 </span>
                </th>
                <th colspan="3" style="text-align:right; " class="text-right "> </th>
                <th colspan="2" style="text-align:center; " class="text-center so-no">SO1912-0640</th>
            </tr>
            <tr>             
                <th colspan="3"  valign="top" style="text-align:left;" class="text-left">
                    <?=Yii::t('common','Transport By')?> : <span class="cust-transport">Kerry</span>
                </th>
                <th colspan="3" style="text-align:right;" class="text-right "> </th>
                <th colspan="2" style="text-align:left;" class="text-right "> </th>
            </tr>
            <tr >             
                <th colspan="8" height="2px" style="border-bottom:0.1em solid #000;"></th>
            </tr>
        </thead>
        <thead>
            <tr>               
                <th align="center" style="border:0.05em solid #000;"><?=Yii::t('common','No..')?></th>
                <th align="center" colspan="2" class="text-center" style="border:0.05em solid #000;"><?=Yii::t('common','Description')?></th>
                <th align="right" style="border:0.05em solid #000;"><?=Yii::t('common','Quantity')?></th>
                <th               style="border:0.05em solid #000;"><?=Yii::t('common','Measure')?></th>
                <th align="right" style="border:0.05em solid #000;"><?=Yii::t('common','Unit Price')?></th>
                <th align="right" style="border:0.05em solid #000;"><?=Yii::t('common','Discount')?></th>
                <th align="right" style="border:0.05em solid #000;"><?=Yii::t('common','Total')?></th>
            </tr>
        </thead>
        <tbody class="render">
            <tr>
              
                <td>1</td>
                <td>Loading </td>
                <td>________Loading____________</td>
                <td>0</td>
                <td>ตัว</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
               
                <td>2</td>
                <td>Loading </td>
                <td>________Loading____________</td>
                <td>0</td>
                <td>ตัว</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
               
                <td>3</td>
                <td>Loading </td>
                <td>________Loading____________</td>
                <td>0</td>
                <td>ตัว</td>
                <td>0</td>
                <td>0</td>
            </tr>
            
        </tbody>

        <tbody style="border-width: thin;">
            <tr >             
                <th colspan="8" height="2px" style="border-top:0.1em solid #000;"></th>
            </tr>
            <tr >
             
                <th colspan="3" rowspan="4" valign="top" style="border:0.1em solid #000; text-align:left;" class="text-left"><?=Yii::t('common','Remark')?> : <span class="remark-text"></span></th>
                <th colspan="3" style="border-top:0.1em solid #000; text-align:right;" class="text-right">รวมเป็นเงิน</th>
                <th colspan="2" style="border:0.1em solid #000; border-bottom:0px; text-align:right;" class="total text-right">00</th>
            </tr>
            <tr >
                           
                <th colspan="3" style="border-left:0.1em solid #000; border-right:0.1em solid #000; text-align:right;" class="text-right">ส่วนลด <span class="percent-discount"></span></th>
                <th colspan="2" style="border-left:0.1em solid #000; border-right:0.1em solid #000; text-align:right;" class="text-right discount">00</th>
            </tr>
            <tr >
                              
                <th colspan="3" style="border-left:0.1em solid #000; border-right:0.1em solid #000; text-align:right;" class="text-right">หลังหักส่วนลด</th>
                <th colspan="2" style="border-left:0.1em solid #000; border-right:0.1em solid #000; text-align:right;" class="text-right after-discount">00</th>
            </tr>
            <tr >
                           
                <th colspan="3" style="border-left:0.1em solid #000; border-right:0.1em solid #000; text-align:right;" class="text-right">ภาษีมูลค่าเพิ่ม <span class="vat-percent"></span></th>
                <th colspan="2" style="border:0.1em solid #000; border-top:0px; text-align:right;" class="after-vat text-right">00</th>
            </tr>

            <tr >
                   
                <th colspan="3" style="border:0.1em solid #000; border-top:0px; text-align:left;" class="text-baht">ศูนย์บาท</th>          
                <th colspan="3" style="border-top:0.1em solid #000; border-bottom:0.1em solid #000; text-align:right;" class="text-right">จำนวนเงิน รวมทั้งสิ้น</th>
                <th colspan="2" style="border-right:0.1em solid #000; border-left:0.1em solid #000; border-bottom:0.1em solid #000; text-align:right;" class="grand-total text-right">00</th>
            </tr>
             
        </tbody>

        <tfoot>
            <tr>
                <th colspan="8" style="height:100px;"></th>
            </tr>
         
        </tfoot>
    </table>
</div>
<?=$this->render('_script')?>
 