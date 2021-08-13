<style> 
.content-wrapper{
    background-image:url('images/keyboard-white-hd.png');
    background-size: cover;   
}
.img-enabled{
     color:#5f82d6;
}
.img-enabled:hover{
    cursor:pointer;
    -webkit-filter: drop-shadow(5px 5px 5px #ccc );
    filter: drop-shadow(5px 5px 5px #ccc);
}
.img-enabled:active{
     margin-top:-1px;
}
.img-disabled{
    -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
    filter: grayscale(100%);
    cursor:not-allowed;    
}
.img-disabled > h4{
    color:#5f82d6 !important;
}
.menu-top{
    height:50px;
    background-color:#f9f9f9;
    margin:-15px 0 5px 0;
    border-bottom:1px solid #e0e0e0;
}
.menu-top > .menu-bread{
    padding:15px  0 0 15px;
}
.menu-top > .menu-bread > h4{
    margin:2px  0 0 5px;
}

</style>
<div class="set-cash">
    <div class="row">
        <div class="menu-top">
            <div class="menu-bread">
                <h4 style="float:left; margin-right:15px;"><?=Yii::t('common','Select Branch')?></h4>
                <select >
                    <option value="0000"><?=Yii::t('common','Head Office')?></option>
                    <option value="0001" disabled="disabled"><?=Yii::t('common','Branch')?>-01</option>
                </select>
            </div>   
        </div>
    </div>
    <div class="row" style="margin-top:70px;">
        <div class="col-sm-2 "></div>
        <div class="col-sm-2 "></div>
        <div class="col-sm-2 pick-cash img-enabled" data-key="101"  ><img src="images/icon/ewinl-pos.png" class="img-responsive " ><h4 class="text-center">CASH-01</h4></div>
        <div class="col-sm-2 pick-cash img-enabled"  data-key="102"><img src="images/icon/ewinl-pos.png" class="img-responsive"><h4 class="text-center">CASH-02</h4></div>
        <div class="col-sm-2 pick-cash"></div>
    </div>
    <div class="row margin-top">
        <div class="col-sm-2 "></div>
        <div class="col-sm-2 img-disabled"  data-key="103"><img src="images/icon/ewinl-pos.png" class="img-responsive"><h4 class="text-center">CASH-03</h4></div>
        <div class="col-sm-2 img-disabled"  data-key="104"><img src="images/icon/ewinl-pos.png" class="img-responsive"><h4 class="text-center">CASH-04</h4></div>
        <div class="col-sm-2 img-disabled"  data-key="105"><img src="images/icon/ewinl-pos.png" class="img-responsive"><h4 class="text-center">CASH-05</h4></div>
        <div class="col-sm-2 img-disabled"  data-key="106"><img src="images/icon/ewinl-pos.png" class="img-responsive"><h4 class="text-center">CASH-06</h4></div>
    </div>
</div>






<?php
    $options = ['depends' => [\yii\web\JqueryAsset::className()]];

    $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js',$options);
    $this->registerJsFile('@web/js/jquery.rippleria.min.js',$options);
?>

<?php
 
$js=<<<JS
//----Shack------
    $('.img-disabled').on('click',function(){
        //$(this).effect( "shake", {times:2}, 1000 );
        $(this).shake(); 
    })

    jQuery.fn.shake = function(intShakes, intDistance, intDuration) {
        
        intShakes = intShakes || 2;
        intDistance = intDistance || 1;
        intDuration = intDuration || 300;

        this.each(function() {
            $(this).css("position","relative"); 
            for (var x=1; x<=intShakes; x++) {
                $(this).animate({left:(intDistance*-1)}, (((intDuration/intShakes)/4)))
                .animate({left:intDistance}, ((intDuration/intShakes)/2))
                .animate({left:0}, (((intDuration/intShakes)/4)));
            }
        });
        return this;
    };
//----/.Shack------


$('body').on('click','.pick-cash',function(){
     
    var id      = $(this).data('key');

    $.ajax({
        url:'index.php?r=SaleOrders/event/cashier&id='+id,
        data:{id:id},
        type:'POST',
        dataType:'JSON',
        success:function(response){
            console.log(response);
            if(response.status==200){
                window.location.href="index.php?r=SaleOrders/event/create";
            }else {
                $.notify({
                    // options
                    message: response.message
                },{
                    // settings
                    type: 'warning',
                    delay: 5000,
                });
            }
        }
    });
});

JS;
$this->registerJS($js);
?>