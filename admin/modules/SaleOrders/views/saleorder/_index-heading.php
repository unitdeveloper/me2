<?php

use yii\helpers\Html;
//use kartik\export\ExportMenu;
use kartik\widgets\ActiveForm;

$Profile  = \common\models\Profile::findOne(Yii::$app->user->identity->id);

$workYear   = Yii::$app->session->get('workyears') ? Yii::$app->session->get('workyears') : date('Y');
$workMonth  = Yii::$app->session->get('workmonth') ? Yii::$app->session->get('workmonth') : date('m');

function DateThai($strDate,$format){

    $strYear = date("Y",strtotime($strDate))+543;
    $strMonth= date("n",strtotime($strDate));
    $strDay= date("j",strtotime($strDate));
    $strHour= date("H",strtotime($strDate));
    $strMinute= date("i",strtotime($strDate));
    $strSeconds= date("s",strtotime($strDate));
    $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
    $strMonthThai=$strMonthCut[$strMonth];
    //return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
    switch ($format) {
        case 'dd-MM-YY':
            return "$strDay $strMonthThai $strYear";
            break;

        case 'MM-YY':
            return "$strDay $strMonthThai $strYear";
            break;

        case 'M, Y':
            return "$strMonthThai, ".date("Y",strtotime($strDate));
            break;

        default:
            return "$strDay $strMonthThai $strYear";
            break;
    }

}



?>
<div class="">
    <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
</div>




