<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

use yii\db\Expression;
 

use yii\helpers\ArrayHelper;
use kartik\icons\Icon;

use common\models\Itemset;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\KitbomLine */
/* @var $form yii\widgets\ActiveForm */
?>
 
<div class="kitbom-line-form">
    <?php $form = ActiveForm::begin([
        'options' => ['data-key' => $model->id]        
    ]); ?>  

        <div class="row">
            <div class="col-xs-12">                 
                <p>
                <a  data-toggle="collapse" href="#contentId" aria-expanded="false" aria-controls="contentId">
                    คำอธิบาย <i class="fa fa-info-circle"></i>
                    </a>
                </p>
                <div class="collapse" id="contentId">                                           
                <ul>
                    <li>ชุด Kit Multi สามารถใช้กับกลุ่มสินค้าได้หลายกลุ่ม</li>
                    <li>ชุด Kit Fix เลือกกลุ่มได้กลุ่มเดียว</li>
                    <li>กำหนด code ของสินค้าในช่อง "<?= Yii::t('common','Item format generation')?>" </li>
                    <li>ระบุจำนวนหลักที่ต้องการ Runing ในช่อง "<?= Yii::t('common','Running digit')?>"</li>
                    <li>กำหนด ชื่อสินค้าได้จากช่อง "ชื่อที่จะสร้าง"</li>
                </ul>
                <div class="text-right" style="font-size:23px;"><a  data-toggle="collapse"  aria-expanded="false" aria-controls="contentId" href="#contentId" >[ X ]</a></div>
                <img src="images/KIT_BOM_GROUP.jpg" class="img-responsive img-rounded" />
                </div>
            </div>
        </div>  
        <div class="row">
            <div class="col-xs-12 text-right"> 
                <?= $form->field($model, 'multiple')->widget(SwitchInput::className(),[
                    'name' => 'KitbomHeader[multiple]',
                    'value' => $model->multiple,
                    'pluginOptions' => [
                        'onColor'   => 'success',
                        'offColor'  => 'info',                             
                        'onText'    => Yii::t('common','Multi (ตัวมิกซ์)'),
                        'offText'   => Yii::t('common','Fix (ตัวหลัก)'),
                        'class'     => 'pull-left'
                    ] 
                ])->label(false);?>   
            </div>
            
            
        </div>

        <div class="row">
            
            <div class="col-sm-4">
                
                    <div class="row">
                        <div class="col-sm-12">                
                            <?php  $model->item_set = explode(',',$model->item_set); ?>
                            <label><?=Html::a(Yii::t('common','Item Set'),['/Itemset/itemset/index'],['target' => '_blank'])?> ONLY ['CHONG', 'CTM']</label>
                            <?= $form->field($model, 'item_set')->dropDownList(
                                arrayHelper::map(Itemset::find()
                                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            //->andWhere(['IN', 'detail', ['CHONG', 'CTM']])
                                            ->all(),'id','name','detail'),[
                                                        'data-live-search'=> "true",
                                                        'class' => 'selectpicker form-control ',
                                                        'prompt' => Yii::t('common','Item Set'),
                                                        'multiple' => $model->multiple==1 ? 'multiple' : false,
                                                        'title' => Yii::t('common','Select'),
                            ])->label(false) ?>                    
                        </div>                        
                        
                        <div class="col-sm-12">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true,  'placeholder' => 'ชื่อเรียก']) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'description')->textInput(['maxlength' => true,  'placeholder' => 'คำอธิบายเพิ่มเติม']) ?>
                        </div>
                    </div>
                
                    <div class="row">       
                        <div class="col-sm-12">
                            <?= $form->field($model, 'photo')->fileInput() ?>
                        </div>
                    </div>

                    <div class="row">      
                        <div class="col-sm-12 mt-10">
                            <div class="well text-center">
                                <?= Html::img($model->photoViewer,['style'=>'max-height:100px;','class'=>'img-rounded ','id' => 'img-preview']); ?>
                            </div>
                        </div>
                    </div>               
               
            </div>
            <div class="col-sm-8">
                <div class="row"> 
                    <div class="col-sm-12">
                            <?= $form->field($model, 'code')->textInput(['maxlength' => true,  'placeholder' => 'ชื่อสินค้าที่จะถูกสร้าง เช่น  CHONG-2'])->label('ชื่อที่จะสร้าง') ?>
                    </div>
                        
                    <div class="col-sm-8">
                        <?= $form->field($model, 'max_val')->textInput(['type' => 'number', 'placeholder' => 'จำนวนสูงสุดที่ใส่ item ลูกได้ 1, 2, 3...'])->label(Yii::t('common','Max child quantity of set')) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model,'priority')->textInput(['type' => 'number', 'placeholder' => 'ลำดับความสำคัญ 1, 2, 3...']);?>
                    </div> 
                </div>   
                <div class="row">                   
                    <div class="col-sm-8 format_gen" ew-format-gen="<?=$model->format_gen;?>">
                        <?= $form->field($model,'format_gen',[
                                                'addon' => ['append' => ['content'=> Yii::t('common','1234')]]
                                                ])->textInput(['class' => 'form-control text-right', 'placeholder' => 'Code ที่จะรัน เช่น 01-CT-']) ?>
                    </div>
                    <div class="col-sm-4 running_digit" ew-digit="<?=$model->running_digit;?>">
                        <?= $form->field($model,'running_digit')->textInput(['type' => 'number', 'placeholder' => 'จำนวนหลัก ที่จะรัน']) ?>
                    </div>                    
                </div>
                <div class="row">   
                    <div class="col-sm-12 mb-10">
                    
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                    <h3 class="panel-title"><?=Yii::t('common','For example') ?></h3>
                            </div>
                            <div class="panel-body">                         
                                <?php $List = [
                                    'CODE%RUNNING%' => 'CODE%RUNNING% เช่น AAA1234',
                                    'CODE%RUNNING%ENDCODE' => 'CODE%RUNNING%ENDCODE เช่น AAA1234BBB',
                                ]; ?>
                                <?= $form->field($model,'format_type')->dropDownList($List,['disabled' => 'disabled']) ?>
                                <div class="input-group">
                                    <input class="form-control text-right ew-gen-text"   style="background-color:#ccc; padding:5px; width:100%;" readonly>
                                    <span class="input-group-addon"  style="background-color:#a0bbe5; padding:5px;">1234</span>
                                </div>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>

        </div>

        

        

        <div class="form-group pull-right">
            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-plus-square-o" ></i> '.Yii::t('common', 'Save') : '<i class="fa fa-floppy-o" ></i> '.Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>





