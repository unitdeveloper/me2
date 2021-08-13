<?php

use yii\helpers\Html;
use yii\grid\GridView;
//use kartik\grid\GridView;

use yii\helpers\ArrayHelper;
 
 
use yii\widgets\Pjax;


// $this->registerJs(
//         "$(document).on('ready pjax:success', function() {
//                 $('#ew-search-items-btn').click(function(e){
//                    e.preventDefault(); //for prevent default behavior of <a> tag.
//                    var tagname = $(this)[0].tagName;
//                    $('#ewGetItemModal').modal('show').find('.ew-Pick-Inv-Item').load($(this).attr('href'));
//                });
//             });
//         ");


 
?>
<style type="text/css">
    input[type="number"]{
        width: 150px;
    }
    .colwidth{
        width: 180px;
    }


    #table-pick-items tr{
      cursor: pointer;
    }
    

    .obj-box{
        width: 105px;
        margin:auto;
         
    }
    .ew-inv-qty{
      display: none;
    }
    .ew-inv-price{
      display: none;
    }
</style>


 <?php Pjax::begin(['id' => 'ew-form-pick-item-pjax', 
            'timeout' => 10000, 
            'enablePushState' => false, 
            'enableReplaceState' => false,
            'clientOptions' => ['method' => 'POST']]); ?>  


 <form id="ew-form-pick-item" name="pickItems">
       

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        // 'tableOptions' => [
        //                     'class' => 'table-revert',
        //                 ],
        'tableOptions' => ['id' => 'table-pick-items','class' => 'table-hover table table-border'],
        //'responsive' => false,
        //'hover'=>false,
        //'striped' => false,
        //'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'No',
                'format' => 'raw',
                'label' => Yii::t('common','Items'),
                'headerOptions'=>['class'=>'checkall'],

                'value' => function($model){

                    $img = 'images/product/'.$model->ItemGroup.'/'.$model->Photo;
                    if($model->Photo=='') $img = 'images/icon/ewinl--.png';

                     
                    $data = '<div class="row" >

                                <div class="col-sm-2 text-center">
                                    <div class="obj-box">
                                    <input 
                                                    type="checkbox" 
                                                    class="items ew-checked" 
                                                    id="'.$model->No.'"
                                                    name="items[]" 
                                                    code="'.$model->master_code.'" 
                                                    value="'.$model->No.'"> '.$model->master_code.'
                                                    <label for="'.$model->No.'" style="background: url('.$img.') no-repeat; background-size: 90px; background-position: center; "> </label>
                                    </div>
                                </div>
                                <div class="col-sm-10" >
                                    
                                         
                                        <div class="row">
                                             
                                            <label class="col-xs-3">'.Yii::t('common','Name').':</label> <div class="col-xs-9">'.$model->Description.' </div>
                                             
                                        </div>
                                        <div class="row">
                                             
                                            <label class="col-xs-3">'.Yii::t('common','Product Name (th)').':</label> <div class="col-xs-9">'.$model->description_th.'</div>
                                             
                                        </div>
                                   
                                        <div class="row">
                                            <div class="col-xs-3" id="" >
                                                    <label>'.Yii::t('common','Quantity').'</label>: '.number_format($model->Inventory).'
                                                    <input type="number" name="qty[]" id="ew-inv-qty" value="1" class="form-control text-right hidden" readonly="readonly">
                                                </div> 
                                            <div class="col-xs-9">
                                                    <label>'.Yii::t('common','Price').'</label>: '.number_format($model->StandardCost).'
                                                    <input type="number" name="price[]" id="ew-inv-price" value="'.$model->StandardCost.'" class="form-control text-right hidden" readonly="readonly">
                                            </div>
                                            
                                        </div>

                                         
                                </div>
                                 
                            </div>';


                    return $data;

                }
            ],
            // [
                 
            //     'format' => 'raw',
            //     'contentOptions' => ['align'=>'right'],
            //     'headerOptions'=>['class'=>'colwidth'],
            //     'label' => Yii::t('common','Quantity'),
            //     'value' => function($model){

                   
            //         return '<input type="number" name="qty[]" id="ew-qty" value="0" class="form-control text-right "><div id="ew-qty"></div>';
            //     },

            // ],
            // [
                 
            //     'format' => 'raw',
            //     'contentOptions' => ['align'=>'right'],
            //     'headerOptions'=>['class'=>'colwidth'],
            //     'label' => Yii::t('common','Price'),
            //     'value' => function($model){

                   
            //         return '<input type="number" name="price[]" id="ew-price" value="'.$model->StandardCost.'" class="form-control text-right "><div id="ew-price"></div>';
            //     },

            // ],

        ],

        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
            'lastPageLabel'=>'<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>

 
</form>

<?php Pjax::end(); ?>  
