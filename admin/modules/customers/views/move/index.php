<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
//use yii\grid\GridView;
//use yii\widgets\Pjax;
use common\models\Province;
use yii\helpers\ArrayHelper;

use common\models\SalesPeople;
use common\models\Company;

use kartik\export\ExportMenu;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\customers\models\SearchCustomer */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('common', 'Customers');
$this->params['breadcrumbs'][] = $this->title;


$gridColumns = [
    //['class' => 'yii\grid\SerialColumn'],
    //['class' => '\kartik\grid\RadioColumn'], 
    ['class' => 'yii\grid\CheckboxColumn'],
    [
        'attribute' => 'code',
        'headerOptions' => ['class' => ' ','style' => 'width:100px;'],
        'contentOptions' => ['style' => 'font-family: "saraban", "roboto";'],
        'format' => 'raw',
        'value' => function ($model) { 
            //return Html::a($model->code, ['view', 'id' => $model->id]);
            return $model->code;
        },
    ],
    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function ($model) { 

           // $html = '<div>'.Html::a($model->name, ['view', 'id' => $model->id],['style' => 'font-size: medium;']).'</div>';
           // $html.= '<small>'.$model->address.'</small>';
           // $html.= '<div style="margin-top:15px;">'.($model->contact? '<i class="far fa-user-circle"></i> '.$model->contact : null).'</div>';

            // $model->phone = str_replace(" ",",",$model->phone);
            // $mobiles = explode(",",$model->phone);

            // foreach ($mobiles as $mobile) {
            //     if($mobile)
            //     $html.= '<div><i class="fas fa-mobile-alt"></i> '.($mobile ? $mobile : '--').'</div>';
            // }
            return $model->name;
        },
    ],
    [
        'attribute' => 'province', 
        'label' => Yii::t('common','Province'),
        'value' => function($model){
            $Province = '';
            if($model->province!='') $Province = $model->provincetb->PROVINCE_NAME;
            return $Province;
        }
    ],
    [
        'attribute' => 'region', 
        //'label' => Yii::t('common','ภาค'),
        'value' => 'provincetb.zone.name',
        'filter'=>ArrayHelper::map(\common\models\Zone::find()->asArray()->all(), 'id', 'name'),
        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('common','Show All')],
    ],
    [
        'attribute' => 'owner_sales',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width:180px; overflow-x:auto;'],
        'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
        'value' => function ($model) { 
            
            if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->count()>0)
            {
                $sales = SalesPeople::find()
                ->where(['code' => explode(',',$model->owner_sales)])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->orderBy(['code' => SORT_ASC])
                ->all();
                $salpeople = '';
                foreach ($sales as $people) {
                    $salpeople.= '<span>['.$people->code.'] '.$people->name.'</span> <br />'."\r\n"; 
                }
                return $salpeople;

            }else {
                return '-';
            }
        },
        'filter' => ArrayHelper::map(\common\models\SalesPeople::find()
                                        ->where(['comp_id' => \Yii::$app->session->get('Rules')['comp_id']])
                                        ->orderBy(['code' => SORT_ASC])
                                        ->all(), 
                                        'code',
                                        function($model){ return '['.$model->code.'] '.$model->name. ' '.$model->surname.' '.($model->status != 0 ? '' : '*'); }
        ),
        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('common','Every one')],
    ],
    //['class' => 'yii\grid\ActionColumn']
];

?>
 
<style>
    .pagination {
        margin: 0px;
    }
    .form-group {
        margin-bottom: 0px;
    }
    @media print {
        .no-print, .no-print *{
            display: none !important;
        }
    }
</style>

<div class="customer-index" ng-init="Title='<?=$this->title;?>'">
    <div class="row">
        <div class="col-xs-12">
            <?= Html::a('<i class="fa fa-home"></i> '.Yii::t("common","Home"),['index'],['class' => 'btn btn-default btn-flat'])?>
            <?= Html::a('<i class="fa fa-id-card"></i> '.Yii::t("common","Customer"),['/customers/customer'],['class' => 'btn btn-default btn-flat'])?>
        </div>
    </div>
  <?php
