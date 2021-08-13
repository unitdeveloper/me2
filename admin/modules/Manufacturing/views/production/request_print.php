<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Unitofmeasure;
use admin\models\Series;

?>
<style>
#print{
    font-family: 'saraban', 'roboto', 'sans-serif' !important; 
}

h3{
    font-family: 'saraban', 'roboto', 'sans-serif' !important; 
}

.item-name{
    font-size:13px !important;
}
        

    @media print{
        @page {
            margin-top:5px !important;
            margin-left:5px !important;
            margin-bottom:0px !important;
            size: A4 landscape; /* portrait*/ 
           
        }
        body{
            font-family: 'saraban', 'roboto', 'sans-serif' !important; 
            font-size:9px !important;
            height: 100% !important;
            
        }
        
        .print-btn,
        .space{
            display: none;
        }

        .head-date{
            font-size:12px !important;
        }

        .key-number{
            color: #e8e8e8 !important;
        }
        h3{
            font-family: 'saraban', 'roboto', 'sans-serif' !important; 
             
        }
        .item-name{
            font-size:13px !important;
        }

        #qty-for-produce {
            font-size:20px;
        }
        .qty-per-unit{
            text-shadow: -1px 0 #fff, 0 1px #fff, 1px 0 #fff, 0 -1px #fff !important;
        }
    }

    @media (min-width: 900px) {
        .footers{
            font-size:10px !important;
        }
    }

    #company-logo{
        position: absolute;
        right: 0px;
    }

</style>

<div id="print">
    
    <div class="row">
        <div class="col-xs-12 text-center"><h3>ใบสั่งผลิต 生产计划订货单</h3></div>
        <div class="col-xs-12 text-right" id="company-logo">
            <img src="images/company/logo/<?=$logo?>" height="65" />
        </div>
    </div>

    <div class="row" style="margin-top:0px;">
        <div class="col-xs-6 head-date">
            <div><div class="pull-left" style="width:120px;">วันที่ 订货日期 </div><div> : .............../............./................</div></div>
            <div>
                <div class="pull-left" style="width:120px;">จำนวน 订货数量 </div>
                <div class="pull-left">: </div>
                <input type="text" id="qty-for-produce" class="text-center" style="border: 1px solid #000; width: 150px; height: 30px; margin-left: 5px;" />
                 
            </div>
        </div>
        <div class="col-xs-6 ">
            <div class="row">
                <div class="col-xs-12 text-right pull-right head-date">
                    <div> วันที่ส่งมอบ (ห้องปั๊มเหล็ก)  交货日期（冲床车间）</div>
                    <div style="margin-top:20px">.............../............./................</div>
                </div>                 
            </div>             
        </div>
    </div>

    <hr class="style1" style="margin:0px 0px 5px 0px !important;" />

    <div class="row">
        <div class="col-xs-2">
            <img src="" class="img-thumbnail  img-responsive img-for-produce pull-left" style="height:80px;" />
        </div>
        <div class="col-xs-10">
            <div style="position:absolute; right:12px; top:0px;"> <?=Series::gen('production_request','no','all') ?> </div>
            <span class="item-name"><span>
            
        </div>
    </div>
    
    <hr class="style2" style="margin:5px 0px 5px 0px !important;"  />

    <div class="row" style="margin-top:5px; margin-bottom:5px;">
        <div class="col-xs-12">
            <div class="row"  id="item-source-render"></div>
        </div>
    </div>
    
    <hr class="style1" style="margin:10px 0px 10px 0px !important;"  />

    <div class="footers row">
        <div class="col-xs-5">
            <div class="row" style="margin-top:2px;">
                
                <div class="col-xs-6 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-top:90px;">________________________________</div>
                        <div >ผู้สั่งผลิต 订货人</div>
                        <div class="mb-5" style="margin-top:30px;">.............../............./................</div>
                    </div>
                </div>
                 
                <div class="col-xs-6 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-top:90px;">________________________________</div>
                        <div >ผูัรับออเดอร์ 接单人</div>
                        <div class="mb-5" style="margin-top:30px;">.............../............./................</div>
                    </div>
                </div>
      
            </div>
        </div>
        <div class="col-xs-7">
            <div class="row" style="margin-top:2px;">
                <div class="col-xs-4 text-center ">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-bottom:20px;">
                            <div style="margin-bottom:10px; margin-top:5px;"> จำนวนที่รับ 收货数量</div>
                            <div style="border:1px solid #000; width:50%; height:30px; margin:auto; margin-bottom: 35px;"> </div>  
                        </div>
                        <div  style="margin-top:30px;">________________________________</div>
                        <div >รับสินค้า(ห้องเชื่อม) 接货人（点焊部门）</div>
                        <div class="mb-5" style="margin-top:30px;">.............../............./................</div>
                    </div>
                </div>
                <div class="col-xs-4 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-bottom:20px;">
                            <div style="margin-bottom:10px; margin-top:5px;"> จำนวนที่รับ 收货数量</div>
                            <div style="border:1px solid #000; width:50%; height:30px; margin:auto; margin-bottom: 35px;"> </div>  
                        </div>
                        <div  style="margin-top:30px;">________________________________</div>
                        <div >รับสินค้า(ห้องพ่นสี) 接货人（喷塑车间）</div>
                        <div class="mb-5" style="margin-top:30px;">.............../............./................</div>
                    </div>
                </div>
                <div class="col-xs-4 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-bottom:20px;">
                            <div style="margin-bottom:10px; margin-top:5px;"> จำนวนที่รับ 收货数量</div>
                            <div style="border:1px solid #000; width:50%; height:30px; margin:auto; margin-bottom: 35px;"> </div>  
                        </div>
                        <div  style="margin-top:30px;">________________________________</div>
                        <div >รับสินค้า(ห้องประกอบ) 接货人（装配车间）</div>
                        <div class="mb-5" style="margin-top:30px;">.............../............./................</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 text-right" title="Production Orders Requests.">
            <div style="mt-2">FM-PDR-001 Rev.00 : 12/06/2563</div>
        </div>
    </div> 
     
