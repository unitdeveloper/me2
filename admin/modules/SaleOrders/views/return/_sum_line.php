<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
//use kartik\grid\GridView;
 
?>


<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive no-padding" >
        <?php
            $gridColumns = [
                [
                    'class' => 'yii\grid\SerialColumn',                                    
                    'headerOptions' => ['class' => 'bg-info text-right hidden-xs','style' => 'width:30px;'],
                    'contentOptions'  => ['class' => 'bg-info hidden-xs','style' => 'vertical-align: middle;'],
                    'footerOptions'   => ['class' => 'bg-info hidden-xs']
                ],

                [
                    'label' => Yii::t('common','Items'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'hidden-xs','style' => 'vertical-align: middle;'],
                    'headerOptions' => ['class' => 'hidden-xs','style' => 'width:150px;'],
                    'footerOptions' => ['style' => 'min-width:150px;'],
                    'footerOptions' => ['class' => 'hidden-xs-cancel'],
                    'value' => function($model){
                        return $model->crossreference->no;
                    },
                    'footer' => '<div class="form-group has-feedback ew-item-insert"  style="margin-bottom:0px !important;">
                                    <div class="form-group has-success"  style="margin-bottom:0px !important;">
                                    <input type="text" name="InsertItem"  class="form-control InsertItem" placeholder="'.Yii::t('common','Search product').'">
                                    <span class="form-control-feedback " aria-hidden=""><i class="glyphicon glyphicon-search"></i></span>
                                    </div>
                                </div>'
                ],

                [
                    'label' => Yii::t('common','Description'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'hidden-xs','style' => 'min-width:200px;'],
                    'contentOptions' => ['class' => ' ','style' => 'vertical-align: middle;'],
                    'footerOptions' => ['class' => 'hidden-xs'],
                    'value' => function($model){
                        if($model->description==''){
                            $desc = $model->items->description_th;
                        }else {
                            $desc = $model->description;
                        }
                        $InvenByBom     = $model->items->invenByBom;
                        $html = '<div class="hidden-xs">'.$desc.'</div>';
                        $html.= '<div class="hidden-sm hidden-md hidden-lg my-10">
                                    <div class="row">
                                        <div class="col-xs-3">'.Html::img($model->items->picture,['class' => 'img-thumbnail go-detail', 'style' => 'max-width:80px;']).'</div>
                                        <div class="col-xs-9">
                                            <div class="row">
                                                <div class="col-xs-4 text-left text-show-calulate font-roboto">
                                                    '.number_format($model->quantity).'<span class="text-yellow"> x </span>'.number_format($model->unit_price,2).'
                                                </div>
                                                <div class="col-xs-8 text-right text-info total-text-line font-roboto">
                                                    <div class="mb-10 text-show-total">'.number_format($model->quantity * $model->unit_price,2).'</div>
                                                    <small class="text-gray mt-10">
                                                        '.Yii::t('common','Remain').': '.($InvenByBom < 0 ? 0 : number_format($InvenByBom)).'
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-aqua my-5 item-description">'.$desc.'</div>
                                    <div class="text-info my-5 item-code font-roboto">'.($model->item != 1414 ? $model->items->master_code : ' ').'</div>
                                </div>';
                                
                        return $html;
                    },
                    'footer' => '<div class="ew-desc"><input type="text" name="InsertDesc" ew-item-code="eWinl" id="InsertDesc" class="form-control"></div>'
                ],

                [
                    'label' => Yii::t('common','Quantity'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:120px; width:120px;'],
                    'contentOptions' => ['class' => 'text-right hidden-xs'],
                    'footerOptions' => ['class' => 'hidden-xs'],
                    'value' => function($model){
                        return '<input type="number"  step=any value="'.($model->quantity *1).'" class="form-control  text-right text-line update-quantity" name="quantity">';
                    },
                    'footer' => '<div class="ew-qty"><input type="number"  step=any  name="InsertQty"  class="form-control InsertQty"></div>',
                ],

                [
                    'label' => Yii::t('common','Measure'),
                    'headerOptions' => ['class' => 'hidden-xs' ,'style' => 'width:80px;'],
                    'contentOptions' => ['class' => 'hidden-xs','style' => 'vertical-align: middle;'],
                    'footerOptions' => ['class' => 'hidden-xs'],
                    'value' => 'items.UnitOfMeasure'
                ],

                [
                    'label' => Yii::t('common','Unit Price'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:120px; width:120px;'],
                    'contentOptions' => ['class' => 'text-right hidden-xs'],
                    'footerOptions' => ['class' => 'hidden-xs'],
                    'value' => function($model){
                        return '<input type="number"  step=any   value="'.($model->unit_price *1).'" class="form-control text-right text-line update-unit_price" name="unit_price">';
                    },
                    'footer' => '<div class="ew-price"><input type="number"  step=any   name="InsertPrice" class="form-control InsertPrice"></div>'
                ],

                [
                    'label' => Yii::t('common','Line Amount'),
                    'format' => 'html',
                    'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:102px; width:120px;'],
                    'contentOptions' => ['class' => 'text-right hidden-xs line-amount bg-yellow','style' => 'vertical-align: middle;'],
                    'footerOptions' => ['class' => 'hidden-xs'],
                    'value' => function($model){
                        return number_format($model->quantity * $model->unit_price,2);
                    },
                    'footer' => '<input type="hidden" name="item-id" id="item-id">'                                     
                ],
                // [
                //     'label' => Yii::t('common', 'Stock'),                   
                //     'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'width:50px;'],
                //     'contentOptions' => ['class' => 'text-right hidden-xs line-stock bg-purple'],
                //     'value' => function($model){
                //         //return $model->items->invenByBom < 0 ? 0 : number_format($model->items->invenByBom - $model->invenByOrder);
                //         $stock = $model->items->invenByBom;
                //         return $stock < 0 ? 0 : number_format($stock);
                //     }
                // ],

                [
                    'format' => 'raw',
                    'label' => Yii::t('common', 'Delete'),
                    'contentOptions' => ['class' => 'hidden-xs bg-gray'],
                    'headerOptions' => ['class' => 'text-center hidden-xs','style' => 'width:50px;'],
                    'footerOptions' => ['class' => 'hidden-xs bg-gray'],
                    'value' => function($model){
                        return Html::a('<i class="fas fa-times btn btn-danger-ew btn-flat"></i>', '#'.$model->id,
                        [
                            'class'=>'RemoveSaleLine',
                            'alt' => $model->items['Description'],
                            'qty' => $model->quantity,
                            'price' => $model->unit_price,
                        ]);
                    },
                    'footer' => '<button type="button" title="'.Yii::t('common','Clear').'" id="clear-line" class="btn btn-default-ew  btn-flat"><i class="fas fa-brush text-primary"></i></button>',
                ]
            ];
            ?>
            <?=  GridView::widget([
                'dataProvider'=> $dataProvider,
                'showFooter' => true,
                'headerRowOptions'=>['class'=>'bg-gray'],
                'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                'columns' => $gridColumns,
                //'responsive'=>true,
                //'hover'=>true,
                'summary' => false,
                'tableOptions' => [
                    'class' => 'table',
                ]
            ]);
            ?>
    </div>
    <!-- /.box -->
    </div>
</div>




<div class="item-detail">
    <div class=" ">
        <div class="content">
            <div class="row">
                <div class="col-xs-6"><a href="javascript:void(0);" id="back-btn"><i class="far fa-arrow-alt-circle-left fa-2x"></i></a></div>
                <div class="col-xs-6 text-right"><a href="javascript:void(0);" id="complete-btn" ><i class="far fa-check-circle fa-2x"></i></a></div>
            </div>
            <div class="row margin-top">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="col-xs-12">
                            <label><?=Yii::t('common','Code')?></label> <span class="item-code text-info">รหัสสินค้า</span>
                    </div>
                    <div class="col-xs-12">
                            <label><?=Yii::t('common','Name')?></label> <span class="item-name text-primary">ชื่อสินค้า</span>
                    </div>
                    <div class="col-xs-12 item-desc text-primary">รายละเอียด</div>
                </div>
            </div>
            <div class="well  margin-top">                
                <div class=" ">
                    <div class="row" style="margin-top:5px;">
                        <div class="col-xs-6"><?=Yii::t('common','Quantity')?> </div>
                        <div class="col-xs-6 text-right text-aqua">
                            <input type="number" value="" name="quantity"  step=any onclick="$(this).select()" style="font-size:16px;" pattern="^(?:\(\d{3}\)|\d{3})[- . ]?\d{3}[- . ]?\d{4}$"   class="form-control  item-qty text-right update-field "  autocomplete="off"/>
                        </div>
                    </div>
                    <div class="row" style="margin-top:5px;">
                        <div class="col-xs-6"><?=Yii::t('common','Price')?> </div>
                        <div class="col-xs-6 text-right text-aqua">
                            <input type="number" value="" name="unit_price" step=any onclick="$(this).select()" style="font-size:16px;" pattern="^(?:\(\d{3}\)|\d{3})[- . ]?\d{3}[- . ]?\d{4}$"   class="form-control  item-price text-right update-field "   autocomplete="off"/>
                        </div>
                    </div>                    
                    <div class="row" style="margin-top:5px;">
                        <div class="col-xs-6"><?=Yii::t('common','Line Amount')?> </div>
                        <div class="col-xs-6 text-right text-aqua">
                            <input type="text" value="" style="font-size:16px;"  class="form-control item-line-amount text-right" readonly="readonly"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" >
                <div class="col-xs-12">
                    <a href="javascript:void(0);" class="delete-btn" style="position:fixed;bottom:10px;"><i class="far fa-times-circle fa-2x text-danger" title="<?=Yii::t('common','Delete')?>"></i> <?=Yii::t('common','Delete')?></a>
                </div>
            </div>            
        </div>
    </div>
</div>
