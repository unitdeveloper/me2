<?php 
use Yii\helpers\Html;


?>
<!-- <?= __FILE__ ?> -->
<style>
    .install-default-index > ul > li{
        margin-top:5px;
    }
</style>
<div class="row">
    <section class="content-header">
        <h1><?=Yii::t('common','Installation')?> <small>ติดตั้งระบบพื้นฐาน</small></h1>
            <?php 
            echo yii\widgets\Breadcrumbs::widget([
                'itemTemplate' => "<li><b>{link}</b></li>\n", // template for all links
                'links' => [
                    ['label' => 'Configuration', 'url' => ['/config/default'],'template' => "<li><b>{link}</b></li>\n"],
                    'Installation',
                ],
            ]);
            ?>
    </section>
</div>

<br><br>

<div class="modal fade" id="modal-install" >
    <div class="modal-dialog" style="width:95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Installing')?> . . .</h4>
            </div>
            <div class="modal-body" style="height:75vh;">
                <h2 id="text-status">กำลังติดตั้งระบบ กรุณารอสักครู่ </h2>
                <div class="progress active">
                    <div class="progress-bar progress-bar-primary progress-bar-striped installing-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" 
                    style="width: 0%;" >
                    <span class="sr-only">40% Complete (success)</span>
                    </div>
                </div>
                
                <div class="panel panel-default">
                      <div class="panel-heading">
                            <h3 class="panel-title"><?=Yii::t('common','Process')?> <i class="fas fa-sync-alt fa-spin pull-right" id="load-icon"></i></h3>
                      </div>
                      <div class="panel-body process-start" style="height: 300px;overflow-y: scroll;" >
                            
                      </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common','Close')?></button>
            </div>
        </div>
    </div>
</div>

<div class="install-default-index">
    
    <p>
        <b>เริ่มต้นการใช้งาน จำเป็นต้องติดตั้งระบบพื้นฐานดังนี้</b>
            <ul>
                <li>สิทธิ์การเข้าใช้งาน </li>
                <li>
                    ระบบคิดภาษี
                    <button class="btn btn-danger btn-xs " id="UnInstall" data-module="vat">
                        <span  id="uninstall-loading" ><i class="fab fa-gripfire" style="opacity:0.5;"></i></span>
                        <span id="text-uninstall"><?=Yii::t('common','UnInstall')?></span>
                    </button>   
                </li>
                <li>
                    การรันเลขที่เอกสาร 
                    <button class="btn btn-danger btn-xs " id="UnInstall" data-module="series">
                        <span  id="uninstall-loading" ><i class="fab fa-gripfire" style="opacity:0.5;"></i></span>
                        <span id="text-uninstall"><?=Yii::t('common','UnInstall')?></span>
                    </button>   
                </li>
                <li>กลุ่มของสินค้า</li>
            </ul>
    </p>
    <p>
        คลิกติดตั้ง เมื่อต้องการติดตั้งระบบพื็นฐาน<br>
    </p>
    
    <div class="panel panel-default">
        <div class="panel-body">
            <button class="btn btn-primary" id="Install">
            <span  id="loading" ><i class="fas fa-refresh fa-spin" style="opacity:0.5;"></i></span>
            <span id="text-install"><?=Yii::t('common','Install')?></span></button>   
            

            <?=Html::a('<i class="fas fa-home"></i> '.Yii::t('common','Go to Home page'),['/site/index'],[
                'class' => 'btn btn-success go-home',
                'style' => 'display:none;'
                ]) ?>
           
            
            <br><br>
                
        </div>
    </div>
    
