<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
//use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\db\Expression;
use kartik\daterange\DateRangePicker;

use admin\modules\SaleOrders\models\FunctionSaleOrder;

 
$this->title = Yii::t('common', 'Sale Order');
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
 

<div style="position: absolute; right: 20px; top: 75px;">
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

            <?php if(Yii::$app->session->get('Rules')['rules_id']!=4): ?>
            <div class=" ">    
                <div class="row">
                    <div class="col-md-4 col-sm-12">      
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="far fa-chart-bar"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text"><?=Yii::t('common',strtoupper('Sales Balance'))?></span>
                            <span class="info-box-number ew-sales-balance"><i class="fas fa-sync-alt fa-spin"></i>
                            <div class="loading"></div>
                            </span>

                            <div class="progress">
                                <!-- <div class="progress-bar" style="width: 70%"></div> -->
                            </div>
                                <span class="progress-description text-right">
                                    <a href="index.php?r=SaleOrders/report/report-daily" style="color: #fff;"  ><i class="fa fa-search-plus" aria-hidden="true"></i> Detail</a>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>    
                    
                    <div class="col-md-4 col-sm-12">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="far fa-chart-bar"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text"><?=Yii::t('common',strtoupper('Sales Invoice'))?></span>
                            <span class="info-box-number ew-sales-invoice"><i class="fas fa-sync-alt fa-spin"></i>
                            <div class="loading"></div>
                            </span>

                            <div class="progress">
                                <!-- <div class="progress-bar" style="width: 70%"></div> -->
                            </div>
                                <span class="progress-description text-right">
                                    <a href="index.php?r=SaleOrders/report/report-daily" style="color: #fff;"  ><i class="fa fa-search-plus" aria-hidden="true"></i> Detail</a>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>

                    

                    <div class="col-md-4 col-sm-12">
                        <div class="info-box bg-orange">
                            <span class="info-box-icon"><i class="far fa-chart-bar"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text"><?=Yii::t('common',strtoupper('Not Invoice'))?></span>
                            <span class="info-box-number ew-sales-notinvoice"><i class="fas fa-sync-alt fa-spin"></i>
                            <div class="loading"></div>
                            </span>

                            <div class="progress">
                                <!-- <div class="progress-bar" style="width: 70%"></div> -->
                            </div>
                                <span class="progress-description text-right">
                                    <a href="javascript:void(0);" style="color: #fff;" class="not-receipt-detail" ><i class="fa fa-search-plus" aria-hidden="true"></i> Detail</a>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                </div>
            </div>    
            <?php else :  ?>
                <?=$this->render('__progress')?>
            <?php endif; ?>
            
            <div class="col-md-offset-8">                
                    
                <?php
                    function activeMonth($mm){
                        if($mm == date('m')){
                            return 'bg-info';
                        }
                        return 'bg-default';
                    }
                    $Y = (Yii::$app->session->get('workyears'))? Yii::$app->session->get('workyears') : date('Y');
                ?>
                
                <div class="col-xs-12 text-right" style="position: relative; margin-bottom: 5px; padding-right: 0px;">
                    <?= Html::a('<i class="fas fa-filter"></i>',
                            'javascript:void(0)',
                            [
                                'class' => 'btn btn-default btn-flat dropdown-toggle',
                                'data-toggle'    => 'dropdown',
                                'data-rippleria' => true
                            ]) ?>      
                    <?= \yii\bootstrap\Dropdown::widget([
                            'id' => 'ew-drop-status',
                            'items' => [
                                [
                                    'label' => Yii::t('common','status-open'), 
                                    'url'   => ['/SaleOrders/saleorder/index','SaleListSearch[status]' => 'Open'],                                        
                                ],
                                [
                                    'label' => Yii::t('common','status-release'), 
                                    'url' => ['/SaleOrders/saleorder/index','SaleListSearch[status]' => 'Release']
                                ],
                                [
                                    'label' => Yii::t('common','status-checking'), 
                                    'url' => ['/SaleOrders/saleorder/index','SaleListSearch[status]' => 'Checking']
                                ],
                                [
                                    'label' => Yii::t('common','status-shipped'), 
                                    'url' => ['/SaleOrders/saleorder/index','SaleListSearch[status]' => 'Shiped']
                                ],
                                [
                                    'label' => Yii::t('common','status-invoiced'), 
                                    'url' => ['/SaleOrders/saleorder/index','SaleListSearch[status]' => 'Invoiced']
                                ],
                                [
                                    'label' => Yii::t('common','Cancel'), 
                                    'url' => ['/SaleOrders/saleorder/index','SaleListSearch[status]' => 'Cancel']
                                ],
                                [
                                    'label' => Yii::t('common','status-not-invoice'), 
                                    'url' => ['/SaleOrders/saleorder/not-invoice']
                                ],
                            ],
                        ]);
                    ?>
                        
                    <?= Html::a(($Y=='2018')? '<i class="far fa-check-square"></i> 2018' : '<i class="far fa-square"></i> 2018',
                        [
                            '/SaleOrders/saleorder/index','Y' => '2018',
                            'SaleListSearch[status]' => isset($_GET['SalehearderSearch']['status'])? $_GET['SaleListSearch']['status'] : ' ',
                        ],
                        [
                            'class' => ($Y=='2018')? 'btn btn-primary btn-flat' : 'btn btn-default btn-flat',
                            'onClick' => '$(this).html(\'<i class="fas fa-sync fa-spin"></i> 2018\')',
                            'data-rippleria' => true
                        ]) ?>

                    <?= Html::a(($Y=='2017')? '<i class="far fa-check-square"></i> 2017' : '<i class="far fa-square"></i> 2017',
                        [
                            '/SaleOrders/saleorder/index','Y' => '2017',
                            'SaleListSearch[status]' => isset($_GET['SaleListSearch']['status'])? $_GET['SaleListSearch']['status'] : ' ',
                        ],
                        [
                            'class' => ($Y=='2017')? 'btn btn-primary btn-flat' : 'btn btn-default btn-flat',
                            'onClick' => '$(this).html(\'<i class="fas fa-sync fa-spin"></i> 2017\')',
                            'data-rippleria' => true
                        ]) ?>
                    
                    <?= Html::button('<i class="fa fa-calendar" ></i>',
                        
                        [
                            'class' => 'btn btn-default btn-flat',
                            'id'    => 'ew-month-menu',
                            'data-rippleria' => true
                        ]) ?>  

                    <div class="text-left ew-month-box" >
                        <ul class="month-list">
                            <h4><i class="fa fa-filter" aria-hidden="true"></i> Month </h4>
                                <li data="1"  class="<?=activeMonth(1)?>"><i class="fas fa-snowflake text-white" aria-hidden="true"></i> มกราคม</li>
                                <li data="2"  class="<?=activeMonth(2)?>"><i class="fas fa-snowflake text-white" aria-hidden="true"></i> กุมภาพันธ์</li>
                                <li data="3"  class="<?=activeMonth(3)?>"><i class="fas fa-snowflake text-white" aria-hidden="true"></i> มีนาคม</li>
                                <li data="4"  class="<?=activeMonth(4)?>"><i class="fas fa-sun text-warning" aria-hidden="true"></i> เมษายน</li>
                                <li data="5"  class="<?=activeMonth(5)?>"><i class="fas fa-sun text-warning" aria-hidden="true"></i> พฤษภาคม</li>
                                <li data="6"  class="<?=activeMonth(6)?>"><i class="fas fa-sun text-warning" aria-hidden="true"></i> มิถุนายน</li>
                                <li data="7"  class="<?=activeMonth(7)?>"><i class="fas fa-tint text-info" aria-hidden="true"></i> กรกฎาคม</li>
                                <li data="8"  class="<?=activeMonth(8)?>"><i class="fas fa-tint text-info" aria-hidden="true"></i> สิงหาคม</li>
                                <li data="9"  class="<?=activeMonth(9)?>"><i class="fas fa-tint text-info" aria-hidden="true"></i> กันยายน</li>
                                <li data="10" class="<?=activeMonth(10)?>"><i class="fab fa-pagelines text-success" aria-hidden="true"></i> ตุลาคม</li>
                                <li data="11" class="<?=activeMonth(11)?>"><i class="fab fa-pagelines text-success" aria-hidden="true"></i> พฤศจิกายน</li>
                                <li data="12" class="<?=activeMonth(12)?>"><i class="fab fa-pagelines text-success" aria-hidden="true"></i> ธันวาคม</li>
                        </ul>                     
                    </div>                        

                </div>
            </div>
            
           
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
 </div>





