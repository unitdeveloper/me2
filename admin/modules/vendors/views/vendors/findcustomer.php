<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\vendors\models\VendorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
// $this->registerJs(
//         "$(document).on('ready pjax:success', function() {
//                 //$.pjax.reload({container:'#grid-pjax'});
//             });
//         ");
?>

<div class="row" >
    <div class="col-md-12">
        <?php Pjax::begin(['id' => 'grid-pjax',
                'timeout'=>5000,
                'enablePushState' => false,
                'enableReplaceState' => true,
                'clientOptions' => ['method' => 'GET']
                    ]); 
        ?>
    <?php
    ?>
    <?php $form = ActiveForm::begin([
        'action' => ['/vendors/vendors/ajax-find-customer'],
        'id' => 'companySearch',
        'method' => 'get',
        'options' => ['data-pjax' => true ],
    ]); ?>
    <div class="row">
        <div class="col-lg-12 text-center">
          <small class="text-warning">ใช้สำหรับการหาข้อมูลเบื้องต้นจากกรมสรรพากร ข้อมูลอาจไม่ตรงกับกรมพัฒนาธุรกิจ</small>
        </div>
        <div class="col-xs-12 col-sm-6 pull-right">
            <div class="row ">
                <div class="col-xs-12">
                    <?=$form->field($searchModel, 'name',[
                        'addon' => ['append' => [
                                            //'content'=>'<i class="fa fa-search pointer data-submit"></i>',
                                            'content' => Html::submitButton('Go', ['class'=>'btn btn-primary']),
                                            'asButton' => true
                                            ]
                        ]
                        ])->textInput(['class' => 'submit-search','placeholder' => Yii::t('common','Search')])->label(false) 
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
    <?php ActiveForm::end(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'rowOptions' => function($model){
                return ['class' => 'pointer getCompany'];
            },
            'tableOptions' => ['class' => 'table table-bordered table-hover'],
            'columns' => [
                // [
                //     'class' => 'yii\grid\SerialColumn',
                //     'contentOptions' => ['class' => 'text-center'],
                // ],
                'code',
                [
                  'attribute' => 'name',
                  'label' => Yii::t('common','Name'),
                  'value' => function($model){
                    return $model->name;
                  }
                ],
                'provincetb.PROVINCE_NAME',
                //'branch_name',
                //'branch',
                [
                  'label' => Yii::t('common','Select'),
                  'format' => 'raw',
                  'headerOptions' => ['class' => 'text-center','style' => 'width:50px;'],
                  'contentOptions' => ['class' => 'text-center'],
                  'value' => function($model){
                    $html = '<div /><button class="btn btn-info-ew">'.Yii::t('common','Select').'</button></div>';
                    return $html;
                  }
                ],
            ],
            'pager' => [
                'options'=>['class' => 'pagination'],   // set clas name used in ui list of pagination
                'prevPageLabel'     => '«',         // Set the label for the "previous" page button
                'nextPageLabel'     => '»',         // Set the label for the "next" page button
                'firstPageLabel'    => Yii::t('common','page-first'),     // Set the label for the "first" page button
                'lastPageLabel'     => Yii::t('common','page-last'),      // Set the label for the "last" page button
                'nextPageCssClass'  => 'next',      // Set CSS class for the "next" page button
                'prevPageCssClass'  => 'prev',      // Set CSS class for the "previous" page button
                'firstPageCssClass' => 'first',     // Set CSS class for the "first" page button
                'lastPageCssClass'  => 'last',      // Set CSS class for the "last" page button
                'maxButtonCount'    => 4,           // Set maximum number of page buttons that can be displayed
                ],
        ]); ?>
        </div>
    <?php Pjax::end(); ?>
    </div>
</div>
