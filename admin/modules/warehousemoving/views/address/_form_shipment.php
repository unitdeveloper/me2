<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\icons\Icon;


use yii\helpers\Url;
use kartik\widgets\DepDrop;

use common\models\Customer;
use common\models\Province;
use common\models\Amphur;
use common\models\District;
use common\models\Zipcode;

use yii\grid\GridView;
//use kartik\grid\GridView;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use kartik\widgets\DatePicker;

$Fnc = new FunctionSaleOrder();
/* @var $this yii\web\View */
/* @var $model common\models\Address */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="addr-body">
<?php

$data  = '<div class="ew-show-address">';
$i = 0;
foreach ($addr as $value) {
    $i++;
    $attrDistrict   = District::findOne($value['district']);
    $attrAmphur     = Amphur::findOne($value['city']);
    $attrProvince   = Province::findOne($value['province']);

    $address        = $value['address'].' '.$value['address2'].' ต.'.($attrDistrict != null ? $attrDistrict->DISTRICT_NAME : '').' อ.'.($attrAmphur ? $attrAmphur->AMPHUR_NAME : ' ').' '.($attrProvince ? $attrProvince->PROVINCE_NAME : ' ').' '.$value['postcode'];

    $source = '';

    if(isset($_POST['source']))
    {
        $source     = $_POST['source'];
    }

    $data.= '<div class="row box-address">

            <div class="col-md-2 hidden-xs">
                <span class="info-box-icon bg-default" style="width: 100%"> '.$i.' </span>
            </div>
            <a href="#'.date('md').$value['id'].date('y').'" class="ew-address-click" ew-id-click="'.$value['id'].'" ew-text="'.$address.'" ew-transport="'.$value['transport'].'" source="'.$source.'">
                <div class="col-md-10 col-xs-12 ">
                  <div class="info-box bg-aqua hover-addr">
                    <span class="info-box-icon"><i class="fa fa-home hover"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">'.$value['source_name'].'</span>
                      <span class="info-box-number">'.$value['transport'].' </span>

                      <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                      </div>
                          <span class="progress-description">
                            '.$address.'
                          </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
            </a>
            <div class="addr-btn">
            <a href="#'.date('md').$value['id'].date('y').'" class="btn btn-primary ew-address-click" ew-id-click="'.$value['id'].'" ew-text="'.$address.'" ew-transport="'.$value['transport'].'" source="'.$source.'">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> '.Yii::t('common','Select').'
            </a>

            <a class="btn btn-success addr-edit" addr-id="'.$value['id'].'" addr-source="'.$value['source_id'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> '.Yii::t('common','Edit').'</a>
            <a class="btn btn-danger addr-delete" addr-id="'.$value['id'].'" addr-source="'.$value['source_id'].'"><i class="fa fa-trash-o" aria-hidden="true"></i> '.Yii::t('common','Delete').'</a>

            </div>
        </div>';
}
$data.= '</div>';
echo $data;

?>
<div class="row">
    <div class="col-md-offset-10">
        <div class="col-md-12 text-right">

            <a href="#" id="ADD-ADDRESS" class="btn btn-info"  ><i class="fa fa-plus-square-o" aria-hidden="true"></i>
            <?=Yii::t('common','Add')?></a>

            <a href="#" id="CANCEL-ADDRESS" class="btn btn-warning"  ><i class="fa fa-arrow-left" aria-hidden="true"></i>
            <?=Yii::t('common','Cancel')?></a>
        </div>
    </div>
</div>
<style type="text/css">
    .box-address{
        position: relative;
    }
    .addr-btn{
        position: absolute;
        top: 5px;
        right: 20px;
        z-index: 1000;

    }
    .addr-edit{

    }
    .hover:hover{
        color: green;
    }
    .hover-addr:hover{
        border: 1px solid #003300;
    }
</style>
<div class="address-form">
    <h4>ที่อยู่ในการจัดส่ง</h4>
    <?php $form = ActiveForm::begin([
        'id' => 'Address',
        'options' => ['class' => 'Address'],
        //'enableAjaxValidation' => true,
    ]); ?>




    <?php
        //$source = ArrayHelper::map(Customer::find()->all(),'id','name');

    ?>
    <div class="row">
     <?= $form->field($model, 'source_id')->textInput(['type' => 'hidden'])->label(false) ?>
        <div class="col-sm-6">



            <?= $form->field($model, 'source_name')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-sm-6">



            <?= $form->field($model, 'transport')->textInput(['maxlength' => true]) ?>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">

            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'postcode')->textInput(['maxlength' => true,'type' => 'number']) ?>

        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'city')->dropDownList(['maxlength' => true]) ?>
        </div>
    </div>



    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'district')->dropDownList(['maxlength' => true]) ?>

        </div>


        <div class="col-sm-6">
            <?= $form->field($model, 'province')->dropDownList(['class' => 'selectpicker form-control '],['maxlength' => true]) ?>

         <?php

          if($model->remark == '') $model->remark = '(ระวังสินค้าแตกง่าย)';


              // echo $form->field($model, 'province')->dropDownList(
              //       ArrayHelper::map(Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all(),
              //                                   'PROVINCE_ID',
              //                                   'PROVINCE_NAME'))
                                                ?>

            <?= $form->field($model,'remark')->textInput()?>
            <?= $form->field($model,'comment')->textarea()?>
        </div>
    </div>














    <div class="form-group" >

        <a href="#" class="btn btn-success ew-submit" style="margin-bottom: 15px;"><i class="fa fa-floppy-o" aria-hidden="true"></i>
        <?=Yii::t('common','Save')?></a>

    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>


