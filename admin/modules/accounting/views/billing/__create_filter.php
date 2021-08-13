


<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
 
use common\models\BillingNote;
use common\models\RcInvoiceHeader;
use common\models\Cheque;
use common\models\Approval;


$NoSeries         = '';






function fetchInvoice($models,$sale_id,$getVat){

   
     //--- Date Filter ---
    //$LastDay    = date('t',strtotime(date('Y-m-d')));

    //$formdate   = date('Y-').date('m-').'01';
    $formdate   = '1970-01-01 00:00:0000';

    $todate     = date('Y-m-d').' 23:59:59.9999';

    if(@$_GET['fdate']!='') $formdate     = date('Y-m-d 00:00:0000',strtotime($_GET['fdate']));

    if(@$_GET['tdate']!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime($_GET['tdate']));

    
    //--- /. Date Filter ---
    //var_dump($getVat);


    $query = RcInvoiceHeader::find()
    ->where(['cust_no_' => $models->cust_no_])
    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
    ->andWhere(['between', 'DATE(posting_date)', $formdate,$todate])
    ->orderBy(['posting_date' => SORT_ASC]);

    //---  Vat Filter ---
    if($getVat!='')     $query->andWhere(['vat_percent' => $getVat]);

    //--- Sale Filter ---
    if($sale_id!='')    $query->andWhere(['sale_id' => $sale_id]);
    
    $data       = '<div class="row">';
    $i          = 0;
    $url        = '';
    $toolTip    = '';

    if($query->count()<=0) $data.= '<div id="no-data" style="min-width:250px;"><div class="col-xs-12">'.Yii::t('common','No data').'</div></div>';
    
    foreach ($query->all() as $key => $Inv) {
        $i++;


        // <!-- ถ้าออกใบวางบิลแล้ว จะเป็นสีฟ้า -->

        $Billing = BillingNote::find()->where(['inv_no' => $Inv->id]);

        $bgColor = NULL;

        $checked = 'checked="checked"';

        if($Billing->exists()){
            $bgColor    = 'bg-danger';

            $Bill       = $Billing->one();
            $url        = 'index.php?r=accounting/billing/update&id='.base64_encode($Bill->no_);
            $toolTip    = $Bill->no_;
            $checked    = "";
        }

        // <!-- /.ถ้าออกใบวางบิลแล้ว จะเป็นสีฟ้า -->



        // <!-- ถ้ารับเงินแล้วจะเป็นสีเขียว -->
        $Recipt = Cheque::find()->where(['apply_to' => $Inv->id]);

        if($Recipt->exists()){
            $checked    = "";
            $Approve    = $Recipt->one();
            if($Approve->getComplete() > 0){

                $bgColor    = 'bg-success';
                //$i--;

            }else {
                $bgColor    = 'bg-warning';
            }
            
        }
        // <!-- /.ถ้ารับเงินแล้วจะเป็นสีเขียว -->


        $data.= '
        <div class="row form-check inv-section" data-id="test">
            <div class="col-xs-12 font-roboto">
                <div class="col-xs-4   inv-row render-tooltip '.$bgColor.'">
                    <input type="checkbox" name="inv" class="form-check-input pointer" id="invoice-'.$Inv->id.'" value="'.$Inv->id.'" '.$checked.'">
                    <label class="form-check-label pointer" for="invoice-'.$Inv->id.'">
                       <span class=" bopup-tooltip"  data-toggle="tooltip" title="'.$toolTip.'">'.$i.') '.$Inv->no_.'</span>
                    </label>
                </div>
                <div class="col-xs-2   inv-row ">'.$Inv->sales_people.'</div>
                <div class="col-xs-3   inv-row ">'.date('Y-m-d',strtotime($Inv->posting_date)).'</div>                
                <div class="col-xs-3  inv-row text-right '.$bgColor.'">'.number_format($Inv->getSumTotal(),2).'</div>
            </div>    
        </div>';
 

    }
    $data.= '</div>';

    return (Object)[
        'getData'=>$data,
        'count' => $query->count(),
        'url' => $url,
        ];
} 


function CountBill($model){

        //--- Date Filter ---
       // $LastDay    = date('t',strtotime(date('Y-m-d')));

        //$formdate   = date('Y-').date('m-').'01';
        $formdate   = '1970-01-01';

        $todate     = date('Y-m-d');

        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));

        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));

        
        //--- /. Date Filter ---

        $BillingNote    = BillingNote::find()
                        ->where(['cust_no_' => $model->cust_no_])
                        ->andWhere(['between', 'paymentdue', $formdate,$todate]);

        $CountBill      = $BillingNote->count();

        return $CountBill;

    }
?>
 
<?php $this->registerCssFile('css/billing-note.css?v=3.2.20');?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
 <?= $this->render('__menu_filter',['dataProvider' => $dataProvider]); ?>
   
