<?php
// use dosamigos\ckeditor\CKEditorInline;
// use dosamigos\ckeditor\CKEditor;
//https://github.com/froala/yii2-froala-editor
use dosamigos\tinymce\TinyMce;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\checkbox\CheckboxX;
 

  ?>
<style>
    .redactor-toolbar{
        background-color: #9dcdff;
    }
    .redactor-toolbar li a:active, .redactor-toolbar li a.redactor-act {
        background-color: #c3f7da;
    }
    .mce-branding{
        display:none;
    }
    .redactor-editor,
    .mce-edit-area,
    h1,h2,h3,h4,h5{
        font-family: saraban, sans-serif !important;
    }
    #printpage-style{
        background-color: #1e2131;
        color: #95aeb1;
        font-family: Menlo, Monaco, 'Courier New', monospace;
    }
   
</style>
<?php $form = ActiveForm::begin([
    'id' => 'form-print-editor',
    'enableClientValidation' => true,
    'enableAjaxValidation' => false,
    'options' => [
        'enctype' => 'multipart/form-data',
    ]
]); ?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div role="tabpanel" class="nav-tabs-custom">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-code"></i> Design</a>
        </li>
        <li role="presentation">
            <a href="#css" aria-controls="css" role="tab" data-toggle="tab"><i class="fab fa-css3"></i> CSS</a>
        </li>
        <li role="presentation">
            <a href="#PREVIEW" aria-controls="tab" role="tab" data-toggle="tab"><i class="fab fa-windows"></i> Preview</a>
        </li>

        <li role="presentation" class="pull-right ">
            <a href="#MANUAL" aria-controls="tab" role="tab" data-toggle="tab" class="text-orange"><i class="fas fa-book"></i> Manual</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">

             
            
            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                <?=$form->field($model,'name')->textinput(['style' => 'background-color:#f9f9ce;'])?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4"><h3>HEADER</h3></div>
                <div class="col-sm-4"><?= $form->field($model, 'paper_size')->dropDownList([ 'A3' => 'A3','A4' => 'A4','A5' => 'A5', 'Letter' => 'Letter (9x11)', ])->label('<i class="fas fa-print"></i> '.Yii::t('common','Paper Size')) ?></div>
                <div class="col-sm-4">
                <?= $form->field($model, 'paper_orientation')->dropDownList([ 'P' => '[⥣] PORTRAIT','L' => '[⥤] LANDSCAPE' ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> '. Yii::t('common', 'Save'), ['class' => 'btn btn-success submit-form ']) ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-10 col-sm-9">                
                    <?= $form->field($model, 'header')->widget(\yii\redactor\widgets\Redactor::className(), [
                        'clientOptions' => [
                            'imageManagerJson' => ['/redactor/upload/image-json'],
                            'imageUpload' => ['/redactor/upload/image'],
                            'fileUpload' => ['/redactor/upload/file'],
                            'lang' => 'th',
                            'plugins' => ['table','fontsize','fontcolor','imagemanager','fullscreen']
                        ]
                    ])?>
                </div>
                <div class="col-md-2  col-sm-3">
                    <?=$form->field($model,'margin_top')->textinput(['placeholder' => 25])->label(Yii::t('common','Paper <i class="fas fa-arrows-alt-v"></i>  top margin'))?>
                    <?=$form->field($model,'header_height')->textinput(['placeholder' => 110])->label('<i class="fas fa-arrows-alt-v"></i> '. Yii::t('common','Header Height'))?> 
                </div>
            </div>
            
            

            <div class="row">
                <div class="col-md-10  col-sm-9">       
                    <div class="panel panel-success">                
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-4"><h3>BODY</h3></div>
                                <div class="col-sm-5 text-right" style="margin-top:10px;"><i class="fas fa-font"></i> <?=Yii::t('common','Font Size')?></div>
                                <div class="col-sm-3">
                                    <?=$form->field($model,'font_size')->textinput(['placeholder' => '11px'])->label(false)?>
                                </div>
                            </div>
                        </div>
                        
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>DESCRIPTION</th>
                                        <th>QUANTITY</th>
                                        <th>PRICE</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>DATA</td>
                                        <td>1</td>
                                        <td>100</td>
                                        <td>100</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>DATA</td>
                                        <td>1</td>
                                        <td>100</td>
                                        <td>100</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>DATA</td>
                                        <td>1</td>
                                        <td>100</td>
                                        <td>100</td>
                                    </tr>
                                </tbody>
                            </table> 
                        <div class="panel-footer">
                            <?=$form->field($model,'pagination')->textinput(['placeholder' => 15,'style' => 'width:100px;'])->label(Yii::t('common','Per page'))?>                            
                        </div>
                    </div>
                </div>
                <div class="col-md-2  col-sm-3">   
                    <?=$form->field($model,'body_height')->textinput(['placeholder' => '120mm'])->label('<i class="fas fa-arrows-alt-v"></i> '. Yii::t('common','Body Height'))?>
                    <?=$form->field($model,'show_table')->widget(CheckboxX::classname(), [                                
                                'autoLabel' => true,
                                'labelSettings' => [
                                    'label' => Yii::t('common','Show table')                             
                                ],
                                'pluginOptions'=>['threeState'=>false]
                            ])->label(false);?>
                </div>
            </div>
            
            
            
            <div class="row">
                <div class="col-xs-12 text-right">
                    
                </div>
            </div>
            <button type="button" class="btn btn-primary-ew" data-toggle="collapse" data-target="#FOOTER"><i class="far fa-window-minimize"></i> <?=Yii::t('common','FOOTER')?></button>
            <div id="FOOTER" class="collapse in margin-top">
                <div class="row">
                    <div class="col-md-10  col-sm-9">                
                        <?php echo $form->field($model, 'footer')->widget(\yii\redactor\widgets\Redactor::className(), [
                            'clientOptions' => [
                                'imageManagerJson' => ['/redactor/upload/image-json'],
                                'imageUpload' => ['/redactor/upload/image'],
                                'fileUpload' => ['/redactor/upload/file'],
                                'lang' => 'th',
                                'plugins' => ['clips','table','fontsize',  'fontcolor','imagemanager','fullscreen']
                            ]
                        ])->label(false)?>
                        <?php /* $form->field($model, 'footer')->widget(TinyMce::className(), [
                            'options' => ['rows' => 10],
                            'language' => 'th_TH',
                            'clientOptions' => [
                                'plugins' => [
                                    "advlist autolink lists link charmap print preview anchor",
                                    "searchreplace visualblocks code fullscreen",
                                    "insertdatetime media table contextmenu paste",
                                ],
                                "skin" => "lightgray",
                                'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                            ]
                        ]);*/?>
                    </div>
                    <div class="col-md-2  col-sm-3">
                        <?=$form->field($model,'footer_height')->textinput(['placeholder' => 0])->label('<i class="fas fa-arrows-alt-v"></i> '. Yii::t('common','Footer Height'))?> 
                        <?=$form->field($model,'show_footer_at_last')->widget(CheckboxX::classname(), [                            
                            'autoLabel' => true,
                            'labelSettings' => [
                                'label' => Yii::t('common','Show footer at last page')                             
                            ],
                            'pluginOptions'=>['threeState'=>false]
                        ])->label(false);?>
                        <?php /*$form->field($model,'show_data_at_last')->widget(CheckboxX::classname(), [                            
                            'autoLabel' => true,
                            'labelSettings' => [
                                'label' => Yii::t('common','Show data at last page')                             
                            ],
                            'pluginOptions'=>['threeState'=>false]
                        ])->label(false); */ ?>
                        
                    </div>
                </div>
            </div>
            
            
            <div class="row  margin-top">
                <div class="col-xs-12">
                    <button type="button" class="btn btn-primary-ew" data-toggle="collapse" data-target="#SIGNATURE"><i class="fas fa-pencil-alt"></i> <?=Yii::t('common','SIGNATURE')?></button>
                    <div id="SIGNATURE" class="collapse in margin-top">
                        <div class="row">
                        <div class="col-md-10  col-sm-9">             
                    
                            <?php /* froala\froalaeditor\FroalaEditorWidget::widget([
                                'model' => $model,
                                'attribute' => 'footer',
                                'options' => [
                                    // html attributes
                                    'id'=>'footer'
                                ],
                                'clientOptions' => [
                                    'toolbarInline' => false,
                                    'theme' => 'royal', //optional: dark, red, gray, royal
                                    'language' => 'th' // optional: ar, bs, cs, da, de, en_ca, en_gb, en_us ...
                                ]
                            ]); */ ?>
                            
                            <?= $form->field($model, 'signature')->widget(\yii\redactor\widgets\Redactor::className(), [
                                'clientOptions' => [
                                    'imageManagerJson' => ['/redactor/upload/image-json'],
                                    'imageUpload' => ['/redactor/upload/image'],
                                    'fileUpload' => ['/redactor/upload/file'],
                                    'lang' => 'th',
                                    'plugins' => ['clips','table','fontsize',  'fontcolor','imagemanager','fullscreen']
                                ]
                            ])->label(false)?> 
                        </div>
                        <div class="col-md-2  col-sm-3">
                            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> '. Yii::t('common', 'Save'), ['class' => 'btn btn-success submit-form pull-right']) ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            


            <div class="row margin-top">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-danger-ew" data-toggle="collapse" data-target="#WATER-MARK"><i class="fas fa-chess-rook"></i> WATER MARK</button>
                    <a href="javascript:void(0);" class="pull-right"  data-toggle="collapse" data-target="#MORE-OPTIONS"><i class="fas fa-wrench"></i> MORE OPTIONS</a>
                </div>
            </div>

            <div  class="margin-top">
                <div class="row">
                    <div class="col-md-10 " >

                    
                        <div class="panel panel-danger collapse" id="WATER-MARK">
                            <div class="panel-heading" style="height:60px;">
                                    <h3 class="panel-title"><i class="fas fa-chess-rook"></i> WATER MARK</h3>
                                    <div class="pull-right " style="margin-top:-15px;">
                                    <?= $form->field($model, 'water_mark_switch')->widget(kartik\widgets\SwitchInput::className(),[
                                                                    'name' => 'Status',                                               
                                                                    'pluginOptions' => [
                                                                        'onText' => '<i class="far fa-image"></i> '. Yii::t('common','Image'),
                                                                        'offText' => '<i class="fas fa-font"></i> '.Yii::t('common','Text'),
                                                                        'onColor' => 'primary',
                                                                        'offColor' => 'info',
                                                                        //'size' => 'mini'
                                                                    ]
                                                ])->label(false);?>
                                    </div>
                            </div>
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-md-6" style="border-right:1px solid #ccc;">

                                        <div class="row" >
                                            <div class="col-sm-12"><h4 class="text-info"><i class="far fa-image"></i> <?=Yii::t('common','Picture')?></h4></div>
                                            <div class="col-sm-6" >                                    
                                                <?=Html::img($model->photothumb,['class' => 'img-responsive'])?>
                                                <?=$form->field($model,'water_mark_img_alpha')->textinput(['placeholder' => '0.5'])->label(Yii::t('common','Opacity'))?>
                                            </div>
                                        
                                            
                                            <div class="col-sm-6">
                                                <?= $form->field($model, 'water_mark_img')->fileInput()->label(Yii::t('common','Water Mark (Image)'))?>
                                                <?=$form->field($model,'water_mark_img_width')->textinput()->label(Yii::t('common','Width'))?>   
                                            </div>
                                            
                                        </div>

                                    </div>
                                    <div class="col-md-6" >
                                    <div class="col-sm-12"><h4 class="text-info"><i class="fas fa-font"></i> <?=Yii::t('common','Text')?></h4></div>
                                        <?=$form->field($model,'water_mark')->textinput(['placeholder' => 'PURCHASE REQUEST','style' => 'background-color:#f9f9ce;'])->label(Yii::t('common','Water Mark (Text)'))?>
                                        <?=$form->field($model, 'water_mark_color')->widget(kartik\color\ColorInput::classname(), [
                                            'options' => ['placeholder' => Yii::t('common','Text Color')],
                                        ])->label(Yii::t('common','Color'));
                                        ?>
                                        <?=$form->field($model,'water_mark_size')->textinput(['placeholder' => '30'])->label(Yii::t('common','Font Size'))?>  
                                        
                                    </div>
                                </div>
                                
                                <div class="well"> 
                                    <h4><i class="far fa-comment-alt"></i> BORDER</h4>                   
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <?=$form->field($model,'water_mark_border')->textinput(['placeholder' => '2'])->label(Yii::t('common','Border'))?> 
                                            <?=$form->field($model, 'water_mark_border_color')->widget(kartik\color\ColorInput::classname(), [
                                                'options' => ['placeholder' => Yii::t('common','Border Color')],
                                                ])->label(Yii::t('common','Border Color'));
                                            ?>
                                            </div>
                                        <div class="col-sm-4">
                                            <?=$form->field($model,'water_mark_radius')->textinput(['placeholder' => '2'])->label(Yii::t('common','Radius'))?>
                                            <?=$form->field($model,'water_mark_padding')->textinput(['placeholder' => '10'])->label(Yii::t('common','Padding'))?>
                                        </div>
                                        <div class="col-sm-2">  </div>
                                        <div class="col-sm-2"> </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                        
                                    </div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-3"></div>
                                    
                                    <div class="col-sm-3"> </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-3">
                                        
                                    </div>
                                    <div class="col-sm-6"> </div>
                    
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4><i class="fab fa-css3-alt text-aqua"></i> STYLE SHEET FOR WATER MARK</h4>
                                        <code>
                                            <div>1. font-size : <?= $model->water_mark_size?>; </div>
                                            <div>2. color : <?= $model->water_mark_color?>; </div>
                                            <div>3. border-radius : <?= $model->water_mark_radius?>; </div>
                                            <div>4. padding : <?= $model->water_mark_padding?>; </div>
                                            <div>5. border : <?= $model->water_mark_border?> solid  <?= $model->water_mark_border_color?>; </div>
                                            <div>6. position : absolute; </div>
                                            <div>7. top : <?= $model->water_mark_top?>; </div>
                                            <div>8. left : <?= $model->water_mark_left?>; </div>
                                            <div>9. z-index:100; </div>
                                            <div>10. opacity: 0.5; </div>
                                            
                                        </code>
                                    </div>
                                    <div class="col-sm-6">
                                        <h4><i class="fas fa-tv"></i> PREVIEW </h4>
                                        <div style="width:90%; margin-left:10px;">
                                            <div class="row" style="border-top:1px dotted #000; border-left:1px dotted #000; height:200px;">

                                                <div class="col-xs-5 col-md-3">
                                                    <div class=" " style="margin-top:80px;">  
                                                        <style> .field-printpage-water_mark_left > label { float: left; margin:10px 10px 0px 10px;} </style>
                                                    <?=$form->field($model,'water_mark_left')->textinput(['placeholder' => '50','style' => 'width:80px;text-align:center;'])->label('<i class="fas fa-arrow-left "></i>')?></div>
                                                </div>
                                                <div class="col-xs-7 col-md-9">
                                                    
                                                    <div class="" > <?=$form->field($model,'water_mark_top')->textinput(['placeholder' => '50','style' => 'width:80px;'])
                                                    ->label('<i class="fas fa-arrow-up"></i>')?></div>
                                                    <div style="
                                                    font-size : <?= $model->water_mark_size?>;
                                                    color : <?= $model->water_mark_color?>;                            
                                                    padding : <?= $model->water_mark_padding?>px;
                                                    border : <?= $model->water_mark_border?>px solid  <?= $model->water_mark_border_color?>;
                                                    border-radius : <?= $model->water_mark_radius?>px;
                                                    ">
                                                    <?php 
                                                        if($model->water_mark_switch==0){  
                                                            echo $model->water_mark;                    
                                                        }else { 
                                                            echo Html::img($model->watermark,['class' => 'img-responsive','style' => 'opacity:'.$model->water_mark_img_alpha]);
                                                        } 
                                                    ?>
                                                        
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                    <div id="MORE-OPTIONS" class="collapse">
                        <div class="row">
                        <div class="col-sm-12"></div>
                                <div class="col-sm-12">
                                    <?=$form->field($model,'module')->textinput(['readonly'=>true])?>   
                                    <?=$form->field($model,'module_group')->textinput()?>   
                                </div>
        
                        </div>
                                                
                    </div>
                    </div>
                </div>
            </div>


            
            <?php /*froala\froalaeditor\FroalaEditorWidget::widget([
                'model' => $model,
                'attribute' => 'signature',
                'options' => [
                    // html attributes
                    'id'=>'signature'
                ],
                'clientOptions' => [
                    'toolbarInline' => false,
                    'theme' => 'red', //optional: dark, red, gray, royal
                    'language' => 'th' // optional: ar, bs, cs, da, de, en_ca, en_gb, en_us ...
                ]
            ]);*/ ?>
            <?php /* $form->field($model, 'footer')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'imageManagerJson' => ['/redactor/upload/image-json'],
                    'imageUpload' => ['/redactor/upload/image'],
                    'fileUpload' => ['/redactor/upload/file'],
                    'lang' => 'th',
                    'plugins' => ['clips', 'fontcolor','imagemanager']
                ]
            ])*/?>
             
                                
        </div>

        <div role="tabpanel" class="tab-pane" id="css">
            <div class="row">
                <div class="col-md-10 col-sm-9">                       
                    <?= $form->field($model, 'style')->textarea(['rows' => '56'])?>
                </div>
                <div class="col-md-2 col-sm-3 text-right">             
                    <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> '. Yii::t('common', 'Save'), ['class' => 'btn btn-success submit-form']) ?>
                </div>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="PREVIEW">            
            <div class="row">
                <div class="col-md-12">
                    <div class="" style="width:100%; background:#999; padding-top:5px; padding-bottom:5px;">
                        <div class="" style="width:900px; background:#fff; margin:10px 10px 20px 20px; padding:10px; ">
                            <?=$model->header?> 
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>DESCRIPTION</th>
                                        <th>QUANTITY</th>
                                        <th>PRICE</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>DATA</td>
                                        <td>1</td>
                                        <td>100</td>
                                        <td>100</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>DATA</td>
                                        <td>1</td>
                                        <td>100</td>
                                        <td>100</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>DATA</td>
                                        <td>1</td>
                                        <td>100</td>
                                        <td>100</td>
                                    </tr>
                                </tbody>
                            </table> 
                            <?=$model->footer?> 
                            
                            <?=$model->signature?> 
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div role="tabpanel" class="tab-pane" id="MANUAL">
            
                    
            <div class="panel panel-info" style="margin:10px;">
                  <div class="panel-heading">
                        <h3 class="panel-title"><div id="tableTitle"></div></h3>
                  </div>
                  <div class="panel-body">                    
                    <div id="RenderTable"></div> 
                  </div>
                  <?php if(Yii::$app->user->identity->id==1) : ?>
                  <div class="panel-footer">
                      <?=Html::a('<i class="fas fa-plus"></i> New','#modal-id',['data-toggle'=>"modal"])?>
                  </div>
                <?php endif;?>
            </div>
        
                     
        </div>
    </div>
</div>

 
<div class="modal fade" id="modal-id">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                <h4 class="modal-title">Variables</h4>
            </div>
            <div class="modal-body">
                <div id="RenderEditTable"></div>                 
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common','Close')?></a>                 
            </div>
        </div>
    </div>
</div>


<div class="content-footer" style="display:none;">
    <div class="row">
        <div class="col-xs-6 col-sm-6">                       
            <?= Html::a('<i class="fas fa-chevron-left"></i> '.Yii::t('common', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-default ']) ?>   
        </div>
        <div class="col-xs-6 col-sm-6 text-right">             
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> '. Yii::t('common', 'Save'), ['class' => 'btn btn-success submit-form']) ?>
        </div>
    </div>
</div>
 

<?php ActiveForm::end(); ?>

<?=$this->render('printer-editable.react.php') ?>

<script>
$("textarea").keydown(function(e) {
    if(e.keyCode === 9) { // tab was pressed
        // get caret position/selection
        var start = this.selectionStart;
            end = this.selectionEnd;

        var $this = $(this);

        // set textarea value to: text before caret + tab + text after caret
        $this.val($this.val().substring(0, start)
                    + "\t"
                    + $this.val().substring(end));

        // put caret at right position again
        this.selectionStart = this.selectionEnd = start + 1;

        // prevent the focus lose
        return false;
    }
});
</script>

<?php 
$js=<<<JS


$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');

    //$('.ew-bt-app-home').attr('href','index.php?r=setting%2Fprinter-index');
 
});

$('body').on('click','.submit-form',function(){
    $('form#form-print-editor').submit();
});

$('body').on('change','.cke_editable',function(){
    console.log($(this).html());
})

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');
 