$layout = <<< HTML
    <div class="pull-right">
        {summary}
    </div>
    {custom}
    <div class="clearfix"></div>
    {items}
    <div class="row">
        <div class="pull-right">
            <div class="col-xs-12">
            {pager}
            </div>
        </div>
    </div>
HTML;
?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table  table-bordered', 'style' => 'font-family: saraban, roboto;'],
        'filterModel' => $searchModel,
        'options' => ['id' => 'grid'],
        'responsiveWrap' => false,
        'pjax'=>false,
        'columns' => $gridColumns,
        'resizableColumns'=>true,
        'layout' => $layout,
        'replaceTags' => [
            '{custom}' => function($widget) {
                // you could call other widgets/custom code here
                if ($widget->panel === false) {
                    return '';
                } else {
                    return '';
                }
            }
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],

    ]); ?>
     
    <div  style="position:fixed; bottom: 10px;">
        <button class="btn btn-success move-customer"><i class="fas fa-arrow-right"></i> <?= Yii::t('common','Move selected customer')?></button>
    </div>
   
</div>

<?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="modal fade" id="modal-point-customer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?= Yii::t('common','Select Destination')?> (<span class="count-customer"></span>)</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">                             
                            <?php 
                                if(Yii::$app->user->identity->id==1){
                                    $CompanyList = Company::find()->orderBy(['name' => SORT_ASC])->all();
                                }else {
                                    $CompanyList = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
                                                                ->orderBy(['name' => SORT_ASC])
                                                                ->all();
                                }
                            ?>
                            <?= $form->field($searchModel, 'comp_id')->dropDownList(ArrayHelper::map($CompanyList,'id','name'),
                                    [
                                        'data-live-search'=> "true",
                                        'class' => 'selectpicker form-control',                                        
                                        'value' => Yii::$app->session->get('Rules')['comp_id']                                       
                                    ])->label(Yii::t('common','Company')) ?>
                        </div>                    
                        <div class="col-sm-6">       
                            <?= $form->field($searchModel, 'select_sale')->dropDownList(
                                ArrayHelper::map(\common\models\SalesPeople::find()
                                                ->where(['comp_id' => \Yii::$app->session->get('Rules')['comp_id']])
                                                ->orderBy(['code' => SORT_ASC])
                                                ->all(), 
                                                'id', function($model){ 
                                                    return '['.$model->code.'] '.$model->name. ' '.$model->surname.' '.($model->status != 0 ? '' : '*'); 
                                                }),
                                [
                                    'data-live-search'  => "true",
                                    'class' => 'selectpicker form-control bg-success',
                                    'multiple'  =>  "multiple"
                                ])->label(Yii::t('common','Assign for'));
                                ?>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i>  <?=Yii::t('common','Close')?></button>
                    <button type="button" class="btn btn-warning btn-assign-customer"><i class="fas fa-random"></i> <?=Yii::t('common','Move')?></button>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

 <?php 
$Yii = 'Yii';
$js=<<<JS


$('body').on('click','button.move-customer',function(){
    let Ids     = $('#grid').yiiGridView('getSelectedRows');
    localStorage.removeItem('customers');

    if(Ids.length > 0){
        $('#modal-point-customer').modal('show');
        $('.count-customer').html(Ids.length);
        console.log(Ids);
        localStorage.setItem('customers',JSON.stringify(Ids));
    }else{
        alert("{$Yii::t('common','Please select one.')}");
    }
    
})
 
$('body').on('click','button.btn-assign-customer',function(){
    let data ={ 
            'customers' : localStorage.getItem('customers') ? JSON.parse(localStorage.getItem('customers')) : [],
            'sales': $('#movecustomer-select_sale').val(),
            'comp_id' : parseInt($('#movecustomer-comp_id').val())
        };
    if(data.sales.length > 0){
        fetch("?r=customers%2Fmove%2Findex-change", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            if(response.status===200){
                swal( response.message, response.count, "success");
                $('#modal-point-customer').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }else{
                swal( response.message, response.suggestion, "error");
            }
        })
        .catch(error => {
            console.log(error);
        });

    }else{
        alert("{$Yii::t('common','Please select sale person.')}");
    }
})
   
JS;
$this->registerJs($js,\yii\web\View::POS_END,'JS');
?>
 