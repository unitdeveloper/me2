<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use admin\modules\apps_rules\models\SysRuleModels;

use common\models\Customer;
use common\models\Company;
use common\models\SalesPeople;
use common\models\SaleHeader;
use common\models\RcInvoiceLine;

use common\models\SaleInvoiceLine;

use admin\modules\accounting\models\FunctionAccounting;
use admin\modules\Management\models\FunctionManagement;
use common\models\Cheque;

use kartik\grid\GridView;
use kartik\export\ExportMenu;

use kartik\widgets\Select2;

$comp = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

$this->registerCssFile('https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css',['rel' => 'stylesheet','type' => 'text/css']);
$this->registerCssFile('https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap.min.css',['rel' => 'stylesheet','type' => 'text/css']);


if (isset($_GET['search-from-sale']) && @$_GET['search-from-sale'] != '') {
    $sales = SalesPeople::findOne(['id' => $_GET['search-from-sale']]);
} else {
    $sales = SalesPeople::findOne(Yii::$app->session->get('Rules')['sale_id']);
}

if(!Yii::$app->session->get('Rules')['sale_id']){
    $sales = (Object)[
        'code' => '',
        'name' => ''
    ];
} 

$Customer 	= Customer::findOne(isset($_GET['customer']) ? $_GET['customer'] : 0);
$startDate  = date('Y-m-').'01';
$endDate    = date('Y-m-d');
$subStr     = 80;
// Set line amount per page.
$PerPage    = 100;
if(isset($_GET['substr']))      $subStr     = $_GET['substr'];

$gridColumns = [];

function DateThai($strDate){
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strMonthCut = ["", 
    Yii::t('common','Jan.'), 
    Yii::t('common','Feb.'), 
    Yii::t('common','Mar.'), 
    Yii::t('common','Apr.'), 
    Yii::t('common','May.'), 
    Yii::t('common','Jun.'), 
    Yii::t('common','Jul.'), 
    Yii::t('common','Aug.'), 
    Yii::t('common','Sep.'), 
    Yii::t('common','Oct.'), 
    Yii::t('common','Nov.'), 
    Yii::t('common','Dec.')];
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";

    //return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
}



if (isset($_GET['fdate'])) {
    if ($_GET['fdate'] != '') {
        $startDate = date('Y-m-d', strtotime($_GET['fdate']));
    }
}


if (isset($_GET['tdate'])) {
    if ($_GET['tdate'] != '') {
        $endDate = date('Y-m-d', strtotime($_GET['tdate']));
    }
}


 
?>
 
<style>
    .select2-search__field{
        font-size:16px;
    }
    .date,
    .doc_no,
    .money{
        font-family: 'Prompt', sans-serif;
        font-size:12px;
    }
    .F3{
        font-family: 'Prompt', sans-serif;
    }
    @media (max-width: 767px) {
        table{
            font-size:12px;
        }
        .customer-name{
            color:#0475cc;
        }
        .money,
        .F3{
            font-family: 'Roboto', sans-serif;
            color:orange;
            font-size:14px;
        }
        .doc-list{
            font-family: 'Roboto', sans-serif;
        }
    }
</style>
<script type="text/javascript">
 app.controller('reportController', ['$scope', function($scope) {
       $scope.fdate = {
        value: new Date(<?=date('Y',strtotime($startDate))?>,<?=date('m',strtotime($startDate))-1?>,<?=date('d',strtotime($startDate))?>),
       };
       $scope.tdate = {         
        value: new Date(<?=date('Y',strtotime($endDate))?>,<?=date('m',strtotime($endDate))-1?>,<?=date('d',strtotime($endDate))?>)
       };
        
       
     }]);
</script>

<?php if(!Yii::$app->request->isAjax) : ?>

<div class="box box-warning">

    <div class="box-body">
        <div class="row btn-print" ng-controller="reportController">
            <div class="col-md-12  col-xs-12">
                <?php $form = ActiveForm::begin(['id' => 'invline-search','method' => 'GET']); ?>
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-sm-6 hidden-xs">                 
                        <label><?=Yii::t('common','Date Filter')?></label>
                        
                        <?php             
                        $FromDate   = Yii::t('common','From Date');
                        $ToDate     = Yii::t('common','To Date');
                        // With Range
                        $layout = <<< HTML
                            <span class="input-group-addon">$FromDate</span>
                            {input1}

                            <span class="input-group-addon">$ToDate</span>
                            {input2}
            
