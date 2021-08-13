<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;

use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

use yii\helpers\ArrayHelper;
use common\models\Province;
use common\models\District;
use common\models\Amphur;
use common\models\Zipcode;

use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;

use common\models\SalesPeople;

use kartik\widgets\SwitchInput;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\customer */
/* @var $form yii\widgets\ActiveForm */
app\assets\SweetalertAsset::register($this);

$model->user_id = Yii::$app->user->identity->id;

if($model->province=='') $model->province = '909';
?>
<?php $form = ActiveForm::begin([
    'id'=>'form-customer',
    'enableAjaxValidation' => false,
    'options' => ['enctype' => 'multipart/form-data'],
    'fieldConfig' => [
        'options' => [
                //'tag' => false,
            ],
    ],
]); ?>
<style>
    .mx-5-mod{
        margin:15px 0px 20px 0px;
    }
</style>
 
<div class="row" >
    <div class="col-sm-2">
        <div class="row text-center">
            <div class="col-xs-6 col-sm-12">
                 
                <?= $form->field($model, 'logo',['options' => ['class' => 'btn btn-file customer-logo']])->fileInput(['id' => 'customer-logo'])->label(false) ?>
            </div>
            <div class="col-xs-6 col-sm-12">
                 
                <?= $form->field($model, 'photo',['options' => ['class' => 'btn btn-file customer-photo']])->fileInput(['id' => 'customer-photo'])->label(false) ?>
            </div>
        </div>
    </div>

    <div class="col-sm-10">
        <div class="panel panel-primary">
            <div class="panel-heading"><?= Icon::show('user-circle-o') ?> <?=Yii::t('common','Customer')?></div>
            <div class="panel-body">
                <div class="customer-form">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="">
                                <a href="#General" data-toggle="tab" aria-expanded="true">
                                    <?= Icon::show('user', [], Icon::BSG) ?>
                                    <?=Yii::t('common','Gen<span class="hidden-xs">eral</span>'); ?> 
                                </a>
                            </li>
                            <li class="">
                                <a href="#Invoicing" data-toggle="tab" aria-expanded="false">
                                    <?= Icon::show('barcode', [], Icon::BSG) ?>
                                    <?=Yii::t('common','Inv<span class="hidden-xs">oicing</span>'); ?> 
                                </a>
                            </li>
                            <li class="">
                                <a href="#Shipping" data-toggle="tab" aria-expanded="false">
                                    <?= Icon::show('shopping-cart', [], Icon::BSG) ?>
                                    <?=Yii::t('common','Ship<span class="hidden-xs">ping</span>'); ?>
                                </a>
                            </li>
                            <li class="">
                                <a href="#Maps" data-toggle="tab" aria-expanded="false">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <?=Yii::t('common','Map Locations'); ?>
                                </a>
                            </li>
                        </ul>


                        <div class="tab-content">

                            <div class="tab-pane  active" id="General">
                                <div class="row">

                                    <div class="col-md-7">
                                        <div class=" ">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?= $form->field($model, 'code')->textInput(['class'=>'input-md','data' => $model->id,'style' => 'background-color: rgba(255, 248, 38, 0.22);']) ?>
                                                </div>
                                                
                                            </div>
                                            <div class="mx-5-mod">
                                                <ul class="nav nav-tabs">
                                                    <a href="#cus-name-th" data-toggle="tab" aria-expanded="true" class="label label-default">
                                                    <?=Yii::t('common','Name'); ?> 
                                                    </a>
                                                    <a href="#cus-name-en" data-toggle="tab" aria-expanded="true" class="label label-default" style="margin-left:5px;">
                                                    <i class="flag-icon flag-icon-gb"></i> <?=Yii::t('common','Name English'); ?> 
                                                    </a>
                                                </ul>
                                           
                                                <div class="tab-content ">
                                                    <div class="tab-pane  active" id="cus-name-th">                                   
                                                        <?= $form->field($model, 'name', [
                                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-store"></i>']]
                                                            ])->textInput(['class'=>'input-md','style' => 'background-color: rgba(79, 255, 239, 0.14); font-size: larger;'])->label(false) ?>
                                                    </div>
                                                    <div class="tab-pane" id="cus-name-en">                                   
                                                        <?= $form->field($model, 'name_en', [
                                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-store"></i>']]
                                                            ])->textInput(['class'=>'input-md','style' => 'background-color: rgba(79, 255, 239, 0.14);','placeholder' => Yii::t('common','Name English')])->label(false) ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row ">
                                                
                                                <div class="col-md-6">
                                                    <?= $form->field($model, 'vat_regis')->textInput(['class'=>'input-md']) ?>
                                                </div>
                                            </div>

                                            <div class="mx-5-mod">
                                                <div class="row ">
                                                    <div class="col-xs-6">

                                                        <?= $form->field($model, 'headoffice')->dropDownList([ 
                                                            '1'=> Yii::t('common','Head Office'), 
                                                            '0' => Yii::t('common','Branch')
                                                        ]) ?>

                                                        
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <?php if($model->branch == NULL) $model->branch = '0000'; ?>
                                                        <?= $form->field($model, 'branch')->textInput()->label(Yii::t('common','Branch no.')) ?>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <?php if($model->branch == NULL) $model->branch = '0000'; ?>
                                                        <?= $form->field($model, 'branch_name')->textInput()->label(Yii::t('common','Branch name')) ?>
                                                    </div>
                                                </div>
                                                <div class="row child-select " style="<?=$model->headoffice==1 ? 'display:none;' : '' ?>">
                                                    <div class="col-xs-12 mb-10">
                                                        <?= $form->field($model, 'child')->textInput(['class'=>'hidden', 'readonly' => true])->label(false) ?>
                                                        <?= $form->field($model, 'child_name', [
                                                            'addon' => ['append' => ['content'=>'<i class="fas fa-caret-up pointer"></i>']]
                                                        ])->textInput(['class'=>'input-md', 'readonly' => true]) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr style="margin:38px 0 50px 0;" />


                                                
                                            <div class="mx-5-mod">
                                                <ul class="nav nav-tabs">
                                                    <a href="#addr-name-th" data-toggle="tab" aria-expanded="true" class="label label-default">
                                                    <?=Yii::t('common','Address'); ?> 
                                                    </a>
                                                    <a href="#addr-name-en" data-toggle="tab" aria-expanded="true" class="label label-default" style="margin-left:5px;">
                                                    <i class="flag-icon flag-icon-gb"></i> <?=Yii::t('common','Address English'); ?> 
                                                    </a>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane  active" id="addr-name-th">               
                                                        <?= $form->field($model, 'address', [
                                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-store"></i>']]
                                                            ])->textInput(['class'=>'input-md','style' => 'background-color: rgba(79, 255, 239, 0.14);'])->label(false) ?>
                                                    </div>
                                                    <div class="tab-pane " id="addr-name-en">               
                                                        <?= $form->field($model, 'address_en', [
                                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-store"></i>']]
                                                            ])->textInput(['class'=>'input-md','style' => 'background-color: rgba(79, 255, 239, 0.14);','placeholder' => Yii::t('common','Address English')])->label(false) ?>
                                                    </div>
                                                </div>    

                                                <?= $form->field($model, 'address2', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-address-card-o"></i>']]
                                                    ])->textInput(['class'=>'input-md']) ?>
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <?php
                                                            echo $form->field($model, 'province')->dropDownList(
                                                                ArrayHelper::map(Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all(),
                                                                                            'PROVINCE_ID',
                                                                                            'PROVINCE_NAME'),[

                                                                                            'data-live-search'=> "true",
                                                                                            'class' => 'selectpicker',
                                                                                            'id'=>'ddl-province',
                                                                                            'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','Province')
                                                                                        ]
                                                            )
                                                        ?>

                                                    </div>
                                                    <div class="col-xs-6">
                                                    <?= $form->field($model, 'city')->widget(DepDrop::classname(), [
                                                                'options'=>['id'=>'ddl-amphur'],
                                                                'data'=> [
                                                                    ArrayHelper::map(Amphur::find()
                                                                    ->where(['PROVINCE_ID' => $model->province])
                                                                    ->orderBy(['AMPHUR_NAME' => SORT_ASC])
                                                                    ->all(),'AMPHUR_ID','AMPHUR_NAME')
                                                                ],
                                                                'pluginOptions'=>[
                                                                    'depends'=>['ddl-province'],
                                                                    'placeholder'=>Yii::t('common','Select'). ' ' .Yii::t('common','Amphur'),
                                                                    'url'=>Url::to(['/member/get-amphur'])
                                                                ]
                                                            ]); ?>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                    <?= $form->field($model, 'district')->widget(DepDrop::classname(), [
                                                                    'options'=>['id'=>'ddl-district'],
                                                                'data' =>[
                                                                ArrayHelper::map(District::find()
                                                                    ->where(['AMPHUR_ID' => $model->city])
                                                                    ->all(),'DISTRICT_ID','DISTRICT_NAME')
                                                                ],
                                                                'pluginOptions'=>[
                                                                    'depends'=>['ddl-province', 'ddl-amphur'],
                                                                    'placeholder'=>Yii::t('common','Select'). ' ' .Yii::t('common','District'),
                                                                    'url'=>Url::to(['/member/get-district'])
                                                                ]
                                                        ]); ?>

                                                    </div>
                                                    <div class="col-xs-6">
                                                        <?= $form->field($model, 'postcode')->textInput(['autocomplete' => 'off']) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <hr style="margin:50px 0 50px 0;" />


                                            

                                            <div class="row">
                                                <div class="col-sm-6 mt-10">
                                                    <?= $form->field($model, 'contact', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fas fa-user-circle"></i>']]
                                                    ])->textInput(['class'=>'input-md']) ?>
                                                </div>
                                                <div class="col-sm-6 mt-10">
                                                    <?= $form->field($model, 'phone', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-phone text-danger"></i>']]
                                                    ])->textInput(['class'=>'input-md','style' => 'background-color: rgba(79, 255, 239, 0.14);']) ?>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <?= $form->field($model, 'email', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-envelope"></i>']]
                                                    ])->textInput(['class'=>'input-md']); ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <?= $form->field($model, 'fax', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-fax text-warning"></i>']]
                                                    ])->textInput(['class'=>'input-md']) ?>

                                                </div>                                                
                                            </div>

                                            
                                        </div>

                                        
                                        
                                    </div>

                                    <div class="col-md-5">
                                    <div class=" ">



                                        <?php

                                            //$model->owner_sales = explode(',',$model->owner_sales);
                                            $sales = \common\models\SalesHasCustomer::find()
                                            ->where(['cust_id' => $model->id])
                                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            ->all();

                                            $sale_id = [];
                                            foreach ($sales as $key => $sale) {
                                                $sale_id[] = $sale->sale_id;
                                            }
                                            
                                            if (count($sale_id) > 0){
                                                $model->owner_sales = $sale_id;
                                            }else{
                                                $sale_code = explode(',',$model->owner_sales);
                                                $getSalePeople = SalesPeople::find()
                                                                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                                                ->andWhere(['code' => $sale_code])
                                                                ->all();
                                                $ss = [];                
                                                foreach ($getSalePeople as $key => $value) {
                                                    $ss[] = $value->id;
                                                }
                                                $model->owner_sales = $ss;
                                            }
                                            
                                            
                                            $SaleList = SalesPeople::find()
                                                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                                        ->all();
                                        ?>
                                        <?= $form->field($model, 'owner_sales')->dropDownList(
                                                                ArrayHelper::map($SaleList,'id',function($model){
                                                                    return '['.$model->code.'] '.$model->name. ' '.$model->surname;
                                                                }),
                                                                [

                                                                    'data-live-search'=> "true",
                                                                    'class' => 'selectpicker form-control ',
                                                                    //'prompt' => Yii::t('common','Sales People'),
                                                                    'multiple'=>"multiple",

                                                                ]
                                        ) ?>

                                         
                                        <div style="margin-top: 20px;">
                                            <?php                    
                                                $TransportList = \common\models\TransportList::find()
                                                ->where(['comp_id'  => Yii::$app->session->get('Rules')['comp_id']])
                                                ->orderBy(['name'   => SORT_ASC])
                                                ->all();                                         

                                                echo $form->field($model, 'default_transport',[
                                                    'addon' => ['append' => ['content'=> Html::a('<i class="fas fa-plus pointer add-transport"></i>',['/transport'],['class' => 'no-border ', 'target' => '_blank'])]]
                                                    ])->widget(Select2::className(),[
                                                        'name' => 'default_transport',
                                                        'data' => arrayHelper::map($TransportList,'id', 'name'),
                                                        'options' => [
                                                            'placeholder' => Yii::t('common','Transport'),
                                                            'multiple' => false,
                                                            'class'=>'form-control  col-xs-12 ',
                                                        ],
                                                        'pluginOptions' => ['allowClear' => false],
                                                        //'value' => @$_GET['customer']
                                                    ])->label();

                                                
                                            ?>
                                        </div>
                                        <hr style="margin:73px 0 30px 0;" />

                                        <?php  $model->customer_group = $model->customerGroup;  ?>
                                        <?= $form->field($model, 'customer_group')->dropDownList(
                                                                ArrayHelper::map(\common\models\CustomerGroups::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all(),'id','name'),
                                                                [
                                                                    'data-live-search'  => "true",
                                                                    'class'             => 'selectpicker form-control ',
                                                                    //'prompt' => Yii::t('common','Customer Group'),
                                                                    'multiple'          => "multiple",
                                                                ]
                                        )->label(Yii::t('common','Region Group')) ?>


                                        <hr style="margin:50px 0 44px 0;" />

                                        <div class="row">
                                            
                                            <div class="col-sm-6">
                                                <?= $form->field($model, 'genbus_postinggroup')->dropDownList(
                                                    ArrayHelper::map(\common\models\CommonBusinessType::find()->orderBy(['name' => SORT_ASC ])->all(),'id',function($model){
                                                        return Yii::t('common',$model->name);
                                                    }),
                                                    [
                                                        'class' => 'selectpicker form-control ',
                                                        //'prompt' => Yii::t('common','Type'),
                                                    ]
                                            ) ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <?= $form->field($model, 'vatbus_postinggroup')->dropDownList(['01' => Yii::t('common','Include Vat'),'02'=> Yii::t('common','Exclude Vat')]); ?>
                                            </div>
                                            <div class="hidden">
                                            <div class="col-sm-12">

                                            <?= $form->field($model, 'biz_type')->dropDownList(
                                                    ArrayHelper::map(\common\models\CommonBusinessType::find()->all(),'id',function($model){
                                                        return Yii::t('common',$model->name);
                                                    }),
                                                    [
                                                        'class' => 'selectpicker form-control ',
                                                        //'prompt' => Yii::t('common','Type'),
                                                    ]
                                            ) ?>
                                            </div>
                                            </div>
                                        </div>


                                        <div class=" ">
                                            <hr />
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <?php if($model->status=='') $model->status = 1; ?>
                                                    <?= $form->field($model, 'status')->widget(SwitchInput::classname(),[
                                                        'name' => ' ',
                                                        'pluginOptions' => [
                                                            'size' => 'mini',
                                                            'onColor' => 'success',
                                                            'offColor' => 'danger',
                                                            'onText'=> Yii::t('common','Show'),
                                                            'offText'=> Yii::t('common','Hide')
                                                        ]
                                                    ]) ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <?= $form->field($model, 'suspend')->widget(SwitchInput::classname(),[
                                                        'name' => ' ',
                                                        'pluginOptions' => [
                                                            'size' => 'mini',
                                                            'onColor' => 'danger',
                                                            'offColor' => 'info',
                                                            'onText'=> Yii::t('common','Suspend'),
                                                            'offText'=> Yii::t('common','Enable')
                                                        ]
                                                    ]) ?>
                                                </div>
                                            </div>

                                            <hr style="margin:50px 0 50px 0;" />

                                            <?= $form->field($model, 'show_item_code')->widget(SwitchInput::classname(),[
                                                        'name' => ' ',
                                                        'pluginOptions' => [
                                                            'size' => 'mini',                                                            
                                                        ]
                                                    ]) ?>

                                             <?= $form->field($model, 'show_lang_addr')->widget(SwitchInput::classname(),[
                                                        'name' => ' ',
                                                        'pluginOptions' => [
                                                            'size' => 'mini',                                                            
                                                        ]
                                                    ]) ?>


                                           
                                        </div>

                                    </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                                <div class="tab-pane fade" id="Invoicing">
                                        <!-- The timeline -->
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <?= $form->field($model, 'payment_term')->dropDownList([
                                                        '0'=> Yii::t('common','Cash'),
                                                        '7'=> '7 '.Yii::t('common','Day'),
                                                        '15' => '15 '.Yii::t('common','Day'),
                                                        '30' => '30 '.Yii::t('common','Day'),
                                                        '45' => '45 '.Yii::t('common','Day'),
                                                        '60' => '60 '.Yii::t('common','Day'),
                                                        '90' => '90 '.Yii::t('common','Day'),
                                                    ]) ?>

                                                </div>
                                                <div class="col-md-6">
                                                    <?php 
                                                        $month = [];
                                                        for ($i=1; $i <= date('t') ; $i++) { 
                                                            $month[$i] = $i;
                                                        }
                                                        
                                                        echo $form->field($model, 'payment_due')->dropDownList($month)->label(Yii::t('common','Every date'). ' <span class="of-month"></span> '.Yii::t('common','of month'));
                                                    ?>
                                                     
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <?= $form->field($model, 'credit_limit', [
                                                    'addon' => ['append' => ['content'=>'บาท']]
                                                    ])->textInput(['type' => 'number']); ?>                                                
                                            <div style="padding-top: 15px;"></div>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane fade" id="Shipping">
                                    <div class="row">
                                        <div class="col-sm-6 my-10">
                                            
                                        </div>                                                      
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">  
                                            <?= $form->field($model, 'nick_name', [
                                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-address-card"></i>']]
                                                ])->textInput(['class'=>'input-md','placeholder'=>Yii::t('common','Nick Name')]); ?>  
                                            
                                            <?= $form->field($model, 'transport', [
                                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-truck"></i>']]
                                                ])->textInput(['value' => $model->transport,'class'=>'input-md', 'placeholder'=>Yii::t('common','Transport By'), 'readonly' => true]); ?>

                                        </div>
                                        <div class="col-xs-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <i class="fas fa-truck-moving"></i>
                                                    <h3 class="box-title"><?= Yii::t('common','History Transport')?></h3>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <ol>
                                                    <?php
                                                        $myTranSport = $model->myTransport;
                                                        foreach ($myTranSport as $key => $list) {
                                                            echo "<li>$list->name</li>";
                                                        }
                                                    ?>
                                                    </ol>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?= $form->field($model, 'ship_name')->textInput()->label(Yii::t('common','Shipment Name')) ?>
                                        </div> 
                                    </div>
                                    <div class="row">                                                                                                 
                                        <div class="col-sm-12">
                                            <?= $form->field($model,'ship_address')->textArea(['placeholder' => $model->getAddress()['address']])?>
                                        </div>                                        
                                    </div>                                  
                                </div>
                                <!-- /.tab-pane -->


                                

