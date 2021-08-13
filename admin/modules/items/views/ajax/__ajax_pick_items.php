<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;

use yii\helpers\ArrayHelper;
 
 
use yii\widgets\Pjax;


// $this->registerJs(
//         "$(document).on('ready pjax:success', function() {
//                 $('.modalButton').click(function(e){
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



    input[type=checkbox] {
      display: none;
    }
    /* 
    - Style each label that is directly after the input
    - position: relative; will ensure that any position: absolute children will position themselves in relation to it
    */

    input[type=checkbox] + label {
      position: relative;
      /*background: url(http://i.stack.imgur.com/ocgp1.jpg) no-repeat;*/
      height: 100px;
      width: 100px;
      display: block;
      border-radius: 50%;
      transition: box-shadow 0.4s, border 0.4s;
      border: solid 2px #FFF;
      box-shadow: 0 0 1px #FFF;/* Soften the jagged edge */
      cursor: pointer;
    }
    /* Provide a border when hovered and when the checkbox before it is checked */

    input[type=checkbox] + label:hover,
    input[type=checkbox]:checked + label {
      border: solid 2px #F00;
      box-shadow: 0 0 1px #F00;
      /* Soften the jagged edge */
    }
    /* 
    - Create a pseudo element :after when checked and provide a tick
    - Center the content
    */

    input[type=checkbox]:checked + label:after {
      content: '\2714';
      /*content is required, though it can be empty - content: '';*/
      height: 1em;
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      margin: auto;
      color: #F00;
      line-height: 1;
      font-size: 18px;
      text-align: center;
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



 <form id="ew-form-pick-item" name="pickItems">
 <?php Pjax::begin(['id' => 'ew-form-pick-item-pjax','timeout' => false,'enablePushState' => false,'clientOptions' => ['method' => 'POST']]); ?>         

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        // 'tableOptions' => [
        //                     'class' => 'table-revert',
        //                 ],
        'rowOptions' => ['style' => 'cursor:pointer;'],
        'responsive' => false,
        'hover'=>false,
        'striped' => false,
        //'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
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
                                                    value="'.$model->No.'">'.$model->master_code.'
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
    ]); ?>

<?php Pjax::end(); ?>  
</form>