</div>


<div class="space" style="margin-top:100px;"></div>
<div class="print-btn" style="position: fixed;
    bottom: 0px;
    right: 0px;
    z-index: 100;
    width: 100%;
    background: rgba(34, 45, 50, 0.52);
    padding: 10px 11px 10px 0px;
    text-align: right; 
    display:none;">
    <a href="#" class="btn btn-success btn-save"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></a>
    <a href="#" onClick="window.print()" class="btn btn-info btn-print"><i class="fa fa-print"></i> <?=Yii::t('common','Print')?></a>
</div>


<?php
$Yii = "Yii"; 
$js =<<<JS
  
  
    let state = [
        {qty : 0}
    ];

    const renderBomTable = (data, callback) => {
        let tbody = ``;

        var i;
        var x;
        /*  style="min-height:150px;  background: url('` +model.img+ `'); background-repeat: no-repeat; background-size: auto 150px;"*/
        data.map((model,i) => {
        
            if(model.id){
                tbody+= `
                    
                    <div class="col-xs-2 list-of-child" 
                        data-key="` + model.id + `"
                        data-item="` + model.item + `"
                    >
                        <div class="mt-5" style="border: 1px dashed  #000; padding: 5px 0px 5px 0px;">

                            <img src="` +model.img+ `" class="img-responsive" style="height:70px; margin: auto;"/>

                            <div style="color: #2623fb; position: absolute; left: 20px; bottom: 1px;"> `+ (model.alias ? model.alias : ' ') + `</div>

                            <div class="key-number" style="color: #e8e8e8;position: absolute;top: 10px;left: 27px;" >`+ (i+1) +`</div>

                            <div class="qty-per-unit" data-val="`+ model.qty + `" 
                                style="
                                position: absolute;
                                right: 41px;
                                bottom: 10px;
                                margin: 0px -10px 0px 10px;
                                font-size:20px;                                       
                                " > `+ model.qty + ` </div>
                            
                        </div>
                    </div>
                    
                `;
            }

        });    
        
        callback(tbody);
    }

    const getItemCraft = (obj, callback) => {
        $('.loading-div').show();
        fetch("?r=items/item-craft/get-item-craft", {
                method: "POST",
                body: JSON.stringify(obj),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                },
        })
        .then(res => res.json())
        .then(response => {            
            callback(response);    
            $('.loading-div').hide();        
        })
        .catch(error => {
            console.log(error);
        });
    }

  
    const calBom = (obj, callback) => {
        
        let produce = parseFloat(obj.val);
        state.qty   = produce;
        $('#qty-for-produce').attr('data-val',produce).val(number_format(produce,2));
    
        console.log(state.qty)

        let raws    = [];
        $('.list-of-child').each((key, el) => {

            let totalQty = produce * $(el).find('.qty-per-unit').attr('data-val');
                
            // แสดงค่าเป็นจำนวนที่ต้องการตัดสต๊อก
            $(el).find('.qty-per-unit').html(number_format(totalQty, 2));
            if(totalQty < 0){
                $(el).find('.qty-per-unit').addClass('text-red')
            }else{
                $(el).find('.qty-per-unit').removeClass('text-red')
            }

            // เก็บข้อมูลไว้สร้างการตัดสต๊อก (ไม่ต้องก็ได้ เพราะเราต้องการแค่จำนวนที่ต้องการผลิต  เมื่อจะผลิตสามารถไปคำนวนในภายหลังได้)
            raws.push({
                        i: key,
                        id: $(el).attr('data-key'),
                        item: $(el).attr('data-item'),
                        perUnit: $(el).find('.qty-per-unit').attr('data-val'),
                        QtyUsage:totalQty
                    });
        })
        
        callback(raws);
    }
  
    
    $(document).ready(function(){
        let qty = parseFloat("{$qty}");
        $('#qty-for-produce').attr('data-val', qty).val(number_format(qty,2)).focus().select();

       

        getItemCraft({id:parseInt("{$id}")}, res => {
            //localStorage.setItem('item:'+id,JSON.stringify(res));
            $('.img-for-produce').attr('src',res.img);
            $('.item-name').html(res.description);

            renderBomTable(res.raws, html => {
                $('#item-source-render').html(html);
                $('.print-btn').show();

                calBom({val:qty}, res => {
                    console.log(res)
                })
            });
        });
        
    });
    
    $('#qty-for-produce').on('change', function(){
        let val = $(this).val();
        calBom({val:val}, res => {
            console.log(res)
        })
    });


    $('body').on('click', '.btn-save', function(){
        alert('test');
        
        // Save to PDR






    });


JS;
$this->registerJS($js);
?>
