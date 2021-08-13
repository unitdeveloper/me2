<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
 
use kartik\widgets\DatePicker;
$Yii = 'Yii';
 
?>
<div style="height:50px; background-color: rgb(50, 54, 57); padding: 7px;">
    <div class="pull-left" style="width: 200px;" >
        <input type="text" name="owner-name" class="form-control" placeholder="<?=Yii::t('common','Name')?> - <?=Yii::t('common','Surname')?>" />
    </div>
    <div style='width:200px;' class="pull-right">
        <?=DatePicker::widget([
                    'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                    'name'      => 'start_date',
                    'options'   => ['id'    => 'start_date'],                                            
                    'value'     => date('Y-m-d'),  
                    'removeButton' => false,     
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'
                    ]                                            
            ]);
        ?>
    </div>  
</div>
<div style="margin:5px;">
    <div class="row" style=" ">           
        <div class="col-xs-4 mt-5 pull-right">
             เลขที่ 
            <div class="input-group">
                <input type="text" name="book_no" class="form-control" />
                <span class="input-group-btn">
                    <button type="button" target="_blank" class="btn btn-default text-red" type="button">#</button>
                </span>
            </div><!-- /input-group -->
        </div>
        <div class="col-xs-4 mt-5 pull-right">
            เล่มที่
            <div class="input-group">
                <input type="text" name="book_id" class="form-control" />
                <span class="input-group-btn">
                    <button type="button" target="_blank" class="btn btn-default" type="button"><i class="fas fa-book text-aqua"></i></button>
                </span>
            </div><!-- /input-group -->
        </div>
    </div>
    <div class="row" style="margin-top:20px;">         
        <div class="col-md-4 col-sm-12 mt-5">
        <div class="pull-left" style="width: 70px;">ผู้ถูกหัก ฯ </div>

            <div class="input-group">
                <input type="text" name="vendor-code"  class="form-control text-center" placeholder="CODE" />
                <span class="input-group-btn">
                    <a href='?r=vendors/vendors/create' target="_blank" class="btn btn-default" type="button">+</a>
                </span>
                <div style="position:absolute; right: 0px; top: -20px; display:none;" class="help-create-vendor"><i class="fas fa-hand-point-down fa-2x text-red blink"></i></div>
            </div><!-- /input-group -->

        </div>
        <div class="col-md-8 col-sm-12">
            
            <div class="panel panel-default mt-5">
                <div class="panel-body text-black">
                   <div class=" mb-2"><input type="text" class="form-control" name="vendor_name" /></div>
                   <div class=""><input type="text" class="form-control" name="vendor_address" /></div>
                   <div class=" mt-1"><input type="text" class="form-control" name="vendor_vat_regis" value="00 000 000 00 00" /></div>
                </div>
            </div>
             
        </div>
    </div>


    <div class="row">
        <div class="col-xs-4 mt-10">
            <div class="pull-left" style="width: 70px;"><?=Yii::t('common','Number')?> </div>
            <input type="text" name="no" id="no" style="width:100px;"  class="form-control text-center" />
        </div>
        <div class="col-xs-8">
            
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="doc-type" id="doc-type1"  value="1">
                    (1) ภ.ง.ด. 1ก.
                </label>

                <label>
                    <input type="checkbox" name="doc-type" id="doc-type2"  value="2">
                    (2) ภ.ง.ด. 1ก.พิเศษ
                </label>

                <label>
                    <input type="checkbox" name="doc-type" id="doc-type3"  value="3">
                    (3) ภ.ง.ด. 2
                </label>

                <label>
                    <input type="checkbox" name="doc-type" id="doc-type4"  value="4">
                    (4) ภ.ง.ด. 3
                </label>

                <label>
                    <input type="checkbox" name="doc-type" id="doc-type5"  value="5">
                    (5) ภ.ง.ด. 2ก
                </label>

                <label>
                    <input type="checkbox" name="doc-type" id="doc-type6"  value="6">
                    (6) ภ.ง.ด. 3ก
                </label>

                <label>
                    <input type="checkbox" name="doc-type" id="doc-type7"  value="7">
                    (7) ภ.ง.ด. 53
                </label>
            </div>
            
        </div>
    </div>
    
    
    

    <?php 


    function getParent($id, $padding){
        $parent     = \common\models\WithholdingList::find()->where(['parent' => $id])->orderBy(['priority' => SORT_ASC])->all();
        $html       = '';
        

        foreach ($parent as $key => $model) {

            //$padding+= 23;  
            $html.= '<tr data-key="'.$model->id.'">                 
                        <td style="padding-left:'.$padding.'px"> '.$model->name.'</td>
                        <td class="date-active"> </td>
                        <td><input type="number" class="form-control text-right" name="amount" /></td>
                        <td><input type="number" class="form-control text-right" name="vat" /></td>                     
                    </tr>';
            if($model->parent != 0){

                $html.= getParent($model->id, $padding);
            }
        }
        
        return $html;
    }


    
    $WithholdingList    = \common\models\WithholdingList::find()->where(['parent' => 0])->orderBy(['priority' => SORT_ASC])->all();
    $table              = '';

    foreach ($WithholdingList as $key => $model) {
        
        $Other = '';
        if($model->id == '12'){ // Mix
            $Other = '<div class="pull-right">
                        <button type="button" class="btn btn-default select-other-choice" >เลือก</button>
                        
                      </div>';
        }else if($model->id == '13'){ // Other
            $Other = '<input type="text" class="form-control " name="other" style="width: 200px; margin-left: 73px;"/>';
        }

        $table.= '<tr data-key="'.$model->id.'">                 
                    <td><div class="pull-left">'.($key + 1) . '. ' .$model->name.'</div> '. $Other.'</td>
                    <td class="date-active"> </td>
                    <td><input type="number" class="form-control text-right" name="amount" /></td>
                    <td><input type="number" class="form-control text-right" name="vat" /></td>                    
                </tr>';

        if($model->parent == 0){
            $table.=getParent($model->id, 23);
        }
    }


    ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th >ประเภทเงินได้ที่จ่าย <a href="?r=accounting%2Fwithholding-list" target="_blank" >+</a></th>
                <th width="80" class="text-center">ว/ด/ป</th>
                <th width="150"  class="text-center">จำนวนเงิน</th>             
                <th width="150"  class="text-center">ภาษีที่หัก</th>             
            </tr>
        </thead>
        <tbody>
            <?=$table;?>
        </tbody>
    </table>



    <div class="row">
        <div class="col-xs-6">
            ผู้จ่ายเงิน
            <div style="padding-left:20px;">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="choice_payer" id="choice_payer0"  value="0">
                        หักภาษี ณ ที่จ่าย
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="choice_payer" id="choice_payer1"   value="1">
                        ออกภาษีให้ตลอดไป
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="choice_payer" id="choice_payer2"   value="2">
                        ออกภาษีให้ครั้งเดียว
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="choice_payer" id="choice_payer3"   value="3">
                        อื่นๆ ให้ระบุ ............
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
 