</div>
<?PHP 
$this->registerJsFile('@web/js/jquery.animateNumber.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
?>
<?=$this->render('install-script'); ?>
<?PHP
$Yii = 'Yii';
$js =<<<JS
    $(document).ready(function(){
        $('#loading').html('<i class="fas fa-download"></i>');
        $('#modal-install').modal(open);        
        setTimeout(() => {
            autoInstall({
                data:{  
                    rules:true,
                    default:true,              
                    vat:true,
                    series:true,
                    itemgroup:true
                }
            });
        }, 10000);
        
    });

    $('body').on('click','#Install',function(){

        if(confirm('Are you sure(ติดตั้ง)?')){
            var el = $(this);
            el.attr('disabled','disabled');
            $('#loading').html('<i class="fas fa-refresh fa-spin" style="opacity:0.5;"></i>');
            $('#text-install').html('{$Yii::t("common","Installing")}');
            var modules = {
                el:el,
                data:{  
                    rules:true,
                    default:true,              
                    vat:true,
                    series:true,
                    itemgroup:true
                }
                
            };
            doInstall(modules);
        }
    
    });

    function doInstall(obj){
        $.ajaxSetup({
            headers:
            { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.each(obj.data,function(modules){
            
            $.ajax({
                url:'?r=install/default/install',
                data: {modules},
                type:'POST',
                dataType:'JSON',
                async:false,  
                success:function(response){
                        obj.el.closest('div').append('<hr>');
                        $.each(response.value.module.message,function(key,install){
                             
                            if(install.status==200){
                                //setTimeout(function(){
                                    $('#loading').html('<i class="fas fa-check-square"></i>');         
                                    var template = '<div class="row">'+
                                                        '<div class="col-xs-8"><i class="fas fa-download text-success"></i> '+install.name+'</div><div class="col-xs-4"><i class="fas fa-check-circle text-success"></i> Install Success.</div>'+
                                                    '</div>';
                                                                               
                                    obj.el.closest('div').append(template);
                                    // $('#text-install').html('{$Yii::t("common","Installed")}');
                                    // $('#text-install').closest('button').attr('class','btn btn-success');
                                    $('#Install').hide();
                                    $('.go-home').show();
                                //}, 1000);
                            }else if(install.status==500){
                                //setTimeout(function(){
                                    $('#loading').html('<i class="fas fa-check-square"></i>');     
                                    
                                    var template = '<div class="row">'+
                                                        '<div class="col-xs-8 text-orange"><i class="fas fa-exclamation-triangle"></i> '+install.name+'</div><div class="col-xs-4 text-orange"><i class="fas fa-times "></i> Install Fail '+install.message+'</div>'+
                                                    '</div>';
                                    obj.el.closest('div').append(template);
                                    $('#text-install').html('{$Yii::t("common","Error")}');
                                    $('#text-install').closest('button').attr('class','btn btn-success');
                                    $('.go-home').hide();
                                //}, 1000);
                            }else{
                                //setTimeout(function(){
                                    $('#loading').html('<i class="fas fa-check-square"></i>');     
                                    
                                    var template = '<div class="row">'+
                                                        '<div class="col-xs-8 text-warning"><i class="fas fa-database"></i> '+install.name+'</div><div class="col-xs-4"><i class="far fa-check-circle text-success"></i> Installed</div>'+
                                                    '</div>';
                                    obj.el.closest('div').append(template);
                                    // $('#text-install').html('{$Yii::t("common","Installed")}');
                                    // $('#text-install').closest('button').attr('class','btn btn-success');
                                    $('#Install').hide();
                                    $('.go-home').show();
                                //}, 1000);
                            }

                        });
                        
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    var template = '<div class="row">'+
                                        '<div class="col-sm-8 text-danger"><i class="fas fa-exclamation-triangle"></i> '+modules+'</div><div class="col-sm-4 text-danger blink"><i class="fas fa-exclamation-circle"></i> '+errorThrown+'</div>'+
                                    '</div>';
                    obj.el.closest('div').append(template);
                } 
                        
            });

            
                
        });
        
        
    }




    // Uninstall

    $('body').on('click','#UnInstall',function(){

        var el = $(this);   
        var fn = el.data('module');
        if(confirm('Do you want to remove(ลบ) "'+fn+'" ?')){
            
            $('#uninstall-loading').html('<i class="fas fa-refresh fa-spin" style="opacity:0.5;"></i>');
    
            var modules = {
                el:el,
                data:{  
                    fn:fn,
                }
                
            };
          
            doUnInstall(modules);
        
        }
    });

    function doUnInstall(obj){

        $.ajaxSetup({
            headers:
            { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.each(obj.data,function(key,modules){
            
            $.ajax({
                url:'?r=install/default/uninstall',
                data: {modules},
                type:'POST',
                dataType:'JSON',
                async:false,  
                success:function(response){
                        obj.el.closest('div').append('<hr>');
                        $.each(response.value.module.message,function(key,install){
                             
                            if(install.status==200){
                                    $('#loading').html('<i class="fas fa-check-square"></i>');         
                                    var template = '<div class="row">'+
                                                        '<div class="col-xs-8"><i class="fas fa-download text-success"></i> '+install.name+'</div><div class="col-xs-4"><i class="fas fa-check-circle text-success"></i> UnInstall Success.</div>'+
                                                    '</div>';             
                                    obj.el.closest('div').append(template);
                                    $('#uninstall-loading').html('<i class="fab fa-gripfire" style="opacity:0.5;"></i>');
                            }else if(install.status==500){
                                    $('#loading').html('<i class="fas fa-check-square"></i>');     
                                    var template = '<div class="row">'+
                                                        '<div class="col-xs-8 text-orange"><i class="fas fa-exclamation-triangle"></i> '+install.name+'</div><div class="col-xs-4 text-orange"><i class="fas fa-times "></i> UnInstall Fail '+install.message+'</div>'+
                                                    '</div>';
                                    obj.el.closest('div').append(template);
                                    $('#text-uninstall').html('{$Yii::t("common","Error")}');                                
                                     
                                    $('#uninstall-loading').html('<i class="fab fa-gripfire" style="opacity:0.5;"></i>');
                            }else{
                                    $('#loading').html('<i class="fas fa-check-square"></i>');     
                                    
                                    var template = '<div class="row">'+
                                                        '<div class="col-xs-8 text-warning"><i class="fas fa-database"></i> '+install.name+'</div><div class="col-xs-4"><i class="fas fa-ban text-danger"></i> Removed</div>'+
                                                    '</div>';
                                    obj.el.closest('div').append(template);
                                    $('#uninstall-loading').html('<i class="fab fa-gripfire" style="opacity:0.5;"></i>');
                            }
                        });
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    var template = '<div class="row">'+
                                        '<div class="col-sm-8 text-danger"><i class="fas fa-exclamation-triangle"></i> '+modules+'</div><div class="col-sm-4 text-danger blink"><i class="fas fa-exclamation-circle"></i> '+errorThrown+'</div>'+
                                    '</div>';
                    obj.el.closest('div').append(template);
                } 
            });
        });
    }
JS;
$this->registerJS($js);
 
?>