HTML;
                    echo DatePicker::widget([
                            'type'      => DatePicker::TYPE_RANGE,
                            'name'      => 'fdate',
                            //'value'     => $startDate,
                            'name2'     => 'tdate',
                            //'value2'    => $endDate,
                            //'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                            'options'   => ['ng-model' => 'fdate.value | date:\'yyyy-MM-dd\'','autocomplete' => 'off',],
                            'options2'  => ['ng-model' => 'tdate.value | date:\'yyyy-MM-dd\'','autocomplete' => 'off',],
                            //'options'   => ['autocomplete' => 'off',],
                            //'options2'  => ['autocomplete' => 'off',],
                            'layout'    => $layout,
                            'pluginOptions' => [
                                'autoclose' =>true,
                                'format'    => 'yyyy-mm-dd',
                                
                                //'format' => 'dd-mm-yyyy'
                            ],

                    ]);

                    ?>
                    </div>
                    <div class="col-sm-6 col-xs-12 hidden-sm hidden-md hidden-lg">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><?=Yii::t('common','From Date')?></button>
                                    </span>
                                    <input type="date" name="fdatem" ng-model="fdate.value" class="form-control" style="max-width: 145px;">
                                </div>
                                
                            </div>
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><?=Yii::t('common','To Date')?></button>
                                    </span>
                                    <input type="date" name="tdatem" ng-model="tdate.value" class="form-control" style="max-width: 145px;">
                                </div>
                                
                            </div>
                        </div>
                    </div>
        
        
                    <div class="col-sm-2  col-xs-12">
                        <?php
                            // function search
                            if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','search'))){
                        ?>
                        <div class="input-group" >
                            <label><?=Yii::t('common','Sales')?></label>
                            <?=Html::dropDownList('search-from-sale', null,
                                    ArrayHelper::map(
                                        SalesPeople::find()
                                        ->where(['status' => 1])
                                        ->andWhere(['comp_id' => $comp])
                                        ->orderBy(['code'=> SORT_ASC])
                                        ->all(),
                                            'id',function($model){
                                                return '['.$model->code.'] '.$model->name. ' '.$model->surname;
                                            }
                                        ),
                                        [
                                            'class'=>'form-control  col-xs-12',
                                            'prompt' => Yii::t('common','Every one'),
                                            'options' => [                        
                                                @$_GET['search-from-sale'] => ['selected' => 'selected']
                                            ],
                                        ]
                                    )
                            ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="col-sm-2  col-xs-12">
                            <label><?=Yii::t('common','Customers')?></label>
                            <?php
                                $cusList = \common\models\ViewRcInvoice::find()
                                ->select('cust_no_')
                                ->where(['comp_id' => $comp,'sales_people' => Yii::$app->session->get('Rules')['sale_code']])
                                ->groupBy(['cust_no_'])
                                ->all();
                                $customerList = [];
                                foreach ($cusList as $key => $cust) {
                                    $customerList[] = $cust->cust_no_;
                                }
        
                            
                                    // function search
                                    if (in_array(Yii::$app->session->get('Rules')['rules_id'], SysRuleModels::getPolicy('Main Function', 'Customer', 'customer', 'actionIndex', 'search'))) {
                                        //if($rules_id==1 || $rules_id == 4 || $rules_id == 7){ // 4 Sale admin,7 Sale Manager
                                        $dataCustomer = Customer::find()
                                            ->where(['comp_id' => $comp])
                                            ->andWhere(['<>', 'status', '0'])
                                            ->orderBy(['code' => SORT_ASC])
                                            ->all();

                                    } else {
                                        $dataCustomer = Customer::find()
                                            ->where(['comp_id' => $comp])
                                            ->andWhere(['or',
                                                new Expression('FIND_IN_SET(:owner_sales, owner_sales)'),
                                                ['id' => $customerList],
                                            ])
                                            ->addParams([':owner_sales' => Yii::$app->session->get('Rules')['sale_code']])
                                            ->orderBy(['code' => SORT_ASC])
                                            ->all();
                                    }



                                echo Select2::widget([
                                    'name' => 'customer',
                                    'data' => ArrayHelper::map($dataCustomer,
                                        'id',
                                        function($model){ 
                                            $Province = '';
                                            if($model->province!='') $Province = '('.trim($model->provincetb->PROVINCE_NAME).')';
                                            return trim($model->name).' '.$Province; 
                                        }
                                    ),
                                    'options' => [
                                        'placeholder' => Yii::t('common','Customer'),
                                        'multiple' => false,
                                        'class'=>'form-control  col-xs-12',				
                                    ],
                                    'language' => 'th',
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    'value' => @$_GET['customer']
                                ]);

                            ?>
                    </div>
                    <div class="col-sm-2  col-xs-12">
                        <div class="input-group pull-right">
                            <label  style="color: #fff"> <?=Yii::t('common','Search')?> </label> <br>
                            <button type="submit" class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i> <?=Yii::t('common','Search')?></button>
                        </div>
                    </div>
                </div><!-- /.row -->
                <?php ActiveForm::end(); ?>
            </div>
            
        </div>
    </div>