$js=<<<JS


    const checkCard = (id, callback) => {
        var total = 0;
        var iPID;
        var chk;
        var Validchk;
        iPID = id.replace(/-/g, "");
        Validchk = iPID.substr(12, 1);
        var j = 0;
        var pidcut;
        for (var n = 0; n < 12; n++) {
            pidcut = parseInt(iPID.substr(j, 1));
            total = (total + ((pidcut) * (13 - n)));
            j++;
        }

        chk = 11 - (total % 11);

        if (chk == 10) {
            chk = 0;
        } else if (chk == 11) {
            chk = 1;
        }
        if (chk == Validchk) {
            //alert("ระบุหมายเลขประจำตัวประชาชนถูกต้อง");
            callback(true);
        } else {
            alert("ระบุหมายเลขประจำตัวประชาชนไม่ถูกต้อง");
            callback(false);
        }

    }

    $('body').on('keyup', 'input[name="vendor_vat_regis"]', function(e){
        let el = $(this);
        if (/\D/g.test(this.value)){
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }

        if(el.val().length > 13){
            alert('เลขเกิน');
            return false;
        }else{
            
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                checkCard(el.val(), res => {
                    if(!res){
                        el.addClass('text-red');
                    }else{
                        el.removeClass('text-red');
                    }
                });
            }
             
        }
    })

    $('body').on('change', 'input[id="start_date"]', function(){
        let now = $(this).val();
        $('body').find('.date-active').html(now);
    });
     

    const findVendor = (obj, callback) =>{
        fetch("?r=accounting/default/get-vendor", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            $('.loading').hide();
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    const doFindVendor = (obj) => {
        findVendor(obj, res =>{
            if(res.status==200){  
                
                state = {
                    name: state.name,
                    header : res.raws
                }
                $('body').find('input[name="vendor-code"]').attr('data-key', res.raws.id);              
                $('body').find('input[name="vendor_name"]').val(res.raws.name);
                $('body').find('input[name="vendor_address"]').val(res.raws.address);
                $('body').find('input[name="vendor_vat_regis"]').val(res.raws.vat_regis);
                $('body').find('.help-create-vendor').hide();
            }else{
                $('body').find('input[name="vendor-code"]').attr('data-key', '');
                $('body').find('input[name="vendor_name"]').val('ยังไม่มีชื่อนี้ สร้างรายชื่อก่อน');
                $('body').find('input[name="vendor_address"]').val('-');
                $('body').find('input[name="vendor_vat_regis"]').val('-');
                $('body').find('.help-create-vendor').show().attr('style','position: absolute; right: 0px; top: -20px; z-index: 2; color:#1bc718; ');
            }
        });
    }

    $('body').on('change', 'input[name="vendor-code"]', function(){
        let code = $(this).val();
        doFindVendor({code:code});
        
    });

    $('body').on('keyup', 'input[name="vendor-code"]', function(e){
        let code = $(this).val();
        
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            doFindVendor({code:code});
        }
    });

    $('body').on('click','.select-other-choice', function(){
        $('#modal-other-choice').modal('show');
    })
 
JS;

$this->registerJS($js,\yii\web\View::POS_END);
?>
 