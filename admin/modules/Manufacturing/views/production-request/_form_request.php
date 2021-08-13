<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Unitofmeasure;
$Yii = 'Yii';
// echo substr('PDR2006-001', 8, 3).'<br />';
// echo substr('PDR2006-001', 7, 3).'<br />';
// echo substr('PDR2006-001', 6, 3).'<br />';
// echo substr('PDR2006-001', 5, 3).'<br />';
// echo substr('PDR2006-001', 4, 3).'<br />';
// echo substr('PDR2006-001', 3, 3).'<br />';
// echo substr('PDR2006-001', 2, 3).'<br />';
// echo substr('PDR2006-001', 1, 3).'<br />';
// echo substr('PDR2006-001', 0, 3).'<br />';
// echo substr('PDR2006-001', -1, 3).'<br />';
// echo substr('PDR2006-001', -2, 3).'<br />';
// echo substr('PDR2006-001', -3, 3).'<br />';
// echo substr('PDR2006-001', -4, 3).'<br />';
// echo substr('PDR2006-001', -5, 3).'<br />';
// echo substr('PDR2006-001', -6, 3).'<br />';
// echo substr('PDR2006-001', -7, 3).'<br />';
// echo substr('PDR2006-001', -8, 3).'<br />';
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
            margin-top:0px !important;
            margin-left:5px !important;
            margin-bottom:0px !important;
            size: A4 landscape; /* portrait*/ 
           
        }
        body{
            font-family: 'saraban', 'roboto', 'sans-serif' !important; 
            font-size:9px !important;
            height: 100% !important;
            
        }

        <?=$model->isNewRecord ? '#print{ display: none !important;  } .save-first{ display: inline !important;}' : null ?>
        
        .print-btn,
        .space,
        .btn-show{
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
            color: red !important;
        }
        .qty-per-unit{
            text-shadow: -1px 0 #fff, 0 1px #fff, 1px 0 #fff, 0 -1px #fff !important;
        }

        input[name="remark"]{
            border:0 !important;
            font-size:12px !important;
            font-weight: 400 !important;
        }

        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: none; /* If you want to remove the border as well */
            background: none;
            margin-top: 7px;
            margin-left: 10px;
        }

        .save-status{
            font-size:7px !important;
            color: #ccc !important;
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
<div class="save-first" style="position:absolute; top:65%; left:30%; display:none;"><h1><i class="fa fa-save"></i> Save First 先保存</h1></div>
<div id="print">
    <div class="row">
        <div class="col-xs-12 text-center"><h3>ใบสั่งผลิต 生产计划订货单</h3></div>
        <div class="col-xs-12 text-right" id="company-logo">
            <img src="images/company/logo/<?=$logo?>" height="65" />
        </div>
    </div>

    <div class="row" style="margin-top:0px;">
        <div class="col-xs-6 head-date">
            <div>
                <div class="pull-left" style="width:120px;">วันที่ 订货日期 </div>
                <div style="position:absolute; left: 148px; top: -11px;">
                    <select style="border: 0px; background: transparent;" name="date" id="date">
                        <option value=""></option>
                        <?php 
                           
                            for ($i=1; $i <= 31; $i++) { 
                                $selected = $model->isNewRecord 
                                            ? ($i==date('d') ? 'selected' : '')
                                            : ($i==date('d', strtotime($model->posting_date)) ? 'selected' : '');
                                echo "<option ".$selected.">".(str_pad($i, 2, "0", STR_PAD_LEFT))."</option>";
                            }
                        ?>                        
                    </select>
                </div>
                <div style="position:absolute; left: 200px; top: -11px;">
                    <select style="border: 0px; background: transparent;" name="month" id="month">
                        <option value=""></option>
                        <?php 
                            for ($i=1; $i <= 12; $i++) { 
                                $selected = $model->isNewRecord 
                                            ? ($i==date('m') ? 'selected' : '')
                                            : ($i==date('m', strtotime($model->posting_date)) ? 'selected' : '');
                                echo "<option ".$selected.">".(str_pad($i, 2, "0", STR_PAD_LEFT))."</option>";
                            }
                        ?>                        
                    </select>
                </div>
                <div style="position:absolute; left: 255px; top: -4px;" id="years"><?=date('Y')?></div>
                <div> : .............../............./...................</div>
            </div>
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
                    <div style="position:absolute; right: 136px; top: 25px;">
                        <select style="border: 0px; background: transparent;" name="tdate" id="tdate">
                            <option value=""></option>
                            <?php 
                            
                                for ($i=1; $i <= 31; $i++) { 
                                    $selected = $model->isNewRecord 
                                                ? ($i==date('d') ? 'selected' : '')
                                                : ($i==date('d', strtotime($model->request_date)) ? 'selected' : '');
                                    echo "<option ".$selected.">".(str_pad($i, 2, "0", STR_PAD_LEFT))."</option>";
                                }
                            ?>                        
                        </select>
                    </div>
                    <div style="position:absolute; right: 80px; top: 25px;">
                        <select style="border: 0px; background: transparent;" name="tmonth" id="tmonth">
                            <option value=""></option>
                            <?php 
                                for ($i=1; $i <= 12; $i++) { 
                                    $selected = $model->isNewRecord 
                                                ? ($i==date('m') ? 'selected' : '')
                                                : ($i==date('m', strtotime($model->request_date)) ? 'selected' : '');
                                    echo "<option ".$selected.">".(str_pad($i, 2, "0", STR_PAD_LEFT))."</option>";
                                }
                            ?>                        
                        </select>
                    </div>
                    <div style="position:absolute; right: 25px; top: 32px;" id="tyears"><?=date('Y')?></div>
                    <div style="margin-top:20px">.............../............./................</div>
                </div>                 
            </div>             
        </div>
    </div>

    <hr class="style1" style="margin:0px 0px 5px 0px !important;" />

    <div class="row">
        <div class="col-xs-2 text-left">
            <img src="" class="img-thumbnail  img-responsive img-for-produce pull-left" style="height:80px;" />
        </div>
        <div class="col-xs-10">
            <div style="position:absolute; right:12px; top:0px;" class="text-right"> 
                <input type="text" name="no"  value="<?=$no;?>" class="no-border text-right" /> 
                <div class="text-right">
                    <a href="#" class="btn btn-warning   btn-show <?=$model->isNewRecord ? '' : '' ?>"><i class="fa fa-list"></i> <?=Yii::t('common','Show All')?></a>
                </div>
            </div>
            
            <div class="item-name"></div>
            <div style="position:absolute; left: 16px; top: 14px;" >
                <div style="margin-top:15px;"> <?=Yii::t('common','Remark')?> 注意 : </div>
                <div class=" "> <input name="remark" class="form-control" style="width:650px;" value="<?=$model->remark?>" /> </div>
            </div>
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
                        <div style="margin-top:80px;">________________________________</div>
                        <div >ผู้สั่งผลิต 订货人</div>
                        <div class="mb-5" style="margin-top:30px;">.............../............./................</div>
                    </div>
                </div>
                 
                <div class="col-xs-6 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-top:80px;">________________________________</div>
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
                            <div style="border:1px solid #000; width:50%; height:30px; margin:auto; margin-bottom: 25px;"> </div>  
                        </div>
                        <div  style="margin-top:25px;">________________________________</div>
                        <div >รับสินค้า(ห้องเชื่อม) 接货人（点焊部门）</div>
                        <div class="mb-5" style="margin-top:15px;">วันที่เริ่ม 起始日期 ............/.........../.............</div>
                        <div class="mb-5" style="margin-top:15px;">วันที่เสร็จ 截止日期 ............/.........../............</div>

                    </div>
                </div>
                <div class="col-xs-4 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-bottom:20px;">
                            <div style="margin-bottom:10px; margin-top:5px;"> จำนวนที่รับ 收货数量</div>
                            <div style="border:1px solid #000; width:50%; height:30px; margin:auto; margin-bottom: 25px;"> </div>  
                        </div>
                        <div  style="margin-top:25px;">________________________________</div>
                        <div >รับสินค้า(ห้องพ่นสี) 接货人（喷塑车间）</div>
                        <div class="mb-5" style="margin-top:15px;">วันที่เริ่ม 起始日期 ............/.........../.............</div>
                        <div class="mb-5" style="margin-top:15px;">วันที่เสร็จ 截止日期 ............/.........../............</div>
                    </div>
                </div>
                <div class="col-xs-4 text-center">
                    <div class=" " style="border: 1px solid  #000;">
                        <div style="margin-bottom:20px;">
                            <div style="margin-bottom:10px; margin-top:5px;"> จำนวนที่รับ 收货数量</div>
                            <div style="border:1px solid #000; width:50%; height:30px; margin:auto; margin-bottom: 25px;"> </div>  
                        </div>
                        <div  style="margin-top:25px;">________________________________</div>
                        <div >รับสินค้า(ห้องประกอบ) 接货人（装配车间）</div>
                        <div class="mb-5" style="margin-top:15px;">วันที่เริ่ม 起始日期 ............/.........../.............</div>
                        <div class="mb-5" style="margin-top:15px;">วันที่เสร็จ 截止日期 ............/.........../............</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 text-right" title="Production Orders Requests.">
            <div style="position:absolute; left:15px;"><small class="save-status" style="font-size:8px; color: #ccc;">Release</small></div>
            <div style="mt-2">FM-PDR-001 Rev.00 : 12/06/2563</div>
        </div>
    </div> 
    
</div>


<div class="space" style="margin-top:100px;"></div>
<div class="print-btn" style="position: fixed;
    bottom: 0px;
    right: 0px;
    z-index: 1000;
    width: 100%;
    background: rgba(171, 171, 171, 0.8);
    padding: 10px 11px 10px 0px;
    text-align: right; 
    display:none;">
    <a href="?r=items%2Fitems%2Flist" target="_blank" class="btn btn-success pull-left btn-create <?=$model->isNewRecord ? 'hidden' : '' ?>" style="margin: 0px 15px;"><i class="fa fa-plus" ></i> <?=Yii::t('common','Create Doc')?></a>
    <a href="#" class="btn btn-primary btn-cutting-consumption-file pull-left mr-5 <?=$model->isNewRecord ? 'hidden' : '' ?>">
        <i class="fas fa-sitemap text-danger"></i> <?=Yii::t('common','Not yet cut stock')?>
    </a>
    <a href="#" class="btn btn-primary btn-cutting-produce-file pull-left ml-5 <?=$model->isNewRecord ? 'hidden' : '' ?>">
        <i class="fab fa-codepen text-yellow"></i> <?=Yii::t('common','Produce')?>
    </a>
    <a href="#" class="btn btn-success btn-save <?=$model->isNewRecord ? '' : 'hidden' ?>"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></a>
    <a href="#" onClick="window.print()" class="btn btn-info btn-print <?=$model->isNewRecord ? 'hidden' : '' ?>"><i class="fa fa-print"></i> <?=Yii::t('common','Print')?></a>
</div>

 

<div class="loading-div text-white" style="position:absolute; 
                                        z-index: 10;
                                        background: rgba(19, 19, 19, 0.58);
                                        width: 100%;
                                        height: 100%;
                                        text-align: center; 
                                        padding: 20% 0px 0px 0px; 
                                        top: 0px;
                                        left: 0px;" >
    <i class="fa fa-refresh fa-spin fa-3x"></i> <br /> Loading
</div>

<?php
$Yii = "Yii"; 
$now = date('Y-m-d');

$js =<<<JS
  
  
    let state = [ {qty : 0} ];

   
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
        let defaultQty = obj.val 
                        ? obj.val 
                        : "{$qty}";
        let produce = parseFloat(defaultQty);
        state.qty   = produce;
        $('#qty-for-produce').attr('data-val',produce).val(number_format(produce,2));
    
        //console.log(state.qty)

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

         

        getItemCraft({id:parseInt("{$item}")}, res => {
            //localStorage.setItem('item:'+id,JSON.stringify(res));
            $('.img-for-produce').attr('src',res.img);
            $('.item-name').html(res.description);

            renderBomTable(res.raws, html => {
                $('#item-source-render').html(html);
                $('.print-btn').show();

                calBom({val:qty}, res => {
                   // console.log(res)
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


    const updatePdr = (obj, callback) => {
        $('.loading-div').show();
        fetch("?r=Manufacturing/production-request/update-ajax", {
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

    $('body').on('change', 'select', function(){
        $('body').find('a.btn-save').removeClass('hidden');
        $('body').find('a.btn-print').addClass('hidden');
        $('.save-status').text('Editing ' + "({$now})");
    })

    $('body').on('keyup', 'input', function(){
        $('body').find('a.btn-save').removeClass('hidden');
        $('body').find('a.btn-print').addClass('hidden');
        $('.save-status').text('Editing ' + "({$now})");
    })

    $('body').on('click', '.btn-save', function(){
        let qty     = parseFloat($('body').find('input#qty-for-produce').attr('data-val'));
        let id      = parseInt("{$id}");
        let remark  = $('body').find('input[name="remark"]').val();
        let no      = $('body').find('input[name="no"]').val();
        let d       = $('body').find('#date :selected').val();
        let m       = $('body').find('#month :selected').val();
        let y       = parseInt($('body').find('#years').text());

        let td       = $('body').find('#tdate :selected').val();
        let tm       = $('body').find('#tmonth :selected').val();
        let ty       = parseInt($('body').find('#tyears').text());

        let date    = y+'-'+m+'-'+d;
        let until   = ty+'-'+tm+'-'+td;

        if(confirm("{$Yii::t('common','Update ? ')}")){
            $('.loading-div').show();
            updatePdr({
                    item: parseInt("{$item}"), 
                    id: id,
                    qty: qty,
                    remark: remark,
                    no: no,
                    date: date,
                    until: until
                }, res => {
                //console.log(res)

                if(res.status==200){
                    $('.loading-div').hide();
                    $('body').find('a.btn-print').show();   
                    if(res.refresh == true){
                        window.location = 'index.php?r=Manufacturing%2Fproduction-request%2Fupdate&id='+res.id;
                    }else{
                        $('body').find('a.btn-save').addClass('hidden');
                        $('body').find('a.btn-print').removeClass('hidden').attr('style','');
                        $('.save-status').text('Release ' + "({$now})");
                    }
                }

            });
        }

    });


    $('body').on('click', '.btn-show', function(){
        $('#modal-show-all').modal('show');
    });


JS;
$this->registerJS($js);
?>


<?= $this->render('_show_all', ['id' => $id])?>