</div>
<?php endif; ?>
 
    <div class="panel panel-info">
        <div class="panel-heading">
            <h5 class="text-primary"><i class="fas fa-balance-scale"></i> <?=Yii::t('common','Sale report')?>  </h5>
            
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="profile-header">
                    <div class="col-md-9 col-sm-8 col-xs-12">
                        
                        <div class="row">
                            <div class="col-xs-12"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-5 col-sm-6 text-warning"><?=Yii::t('common','From')?> <u><?=DateThai(isset($_GET['fdate']) ? $_GET['fdate'] : date('Y-m-').'01') ?></u></div>
                                    <div class="col-xs-7 col-sm-6 text-warning">: <?=Yii::t('common','To')?> <u><?=DateThai(isset($_GET['tdate'])? $_GET['tdate'] : date('Y-m-d'))?></u></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xs-5 col-sm-6"><?=Yii::t('common','Customer code')?>
                                <?php if(isset($_GET['customer'])) {
                                    if($_GET['customer']!=''){
                                        echo $Customer->code;
                                        }
                                    }
                                ?>
                            </div>
                            <div class="col-xs-7 col-sm-6">:
                                <?php if(isset($_GET['customer'])) {
                                    if($_GET['customer']!=''){
                                        echo $Customer->name;
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        
                        <div class="row">                    
                            <div class="col-xs-5 col-sm-6"><?=Yii::t('common','Salesman')?> <?=$sales->code ?></div>
                            <div class="col-xs-7 col-sm-6">: <?=$sales->name;?> </div>                     
                        </div>
                        <div class="row">
                            <div class="col-xs-5 col-sm-6"><?=Yii::t('common','Total')?>  </div>
                            <div class="col-xs-7 col-sm-6">: <span class="text-primary" style="font-size:17px;" ng-bind="total | number:2"></span></div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>       
    </div>
 
 
    <div class="row" >
        <div class="col-sm-3 ">                    
        <i class="far fa-clock"></i> <?=Yii::t('common','Date')?> :  <?=DateThai(date('Y-m-d'))?>              
        </div>
    </div>
    <div class="row" >
        <div class="col-xs-12 ">
        <div id="export_wrapper" class="table-responsive">
    
        <?php
            $sumF0 		= 0;
            $sumF1 		= 0;
            $sumF2 		= 0;
            $sumF3 		= 0;
            $sumF4 		= 0;
            $sumF5 		= 0;
            $sumF6 		= 0;
            $sumF7 		= 0;
            $AllData 	= $dataProvider->getTotalCount();
            $data 		= $dataProvider->models;
            //$data 		= array_chunk($data, $PerPage);
            $ix 		= 0;
            $c 			= 0;
            //for ($i=0; $i < count($data); $i++) {
                $td = '<table class="table " id="export_table" >';
                $td.= '<thead>';
                $td.= '<tr>';
                    $td.= '<th class="hidden-xs">'.Yii::t('common','No.').'</th>';   
                    $td.= '<th class="hidden-xs">'.Yii::t('common','Date').'</th>';   
                    $td.= '<th class="hidden-xs">'.Yii::t('common','Order No').'</th>';  
                    $td.= '<th class="hidden-xs">'.Yii::t('common','Invoice No').'</th>';  
                    $td.= '<th class="hidden-xs">'.Yii::t('common','Customer code').'</th>';  
                    $td.= '<th class="hidden-xs">'.Yii::t('common','Customer').'</th>';  
                    $td.= '<th class="hidden-xs">'.Yii::t('common','Province').'</th>';  
                    $td.= '<th class="hidden">'.Yii::t('common','Detail').'</th>';  
                    $td.= '<th class="text-right hidden-xs">'.Yii::t('common','Amount').'</th>';               
                $td.= '</tr>';
                $td.= '</thead>';
                $td.= '<tbody>';
                foreach ($data as $model) {
                    $ix++;
                    $Dotted 		= '';
                    if(strlen($model->customer->name) > $subStr) $Dotted = '...';				
                    if($model->status=='Posted'){
                        $invLine 	= RcInvoiceLine::find()->where(['source_id' => $model->id]);
                        //$Total 		= FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');
                    }else{
                        $invLine 	= SaleInvoiceLine::find()->where(['source_id' => $model->id]);
                        //$Total 		= FunctionAccounting::getTotalBalance($model,'SaleInvoiceLine');
                    }
                    $Total			= $model->total;					
                    $sumLine 		= $invLine->sum('quantity * unit_price');       	
                    $vatTotal 		= $sumLine * $model->vat_percent/100;
                    // ###################################
                    // ###################################
                    // ###################################
                    // ###################################
                    $RcCheque 		= FunctionManagement::validateCheque($model,'Cheque');
                    $RcCash 		= FunctionManagement::validateCheque($model,'Cash');
                    $RcOver 		= ($RcCheque + $RcCash) - $Total;
                    if(abs($RcCheque + $RcCash) > abs($Total)){
                        //$RcOver 	= 0;
                        $OverColor  = 'text-danger';
                    }else {
                        $RcOver 	= 0;
                        $OverColor  = '';
                        }
                    

                    $SumColor       = '';
                    $statusColor = 'text-warning';
                    if($sumLine<=0) $SumColor   = 'text-danger';
                    if($model->status == 'Posted') $statusColor = 'text-success';
                    
                    $saleOrder      = ($model->saleorder->status==200)? $model->saleorder->no : '000000-0000';
                    $saleOrderDate  = ($model->saleorder->status==200)? date('d/m/y',strtotime($model->saleorder->order_date."+543 Years")) : '00/00/00';

                    $Province = '';
                    $Province_ = "";
                    if($model->customer->province!=''){ 
                        $Province = '('.trim($model->customer->provincetb->PROVINCE_NAME).')';
                        $Province_ = trim($model->customer->provincetb->PROVINCE_NAME);
                    }

                    
                    $date = 'date';
                    $c++;
                    $td.= '<tr >';
                    /* R0 */ 	$td.= "<td class='item no hidden-xs'>{$c}</td>";
                    /* R1 */ 	$td.= "<td class='item date hidden-xs'>".date('d/m/y',strtotime($model->posting_date."+543 Years"))."</td>";
                    /* R2 */    $td.= "<td class='item doc_no hidden-xs' > ".Html::a($saleOrder,['/SaleOrders/saleorder/view','id' => $model->saleorder->id],['target' => '_blank']) ."</td>";
                    /* R3 */    $td.= "<td class='item doc_no hidden-xs ' > 
                                        ".Html::a($model->no_,[ $model->status == 'Posted' ? '/accounting/posted/posted-invoice' : '/accounting/saleinvoice/update' 
                                        ,'id' => $model->status == 'Posted' ?  base64_encode($model->id) : $model->id],
                                        [
                                            'target' => '_blank',
                                            'class' => $statusColor
                                        ]) ."</td>";
                    /* R4 */    $td.= "<td class='hidden-xs'  >{$model->customer->code}</td>"; 
                    /* R5 */    $td.= "<td class='hidden-xs'  >{$model->customer->name}</td>"; 
                    /* R6 */    $td.= "<td class='hidden-xs'  >{$Province_}</td>"; 
                    /* R7 */    $td.= "<td class='item hidden-sm hidden-md hidden-lg'  >                                        
                                        <div class='customer-name'>
                                            <small class='pull-right text-gray' style='margin-top:5px;'>{$Province}</small>
                                            <div class='hidden-xs' style='font-size:15px;'>".mb_substr($model->customer->name,0,$subStr).$Dotted."</div>      
                                            <div class='hidden-sm hidden-md hidden-lg' style='font-size:15px;'>".mb_substr($model->customer->name,0,35).((strlen($model->customer->name) > 35)? '...' : null)."</div>                                      
                                        </div>                                       
                                        <small class='row hidden-sm hidden-md hidden-lg doc-list'>
                                            <div class='col-xs-8'>
                                                <div><span class='text-warning'>{$c})</span> {$saleOrder}</div>
                                                <div class='{$statusColor}' style='margin-left:15px;'>↳ {$model->no_} </div>
                                            </div>
                                            <div class='col-xs-4 text-right'>
                                                <div>{$saleOrderDate}</div>
                                                <div class='{$statusColor}'>{$date('d/m/y',strtotime($model->posting_date."+543 Years"))}</div>
                                            </div>
                                        </small>
                                            <div  class='money text-right hidden-sm hidden-md hidden-lg '>".number_format($Total,2)."</div>
                                        </td>";
                    /* R10 */ 	$td.= "<td class='item text-right money {$SumColor} '  ><b>".number_format($Total,2)."</b></td>";

                    $td.= '</tr>';
                    /// Sum Zone
                    $sumF3 += $Total;
                    $x 	= 0;
                }
                $td.= '</tbody>';
                $td.= '<tfooter>';
                    $td.= '<tr>';
                    $td.= ' <td class="hidden-xs" colspan="6">รวม '.$AllData.' ใบ</td>';
                    $td.= ' <td colspan="2" class="footer-value" > 
                                <div class="row">                          
                                <div class="hidden-sm hidden-md hidden-lg col-xs-6"><h4> รวม '.$AllData.' ใบ </h4></div>
                                <div class="text-right col-xs-6 col-sm-12 F3" ng-init="total='.$sumF3.'"><h4>'.number_format($sumF3,2).'</h4></div>        
                                </div>                     
                            </td>';
                    $td.= '</tr>';
                $td.= '</tfooter>';
                $td.= '</table>';
                echo $td;
            //}
        ?>
        </div>  
        </div>
    </div>
    <button  id="myBtn" class="btn btn-default" style="position: fixed; bottom: 5px; right: 10px; z-index: 99; color:red;"><i class="fas fa-arrow-up"></i> Top</button>

    