<script type="text/javascript" charset="utf-8">


    $(document).ready(function(){
        $('.address-form').hide();

        var customer = $('ew[id="customerid"]').attr('data');
        $('#address-source_id').val(customer);

        $.ajax({

                url:"index.php?r=customers/ajax/json-get-customer&id="+customer,
                type: "POST",
                data: {id:customer},
                async:false,
                success:function(getData){


                    var obj = jQuery.parseJSON(getData);

                    //alert( obj.name === "John" );
                    $('#address-source_name').val(obj.name);
                    $('#address-address').val(obj.address);
                    $('#address-address2').val(obj.address2);

                    //$('#address-district').val(obj.district);

                    //$('#address-city').val(obj.city);
                    $('#address-transport').val(obj.transport);

                    $('#address-postcode').val(obj.postcode);

                    getProvince(obj.postcode);
                    getCityDefault(obj.city,obj.province);
                    getDistrictFromCity(obj.city,obj.district);

                }
            });



    });

    $('body').on('change','#address-postcode',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');

        getProvince($(this).val());
        getCity($(this).val());
        getDistrict($(this).val());


    });

    $('body').on('change','#address-city',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');


        getDistrictFromCity($(this).val());


    });

    $('body').on('change','#address-province',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');


        //getPostcodeFromDisrtict($(this).val());



        getCityFromProvince($(this).val());





    });
    $('body').on('change','#address-district',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');

        getPostcodeFromDisrtict($(this).val());



    });
    function getCityFromProvince(province)
    {

        $.ajax({

            url:"index.php?r=ajax/city-from-province&province="+province,
            type: "POST",
            data: {province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#address-city').html('');
                $.each( obj, function( key, value ) {

                    $('#address-city').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));



                });

            }

        });

    }
    function getPostcodeFromDisrtict(discrict)
    {

        $.ajax({

            url:"index.php?r=ajax/postcode-from-discrict&discrict="+discrict,
            type: "POST",
            data: {discrict:discrict},
            success:function(getData){


                $('#address-postcode').val(getData);




            }

        });

    }

    function getDistrictFromCity(city,district)
    {
        $.ajax({

            url:"index.php?r=ajax/get-district-city&district="+district+"&city="+city,
            type: "POST",
            data: {city:city},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#address-district').html('');
                $.each( obj, function( key, value ) {

                   $('#address-district').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                });



            }

        });
    }

    function getDistrict(postcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-tumbol&postcode="+postcode,
            type: "POST",
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#address-district').html('');
                $.each( obj, function( key, value ) {

                   $('#address-district').append($('<option></option>').val(value.val).html(value.text));
                });



            }

        });
    }

    function getCityDefault(city,province)
    {
        $.ajax({

            url:"index.php?r=ajax/get-city-default",
            type: "GET",
            data: {postcode:$('#address-postcode').val(),city:city,province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#address-city').html('');
                $.each( obj, function( key, value ) {

                   $('#address-city').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                   $("#address-city select").val(city);
                });




            }

        });
    }

    function getCity(postcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-city&postcode="+postcode,
            type: "POST",
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#address-city').html('');
                $.each( obj, function( key, value ) {

                   $('#address-city').append($('<option ></option>').val(value.val).html(value.text));
                });




            }

        });
    }


    function getProvince(postcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-province&postcode="+postcode,
            type: "POST",
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#address-province').html('');
                $.each( obj, function( key, value ) {

                    $('#address-province').append($('<option ></option>').val(value.val).html(value.text).attr('selected',value.selected));
                    $('option[value="maxlength"]').remove();

                });

            }

        });
    }
    $('#Address').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });


    $('#address-postcode').on('keyup keypress', function(e) {
            //getProvince($('#address-postcode').val());
            //getCity($('#address-postcode').val());
            //getDistrict($('#address-postcode').val());

    });
    $("body").keydown(function(event) {
        if(event.which == 27) { // ESC

            //$('#ew-modal-Approve').modal('hide');

        }else if(event.which == 112) { // F1
            //alert('F1');
            //$('.reject-reason #reason-text').focus();

        }else if(event.which == 113) { //F2

            //alert('F2');
        }
        else if(event.which == 114) { //F3
            // if (confirm('Create New Document?')) {

            // window.location.replace("index.php?r=SaleOrders/saleorder/create");


            // }
            // return false;

        }
        else if(event.which == 116) { //F5

            //alert('Function Disable.');
        }else if(event.which == 118) { //F7
            //$('#ew-modal-Approve').modal('toggle');
            //BtnApprove($('#ew-reject'));
        }
        else if(event.which == 121) { //F10
            //$('#ew-modal-Approve').modal('toggle');
            //∂BtnApprove($('#ew-confirm'));

        }
        else if(event.which == 13) { // Enter


            // If Model Open
            // if($('.ew-confirm').is(':visible')){






            // }




        }



    });
</script>
