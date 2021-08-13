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

    let loadData = () => {
        $.ajax({ 
            url:"index.php?r=SaleOrders/ajax/sale-balance-header",
            type: 'GET', 
            data:{reload:true},
            async:true,
            dataType:'JSON',
            success:function(obj){
                var x = Math.floor((Math.random() * 100) + 3);
                var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
                //var obj = jQuery.parseJSON(getData); 
                $('.ew-sales-balance').prop('number', x).animateNumber({ number: obj.saleorder,numberStep: comma_separator_number_step },2000);
                $('.ew-sales-invoice').prop('number', x).animateNumber({ number: obj.invoice,numberStep: comma_separator_number_step },2000);
                $('.ew-sales-notinvoice').prop('number', x).animateNumber({ number: obj.notinvoice,numberStep: comma_separator_number_step },2000);     

                $('.sale-this-month').prop('number', x).animateNumber({ number: obj.salethismonth,numberStep: comma_separator_number_step },2000);   
                $('.inv-this-month').prop('number', x).animateNumber({ number: obj.invthismonth,numberStep: comma_separator_number_step },2000);    

                $('a.reload-sale-data').find('i').removeClass('fa-spin');        
            }
        });  
    }
    
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

                $('.sale-this-month').prop('number', x).animateNumber({ number: obj.salethismonth,numberStep: comma_separator_number_step },2000);   
                $('.inv-this-month').prop('number', x).animateNumber({ number: obj.invthismonth,numberStep: comma_separator_number_step },2000);            
            }
        });         
    });

    $('body').on('click','a.reload-sale-data',function(){
        $(this).find('i').addClass('fa-spin');
        loadData();
    })

JS;

if(Yii::$app->session->get('Rules')['rules_id']!=4){ $this->registerJs($onload); }

$js =<<<JS
 
    // $('.show-doc').click(function (e) {
    //     var id = $(this).closest('tr').data('key');
    //     location.href = 'index.php?r=SaleOrders/saleorder/view&id='+id;        
    // });

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
    $('body').on('click','.actions-more',function(e){    

        var key = $(this).closest('tr').attr('data-key');

        $(this).children('#actions'+key+'').remove();
        var template = '<div class="actions" id="actions'+key+'" data-key="'+ key +'">'+
                            '<a href="javascript:void(0);" class="more"   ><i class="fas fa-ellipsis-h"></i>  <p> {$Yii::t("common","More")}</p>    </a>'+
                            '<a href="javascript:void(0);" class="delete" ><i class="far fa-trash-alt"></i><p>   {$Yii::t("common","Delete")}</p>      </a>'+
                            '<a href="javascript:void(0);" class="cancel" ><i class="fas fa-power-off"></i><p> {$Yii::t("common","Close")}</p>       </a>'+
                        '</div>';
        $(this).closest('td').find('.show-doc').prepend(template);

        $('#actions'+key+'').toggle("slide", { direction: "right" }, 500);

        $('#actions'+key+' a').rippleria();
    })

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