<?php
 

$marker_dragend_event_js = <<<JS
    document.getElementById('customer-latitude').value = event.latLng.lat();
    document.getElementById('customer-longitude').value = event.latLng.lng();
    console.log(event.latLng);
JS;


 ?>
                                <div class="tab-pane fade" id="Maps">

                                    <div class="row">
                                        <div class="col-sm-4">
                                        
                                        </div>
                                    </div>
                                    <div style="margin:5px 0 10px 0;"><i class="fas fa-map-marked-alt"></i> <?=Yii::t('common','Map Locations')?></div>

                                    <?php
                                    use dosamigos\google\maps\LatLng;
                                    use dosamigos\google\maps\Map;
                                    use dosamigos\google\maps\overlays\InfoWindow;
                                    use dosamigos\google\maps\overlays\Marker;
                                    use dosamigos\google\maps\Event;


                                    $coord = new LatLng(['lat' => ($model->latitude? $model->latitude : 14.513698),'lng' => ($model->longitude? $model->longitude : 101.305129)]);
                                    $map = new Map([
                                        'center'=>$coord,
                                        'zoom'=>7,
                                        'width'=>'100%',
                                        'height'=>'400'                                         
                                    ]);

                                    
                                                                        
                                    $marker = new Marker([
                                        'position'  => $coord,
                                        'draggable' => true,
                                        'title'     => 'Marker',
                                        'events'    => [
                                            new Event([
                                                'trigger' => 'dragend',
                                                'js'      => $marker_dragend_event_js,
                                            ]),
                                        ],
                                    ]);

                                    $marker->attachInfoWindow(
                                        new InfoWindow([
                                            'content' => '<p>'.Yii::t('common','This is company').'</p>'
                                        ])
                                    );


                                    $map->addOverlay($marker);

                                    ?>
                                    <div  style="border: 1px solid #ccc; position: relative; height: 400px; margin-bottom:15px;" class="box-chadow">
                                        <div class="map-present">
                                            <div class="text-center loading-map" style="position: absolute; top: 45%; right: 45%;">
                                            <i class="fa fa-spinner fa-spin fa-2x fa-fw text-info" aria-hidden="true"></i>
                                            <div class="blink"> Loading </div>
                                            </div>
                                            <?PHP
                                            echo $map->display();
                                             
                                            ?>

                                        </div>
                                        <div class="text-center loading-map" style="position: absolute; top: 45%; right: 45%; display: none;">
                                            <i class="fa fa-spinner fa-spin fa-2x fa-fw text-info" aria-hidden="true"></i>
                                            <div class="blink"> Loading </div>
                                        </div>
                                    </div>

                                     
                                  
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                            if($model->country=='') $model->country = '213';

                                                        echo $form->field($model, 'country')->dropDownList(
                                                            ArrayHelper::map(\common\models\Countries::find()->orderBy(['country_name' => SORT_ASC])->all(),
                                                                                        'id',
                                                                                        'country_name'),[

                                                                                        'data-live-search'=> "true",
                                                                                        'class' => 'selectpicker',
                                                                                        'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','country')

                                                                                    ]
                                                        )
                                                    ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
                                        </div>
                                    </div>  
                                </div>
                                <!-- /.tab-pane -->             
                             
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'create_date')->hiddenInput(['class'=>'input-md'])->label(false) ?>
                                        <?= $form->field($model, 'user_id')->hiddenInput(['class'=>'input-md'])->label(false) ?>
                        </div>
                    </div>







                    <div class="form-group pull-right">
                        <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                                                                    
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>

 <?php ActiveForm::end(); ?>
