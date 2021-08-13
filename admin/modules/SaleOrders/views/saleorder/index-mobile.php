<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;

use yii\db\Expression;
use kartik\daterange\DateRangePicker;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\apps_rules\models\SysRuleModels;

 
 
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
$this->registerCssFile('css/sales_order/index.css?v=4',['rel' => 'stylesheet','type' => 'text/css']);
?>
 


<?=$this->render('_index-heading',['model' => $searchModel, 'dataProvider' => $dataProvider])?>
<div class="sale-header-index" ng-init="Title='<?=$this->title;?>'">
    <div style="position: absolute; right: 20px; top: 75px;">
        <div class="hidden-xs hidden-sm text-right" >
        <?php
            echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'showColumnSelector' => false,
                'target' => ExportMenu::TARGET_BLANK,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('common','Date'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return date('d/m/Y',strtotime($model->order_date));
                        },
                    ],
                    'no',                     
                    'customer.name',                                      
                    [
                        'attribute' => 'salespeople.code',
                        'label'     => Yii::t('common','Sales'),
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->salespeople? (string)$model->salespeople->code : Yii::t('common','Not Set');
                        }
                    ],                   
                    [
                        'attribute' => 'sales_people',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->salespeople? $model->salespeople->name. ' '.$model->salespeople->surname : Yii::t('common','Not Set');
                        }
                    ],            
                    'balance'    
                ],
                'filename'        => Yii::t('app', 'SaleOrder'),
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
                //'styleOptions' => [
                    // ExportMenu::FORMAT_PDF => [
                    //     'font' => [
                    //          'family' => ['THSarabunNew','garuda'],
                    //             'bold' => true,
                    //             'color' => [
                    //                  'argb' => 'FFFFFFFF',
                    //          ],
                    //     ],
                    // ],
                //],
            ]);
            ?>
        </div>    
    </div>

    <div class="row-">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
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
            'options' => ['class' => 'bg-white'],
            'rowOptions' => ['style' => 'height:58px;'],
            'columns' => [
                [
                    'class'             => 'yii\grid\SerialColumn',
                    'options'           => ['style' => 'width:50px;'],
                    'headerOptions'     => ['class' => 'text-center hidden-xs'],
                    'filterOptions'     => ['class' => 'hidden-xs'],
                    'contentOptions'    => ['class' => 'text-center hidden-xs  show-doc']
                ],            
                [
                    'attribute'     => 'order_date',
                    'label'         => Yii::t('common','Order Date'),
                    'format'        => 'html',
                    'headerOptions' => ['class' => 'hidden-xs '],
                    'contentOptions'=> ['class' => 'hidden-xs '],
                    //'visible'       => Yii::$app->session->get('collapse') ? true : false,
                    'filterOptions' => ['class' => 'hidden-xs','style' => 'width:102px;'],
                    'value'         => function($model){
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
                    'filterOptions'     => ['class' => 'filter-no'],
                    'contentOptions'    => ['class' => 'doc-no','style' => 'position:relative;'],
                    'value'             => function($model){

                        // ตัดตัวอักษร ถ้ามากกว่า 35 ตัว
                        $count_char = utf8_strlen($model->customer['name']);
                        if($count_char >=50 )
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
                        //----DESKTOP----

                        $html = '<div class="hidden-xs ">
                                    
                                    '.Html::a('<div>'.Yii::t('common',$cust_name).'</div>'.$model->no.' '.$icon,['/SaleOrders/saleorder/view','id' => $model->id],['class' => $vat_color.' text-order-number']).'
                                </div>';

                        //----MOBILE----
                        $html.= '<div class="show-doc hidden-sm hidden-md hidden-lg">';
                        
                            $html.= '<div class="row heading">
                                        <div class="col-xs-4 ">                                        
                                            <div class="head">'.Yii::t('common','Order No.').'</div>
                                            <div class="actions-more font-roboto text-danger">'.$model->no.'</div>
                                        </div>
                                        <div class="col-xs-3 no-padding">
                                            <div class="head">'.Yii::t('common','Balance').'</div>
                                            <div class="text-success font-roboto">'.number_format($model->balance,2).'</div>
                                        </div>
                                        <div class="col-xs-1 text-center no-padding">                                          
                                            <div class="head">'.Yii::t('common','Items').'</div>
                                            <div class="text-center font-roboto">'.number_format($model->countRow).'</div>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            '.Html::a('<i class="fas fa-caret-right"></i> '.Yii::t('common','View Detail'),['/SaleOrders/saleorder/view','id' => $model->id],['class' => 'btn   btn-xs '.$model->jobColor->class.'']).'                                             
                                        </div>
                                    </div>';

                            $html.= '<div class="row">'; 
                            $html.= '   <div class="col-xs-12 text-customer-info"><span class="text-info">'.Yii::t('common',$cust_name).'</span></div>';
                            $html.= '</div>';

                            $html.= '<div class="row progress-head">'; 
                            $html.= '   <div class="col-xs-6"><span class="text-default">'.Yii::t('common','Status').'</span></div>';
                            $html.= '   <div class="col-xs-6 text-right"><span class="text-aqua"  id="ew-tr-modal"   data="'.base64_encode($model->id).'">'.$model->jobStatus->progress.'</span></div>';
                            $html.= '</div>';                             
                             
                            $create     = ($model->jobStatus->release)? 'complete'  : 'active';

                            if ($model->jobStatus->checking){
                                $create = 'complete';
                            }
                            $shipping   = ($model->jobStatus->ship)? 'disabled' : 'active';

                            $_open      = false;
                            $_create    = false;
                            $_packing   = false;
                            $_transport = false;
                            $_shiped    = false;
                            $_inv_date  = $model->order_date;

                            if($model->status=='Open'){
                                $_open      = true;
                                $_create    = true;
                                $_packing   = false;
                                $_transport = false;
                                $_shiped    = false;
                                $_inv_date  = $model->rcInvoiceHeader ? $model->rcInvoiceHeader->posting_date : date('Y-m-d');
                                $create     = 'active';
                            }else if($model->status=='Shiped'){
                                $_open      = true;
                                $_create    = true;
                                $_packing   = true;
                                $_transport = true;
                                $_shiped    = true;
                                $_inv_date  = $model->rcInvoiceHeader ? $model->rcInvoiceHeader->posting_date : date('Y-m-d');
                                $create     = 'complete';
                            }else if($model->status=='Release'){
                                $_open      = true;
                                $_create    = true;
                                $_packing   = false;
                                $_transport = false;
                                $_shiped    = false;
                                $_inv_date  = $model->rcInvoiceHeader ? $model->rcInvoiceHeader->posting_date : date('Y-m-d');
                                $create     = 'complete';
                            }else if($model->status=='Checking'){
                                $_open      = true;
                                $_create    = true;
                                $_packing   = true;
                                $_transport = false;
                                $_shiped    = false;
                                $_inv_date  = $model->rcInvoiceHeader ? $model->rcInvoiceHeader->posting_date : date('Y-m-d');
                                $create     = 'complete';
                            }else if($model->status=='Invoiced'){
                                $_open      = true;
                                $_create    = true;
                                $_packing   = true;
                                $_transport = true;
                                $_shiped    = false;
                                $_inv_date  = $model->rcInvoiceHeader ? $model->rcInvoiceHeader->posting_date : date('Y-m-d');
                                $create     = 'complete';
                            }else if($model->jobStatus->invoice){
                                $_open      = false;
                                $_create    = false;
                                $_packing   = false;
                                $_transport = false;
                                $_shiped    = false;
                                $_inv_date  = $model->order_date;
                                $create     = 'active';
                            }
 
                            $html.= ' <div class="row bs-wizard" style="border-bottom:0;">
                                            
                                            <div class="col-xs-3 bs-wizard-step '.(($_create)? $create : 'disabled' ).'"" >
                                                <div class="text-center bs-wizard-stepnum '.(($_create)? 'text-aqua' : '').'">'.Yii::t('common','status-release').'</div>
                                                <div class="progress"><div class="progress-bar"></div></div>
                                                <a href="#" class="bs-wizard-dot"></a>
                                                <div class="text-center bs-wizard-footer font-roboto">'.(($_create)? date('d/m/Y',strtotime($model->order_date)) : ' ').'</div>
                                            </div>
                                            
                                            <div class="col-xs-3 bs-wizard-step '.(($_packing)? 'complete' : 'disabled' ).'"" >
                                                <div class="text-center bs-wizard-stepnum '.(($_packing)? 'text-aqua' : '').'">'.Yii::t('common','Packing').'</div>
                                                <div class="progress"><div class="progress-bar"></div></div>
                                                <a href="#" class="bs-wizard-dot"></a>
                                                <div class="text-center bs-wizard-footer font-roboto">'.(($_packing)? date('d/m/Y',strtotime(
                                                    $model->jobStatus->ship != 'Sale-Ship' 
                                                        ? $model->jobStatus->ship 
                                                            ? $model->jobStatus->ship 
                                                            : $model->order_date
                                                        : $model->order_date
                                                    )) : ' ').'</div>
                                            </div>
                                            
                                            <div class="col-xs-3 bs-wizard-step '.(($_transport)? 'complete' : 'disabled').'"><!-- complete -->
                                                <div class="text-center bs-wizard-stepnum '.(($_transport)? 'text-aqua' : '').'">'.Yii::t('common','Ship').'</div>
                                                <div class="progress"><div class="progress-bar"></div></div>
                                                <a href="#" class="bs-wizard-dot"></a>
                                                <div class="text-center bs-wizard-footer font-roboto">'.(($_transport)? date('d/m/Y',strtotime(
                                                    $model->jobStatus->invoice != 'Sale-Inv' 
                                                        ? $model->jobStatus->invoice 
                                                            ? $model->jobStatus->invoice
                                                            : $model->order_date
                                                        : $model->order_date
                                                    )) : ' ').'</div>
                                            </div>
                                            
                                            <div class="col-xs-3 bs-wizard-step '.(($_shiped)? 'complete' : 'disabled' ).'"><!-- complete -->
                                                <div class="text-center bs-wizard-stepnum '.(($_shiped)? 'text-aqua' : '').'">'.Yii::t('common','Shiped').'</div>
                                                <div class="progress"><div class="progress-bar"></div></div>
                                                <a href="#" class="bs-wizard-dot"></a>
                                                <div class="text-center bs-wizard-footer font-roboto">'.(($_shiped)? date('d/m/Y',strtotime($_inv_date)) : ' ').'</div>
                                            </div>
                                            
                                        </div>';
                        $html.'</div>';    
                       

                        return $html;
                    },
                    'filterInputOptions' => ['class' => 'form-control search-no', 'placeholder' => Yii::t('common','Search')],
                ],
                [
                    'attribute'         => 'sale_id',
                    'label'             => Yii::t('common','Sale Person'),
                    'format'            => 'raw',
                    'visible'           => in_array(Yii::$app->session->get('rules_id'),SysRuleModels::getPolicy('Data Access','SaleOrders','saleorder','common','show_sale')),
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
                    'contentOptions'    => ['class' => 'text-right hidden-xs font-roboto'],
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
                    'filterOptions'     => ['class' => 'hidden-xs','style' => 'max-width:160px;'],
                    'contentOptions'    => ['class' => 'hidden-xs status-content','style' => 'position:relative; max-width:160px;'],
                    'headerOptions'     => ['class' => 'hidden-xs','style' => 'max-width:160px;'],                    
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
                            $ship.='<div  class="pull-left" style="min-width: 80px;">
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
                                            <div class="pointer"> '.$JobStatus.' </div>
                                            '.$ship.'
                                        </div> 
                                        <div  class="text-left col-xs-6" />'.$html_confirm.'</div>
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

<?= $this->render('../modal/_tracking'); ?>
<?= $this->render('_index-script'); ?>