<div class="row">
    <div class="col-sm-12">

        <?php if(Yii::$app->session->get('Rules')['rules_id']!=4): ?>
        <div class="">    
            <div class="row">
                <div class="col-md-4 col-sm-6  hidden-xs">      
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
                
                <div class="col-md-4 col-sm-6  hidden-xs">
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

                

                <div class="col-md-4 col-sm-6  hidden-xs">
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

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="info-box bg-gray">
                        <span class="info-box-icon"><i class="fas fa-wallet"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text">
                            <?=Yii::t('common',strtoupper('Sales Balance'))?>  : 
                            <?= DateThai($workYear.'-'.$workMonth.date('-d'),'M, Y');?></span>
                        <span class="info-box-number sale-this-month"><i class="fas fa-sync-alt fa-spin"></i>
                        <div class="loading"></div>
                        </span>

                        <div class="progress">
                            <!-- <div class="progress-bar" style="width: 70%"></div> -->
                        </div>
                            <span class="progress-description text-right">
                                <?= $Profile->sales != null ? $Profile->sales->name : Yii::t('common','None') ?>                                
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>  
            </div>
        </div>    
        <?php else :  ?>
            <?php //$this->render('__progress')?>
        <?php endif; ?>
  
            
        <?php $form = ActiveForm::begin(['id' => 'order-search','method' => 'GET']); ?>
        <div class="row">
            
            <div class="col-xs-12 col-sm-6 col-md-4 pull-right mb-10">
                <div class="box-tools">
                    <div class="input-group  "  >
                        <?= $form->field($model,'search')->textInput(['class' => 'form-control','style' => 'margin-top:-10px;','placeholder' => Yii::t('common','Search')])->label(false)?>                    
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
             
                
                 
                <?php echo kartik\widgets\DatePicker::widget([
                    'name'      => 'SaleListSearch[fdate]',
                    'value'     => $model->fdate == '' ? '' : date('Y-m-d',strtotime($model->fdate)),
                    'type'      => kartik\widgets\DatePicker::TYPE_RANGE,
                    'name2'     => 'SaleListSearch[tdate]',
                    'value2'    => $model->tdate == '' ? '' : date('Y-m-d',strtotime($model->tdate)),
                    //'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                    'pluginOptions' => [
                            'autoclose' =>true,
                            'format'    => 'yyyy-mm-dd'
                    ],
                    //'layout' => $layout3,
                    'options'   => ['autocomplete' => 'off', 'placeholder' => Yii::t('common','From Date')],
                    'options2'  => ['autocomplete' => 'off', 'placeholder' => Yii::t('common','To Date')],
                    //'pluginEvents' => [ "changeDate"=> "function(e) { SubmitSearchOrder(); }",],
                ]);

                ?>
                
            </div>
            
        </div>
        <?php ActiveForm::end(); ?>
 


        <div class="col-md-offset-6 col-lg-offset-8">                
            <?php
                function activeMonth($mm){
                    $month = Yii::$app->session->get('workmonth') ? Yii::$app->session->get('workmonth') : date('m');
                    if($mm == $month){
                        return 'bg-info';
                    }
                    return 'bg-default';
                }
                $Y = (Yii::$app->session->get('workyears'))? Yii::$app->session->get('workyears') : date('Y');
            ?>
            <div class="col-xs-12 text-right" style="position: relative; margin-bottom: 5px; padding-right: 0px;">
                <a href="#" class="reload-sale-data btn btn-default btn-flat"><i class="fas fa-sync-alt"></i> <?=Yii::t('common','Reload')?></a>
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

                

                <?php /* Html::a(($Y=='2019')? '<i class="far fa-check-square"></i> 2019' : '<i class="far fa-square"></i> 2019',
                    [
                        '/SaleOrders/saleorder/index','Y' => '2019',
                        'SalehearderSearch[status]' => isset($_GET['SalehearderSearch']['status'])? $_GET['SalehearderSearch']['status'] : ' ',
                    ],
                    [
                        'class' => ($Y=='2019')? 'btn btn-primary btn-flat' : 'btn btn-default btn-flat',
                        'onClick' => '$(this).html(\'<i class="fas fa-sync fa-spin"></i> 2019\')',
                        'data-rippleria' => true
                    ]) */?>
                    
                <?php /* Html::a(($Y=='2018')? '<i class="far fa-check-square"></i> 2018' : '<i class="far fa-square"></i> 2018',
                    [
                        '/SaleOrders/saleorder/index','Y' => '2018',
                        'SalehearderSearch[status]' => isset($_GET['SalehearderSearch']['status'])? $_GET['SalehearderSearch']['status'] : ' ',
                    ],
                    [
                        'class' => ($Y=='2018')? 'btn btn-primary btn-flat' : 'btn btn-default btn-flat',
                        'onClick' => '$(this).html(\'<i class="fas fa-sync fa-spin"></i> 2018\')',
                        'data-rippleria' => true
                    ]) */?>

                <?php /* Html::a(($Y=='2017')? '<i class="far fa-check-square"></i> 2017' : '<i class="far fa-square"></i> 2017',
                    [
                        '/SaleOrders/saleorder/index','Y' => '2017',
                        'SalehearderSearch[status]' => isset($_GET['SalehearderSearch']['status'])? $_GET['SalehearderSearch']['status'] : ' ',
                    ],
                    [
                        'class' => ($Y=='2017')? 'btn btn-primary btn-flat' : 'btn btn-default btn-flat',
                        'onClick' => '$(this).html(\'<i class="fas fa-sync fa-spin"></i> 2017\')',
                        'data-rippleria' => true
                    ]) */?>
                
                <?= Html::button('<i class="fa fa-calendar" ></i>',
                    
                    [
                        'class' => 'btn btn-default btn-flat',
                        'id'    => 'ew-month-menu',
                        'data-rippleria' => true
                    ]) ?>  

                <div class="text-left ew-month-box" >
                    <ul class="month-list">
                        <h4><i class="fa fa-filter" aria-hidden="true"></i> <?=Yii::t('common','Month')?></h4>
                        <li data="1"  class="<?=activeMonth(1)?>"><i class="fas fa-snowflake text-white"></i> มกราคม</li>
                        <li data="2"  class="<?=activeMonth(2)?>"><i class="fas fa-snowflake text-white"></i> กุมภาพันธ์</li>
                        <li data="3"  class="<?=activeMonth(3)?>"><i class="fas fa-snowflake text-white"></i> มีนาคม</li>
                        <li data="4"  class="<?=activeMonth(4)?>"><i class="fas fa-sun text-warning"></i> เมษายน</li>
                        <li data="5"  class="<?=activeMonth(5)?>"><i class="fas fa-sun text-warning"></i> พฤษภาคม</li>
                        <li data="6"  class="<?=activeMonth(6)?>"><i class="fas fa-sun text-warning"></i> มิถุนายน</li>
                        <li data="7"  class="<?=activeMonth(7)?>"><i class="fas fa-tint text-info"></i> กรกฎาคม</li>
                        <li data="8"  class="<?=activeMonth(8)?>"><i class="fas fa-tint text-info"></i> สิงหาคม</li>
                        <li data="9"  class="<?=activeMonth(9)?>"><i class="fas fa-tint text-info"></i> กันยายน</li>
                        <li data="10" class="<?=activeMonth(10)?>"><i class="fab fa-pagelines text-success"></i> ตุลาคม</li>
                        <li data="11" class="<?=activeMonth(11)?>"><i class="fab fa-pagelines text-success"></i> พฤศจิกายน</li>
                        <li data="12" class="<?=activeMonth(12)?>"><i class="fab fa-pagelines text-success"></i> ธันวาคม</li>
                    </ul>                     
                </div>                        

                <select class="year-change btn btn-default" style="height:35px">
                    <?php 
                        for ($i=date('Y'); $i >= 2017 ; $i--) { 
                            echo '<option value="'.$i.'" '.($workYear == $i ? 'selected' : '').'>'.$i.'</option>';
                        }
                    ?>
                    
                </select>
            </div>
        </div>
        
        
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>

<?php 
$JS=<<<JS

$('body').on('change','.year-change',function(){
    let years = $(this).val();
    window.location = '?r=/SaleOrders/saleorder/index&Y='+years;
})

JS;
$this->registerJS($JS);
?>