<div class="billing-render">        
    <div class="table-responsive-disable" style="padding-top: 15px;">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions'=> ['class' => 'table table-hover font-roboto'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],     
                'paymentdue',
                // [
                //     'attribute' => 'paymentdue',
                //     'format' => 'raw',
                //     'value' => function($model){
                //         return '
                //                 <div>'.date('Y-m-d', strtotime($model->posting_date)).'</div>
                //                 <div class="text-yellow">
                //                     <div style="margin-top:10px;">'.Yii::t('common','Due date').'</div>
                //                     <div>'.date('Y-m-d', strtotime($model->paymentdue)).'</div>
                //                 </div>
                //             ';
                        
                //     }
                // ],  
                
                [
                    'label' => Yii::t('common','Invoice'),
                    'format' => 'raw',
                    'value' => function($model){
                        $sale_id    = (isset($_GET['search-from-sale']))? $_GET['search-from-sale']:'';
                        $getVat     = (isset($_GET['searchVat']))? $_GET['searchVat']:'';

                        $html = '<div class="panel panel-default padding ">';
                        $html.= fetchInvoice($model,$sale_id,$getVat)->getData;
                        $html.= '</div>';

                        return $html;
                    }
                ],  
                [
                    'label' => Yii::t('common','Customer'),
                    'format' => 'raw',
                    'value' => function($model){
                        $html = '<div class="col-xs-9">';
                        $html.= '   <a href="javascript:void(0)" class="btn-create-bill" data-cust="'.$model->cust_no_.'">['.$model->customer->code.'] '.$model->customer->name.'</a>';                        
                        $html.= '</div>';
                        $html.= '<div class="col-xs-3 text-right">';
                        $html.= '   <a href="javascript:void(0)" class="btn btn-success btn-create-bill" data-cust="'.$model->cust_no_.'"><i class="far fa-file-alt"></i> วางบิล</a>';
                        $html.= '</div>';
                        return $html;
                    }
                ],  
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

    </div>

</div> 

 
<?php if(!isset($_GET['fdate'])) : ?>


<div class="row footer-section">

    <div class="col-sm-12">
    <div class="footer-info" >

        <div class="row contact-info">
        <div class="col-md-12 col">
            
                <div class="col-md-6">
                    <div class="" style="margin-top: 15px; height: 160px;">
                        <div class=" " style="padding: 5px;">
                            <h3><?=Yii::t('common','Explanation')?></h3>
                            <ul>
                                <li><span class="col-md-12"> รายการที่แสดง คือรายการที่<u>ใกล้วันกำหนด</u>รับชำระ</span></li>
                                <li><span class="col-md-12"> *ไม่สามารถออกวางบิลซ้ำได้</span></li>
                                <li><span class="col-xs-12"> ** หาก Approve การชำระเงินแล้ว รายการจะหายไป </span></li>
                            </ul> 
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="" style="margin-top: 15px;">
                        <div class=" " style="padding: 5px;">
                            <h3><?=Yii::t('common','Remark')?></h3>
                            <ul>
                                <li><span class="bg-gray col-lg-2 col-xs-3  text-center text-red"> สีเทา </span><span class="col-lg-10 col-xs-9 "> หมายถึง ออกใบวางบิลครบแล้ว</span></li>
                                <li><span class="bg-danger  col-lg-2 col-xs-3  text-center text-info"> แดง </span><span class="col-lg-10 col-xs-9"> หมายถึง ออกใบวางบิลแล้ว</span></li>                                            
                                <li><span class="bg-warning col-lg-2 col-xs-3  text-center text-red"> สีส้ม </span><span class="col-lg-10 col-xs-9"> หมายถึง ออกใบวางบิลแล้ว + แจ้งชำระเงินแล้ว </span></li>
                                <li><span class="bg-success col-lg-2 col-xs-3  text-center text-red"> สีเขียว </span><span class="col-lg-10 col-xs-9"> หมายถึง ออกใบวางบิลแล้ว + อนุมัติเงินที่ชำระแล้ว </span></li> 

                            </ul>
                        </div>
                    </div>
                </div>

        </div> 

        </div>

    </div>
    </div>
</div>    

<?php endif; ?>

 


 




<?php $this->registerJsFile('js/slide-menu-right.js');?>