<style>
#customer-postcode-list
{
 
    border:1px solid #ccc;
    position:absolute; 
    background:#FFF; 
    width:90%;
}
#customer-postcode-list > .hover
{
    cursor:pointer;
    margin:10px 0px 10px 0px;
    padding: 5px;
}
#customer-postcode-list > .hover:hover
{
    color:#000;
    background:rgba(0,0,0,0.5);
}
</style>



<?PHP 
$Yii = 'Yii';
$js= <<<JS

    activaTab = (tab) => {
        if(!tab) tab = 'General';
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    };

    $(document).ready(function(){

        var url      = window.location.href.split('#!#');     // Returns full URL
        activaTab(url[1]);

        $('div.customer-logo').fadeOut(500, function() {
        $('div.customer-logo').prepend('<img class=\"img-responsive img-rounded img-thumbnail\" src="{$model->getPhotothumb('logo')}" id=\"img-preview-logo\">');    
        }).fadeIn(500);  

        $('div.customer-photo').fadeOut(800, function() {
        $('div.customer-photo').prepend('<img class=\"img-responsive img-rounded img-thumbnail\" src="{$model->getPhotothumb('photo')}" id=\"img-preview-photo\">');    
        }).fadeIn(800);  
        


        $('#ddl-amphur').children('optgroup').attr('label','{$Yii::t('common','Amphur')}');
        $('#ddl-district').children('optgroup').attr('label','{$Yii::t('common','District')}');



    });


        



    


    function getPostcodeFromDisrtict(discrict){
        $.ajax({
            url:"index.php?r=ajax/postcode-from-discrict-list&discrict="+discrict,
            type: "POST",
            data: {discrict:discrict},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                
                if(obj.length > 1){

                    $('#customer-postcode').html('').val('');
                    $('#customer-postcode-list').html('');
                    $('#customer-postcode').parent('div').append('<div id="customer-postcode-list"></div>');
                    $.each( obj, function( key, value ) {                        
                        $('#customer-postcode-list').append($('<div class="hover"></div>').attr('data-key',value.val).html('» '+value.text + ' » ' +value.val));
                    });

                }else {

                    if(obj[0] != '') {
                        $('#customer-postcode').val(obj[0].val);
                    }

                }

                
                
               
            }
        });
    }

    $('body').on('click','#customer-postcode-list .hover',function(){
        $('#customer-postcode').val($(this).attr('data-key'));
        $(this).parent('div').remove();         
    });

    $('form#form-customer').on('click','#customer-postcode',function(e){
        //getPostcodeFromDisrtict($('#ddl-district option:selected').val());
    });

    actionVatAvalibleBusinessType = (id,callback) => {
        $.ajax({
            url:'index.php?r=customers/ajax/vat-avalible-business-type&id=' + id,
            type:'GET',
            dataType:'JSON',
            success:function(res){
                callback(res);
            }
        })
    }

    $('body').on('change','#customer-genbus_postinggroup',function(){

        var id = $(this).val();
        actionVatAvalibleBusinessType(id,(res) => {
            if (res.status==200){
                if (res.data.allow_vat==0){
                    $('#customer-vatbus_postinggroup').val('02');
                }else{
                    $('#customer-vatbus_postinggroup').val('01')
                }
            }
        });        
        
    });

    $('body').on("click",".field-customer-child_name .input-group-addon",function(){
        $("#modal-pick-customer-wizard").modal("show");
    })




    // ----- Select Head office -----

    $('body').on('change','#customer-headoffice',function(){
        if(parseInt($(this).val())===0){
            $('body').find('.child-select').slideDown('fast');
        }else {
            $('body').find('.child-select').slideUp('fast');
        }
    });
    
    let renders = (data, div) => {
    let html = `<table class="table table-bordered" หะั>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th class="text-right">Select</th>
                            </tr>
                        </thead>`;
    html += "<tbody>";

    data.length > 0
        ? data.map(model => {
            html +=
            `<tr data-key="` +
            model.id +
            `">
                            <td class="code" style="font-family:roboto;">` +
            model.code +
            `</td>
                            <td class="name">` +
            model.name +
            `</td>
                            <td class="text-right"><button type="button" class="selected-customer btn btn-primary btn-flat">Select</button></td>
                        </tr>`;
        })
        : (html += "");

    html += "</tbody>";
    html += "</table>";

    $("body")
        .find(div)
        .html(html);
    };

    let search = search => {
        fetch("?r=SaleOrders/wizard/find-customers", {
                method: "POST",
                body: JSON.stringify({ search: search }),
                headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
                }
            })
            .then(res => res.json())
            .then(response => {
                renders(response.data, "#renderCustomer");
            })
            .catch(error => {
                console.log(error);
            });
    };

    $("body").on("submit", 'form[name="search"]', function() {
        let words = $('input[name="search"]').val();
        search(words);
    });

    // Select customer
    $("body").on("click", "button.selected-customer", function() {
    let id = parseInt(
        $(this)
        .closest("tr")
        .attr("data-key")
    );
    let name = $(this)
        .closest("tr")
        .find("td.name")
        .text();
    let code = $(this)
        .closest("tr")
        .find("td.code")
        .text();

    let customer = { id: id, name: name, code: code };

    localStorage.setItem("customer-child", JSON.stringify(customer));
        $("#modal-pick-customer-wizard").modal("hide");
        // set to field
        $('#customer-child').val(id);
        $('body').find('#customer-child_name').val(name);
    });

    // <----- Select Head office -----

