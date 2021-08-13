


<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
 
use common\models\BillingNote;

$NoSeries         = '';

            

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
 
          
<!-- tabbable -->
<div class="tabbable" style="padding-top: 15px;">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#1" data-toggle="tab" style="width: 60px; text-align: center;"><i class="fa fa-bars" aria-hidden="true"></i></a></li>
      <li><a href="#2" data-toggle="tab" style="width: 60px; text-align: center;"><i class="fa fa-file-o" aria-hidden="true"></i></a></li>
       <!-- <span class="pull-right">
        <a href="index.php?r=accounting/billing/create" class="btn btn-warning-ew"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
    </span> -->
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="1">
           
            <div class="table-responsive" style="padding-top: 15px;">
                <?php Pjax::begin(); ?> 
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'rowOptions' => function($model){

                            $PanelStyle    = ['class' => 'bg-warning'];
                                
                            if($model->getBalance()['paid'] == 0){

                                return ['class' => 'bg-danger'];

                            }else if($model->getBalance()['amount'] - $model->getBalance()['paid'] == 0){

                                return ['class' => 'bg-success'];

                            }else {
                               return ['class' => $PanelStyle]; 
                            }
                            
                        },
                        'tableOptions'=> ['class' => 'table table-hover'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            
                            [
                                'attribute' => 'create_date',
                                'format' => 'raw',
                                'value' => function($model){
                                    
                                    $html = '<div>';
                                    //$html.=     '<a href="index.php?r=accounting/billing/update&id='.base64_encode($model->paymentdue).'">'.$model->no_.'</a>';
                                    $html.=     '<i class="fa fa-calendar" aria-hidden="true"></i> '.date('Y-m-d',strtotime($model->getDataFromNo()->create_date));
                                    $html.= '</div>';
                                    return $html;
                                }
                            ],

                            [
                                'attribute' => 'no_',
                                'format' => 'raw',
                                'value' => function($model){

                                     

                                    $html = '<div>';
                                    $html.=     '<a href="index.php?r=accounting/billing/update&id='.base64_encode($model->no_).'">
                                                    <i class="fa fa-file-word-o text-primary" aria-hidden="true"></i> '.$model->no_.' ('.$model->getCount().')</a>';
                                    //$html.=     $model->paymentdue;
                                    $html.= '</div>';
                                    return $html;
                                }
                            ],

                            [
                                //'attribute' => 'amount',
                                'format' => 'raw',
                                'label' => Yii::t('common','Amount'),  
                                'headerOptions' => ['class' => 'text-right'],  
                                'contentOptions' => ['class' => 'text-right'],                         
                                'value' => function($model){                                    
                                    
                                    
                                    return number_format($model->getBalance()['amount'],2);

                                }
                            ],
                            // 'payment',
                            [
                                //'attribute' => 'paid',
                                'format' => 'raw',
                                'label' => Yii::t('common','Paid'), 
                                'headerOptions' => ['class' => 'text-right'],  
                                'contentOptions' => ['class' => 'text-right'],                          
                                'value' => function($model){                                    
                                    
                                    $html = '<a href="javascript:void(0)" data-id="'.$model->cust_no_.'" class="payment">'.number_format($model->getBalance()['paid'],2).'</a>';
                                    return $html;

                                }
                            ],
                            [
                                //'attribute' => 'balance',
                                'format' => 'raw',
                                'label' => Yii::t('common','Outstanding debt report'), 
                                'headerOptions' => ['class' => 'text-right'],  
                                'contentOptions' => ['class' => 'text-right'],                       
                                'value' => function($model){                                    
                                    
                                     
                                    return number_format($model->getBalance()['amount'] - $model->getBalance()['paid'],2) ;

                                }
                            ],


                            [
                                //'attribute' => 'cust_no_',
                                'format' => 'raw',
                                'label' => Yii::t('common','Customer'),                        
                                'value' => function($model){                                    
                                   
                                    return '<i class="fa fa-address-card-o" aria-hidden="true"></i> '.$model->getDataFromNo()->customer->name;

                                }
                            ],
                            [
                                 
                                'format' => 'raw',
                                'label' => Yii::t('common','Status'), 
                                'headerOptions' => ['class' => 'text-center'], 
                                'contentOptions' => ['class' => 'text-center render-tooltip'],                       
                                'value' => function($model){                                    
                                   
                                    //     $PanelStyle    = '<i class="fa fa-hourglass-half" aria-hidden="true"></i>';
                                         
                                    //     $Recipt = \common\models\Cheque::find()->where(['apply_to' => $model->getDataFromNo()->inv_no]);

                                    //     if($Recipt->exists()){

                                    //         $Approve    = $Recipt->one();
                                    //         if($Approve->getComplete() > 0){
                                    //             // Approved
                                    //             $PanelStyle    = '<i class="fa fa-check-square text-green" aria-hidden="true"></i>';                           

                                    //         }else {
                                    //             // Not yet Approve
                                    //             $PanelStyle    = '<i class="fa fa-check-square" aria-hidden="true"></i>'; 

                                    //         }
                                            
                                    //     }
                                         
                                    // return $PanelStyle;

                                    $Status = new \admin\models\AlertException;


                                    $Tracking = \common\models\OrderTracking::find()
                                    ->where(['doc_type' => 'BillingNote'])
                                    ->andWhere(['doc_no' => $model->no_])
                                    ->orderBy(['id' => SORT_DESC])
                                    ->asArray()
                                    ->one();

                                    $tooltip = '<a href="index.php?r=accounting/billing/update&id='.base64_encode($model->no_).'">';
                                    
                                    $tooltip.= '<div class="bopup-tooltip text-info" data-tooltip="'.$Tracking['remark'].'">'.$Status->DocStatus($Tracking['doc_status']).'</div>';

                                    $tooltip.= '</a>';

                                    return $tooltip;

                                }
                            ],
                            //'paymentdue',
                           // 'customer.name',


                             
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

        </div><!-- /.tab-1 -->

        <div class="tab-pane fade" id="2">


            <div class="row body-content" >

                <?php 
                    $html = '<div class=" " style="width: 78%;">';
                    foreach ($dataProvider->models as $key => $model) {



                       
                         
                       

                        $PanelStyle    = 'bg-warning';
                                
                        if($model->getBalance()['paid'] == 0){

                            $PanelStyle    = 'bg-danger';

                        }else if($model->getBalance()['amount'] - $model->getBalance()['paid'] == 0){

                            $PanelStyle    = 'bg-success';

                        }
                        // <!-- /.ถ้ารับเงินแล้วจะเป็นสีเขียว -->


                        $html.= '<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                     <div class="body-print-mini open-file" data-file='.base64_encode($model->no_).'>
                                        <div class="print-header" >
                                            <div class="row">

                                                <div class="col-xs-12">
                                                    <p class="iconApp-mini">
                                                    <span style="color:red;">E</span>
                                                    <span style="color:yellow;">I</span>
                                                    <span style="color:green;">L</span>
                                                    </p>                
                                                </div> 

                                                <div class="col-xs-12 text-right">
                                                    ใบวางบิล
                                                </div>
                                            </div><!-- /. row -->
                                            <div class="row">
                                                <div class="col-xs-12"><div class="hr-mini"></div></div>
                                            </div>
                                            <table width="100%" border="1">
                                                <tr align="center">
                                                    <td>#</td>
                                                    <td>A</td>
                                                    <td>B</td>
                                                    <td>C</td>
                                                    <td>D</td>
                                                </tr>
                                                <tr align="center">
                                                    <td>1 <div class="cust-file">'.$model->getDataFromNo()->customer->code.'</div></td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                </tr>
                                                <tr align="center">
                                                    <td>2 </td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                </tr>
                                                <tr align="center">
                                                    <td>3 <div class="date-file">'.date('Y-m-d',strtotime($model->getDataFromNo()->create_date)).'</div></td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                    <td class="content-mini">x</td>
                                                </tr>
                                            </table>
                                            <div class="row">
                                                <div class="col-xs-12"><div class="hr-mini"></div></div>
                                            </div> 
                                            <div class="row">
                                                <div class="col-xs-12"><div class="footer-mini '.$PanelStyle.'" style="">'.$model->no_.'</div></div>
                                            </div>
                                            
                                        </div><!-- /. print-header -->

                                     </div>
                                    </div>';

                    }
                    $html.= "</div>";

                    echo $html;




                ?>


                <?php

                    function fetchInvoice($model){ 

                        $query = \common\models\RcInvoiceHeader::find()
                        ->where(['cust_no_' => $model->getDataFromNo()->cust_no_])
                        ->orderBy(['paymentdue' => SORT_DESC]);

                        return $query->count();

                    }
                    function CountBill($model){

                        $BillingNote    = \common\models\BillingNote::find()->where(['cust_no_' => $model->getDataFromNo()->cust_no_]);

                        return $BillingNote->count();

                    }


                ?>



            </div>

        </div><!-- /.tab-2 -->

    </div><!-- /.tab-content -->
</div><!-- /tabbable -->

<?php $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>


<?php $this->registerJsFile('js/slide-menu-right.js');?>

<?php
$js =<<<JS

    $(function(){
        $(".body-print-mini").draggable({
          handle: ".print-header"
      });
    })

    

     

    $(document).ready(function(){

        setTimeout(function(){
            //$('.menu-right-slide').show("slide", { direction: "right" }, 500);
            //$('body').attr('style','overflow: auto;');
            //$('.menu-right-slide').animate({"height": '100%'});
        },500);


        
        $('.custom-menu-print').hide();
        $('.custom-menu-content').hide();

        toolTip('.bopup-tooltip');
        

    })

    $('body').on('dblclick','.open-file',function(){
        // var id = $(this).attr('data-file');
        // window.location.replace("index.php?r=accounting/billing/update&id="+id);
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