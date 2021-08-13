<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;
use kartik\form\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Purchase\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Purchase Order');
$this->params['breadcrumbs'][] = $this->title;

$column = [
    [
        'headerOptions' => ['class' => 'hidden-xs', 'style' => 'width:30px;'],
        'contentOptions' => ['class' => 'hidden-xs font-roboto'],
        'filterOptions' => ['class' => 'hidden-xs'],
        'class' => 'yii\grid\SerialColumn'
    ],

    [
        'label' => 'Vat',
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-center hidden-xs', 'style' => 'width:20px;'],
        'contentOptions' => ['class' => 'text-center font-roboto hidden-xs'],
        'value' => function($model){
            $html = $model->vat_percent > 0 
                        ? ($model->include_vat == 1 
                            ? '<i class="far fa-check-square text-green"></i>'
                            : '<i class="far fa-check-square text-success"></i>')
                        : '<i class="far fa-square"></i>';

            return $html;
        }
    ],

    [
        'attribute' => 'doc_no',
        'format' => 'raw',
        'headerOptions' => ['class' => 'hidden-xs'],
        'filterOptions' => ['class' => 'hidden-xs'],
        'contentOptions' => [
            'style'=>'width:150px;',
            'class' => 'hidden-xs font-roboto'
        ],
        'value' => function($model){
            $date = ($model->order_date)? $model->order_date : ' ';
            $html = '<div>'.Html::a($model->doc_no,['/Purchase/order/view', 'id' => $model->id],[
                        'target' => '_blank',
                        'data-pjax' => "0"
                        // 'class' => ($model->completeReceive 
                        //                 ? 'text-green' 
                        //                 : ($model->received ? 'text-yellow' : 'text-red')
                        //             )
                    ]).'<div>';
            $html.= '<small>'.$date.'</small>';
            //$html.= '<small>'.number_format($model->balance).'</small>';
            return $html;
        }
    ],

    
    [
        'attribute' => 'vendor_name',
        'label' => Yii::t('common','Vendors'),
        'format' => 'html',
        'headerOptions' => ['class' => 'hidden-xs', 'style' => 'min-width:150px;'],
        'filterOptions' => ['class' => 'hidden-xs'],
        'contentOptions' => [
            'class' => 'hidden-xs',
            'style'=>'max-width:350px; overflow: auto; white-space: normal; word-wrap: break-word; font-family: saraban;'
        ],
        'value' => function($model){
            $html = '<div class="hidden-sm hidden-md hidden-lg text-aqua">'.$model->doc_no.'</div>';
            $html.= '<div>'.$model->vendor->code.'</div>';
            $html.= '<div class="text-info">'.$model->vendor_name.'</div>';
            $html.= '<div class="hidden-sm hidden-md hidden-lg "><small>'.$model->detail.'</small></div>';
            //$html.= '<small>'.$model->address.'</small>';
            return $html;
        }
    ],
    
    [
        'label' => Yii::t('common','Reference No'),
        'headerOptions' => ['class' => 'hidden-xs', 'style' => 'width:120px;'],
        'contentOptions' => ['class' => 'hidden-xs', 'style' => 'font-family: saraban;'],
        'value' => function($model){
            return $model->ref_no ? $model->ref_no : '';
        }
    ],
    [
        'label' => Yii::t('common','Comment'),
        'headerOptions' => ['class' => 'hidden-xs', 'style' => 'width:120px;'],
        'contentOptions' => ['class' => 'hidden-xs', 'style' => 'font-family: saraban;'],
        'value' => function($model){
            return $model->detail ? $model->detail : '';
        }
    ],
    
    [
        'label' => Yii::t('common','Balance'),
        'headerOptions' => ['class' => 'text-right hidden-xs', 'style' => 'width:100px;'],
        'contentOptions' => ['class' => 'text-right font-roboto hidden-xs'],
        'value' => function($model){
            return number_format($model->total->total);
        }
    ],
    //'address',             
    [
        'class' => 'yii\grid\ActionColumn',
        'buttonOptions'=>['class'=>'btn btn-default'],
        'contentOptions' => ['class' => 'text-right','style'=>'min-width:260px;'],
        'headerOptions' => ['class' => 'hidden-xs'],
        'filterOptions' => ['class' => 'hidden-xs'],
        'template'=>'<div class="btn-group btn-group text-center" role="group">{document}  {edit} {status}  {print}  </div>',
        'options'=> ['style'=>'width:300px;'],
        'buttons'=>[
            
            'document' => function($url,$model,$key){
                return '<div class="font-roboto pull-left hidden-sm hidden-md hidden-lg mr-10">
                            '.Html::a($model->doc_no,['view', 'id' => $model->id]).'</a>
                        </div>';
            },

            'status' => function($url,$model,$key){
                if($model->status==0){
                    $Received =  '';                    
                }else if($model->status==1){ // Release
                    $Received =  '<li class="change-order-status line" data-key="10">'.Html::a('<i class="fas fa-hand-holding text-green"></i> '.Yii::t('common','Received'),'#',['class'=>' ', 'title' => Yii::t('common','Product Receive')]).'</li>';
                }else if($model->status==10){ // Received
                    $Received =  '<li class="line" data-key="10">'.Html::a('<i class="fa fa-eye text-info"></i>'.Yii::t('common', 'Detail'), ['view','id' => $model->id], ['class' => ' ', 'target' => '_blank','data-pjax' => "0"]).'</li>';
                }else{
                    $Received =  '<li class="change-order-status line" data-key="10">'.Html::a('<i class="fa fa-eye text-info"></i> '.Yii::t('common', 'Detail'), '#', ['class' => ' ', 'target' => '_blank','data-pjax' => "0"]).'</li>';
                }

                //if($model->status != 10){
                    return '<div class="btn-group" role="group">
                                        <button type="button" class="btn  dropdown-toggle '.($model->status == 10 ? 'bg-teal' : 'btn-default-ew').' " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:80px">
                                        '.($model->status == 0 
                                            ? '<i class="fas fa-lock-open"></i> O'
                                            : ( $model->status == 1 
                                                ? '<i class="fas fa-lock text-red"></i> Re'
                                                : '<i class="fas fa-check"></i> '.Yii::t('common','Received'))                                                
                                            ).'
                                        <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu option-list-menu">
                                            <li class="change-order-status line" data-key="0"><a href="#" style="color: #616161 !important;"><i class="fas fa-lock-open"></i> Open</a></li>
                                            <li class="change-order-status line" data-key="1"><a href="#"><i class="fas fa-lock text-red mr-5"></i> Release</a></li>                                        
                                            '.$Received.'
                                        </ul>
                                </div>';
                //}else{
                //    return  Html::a('<i class="fa fa-eye text-info"></i> '.Yii::t('common', 'Detail'), ['view','id' => $model->id], ['class' => 'btn btn-default-ew', 'target' => '_blank']);
                //}
            },

            'edit' => function($url,$model,$key){

                return '<div class="btn-group" role="group">
                            '.Html::a('<i class="fa fa-pencil"></i> '.Yii::t('common','Edit').'  <span class="caret"></span>',['#'],[
                                'class' => "btn btn-warning-ew",
                                'data-toggle' => 'dropdown',
                                'aria-haspopup' => 'true',
                                'aria-expanded' => 'false'
                            ]).'
                                
                            <ul class="dropdown-menu">
                                <li class=" ">'.Html::a('<i class="fas fa-hand-holding text-green mr-5"></i> '.Yii::t('common','Product Receive'),['receive', 'id' => $model->id],['class'=>' ', 'title' => Yii::t('common','Product Receive')]).'</li>                                            
                                <li class=" ">'.Html::a('<i class="fa fa-eye text-info"></i> '.Yii::t('common', 'View'), ['view','id' => $model->id], ['class' => ' ', 'target' => '_blank','data-pjax' => "0"]).'</li>
                                <li class=" ">'.Html::a('<i class="fa fa-pencil text-warning"></i> '.Yii::t('common','Edit'),['update','id' => $model->id], ['class' => " ", 'target' => '_blank','data-pjax' => "0"]).'</li>
                                <li class=" ">'.Html::a('<i class="far fa-trash-alt mr-10"></i> '.Yii::t('common','Delete'),['delete', 'id' => $model->id],[
                                    'class' => 'text-red',
                                    'data' => [
                                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]).'</li>
                            </ul>
                        </div>';
            },

             
            'print' => function($url,$model,$key){                      
                return Html::a('<i class="fas fa-print"></i> ',$url,['class'=>'btn btn-info-ew','target'=>'_blank','data-pjax' => "0"]);
            },

            'view' => function($url,$model,$key){
                return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-primary-ew']);
            },

            'delete' => function($url,$model,$key){
                return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                    'class' => 'btn btn-danger-ew',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            },

            'update' => function($url,$model,$key){
                return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success-ew']);
            }

          ]
      ],
    ];
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="purchase-header-index" ng-init="Title='<?=$this->title?>'">

    <div class="row">
        <div class="col-xs-12">
              <div class="row">
                <div class="col-sm-4 pull-right">
                    <?php $form = ActiveForm::begin([
                        'action' => ['index'],
                        'method' => 'get',
                    ]); 
                    $searchModel->search = trim($search);
                    ?>
 
                    <?= $form->field($searchModel, 'search',[
                        'addon' => [
                            'prepend' => [
                                'content' => Html::a(Yii::t('common', 'Reset'),['index'], ['class'=>'btn btn-primary-ew']),
                                'asButton' => true
                            ],
                            'append' => [
                                'content' => Html::submitButton(Yii::t('common', 'Search'), ['class'=>'btn btn-primary']),
                                'asButton' => true
                            ]
                        ]
                    ])->textInput(['placeholder' => Yii::t('common','Search'), 'class' => 'form-control'])
                    ->label(false) ?>

                  
                    <?php ActiveForm::end(); ?>                   
                </div>
              </div>  
        </div>
        <div class="col-sm-6"> </div>
        <div class="col-sm-6 text-right hidden-xs">
        <?= Html::a('<i class="fas fa-list-ul"></i> '.Yii::t('common', 'Purchase Line'), ['/Purchase/purchase-line'], ['class' => 'btn btn-default-ew ']) ?>   
            <?=ExportMenu::widget([
                'dataProvider' => $work,
                'columns' => $column,
                'columnSelectorOptions'=>[
                    'label' => 'Columns',
                    'class' => 'btn btn-success-ew'
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,                                
                ],
                 
                'target' => ExportMenu::TARGET_BLANK,
            ]); 
        ?>   
        </div>
        
    </div>
    <h3 class="text-yellow" style="margin-top:20px;">PO ที่ยังไม่ส่ง</h3>
    <hr class="style19" />
    <div class=" "  >
        <?= GridView::widget([
            'dataProvider' => $wait,
            //'filterModel' => $searchModel,
            'pjax' => true,
            'tableOptions' => ['class' => 'table  table-condensed '],
            'columns' => $column,
            'responsive' => false,
            'responsiveWrap' => false
        ]); ?>
    </div>
    
    <h3 class="text-red" style="margin-top:120px;">PO ค้างรับ</h3>
    <hr class="style19" />
    <div class=" "  >
        <?= GridView::widget([
            'dataProvider' => $work,
            //'filterModel' => $searchModel,
            'pjax' => true,
            'tableOptions' => ['class' => 'table  table-condensed'],
            'columns' => $column,
            'responsive' => false,
            'responsiveWrap' => false
        ]); ?>
    </div>
    
    <h3 class="text-green" style="margin-top:120px;">รับสินค้าแล้ว</h3>
    <hr class="style19" />
    <div class=" " style="margin-bottom:120px;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'pjax' => true,
            'tableOptions' => ['class' => 'table  table-condensed'],
            'columns' => $column,
            'responsive' => false,
            'responsiveWrap' => false
        ]); ?>
    </div>
   