JS;

$this->registerJS($js,\yii\web\View::POS_END);

 ?>

<div class="modal fade " id="modal-pick-customer-wizard" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Customer')?></h4>
            </div>
           
            <div class="modal-body">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-6 pull-right">
                        <form name="search">
                            <div class="input-group"  >
                                <input type="text" name="search" class="form-control" autocomplate="off" placeholder="<?=Yii::t('common','Search')?>" />                 
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" id="renderCustomer"></div>
                </div>
            </div>
             
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <i class="fas fa-power-off"></i> Close</button>
                 
            </div>
        </div>
    </div>
</div>




<?PHP

$deep =<<<JS
 


     function readURL(input,div) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $(div)
                .fadeOut(400, function() {
                    $(div).attr('src', e.target.result);
                })
                .fadeIn(400);

                //$('#img-preview').attr('src', e.target.result).fadeIn('slow');

            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customer-logo").change(function(){
        readURL(this,'#img-preview-logo');
    });

    $("#customer-photo").change(function(){
        readURL(this,'#img-preview-photo');
    });


 
    


    $('form#form-customer').on('keydown','#customer-postcode', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        if($('body').find('#ddl-district').val()){
            // เปลี่ยนเองได้
        }else{
            findAutoPostCode($(this).val());
        }
        
        return false;

        //findAutoPostCode($(this).val());
      }
    });

    $('body').on('change','#customer-postcode',function(e){
        //findAutoPostCode($(this).val());
        if($('body').find('#ddl-district').val()){
            // เปลี่ยนเองได้
        }else{
            findAutoPostCode($(this).val());
        }
    });

    $('body').on('change','#ddl-province',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');
        // Clear Postcode;
        $('#customer-postcode').val('');
        //getPostcodeFromDisrtict($(this).val());
        getCityFromProvince($(this).val());
    });

    

    // $('body').on('change','#customer-city',function(){
    //     //route('index.php?r=ajax/get-amphur','GET',{data:1},'');


    //     getDistrictFromCity($(this).val());
    //     $('option[value="0"]').attr('disabled','disabled');

    // });


    $('body').on('change','#ddl-district',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');

        //$('#customer-postcode').show();
        getPostcodeFromDisrtict($(this).val());
        $('#ddl-district option[value="0"]').attr('disabled','disabled');


    });

    function findAutoPostCode(postcode)
    {



        var zipcode = postcode;

        $.ajax({

              url:"index.php?r=ajax/postcode-validate",
              type: "GET",
              data: {postcode:zipcode},
              async:false,
              success:function(getData){

                if(Number(getData) >= 1)
                {
                          //getProvince(zipcode);
                          getOneProvince(zipcode);
                          //getCityFromProvince($('#ddl-province').val());
                          getCityFromZipcode(zipcode);
                          //getDistrictFromCity($('#ddl-amphur').val());
                          getDistrictFromZipcode(zipcode);


                          $('#ddl-amphur').removeAttr('disabled');
                          $('#ddl-district').removeAttr('disabled');
                          //getDistrict(zipcode);


                }else {



                  swal(
                      "{$Yii::t('common','No zip code of your choice.')}",
                      "{$Yii::t('common','Please re-enter your zip code.')}",
                      'warning'
                    );
                }

               }
          });
    }

    function getOneProvince(postcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-one-province&postcode="+postcode,
            type: "GET",
            data: {postcode:postcode},
            success:function(getData){
                //var obj = jQuery.parseJSON(getData);
                 
                $('select[id="ddl-province"]').val(getData);
                $('button[data-id="ddl-province"').attr('title',' ');
                $('.selectpicker').selectpicker('refresh');
            }

        });

    }
    function getProvince(postcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-province",
            type: "GET",
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#ddl-province').html('');
                $.each( obj, function( key, value ) {

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

                $('#ddl-amphur').html('');
                $.each( obj, function( key, value ) {

                   $('#ddl-amphur').append($('<option ></option>').val(value.val).html(value.text));

                });




            }

        });
    }




    function getDistrict(postcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-tumbol&postcode="+postcode,
            type: "GET",
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#ddl-district').html('');
                $.each( obj, function( key, value ) {

                   $('#ddl-district').append($('<option></option>').val(value.val).html(value.text));


                });



            }

        });
    }


   function getCityDefault(city)
    {
        $.ajax({

            url:"index.php?r=ajax/get-city-default&city="+city+"&postcode="+$('#customer-postcode').val(),
            type: "GET",
            data: {postcode:$('#customer-postcode').val(),city:city},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#customer-city').html('');
                $.each( obj, function( key, value ) {

                   $('#customer-city').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                   $("#customer-city select").val(city);
                });




            }

        });
    }





    function getCityFromProvince(province)
    {

        $.ajax({

            url:"index.php?r=ajax/city-from-province&province="+province,
            type: "POST",
            data: {province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#ddl-amphur').html('');
                $.each( obj, function( key, value ) {

                    $('#ddl-amphur').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));



                });

            }

        });

    }

    function getCityFromZipcode(zipcode)
    {

        $.ajax({

            url:"index.php?r=ajax/city-from-zipcode&zipcode="+zipcode,
            type: "POST",
            data: {zipcode:zipcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#ddl-amphur').html('');
                $.each( obj, function( key, value ) {

                    $('#ddl-amphur').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));



                });

            }

        });

    }



    function getDistrictFromCity(city,district)
    {
        $.ajax({

            url:"index.php?r=ajax/get-district-city&district="+district+"&city="+city,
            type: "GET",
            data: {city:city},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#ddl-district').html('');
                $.each( obj, function( key, value ) {

                   $('#ddl-district').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                });



            }

        });
    }

    function getDistrictFromZipcode(zipcode)
    {
        $.ajax({

            url:"index.php?r=ajax/get-district-zipcode&zipcode="+zipcode,
            type: "GET",
            data: {zipcode:zipcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#ddl-district').html('');
                $.each( obj, function( key, value ) {

                   $('#ddl-district').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                });



            }

        });
    }

    $(document).ready(() => {
        $('.of-month').html($('#customer-payment_due').val());
    })
    
    $('body').on('change','#customer-payment_due',function(){
        $('.of-month').html($(this).val());
    })

JS;

$this->registerJS($deep);