<div class="row-">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table   table-hover'],
        'rowOptions'=>function($model){

                if($model->status == 'Release'){
                  return ['class' => 'danger  viewOrder'];
                }

                if($model->getCompletePayment()->status==1){
                    return ['class' => 'success  viewOrder'];
                }else if($model->getCompletePayment()->status==0){
                    return ['class' => 'warning  viewOrder'];
                }

                return ['class' => ' viewOrder'];
        },
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
                'headerOptions' => ['class' => 'hidden-xs '],
                'contentOptions' => ['class' => 'hidden-xs '],
                'filterOptions'     => ['class' => 'hidden-xs','style' => 'width:150px;'],
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


                    if($model->vat_percent > 0)
                    {
                        $vat_color =  'text-success';
                        $icon       = '<i class="far fa-file-archive text-orange"></i>';
                    }else {
                        $vat_color =  'text-primary';
                        $icon       = ' ';
                    }
                    if(date('Ymd') == date('Ymd', strtotime($model->create_date )))
                    {
                        $Showdate = date('H:i',strtotime($model->create_date));
                    }else {
                        $Showdate = date('d/m/Y',strtotime($model->create_date));
                    }

                    $cus = '<div class="show-doc">';
                    $cus.= '<div class="text-customer-info">'.Yii::t('common',$cust_name).'</div>';

                    $cus.= '<div class="'.$vat_color.' text-order-number">'.$model->no.' '.$icon.'</div>';

                    $cus.= '<div class="hidden-sm hidden-md hidden-lg text-right" style="position:absolute; right:15px; top:10px; color:#ccc;">
                              <div class="text-aqua text-balance">
                                <span  style="background-color:#fff; padding-left:5px;padding-right:5px;">'.number_format($model->balance,2).'</span>
                              </div>
                              <small class="hidden-sm hidden-md hidden-lg " style="padding-left:5px;padding-right:5px;"><i class="fas fa-clock"></i> '.$Showdate.'</small>
                            </div>'."\r";


                    $Fnc = new FunctionSaleOrder();

                    $JobStatus = $Fnc->OrderStatus($model);

                    

                    if($model->status=='Shiped')
                    {
                        if($model->log->status == 200 ){
                            $shipdate = $model->log->value->event_date;
                        }else {
                            $shipdate = $model->ship_date;
                        }
                        $cus.= '<span class="label label-primary hidden-sm hidden-md hidden-lg text-ship-status">
                                    <i class="fa fa-truck"></i> '.date('d/m/Y',strtotime($shipdate)).'
                                </span>'."\r";
                    }else {
                        $cus.= '<div class="hidden-sm hidden-md hidden-lg text-ship-status">'.Yii::t('common',$JobStatus).'</div>'."\r";
                    }

                    
                    $cus.= '</div>';
                    $cus.= '<div class="hidden-sm hidden-md hidden-lg">
                                <a class="actions-menu" href="javascript:void(0);" data-rippleria></a>
                            </div>';

                    return $cus;
                },
            ],
            [
                'attribute'         => 'sale_id',
                'label'             => Yii::t('common','Sale Person'),
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'hidden-xs show-doc'],
                'headerOptions'     => ['class' => 'hidden-xs'],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'value'             => function($model){
                    $html = '<div id="sale-name">'.$model->salespeople['name'].' '. $model->salespeople['surname'].'</div>';
                    $html.= '<small style="color:#ccc;">['.$model->salespeople['code'].']</small>';
                    return $html;
                }
            ],            
            [
                'attribute'         => 'balance',
                'label'             => Yii::t('common','Balance'),
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'text-right hidden-xs '],
                'filterOptions'     => ['class' => 'hidden-xs'],
                'headerOptions'     => ['class' => 'hidden-xs text-right'],
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
                'attribute'         => 'status',
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'hidden-xs status-content','style' => 'position:relative; max-width:160px;'],
                'headerOptions'     => ['class' => 'hidden-xs','style' => 'max-width:160px;'],
                'filterOptions'     => ['class' => 'hidden-xs','style' => 'max-width:160px;'],
                'value'             => function($model){

                    $Fnc        = new FunctionSaleOrder();
                    $JobStatus  = $Fnc->OrderStatus($model);                    
                    $ship       = '';

                    if($model->status=='Shiped')
                    {
                        if($model->log->status == 200 ){
                            $shipdate = $model->log->value->event_date;
                        }else {
                            $shipdate = $model->ship_date;
                        }
                        $ship.='<div  class="pull-left" >
                                  <small>
                                    <i class="fa fa-calendar" aria-hidden="true"></i> '.date('d/m/Y',strtotime($shipdate)).'
                                  </small>
                                </div>';
                    }

                    
                    $html_confirm       = '';
                    if($model->status=='Checking'){
                        $confirm            = ($model->confirm * 1) > 0 ? '<i class="fa fa-check"></i> <span class="hidden-sm">'.Yii::t('common','Comfirmed').'</span>' : '';
                        $html_confirm   = '<small class="pull-left alert-success" style="padding:0px 3px 0px 3px;"/>'.$confirm.'</small>';
                    }

                    $html       = '<div class="row" />
                                    <div  class="text-left col-xs-6" id="ew-tr-modal"   data="'.base64_encode($model->id).'"/>
                                        <div class="pointer">'.$JobStatus.' </div>
                                        '.$ship.'
                                    </div> 
                                    <div  class="text-left col-xs-6"  />                                        
                                        '.$html_confirm.'
                                    </div>
                                    <a class="actions-menu" href="javascript:void(0);" data-rippleria></a>
                                   </div>';                  
                    
                    return $html;
                },
                'filter' => Html::activeDropDownList($searchModel,'status',
                    [
                        'Open'          => Yii::t('common','status-open'),
                        'Release'       => Yii::t('common','status-release'),
                        'Checking'      => Yii::t('common','status-checking'),
                        'Shiped'        => Yii::t('common','status-shipped'),
                        'Reject'        => Yii::t('common','status-reject'),
                        'Invoiced'      => Yii::t('common','status-invoiced'),
                        'Credit-Note'   => Yii::t('common','status-credit-note'),
                        'Pre-Cancel'    => Yii::t('common','status-cancel-req'),
                        'Cancel'        => Yii::t('common','Cancel'),
                    ],
                    [
                        'class'         => 'form-control hidden-xs',
                        'prompt'        => Yii::t('common','Show All'),
                    ]),
            ],


        ],
    ]); ?>

    </div>


