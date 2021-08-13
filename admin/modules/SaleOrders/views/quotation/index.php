<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
//use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\db\Expression;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
//var_dump(Yii::$app->session->get('Rules')['sale_id']);
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\SalehearderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//var_dump(Yii::$app->session->get('workyears'));
 
$this->title = Yii::t('common', 'Sale Quotation');
$this->params['breadcrumbs'][] = $this->title;

//นับจำนวนตัวอักษร ของข้อความ ภาษาไทย แบบ UTF-8
function utf8_strlen($string) {
    $c = strlen($string); $l = 0;
    for ($i = 0; $i < $c; ++$i)
    if ((ord($string[$i]) & 0xC0) != 0x80) ++$l;
    return $l;
}

$this->registerCssFile('css/sale-order.css?v=3.6.01',['rel' => 'stylesheet','type' => 'text/css']);
?>
 

<div style="position: absolute; right: 20px;">
 <div class="hidden-xs hidden-sm text-right" >
    <?php
        echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [

                    [
                        'label' => Yii::t('common','Date'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return date('d/m/Y',strtotime($model->order_date));
                        },
                    ],
                    'no',
                    'customer.name',
                    'customer.address',
                    'customer.locations.province',
                    'transport',
                    'salespeople.code',                    
                    [
                        'attribute' => 'sales_people',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->salespeople['name']. ' '.$model->salespeople['surname'] ;
                        }
                    ],            
                    'balance'    
                ],
                'columnSelectorOptions'=>[
                    'label' => 'Columns',
                    'class' => 'btn btn-success-ew'
                ],

                'fontAwesome'       => true,
                'dropdownOptions'   => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_PDF => false,
                ],
                'styleOptions' => [
                    ExportMenu::FORMAT_PDF => [
                        'font' => [
                             'family' => ['THSarabunNew','garuda'],
                                'bold' => true,
                                'color' => [
                                     'argb' => 'FFFFFFFF',
                             ],
                        ],
                    ],
                ],
            ]);
        ?>
    </div>
    
</div>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-header-index" ng-init="Title='<?=$this->title;?>'">