</div>

<div class="content-footer" >
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            
        </div>
        <div class=" hidden-lg hidden-md hidden-sm hidden-xs col-sm-6 text-right">            
            <?= Html::a('<i class="fas fa-th"></i> '.Yii::t('common', 'แสดงทั้งหมด'), '#', ['class' => 'btn btn-primary ']) ?>   
            <?= Html::a('<i class="far fa-clock"></i> '.Yii::t('common', 'ยังไม่รับสินค้า'), '#', ['class' => 'btn btn-warning ']) ?>   
            <?= Html::a('<i class="fas fa-warehouse"></i> '.Yii::t('common', 'รับสินค้าแล้ว'), ['index','cond' => 'received'], ['class' => 'btn btn-success ']) ?>   
        </div>
    </div>
</div>


<?php 
$js=<<<JS

$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');
});


const changeStatus = (obj, callback) => {
    fetch("?r=Purchase%2Forder%2Fupdate-field", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => { callback(response); })
    .catch(e => { swal("Fail!", "Something Wrong. "+ e.responseText +' '+ new Date().toTimeString().slice(0, 8), "error"); });
};

$('body').on('click', '.change-order-status', function(){
    let id      = $(this).closest('tr').attr('data-key');
    let value   = parseInt($(this).attr('data-key'));
    
    let doChange = (id,value) => {    
        changeStatus({id:id, field:'status', value:value}, res => {
            if(res.status===200){
                window.location = "?r=Purchase%2Forder%2Findex"; 
            }else{
                swal("Fail!", res.message, "error");
            }
        })
    }

    if(value===10){
        if(confirm('ต้องการเปลี่ยนสถาณะ (โดยไม่รับสินค้า) ใช่หรือไม่ ?')){
            doChange(id,value);
        }else{
            return false;
        }
    }else{
        if(confirm('ต้องการเปลี่ยนสถาณะ ใช่หรือไม่ ?')){
            doChange(id,value);
        }else{
            return false;
        }
    }

});

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');