


<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
 


$NoSeries         = '';

$this->title = Yii::t('common','Billing Note');        

?>
<style type="text/css">
    .body-content{
        min-height: 900px;
    }
    .body-print-mini{
        position: relative;

        margin:30px 0px 0px 0px;

        background-color: #fff;

        box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.5);

        padding:5px;

        width: 100px; 
        height: 130px;

        font-size: 11px;
        font-family: 'saraban';

        border-top-left-radius: 5px;
        border-bottom-right-radius: 5px;

    }

    .body-print-mini:hover{
         
        cursor: pointer;
    }
    .body-print-mini:active{
         
        cursor: pointer;
        box-shadow: 7px 7px 7px rgba(0, 0, 0, 0.3);
    }

    .body-print-mini table{
        font-size: 8px;
    }

    .content-mini{
       /* color: #efefef;*/
        color: #fff;
        border:none;

    }

    .hr-mini{
        border-bottom: 1px solid #000;
        margin-bottom: 4px;
        margin-top: 4px;
    }
    .footer-mini{
        height: 30px;
        border:1px solid #000;
        margin-top: 3px;
        text-align: center;
        padding-top: 5px;
        font-size: 14px;        
        /*color: #fff;*/
    }

    .iconApp-mini{
        position: absolute;
        font-size: 17px;

    }
    .cust-file{
        position: absolute;
        left: 45%;
        top:35%;
        color: blue;
        font-size: 12px;
    }
    .date-file{
        position: absolute;
        left: 30px;
        top:47%;
         
        font-size: 12px;
    }
    
</style>
<?php $this->registerCssFile('css/billing-note.css');?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 

<div class="tabbable" style="padding-top: 15px;" ng-init="Title='<?=$this->title?>'">
    <div class="table-responsive" style="padding-top: 15px;">
        <?php Pjax::begin(); ?> 
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,                        
                'tableOptions'=> ['class' => 'table table-hover table-striped', 'style' => 'font-family: saraban, roboto;'],
                'columns' => [
                    [
                        'headerOptions' => ['class' => 'bg-gray'],  
                        'class' => 'yii\grid\SerialColumn'
                    ],

                    
                    [
                        //'attribute' => 'create_date',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-gray'], 
                        'contentOptions' => ['class' => 'font-roboto'],              
                        'value' => function($model){
                            $html = '<div>';
                            $html.=     '<i class="fa fa-calendar" aria-hidden="true"></i> '.date('Y-m-d',strtotime($model->getDataFromNo()->create_date));
                            $html.= '</div>';
                            return $html;
                        }
                    ],

                    [
                        'attribute' => 'no_',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-gray'], 
                        'contentOptions' => ['class' => 'font-roboto'],       
                        'value' => function($model){
                            $html = '<div>';
                            $html.=     '<a href="index.php?r=accounting/billing/update&id='.base64_encode($model->no_).'">
                                            <i class="fa fa-file-word-o text-primary" aria-hidden="true"></i> '.$model->no_.' ('.$model->getCount().')</a>';
                            $html.= '</div>';
                            return $html;
                        }
                    ],

                    [
                        'attribute' => 'customer',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-gray'], 
                        'label' => Yii::t('common','Customer'),                        
                        'value' => function($model){                                    
                            $html = Html::a('<i class="fa fa-address-card-o" aria-hidden="true"></i> ['.$model->customer->code.'] '.$model->customer->name ,['/customers/customer/view', 'id' => $model->cust_no_],['target' => '_blank', 'data-pjax' => "0"]);
                            return $html;
                        }
                    ],

                    [
                        'format' => 'raw',
                        'label' => Yii::t('common','Amount'),  
                        'headerOptions' => ['class' => 'text-right bg-gray'],  
                        'contentOptions' => ['class' => 'text-right font-roboto'],                         
                        'value' => function($model){                                    
                            return number_format($model->getBalance()['amount'],2);
                        }
                    ],
                    
                    [
                
                        'class' => 'yii\grid\ActionColumn',
                        'buttonOptions'     => ['class'=>'btn btn-default'],
                        'headerOptions'     => ['class' => 'bg-gray'],
                        'contentOptions'    => ['class' => 'text-right','style'=>'min-width:100px;'],
                        'template'          => '<div class="btn-group btn-group text-center" role="group"> {update} {delete} </div>',
                        'options'           => ['style'=>'width:100px;'],
                        'buttons'           => [
                            'view'  => function($url,$model,$key){
                                return Html::a('<i class="fas fa-eye"></i> ',['/accounting/billing/view', 'id' => base64_encode($model->no_)],['class'=>'btn btn-info']);
                            },
                            'delete' => function($url,$model,$key){
                                return Html::a('<i class="far fa-trash-alt"></i> ',['/accounting/billing/delete', 'no' => base64_encode($model->no_)],[
                                    'class' => 'btn btn-warning',
                                    'data' => [
                                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                            'update' => function($url,$model,$key){                           
                                return Html::a('<i class="far fa-edit"></i> ',['/accounting/billing/update', 'id' => base64_encode($model->no_)],['class'=>'btn btn-success']);
                            }
                          ]
                      ]
                        
                ],
                //'tableOptions' => ['class'=>''],
                //'summary' => false,
                'pager' => [
                    'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel' => '«',   // Set the label for the "previous" page button
                    'nextPageLabel' => '»',   // Set the label for the "next" page button
                    'firstPageLabel'=>'First',   // Set the label for the "first" page button
                    'lastPageLabel'=>'Last',    // Set the label for the "last" page button
                    'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
                    'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
                    'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
                    'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
                    'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
                    ],
            ]); ?>
        <?php Pjax::end(); ?>
    </div>
 
</div><!-- /tabbable -->

 
 
<?php
$js =<<<JS

    // $(function(){
    //     $(".body-print-mini").draggable({
    //       handle: ".print-header"
    //   });
    // })

    $(document).ready(function(){
        $('.custom-menu-print').hide();
        $('.custom-menu-content').hide();
        toolTip('.bopup-tooltip');
    })

    // Mobile
    var touchtime = 0;
    $('.open-file').on('click', function() {
        if(touchtime == 0) {
            //set first click
            touchtime = new Date().getTime();
        } else {
            //compare first click to this click and see if they occurred within double click threshold
            if(((new Date().getTime())-touchtime) < 300) {
                //double click occurred
                //alert("double clicked");

                var id = $(this).attr('data-file');
                window.location.replace("index.php?r=accounting/billing/update&id="+id);

                touchtime = 0;
            } else {
                //not a double click so set as a new first click
                touchtime = new Date().getTime();
            }
        } 
    });

    $('body').on('click','.payment',function(){
        window.open('index.php?r=Management/report/approved&cust_no_='+btoa($(this).data('id')),'_blank');
    })

JS;

$this->registerJS($js);
?>