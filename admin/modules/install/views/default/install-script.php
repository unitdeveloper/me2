
<?PHP
$Yii = 'Yii';
$jsx =<<<JS

    function autoInstall(obj){
        $.ajaxSetup({
            headers:
            { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var offset = 0;
        var count   = Object.keys(obj.data).length,
            chunk   = Math.round(100 / count),
            progress= 0,
            prog_sec= 0,
            errors = 0,
            percent_number_step = $.animateNumber.numberStepFactories.append(' %');

        $('body').find('div.installing-bar').attr('style','width: '+ (chunk / 2)  +'%;')
        .animateNumber({ number:(chunk / 2) ,numberStep: percent_number_step });
 

        $.each(obj.data,function(modules){
            
            
            setTimeout(function(){
                
                progress += chunk;
            
                $.ajax({
                    url:'?r=install/default/install',
                    data: {modules},
                    type:'POST',
                    dataType:'JSON',
                    async:false,  
                    success:function(response){

                        $('body').find('div.installing-bar').attr('style','width:'+ progress +'%;')
                        .animateNumber({ number:progress ,numberStep: percent_number_step });

                        $.each(response.value.module.message,function(key,install){
                            
                            if(install.status==200){
                                
                                

                                var template = '<div class="row">'+
                                                    '<div class="col-xs-8"><i class="fas fa-download text-success"></i> '+install.name+'</div><div class="col-xs-4"><i class="fas fa-check-circle text-success"></i> Install Success.</div>'+
                                                '</div>';
                                                                        
                                $('body').find('div.process-start').prepend(template);
                               
                                
                            }else if(install.status==500){
                                errors += 1;
                                var template = '<div class="row">'+
                                                    '<div class="col-xs-8 text-orange"><i class="fas fa-exclamation-triangle"></i> '+install.name+'</div><div class="col-xs-4 text-orange"><i class="fas fa-times "></i> Install Fail '+install.message+'</div>'+
                                                '</div>';
                                $('body').find('div.process-start').prepend(template);
                               

                            }else{
                                 
                                var template = '<div class="row">'+
                                                    '<div class="col-xs-8 text-warning"><i class="fas fa-database"></i> '+install.name+'</div><div class="col-xs-4"><i class="far fa-check-circle text-success"></i> Installed</div>'+
                                                '</div>';
                                $('body').find('div.process-start').prepend(template);
                                
                            }

                        });
                            
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        errors += 1;
                        var template = '<div class="row">'+
                                            '<div class="col-sm-8 text-danger"><i class="fas fa-exclamation-triangle"></i> '+modules+'</div><div class="col-sm-4 text-danger blink"><i class="fas fa-exclamation-circle"></i> '+errorThrown+'</div>'+
                                        '</div>';
                        $('body').find('div.process-start').prepend(template);
                    } 
                            
                });

                if (progress===100){
                    
                    if (errors > 0){

                        //window.location.href = "?r=install/default";

                    }else{
                        setTimeout(() => {
                            $('body').find('#load-icon').attr('class','fas fa-check text-success pull-right');                    
                            $('body').find('#text-status').html('การติดตั้งเสร็จสมบูรณ์').attr('class','text-success');
                        
                            swal({
                                title: '{$Yii::t("common","Success")}',
                                text: "การติดตั้งเสร็จสมบูรณ์ คุณต้องการใช้งานระบบตอนนี้หรือไม่",
                                type: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: '{$Yii::t("common","Open App Now")}'
                            }).then((result) => {
                                console.log(result);
                                if (result) {
                                    window.location.href = "?r=";
                                    // swal(
                                    // 'Deleted!',
                                    // 'Your file has been deleted.',
                                    // 'success'
                                    // )
                                }
                            })

                        }, 800);
                    }

                }
                
                console.log(errors);

            }, 1000 + offset);    
            offset += 1000;

            
        });
        
        
    }


JS;


$this->registerJs($jsx);
/*

 ¯\_(ツ)_/¯
     -
    |-|

*/