<script type="text/javascript">

    $( document ).ready(function() {
         


        var str = $('.format_gen').attr('ew-format-gen') + '1234';
        var word = str.replace("%RUNNING%", "");

        $('.ew-gen-text').val(word);
        $('#kitbomheader-format_gen').val($('.format_gen').attr('ew-format-gen').replace("%RUNNING%", ""));


    });

    $('body').on('change','#kitbomheader-format_type',function(){

        //var genCode = $('.format_gen').attr('ew-format-gen');
        var genCode = $('#kitbomheader-format_gen').val();

        if($('#kitbomheader-format_type').val()==='CODE%RUNNING%ENDCODE')
        {
            $('#kitbomheader-format_gen').val(genCode + '%RUNNING%-ABC' );
            $('.ew-gen-text').text(genCode + '1234-ABC')
            //$('#kitbomheader-format_gen').val(genCode.replace("%RUNNING%", ""));

        }else  if($('#kitbomheader-format_type').val()==='CODE%RUNNING%')
        {
            $('#kitbomheader-format_gen').val(genCode + '%RUNNING%' );
            $('.ew-gen-text').text(genCode + '1234-CT')
            $('#kitbomheader-format_gen').val(genCode.replace("%RUNNING%", ""));

        }


    });



    

</script>

<script>   

  $(function() {     
    $('input[id="kitbomheader-multiple"]').change(function() {
      
      if($(this).prop('checked')===true)
      {
        $('select[id="kitbomheader-item_set"]').attr('multiple','multiple');  
        $('select[id="kitbomheader-item_set"]').attr('name','KitbomHeader[item_set][]'); 
      }else{
        $('select[id="kitbomheader-item_set"]').removeAttr('multiple');
        $('select[id="kitbomheader-item_set"]').attr('name','KitbomHeader[item_set]'); 
      }
    })
  });


    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $("#img-preview")
                .fadeOut(400, function() {
                    $("#img-preview").attr('src', e.target.result);
                })
                .fadeIn(400);

                //$('#img-preview').attr('src', e.target.result).fadeIn('slow');

            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#kitbomheader-photo").change(function(){
        readURL(this);
    });
   
</script>


<?php 

$js=<<<JS

$('input[name="KitbomHeader[multiple]"]').on('switchChange.bootstrapSwitch', function (event, state) {
    let id      = $(this).closest('form').attr('data-key');
    let value   = state;
 
    

    $.ajax({
        url:'index.php?r=Manufacturing/ajax/update-multiple',
        type:'POST',
        data: {param:{id:id,val:value}},
        dataType:'JSON',
        success:function(response){

            if(response.status===200){

                setTimeout(() => {
                    //window.location.reload();
                    window.location.href = window.location.href + "&multiple=" + response.message;
                    //$('form').submit();
                }, 800);
                
                $.notify({
                    // options
                    icon: 'far fa-save',
                    message: 'Saved ',                         
                },{
                    // settings
                    type: 'success',
                    delay: 1500,
                    z_index:3000,
                    placement: {
                        from: "top",
                        align: "center"
                    }
                });
            }
            
        }
    })
});


JS;

$this->registerJs($js,\yii\web\View::POS_END,'JS');
?>