<div class="row">
        <div class="col-sm-12">

             
            
            <div class="col-sm-offset-8">
                
                    
                <?php

                    function activeMonth($mm){

                        if($mm == date('m')){
                            return 'bg-info';
                        }

                        return 'bg-default';

                    }

                    $Y = (Yii::$app->session->get('workyears'))? Yii::$app->session->get('workyears') : date('Y');


                ?>
                
                
            </div>
            
           
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
 </div>




 <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<div class="row-">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table  table-bordered table-hover'],
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
        'options' => ['class' => 'table-responsive-'],        
        'columns' => [
            [
                'class'             => 'yii\grid\SerialColumn',
                'options'           => ['style' => 'width:50px;'],
                'headerOptions'     => ['class' => 'text-center hidden-xs'],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'contentOptions'    => ['class' => 'text-center hidden-xs  show-doc']
            ],  
            [
                'attribute' => 'order_date',
                'label' => Yii::t('common','Order Date'),
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs ','style' => 'min-width:50px; max-width:80;'],
                'contentOptions' => ['class' => 'hidden-xs font-roboto','style' => 'min-width:50px;max-width:80;'],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'value' => function($model){
                    return ($model->order_date)? $model->order_date : ' ';
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'order_date',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                        ],                                
                    ],
                    
                ]),
            ],
                     
            


            [
                'attribute'         => 'no',
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'text-info doc-no','style' => 'position:relative;'],
                'value'             => function($model){

                    // ตัดตัวอักษร ถ้ามากกว่า 35 ตัว
                    $count_char = utf8_strlen($model->customer['name']);
                    if($count_char >=32 )
                    {
                        $cust_name = iconv_substr($model->customer['name'],0,32,'UTF-8').'...';
                    }else {
                        $cust_name = $model->customer['name'];
                    }


                    if($model->vat_type==1)
                    {
                        $vat_color =  'text-success';
                    }else {
                        $vat_color =  'text-primary';
                    }
                    if(date('Ymd') == date('Ymd', strtotime($model->create_date )))
                    {
                        $Showdate = date('H:i',strtotime($model->create_date));
                    }else {
                        $Showdate = date('d/m/Y',strtotime($model->create_date));
                    }

                    $cus = '<div class="show-doc"  title="'.$model->remark.'">';
                    $cus.= '<div class="text-customer-info pointer">'.Yii::t('common',$cust_name).'</div>';

                    $cus.= '<small class="'.$vat_color.' text-order-number font-roboto pointer">'.$model->no.'</small>';

                    $cus.= '<div class="hidden-sm hidden-md hidden-lg text-right" style="position:absolute; right:15px; top:10px; color:#ccc;">
                              <div class="text-aqua text-balance">
                                <span  style="background-color:#fff; padding-left:5px;padding-right:5px;">'.number_format($model->balance,2).'</span>
                              </div>
                              <small class="hidden-sm hidden-md hidden-lg " style="padding-left:5px;padding-right:5px;"><i class="fas fa-clock"></i> '.$Showdate.'</small>
                            </div>'."\r";                    

                    
                    $cus.= '</div>';
                    $cus.= '<div class="hidden-sm hidden-md hidden-lg">
                                <a class="actions-menu" href="javascript:void(0);" data-rippleria></a>
                            </div>';

                    return $cus;
                },
            ],
            [
                'attribute'         => 'sale_address',
                'label'             => Yii::t('common','Address'),
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'hidden-xs show-doc'],
                'headerOptions'     => ['class' => 'hidden-xs'],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'value'             => function($model){
                    if($model->customer_id!=''){
                        $html = '<div id="sale-name">'.$model->customer->fullAddress['province'].' </div>';
                        $html.= '<small style="color:#ccc;">'.$model->customer->fullAddress['zipcode'].'</small>';
                    }else{
                        $html = '';
                    }
                    return $html;
                }
            ],            
            [
                'attribute'         => 'balance',
                'label'             => Yii::t('common','Balance'),
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'text-right hidden-xs '],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'headerOptions'     => ['class' => 'hidden-xs'],
                'value'             => function($model){

                    $color = NULL;

                    $SumLine = $model->balance_befor_vat;
                    if($model->balance != $SumLine)
                    {
                        $color = 'text-danger';
                    }
                    $html = '<div><div class="'.$color.'">'.number_format($model->balance,2).'</div></div>';

                    return $html;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'hidden-xs text-right','style'=>'min-width:320px;'],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'headerOptions'     => ['class' => 'hidden-xs'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{view} {print}  {update}  {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Print'),$url,['class'=>'btn btn-info','target'=>'_blank']);
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> '.Yii::t('common','View'),$url,['class'=>'btn btn-default']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common','Delete'),$url,[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> '.Yii::t('common','Update'),$url,['class'=>'btn btn-success']);
                    }

                  ]
              ],
        ],

    ]); ?>

    </div>


</div>






<?php echo  $this->render('../modal/_tracking'); ?>
<?php
    $options = ['depends' => [\yii\web\JqueryAsset::className()]];

    $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js',$options);
    $this->registerJsFile('@web/js/jquery.rippleria.min.js',$options);