<?php
$Yii = 'Yii';
$js =<<<JS

    $(document).ready(function(){

        //toolTip('.bopup-tooltip'); 
        $('[data-toggle="tooltip"]').tooltip();

        setTimeout(function(){
            // $('.menu-right-slide').show("slide", { direction: "right" }, 500);
            // $('body').attr('style','overflow: auto;');
            // $('.menu-right-slide').animate({"height": '100%'});

            // $('.menu-left-click').fadeIn('slow');
            // $(this).html('<i class="fa fa-refresh fa-spin text-info "></i>');

        },500);

        
        
        
        
         
        $('.custom-menu-print').hide();

         

        if(getUrlVars('getView')=='true'){

            getRenderBilling();

            setTimeout(function(){
                $('.menu-right-slide').show("slide", { direction: "right" }, 500);
                $('body').attr('style','overflow: auto;');
                $('.menu-right-slide').animate({"height": '80.48%'});
            },500);

            //$('p#bill-box').attr('style','background-color : #f0ad4e; color:#fff;');
        }else {
            //getCustomerStyle();
        }


        // Hidden button save
        $('.ew-save-common').css('visibility','hidden');

        

    });
    

    $('body').on('click','button.save-billing',function(){
        
        // var no = $(this).attr('data-file');
  //       window.location.replace("index.php?r=accounting/billing/update&no="+no);

        if($('.customer-billing').attr('data')!=''){

            var inv = [];
            $(".iv-no").each(function(){
                inv.push($(this).attr('data'));
            });

            var getUrl = {                
                inv:inv,
                customer:$('.customer-billing').attr('data'),
                action:'update',
                no: $('body').find('input#no').val()
            };
             
            //console.log(getUrl);
            $.ajax({
                url:'index.php?r=accounting/billing/confirm-bill',
                type:'POST',
                data:getUrl, 
                dataType: 'json',            
                success:function(response){
                    $('.billing-render').html(response.html);
                    $('body').find('input#no').val(response.no).attr('readonly', true);
                    $('.footer-section').hide();
                    setTimeout(function(){
                        $('.menu-right-slide').show("slide", { direction: "right" }, 500);
                        $('body').attr('style','overflow: auto;');
                        $('.menu-right-slide').animate({"height": '80.48%'});
                    },500);
                }
            });
            // var data = {
            //     fdate:getUrlVars('fdate'),
            //     tdate:getUrlVars('tdate'),
            //     searchVat:getUrlVars('searchVat'),
            //     customer:getUrlVars('customer'),
            //     sale:getUrlVars('search-from-sale'),
            //     action:'update',            

            // };

 
            // $.ajax({
            //     url:'index.php?r=accounting/billing/render-table',
            //     type:'GET',
            //     data:data,
            //     async:true,
            //     success:function(getData){
            //         $('.Navi-Title').html(getData);
            //     }
            // })
        }else {
            swal(
              'ดูเหมือนว่า "ยังไม่ได้ค้นหาลูกค้าเลย"',
              'กรุณาเลือกลูกค้า',
              'warning'
            );
            return false;
        }
        
         
    });

    function getCustomerStyle(){

        $.ajax({
            url:'index.php?r=accounting/billing/customer-style',
            type:'GET',          
            async:true,
            success:function(getData){
                $('.billing-render').html(getData);
            }
        })
        
    }

    function getRenderBilling(){
        var getUrl = {
                fdate:getUrlVars('fdate'),
                tdate:getUrlVars('tdate'),
                searchVat:getUrlVars('searchVat'),
                customer:getUrlVars('customer'),
                sale:getUrlVars('search-from-sale')

        };



        $.ajax({
            url:'index.php?r=accounting/billing/render-table',
            type:'GET',
            data:getUrl,
            async:true,
            success:function(getData){
                $('.billing-render').html(getData);
            }
        })
    }

 
    $('body').on('click','.btn-create-bill',function(){
         
        
        var inv = [];
        $(this).closest('tr').find("input[name='inv']:checked").each(function(){
            inv.push(this.value);
        });

        var getUrl = {                
            inv:inv,
            customer:$(this).data('cust')

        };

        console.log(getUrl);

        
        if(inv.length<=0){
            $.notify({
                // options
                message: "{$Yii::t('common','No data')}"
            },{
                // settings
                type: 'warning',
                delay: 2000,
            });
        }else{

            $.ajax({
                url:'index.php?r=accounting/billing/confirm-bill',
                type:'POST',
                data:getUrl,      
                dataType: 'json',       
                success:function(response){
                    $('.billing-render').html(response.html);
                    $('body').find('input.bill_no').val(response.no).attr('value',response.no);
                    $('.footer-section').hide();
                    setTimeout(function(){
                        $('.menu-right-slide').css('height','300');
                        $('.menu-right-slide').show("slide", { direction: "right" }, 500);
                        $('body').attr('style','overflow: auto;');
                        $('.menu-right-slide').animate({"height": '80.48%'});
                    },500);

                    /**
                    * Change Menu 'SAVE BTN'
                    */
                    $('.ew-save-common').closest('div').append('<button class="btn btn-app rippleria-dark save-billing"><i class="fa fa-save text-primary"></i> {$Yii::t("common","Confirm")}</button>');
                    $('.ew-save-common').remove();
                    //$('.ew-save-common').removeClass('ew-save-common').addClass('save-billing').removeAttr('onclick').attr('href','javascript:void(0)');
                    
                    // Move to top page
                    $('html, body').animate({ scrollTop: 0 }, 'nomal');
                }
            });

        }
    });
JS;
//$this->registerJS($js);
$this->registerJs($js,\yii\web\View::POS_END);