</div>





<style>
.daterangepicker{
    box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
}
</style>

<?php echo  $this->render('../modal/_tracking'); ?>
<?php
    $options = ['depends' => [\yii\web\JqueryAsset::className()]];

    $this->registerJsFile('@web/js/jquery.animateNumber.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js',$options);
    $this->registerJsFile('@web/js/jquery.rippleria.min.js',$options);
    $this->registerJsFile('@web/js/saleorders/saleorder_index.js?v=3.06.21'); 
?>
<?php
$Yii = 'Yii';

$onload =<<<JS

    $(document).ready(function(){   
        $.ajax({ 
            url:"index.php?r=SaleOrders/ajax/sale-balance-header",
            type: 'GET', 
            data:"",
            async:true,
            dataType:'JSON',
            success:function(obj){
                var x = Math.floor((Math.random() * 100) + 3);
                var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
                //var obj = jQuery.parseJSON(getData); 
                $('.ew-sales-balance').prop('number', x).animateNumber({ number: obj.saleorder,numberStep: comma_separator_number_step },2000);
                $('.ew-sales-invoice').prop('number', x).animateNumber({ number: obj.invoice,numberStep: comma_separator_number_step },2000);
                $('.ew-sales-notinvoice').prop('number', x).animateNumber({ number: obj.notinvoice,numberStep: comma_separator_number_step },2000);              
            }
        });         
    });

JS;

if(Yii::$app->session->get('Rules')['rules_id']!=4){ $this->registerJs($onload); }

$js =<<<JS

    $('.show-doc').click(function (e) {
        var id = $(this).closest('tr').data('key');
        location.href = 'index.php?r=SaleOrders/saleorder/view&id='+id;        
    });

    $('body').on('click','#ew-month-menu',function(){
        $('div.ew-month-box').slideToggle();        
    })

    $('body').on('click','.ew-month-box li',function(){
        var month =$(this).attr('data');
        $(this).children('i').attr('class','fas fa-sync fa-spin text-info');
        setTimeout(function(e){
            window.location.href = 'index.php?r=SaleOrders/saleorder/index&month='+month;
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
            window.location.href = 'index.php?r=SaleOrders/saleorder/view&id='+key;
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
                    url:"index.php?r=SaleOrders/saleorder/delete&id=" + key,
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