?>
<?php
$Yii = 'Yii';
$js =<<<JS

    // $(document).ready(function(){

    //     $('rippleria').rippleria();

    // });
    // Date Fillter
    $(document).ready(function(){  
        var element = $('input[name=\"SalehearderSearch[order_date]\"]');
        var template = '<div class=\"input-group date\" data-provide=\"datepicker\">'+
                            '<input type=\"text\" class=\"form-control \" name=\"SalehearderSearch[order_date]\">'+
                            '<div class=\"input-group-addon\">'+
                                '<span class=\"glyphicon glyphicon-th\"></span>'+
                            '</div>'+
                        '</div>';
        element.parent('td').html(template);

        $('body').find('a.ew-save-common').hide();
    })
    // /.Date Fillter


  

    $('.show-doc').click(function (e) {
        var id = $(this).closest('tr').data('key');
        location.href = 'index.php?r=SaleOrders/quotation/view&id='+id;
        
    });


    // Auto Refresh
    //autoRefresh(60);



 

    $('body').on('click','#ew-month-menu',function(){
        $('div.ew-month-box').slideToggle();
         
    })

    $('body').on('click','.ew-month-box li',function(){
        var month =$(this).attr('data');
        $(this).children('i').attr('class','fas fa-sync fa-spin text-info');
        setTimeout(function(e){
            window.location.href = 'index.php?r=SaleOrders/quotation/index&month='+month;
        }, 300);
    });





    // Actons Menu
    $('body').on('click','.actions-menu',function(e){    

        var key = $(this).closest('tr').attr('data-key');

        $(this).children('#actions'+key+'').remove();
        var template = '<div class="actions" id="actions'+key+'" data-key="'+ key +'">'+
                            '<a href="javascript:void(0);" class="more"   ><i class="fas fa-ellipsis-h"></i>  <p> {$Yii::t("common","More")}</p>    </a>'+
                            '<a href="javascript:void(0);" class="delete" ><i class="far fa-trash-alt"></i><p>   {$Yii::t("common","Delete")}</p>      </a>'+
                            '<a href="javascript:void(0);" class="cancel" ><i class="fas fa-power-off"></i><p> {$Yii::t("common","Close")}</p>       </a>'+
                        '</div>';
        $(this).closest('td').prepend(template);
        
        $('#actions'+key+'').toggle("slide", { direction: "right" }, 500);
       
        $('#actions'+key+' a').rippleria();
    })

    $('body').on('click','.actions .more',function(){
        var key = $(this).parent('div.actions').attr('data-key');

        setTimeout(function(){
            window.location.href = 'index.php?r=SaleOrders/quotation/view&id='+key;
        }, 500);
         
    });

    $('body').on('click','.actions .cancel',function(){
        var key = $(this).parent('div.actions').attr('data-key');
        setTimeout(function(){
            $('#actions'+key+'').toggle("slide", { direction: "right" }, 500);
        }, 450); 
    });



    $('body').on('click','.actions .delete',function(){

        var key = $(this).parent('div.actions').attr('data-key');
        var el = $(this).closest('tr');

        setTimeout(function(){
 
            if (confirm('{$Yii::t("common","Do you want to confirm ?")}')) {    
                
                $.ajax({
                    url:"index.php?r=SaleOrders/quotation/delete&id=" + key,
                    type: 'POST',
                    data:{id:key},
                    success:function(respond){
                        var obj = jQuery.parseJSON(respond);
                        if(obj.status == 200){
                            // When delete
                            el.css('background','#a8d3ff');
                            setTimeout(function(){
                                el.remove();
                            },200);

                        }else {

                            $.notify({
                                // options
                                message: '{$Yii::t("common","Not allowed to delete documents with status")} = '+ obj.value.status 
                            },{
                                // settings
                                type: 'error',
                                delay: 5000,
                            });
                        

                            $('#actions'+key+'').toggle("slide", { direction: "right" }, 700);  
                        }

                    }

                });
            
            }else{
                $('#actions'+key+'').toggle("slide", { direction: "right" }, 700);  
            }

        }, 500);
         
    })
    // \. End Actons Menu



    $('body').on('click','.not-receipt-detail',function(){
        $('.ew-tracking-modal').modal('show');
        $('.ew-tracking-modal .modal-title').html('<i class="fa fa-arrows"></i> {$Yii::t("common","Not Receipt")}');

        $.ajax({
                url:"index.php?r=SaleOrders/report/sales-dashboard",
                type: 'POST',
                data: {status:2,model:'diff'},                
                success:function(respond){
                    var obj = jQuery.parseJSON(respond);
                    if(obj.status == 200){
                        
                        $('.ew-tracking-body').show();
                        $('.ew-tracking-modal .ew-render-tracking-info').html('ddd');

                    }else {

                        $.notify({
                            // options
                            message: '{$Yii::t("common","Something went wrong.")}'
                        },{
                            // settings
                            type: 'warning',
                            z_index: 3000,
                            delay: 1500,
                        });
                    
                        
                    }

                }

            });
        
    })
   
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>



