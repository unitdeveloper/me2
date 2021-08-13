<?php

use yii\helpers\Html;
use yii\grid\GridView;

 

// $this->registerJs(
//         "$(document).on('ready pjax:success', function() {
//                 $('.modalButton').click(function(e){
//                    e.preventDefault(); //for prevent default behavior of <a> tag.
//                    var tagname = $(this)[0].tagName;
//                    $('#editModalId').modal('show').find('.modalContent').load($(this).attr('href'));
//                });
//             });
//         ");
?>


<?php yii\widgets\Pjax::begin(['id' => 'grid-user-pjax',
                                'timeout'=>false,
                                'enablePushState' => false,
                                'enableReplaceState' => false,
                                'clientOptions' => ['method' => 'POST']
                                ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'options' => ['style' => 'table-layout:fixed;'],

        'columns' => [

            [
                'label' => Yii::t('common','Code'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'hidden-xs'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'value' => function ($model) {
                    //return $model->code;

                    return '<a href="#" id="ew-pick-customer" ew-val="'.$model->id.'" data-payment="'.$model->payment_term.'">'.$model->code.'</a>';
                    //return '<button class="btn btn-info">'.Yii::t('common',$model->code).'</button>';
                },
            ],
            [
                'label' => Yii::t('common','Name'),
                'format' => 'raw',
                'contentOptions' => ['style' => 'word-wrap:break-word;'],
                'value' => function ($model) {
                    $Province = '';
                    if($model->province!='') $Province = 'จ. '.$model->provincetb->PROVINCE_NAME;

                    $name = '<div class="hidden-sm hidden-md hidden-lg"><a href="#" id="ew-pick-customer" ew-val="'.$model->id.'" data-payment="'.$model->payment_term.'" class="text-black">'.$model->code.'</div></a>';
                    $name.= '<a href="#" id="ew-pick-customer" ew-val="'.$model->id.'" data-payment="'.$model->payment_term.'" style="max-width:200px; display:inline-block;" >'.$model->name.'</a>';
                    $name.= '<div class="hidden-sm hidden-md hidden-lg text-warning"><small>'.$Province.'</small></div>';

                   return $name;
                   //return $model->name;
                },
            ],
            [
                'label' => Yii::t('common','Address'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'hidden-xs'],
                'headerOptions' => ['class' => 'hidden-xs'],
                //'filterOptions' => ['class' => 'hidden-xs'],
                'value' => function ($model) {
                    return $model->address;
                },
            ],
            [
                'attribute' => 'province',
                'label' => Yii::t('common','Province'),
                'contentOptions' => ['class' => 'hidden-xs hidden-sm'],
                'headerOptions' => ['class' => 'hidden-xs hidden-sm'],
                'value' => function($model){
                    $Province = '';
                    if($model->province!='') $Province = $model->provincetb->PROVINCE_NAME;
                    return $Province;

                }
            ],
            //'address',
            //'owner_sales',
            [
                //'attribute' => 'owner_sales',
                'label' => Yii::t('common','Select'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    $html = '<a href="#" class="btn btn-primary btn-flat" id="ew-pick-customer" ew-val="'.$model->id.'" data-payment="'.$model->payment_term.'">
                                 '.Yii::t('common','Select').'
                            </a>';
                    return $html;
                },
            ],



        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=>Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
<?php yii\widgets\Pjax::end() ?>
