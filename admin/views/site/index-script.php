<?php 

$Yii = 'Yii';
$api = Yii::$app->params['api'];
$now = Yii::$app->session->get('workdate');

$MyToken = \common\models\Authentication::findOne(['user_id' => Yii::$app->user->identity->id]);
$token = $MyToken != null ? base64_encode($MyToken->token) : '';


$this->registerJsFile('@web/js/jquery.animateNumber.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$translate=<<<JS
 
let t = {
        LABEL_ALL_CUSTOMER: "{$Yii::t('common','All Customers')}",
        LABEL_NEW_CUSTOMER: "{$Yii::t('common','New Customers')}",
        LABEL_CANCEL_CUSTOMER: "{$Yii::t('common','Cancel Customers')}",
        LABEL_SUSPEND_CUSTOMER: "{$Yii::t('common','Suspend Customers')}",
        LABEL_DATE: "{$Yii::t('common','Date')}",
        LABEL_AMOUNT: "{$Yii::t('common','Amount')}",
        LABEL_MODAL_TITLE: "{$Yii::t('common','Detail')}",
        LABEL_MONTHLY_RECAP_REPORT: "{$Yii::t('common','Monthly Recap Report')}",
        LABEL_CUSTOMER_DUE: "{$Yii::t('common','Customer due')}",
        LABEL_NAME: "{$Yii::t('common','Name')}",
        LABEL_DUE_DATE: "{$Yii::t('common','Payment due')}",
        LABEL_MILLION: "{$Yii::t('common','Million')}", 
        LABEL_EVERY_DATE: "{$Yii::t('common','Every date')}", 
        January: "{$Yii::t('common','January')}",
        February: "{$Yii::t('common','February')}",
        March: "{$Yii::t('common','March')}",
        April: "{$Yii::t('common','April')}",
        May: "{$Yii::t('common','May')}",
        June: "{$Yii::t('common','June')}",
        July: "{$Yii::t('common','July')}",
        August: "{$Yii::t('common','August')}",
        September: "{$Yii::t('common','September')}",
        October: "{$Yii::t('common','October')}",
        November: "{$Yii::t('common','November')}",
        December: "{$Yii::t('common','December')}",
    
    };

JS;

$this->registerJs($translate,\yii\web\view::POS_HEAD,'Yii2');

$js =<<<JS

    realTimeSummary = (id,data) => {

        var x = Math.floor((Math.random() * 10) + 3);
        var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
        var percent_number_step = $.animateNumber.numberStepFactories.append(' %');

        var decimal_places = 2;
        var decimal_factor = decimal_places === 0 ? 1 : Math.pow(10, decimal_places);
        
        $('#'+id+'-panel').attr('class',data.panel.class);
        $('#'+id+'-panel-bg-head').attr('class',data.panel.head);
        $('#'+id+'-icon').attr('class',data.icon);
        $('#'+id+'-currency').html(data.currency);

        if(id=='daily'){
            $('#'+id+'-yesterday').html('<span class="text-gray">{$Yii::t("common","Yesterday")} : <span id="yesterday"></span></span>');
            $('#yesterday').prop('number', x).animateNumber({ number: data.yesterday,numberStep: comma_separator_number_step },2000);
        }

        if(id=='weekly'){
            $('#'+id+'-lastweek').html('<span class="text-gray">{$Yii::t("common","Last Week")} : <span id="lastweek"></span></span>');
            $('#lastweek').prop('number', x).animateNumber({ number: data.lastweek,numberStep: comma_separator_number_step },2000);
        }

        if(id=='monthly'){
            $('#'+id+'-lastmonth').html('<span class="text-gray">{$Yii::t("common","Last Week")} : <span id="lastmonth"></span></span>');
            $('#lastmonth').prop('number', x).animateNumber({ number: data.lastweek,numberStep: comma_separator_number_step },2000);
        }


        $('#'+id+'-color').attr('style','color:'+data.color);
        

		$('#'+id+'-amount').prop('number', x).animateNumber({ number: data.amount,numberStep: comma_separator_number_step },2000);

		if(data.percent!=0){
            $('#'+id+'-sign').html(data.sign);
            //$('#'+id+'-percent').prop('number', x).animateNumber({ number: data.percent, color: data.color,easing: 'easeInQuad',numberStep: percent_number_step},2000);
            $('#'+id+'-percent').prop('number', x).animateNumber({ 
                number: data.percent * decimal_factor,
                color: data.color,
                easing: 'easeInQuad',
                numberStep: function(now, tween) {
                    var floored_number = Math.floor(now) / decimal_factor,
                        target = $(tween.elem);

                    if (decimal_places > 0) {
                    // force decimal places even if they are 0
                    floored_number = floored_number.toFixed(decimal_places);

                    // replace '.' separator with ','
                    //floored_number = floored_number.toString().replace('.', '.');
                    }

                    target.text(floored_number + '%');
                }
            },2000);
        }
       
		
		$('#'+id+'-lastmonth').prop('number', x).animateNumber({ number: data.lastmonth,numberStep: comma_separator_number_step},3000);
    }

    actionCountSchedule = (url,callback) => {
        $.ajax({
            url:url,
            type:'GET',
            dataType:'JSON',
            success:function(res){
                callback(res);
            }
        })
    };

   $(document).ready(function(){

        actionCountSchedule('?r=SaleOrders/ajax/count-schedule',(res) => {

            var x = Math.floor((Math.random() * 100) + 3);

            var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');

            realTimeSummary('daily',res.dialy);

            realTimeSummary('monthly',res.monthly);

            $('.sale-order').prop('number', x).animateNumber({ number: res.summary.saleorder,numberStep: comma_separator_number_step},5000);

            $('.sale-invoice').prop('number', x).animateNumber({ number: res.summary.invoice,numberStep: comma_separator_number_step},5000);

            $('.not-receipt').prop('number', x).animateNumber({ number:res.summary.notinvoice ,numberStep: comma_separator_number_step},5000);

        });

        actionCountSchedule('?r=SaleOrders/ajax/count-orders',(res) => {        

            var x = Math.floor((Math.random() * 100) + 3);

            var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');

            $('.ew-new-orders-count').prop('number', x).animateNumber({ number: res.Release },2000);

            $('.ew-checking-count').prop('number', x).animateNumber({ number: res.Checking },2000);

            $('.ew-shipped-count').prop('number', x).animateNumber({ number: res.Shiped },2000);

            $('.ew-invoice-count').prop('number', x).animateNumber({ number: res.invoiced },2000);            
            
        })
    });


    	
	$('body').on('change','.on-years-change',function(){
        var y = $(this).val();
        $.ajax({
            url:'index.php?r=ajax/set-workdate',
            type:'POST',
            data: {date:'{$now}',y:y},
            dataType:'JSON',
            success:function(res){

            }
        });
        
        $.ajax({
            url:'{$api}/dashboard/balance?y=' + y,
            type:'GET',
            headers: {"token": '{$token}',"X-CSRF-Token": $('meta[name="csrf-token"]').attr('content')},
            dataType:'JSON',
            success:function(res){
                if (res.data.length <= 0){
                    //location.reload();
                }else{
                    var x = Math.floor((Math.random() * 100) + 3);
                    var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
                    $('.sale-order').prop('number', x).animateNumber({ number: res.data[0].saleorder,numberStep: comma_separator_number_step},200);
                    $('.sale-invoice').prop('number', x).animateNumber({ number: res.data[0].invoice,numberStep: comma_separator_number_step},200);
                    $('.not-receipt').prop('number', x).animateNumber({ number:res.data[0].notinvoice ,numberStep: comma_separator_number_step},200); 
                    $('.ew-new-orders-count').prop('number', x).animateNumber({ number:res.order.Release ,numberStep: comma_separator_number_step},200); 
                    $('.ew-checking-count').prop('number', x).animateNumber({ number:res.order.Checking ,numberStep: comma_separator_number_step},200);
                    $('.ew-shipped-count').prop('number', x).animateNumber({ number:res.order.Shiped ,numberStep: comma_separator_number_step},200);
                    $('.ew-invoice-count').prop('number', x).animateNumber({ number:res.order.invoiced ,numberStep: comma_separator_number_step},200);
                }
            }
        })
    })	
JS;
$this->registerJs($js,\yii\web\view::POS_END,'Yii2');
?>