<?php 
// Extensions from data table
// https://datatables.net/extensions/buttons/examples/styling/bootstrap.html

$options = ['depends' => [\yii\web\JqueryAsset::className()]];
 
$this->registerJsFile('https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',$options);
$this->registerJsFile('https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js',$options);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js',$options);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js',$options);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',$options);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js',$options);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js',$options);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js',$options);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js',$options);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js',$options);
 
$Yii = 'Yii';
$js=<<<JS

$('body').on('click','#myBtn',function(){
    topFunction();
});
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 150 || document.documentElement.scrollTop > 150) {
        document.getElementById("myBtn").style.display = "block";
    } else {
        document.getElementById("myBtn").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}



		
        
            
          
		
$(document).ready(function() {
 
    var table = $('#export_table').DataTable( {
                    language: {
                        paginate: {
                            next: '<span class="glyphicon glyphicon-menu-right"></span>',
                            previous: '<span class="glyphicon glyphicon-menu-left"></span>'
                        },
                        search: "{$Yii::t("common","Search")}",
                        info: "{$Yii::t("common","Showing")} _START_ {$Yii::t("common","of")} _END_  {$Yii::t("common","from")} _TOTAL_"
                    },
                    processing: true,
                    lengthChange: false,
                    paging: true,
                    pageLength: 25,
                    buttons: [ 
                        { 
                            extend: 'excel',
                            text: "{$Yii::t("common","Excel")}"
                        },
                        { 
                            extend: 'csv',
                            text: "{$Yii::t("common","CSV")}"
                        },, 
                        { 
                            extend:'colvis',
                            text: "{$Yii::t("common","Column")}"
                        }
                    /*{
                        extend: 'pdf',
                        text: "PDF",
                        pageSize: 'A4',
                        customize: function(doc){
                            doc.defaultStyle = {
                                font: 'THSarabun',
                                fontSize: 16
                            };
                        }
                    }*/],
                    // columnDefs : [
                    //     {
                    //         "targets": [ 1 ],
                    //         "visible": false,
                    //         "searchable": false
                    //     },
                    //     {
                    //         "targets": [ 7 ],
                    //         "visible": false
                    //     }
                    // ]
                } );
 
        table.buttons().container().appendTo( '#export_wrapper .col-sm-6:eq(0)' );
} );


JS;
$this->registerJS($js,\yii\web\View::POS_END,'Yii');
?>