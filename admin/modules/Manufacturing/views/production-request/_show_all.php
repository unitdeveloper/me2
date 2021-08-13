
<div class="modal fade modal-full" id="modal-show-all">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Production Order List')?></h4>
            </div>
            <div class="modal-body">
                <div class="render font-roboto" ></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                 
            </div>
        </div>
    </div>
</div>




<?php
$Yii = 'Yii';
$js =<<<JS
  
    const renderAll = (obj, callback) => {
        let tbody = ``;

        obj.raws.map((model, i) => {
            let status = model.status == 1 
                            ? `<i class="fas fa-cubes"></i>`
                            : `<i class="fas fa-hourglass-half text-aqua"></i>`;

            let cons   = model.cons == 1
                            ? `<div class="text-green"><i class="far fa-check-circle"></i> ตัดวัตถุดิบแล้ว</div>`
                            : `<div class="text-red">
                                    <a href="#" class="btn btn-primary btn-cutting-consumption">
                                        <i class="fas fa-sitemap text-danger"></i> {$Yii::t('common','Not yet cut stock')}
                                    </a>
                                </div>`;

            tbody+=`
                <tr data-key="` +model.id+ `">
                    <td class="text-center">`+ (i+1) +`</td>
                    <td class="text-center">`+ model.date +`</td>
                    <td class="text-center">`+ status +`</td>
                    <td><a href="?r=Manufacturing%2Fproduction-request%2Fupdate&id=`+model.id+`" target="_blank">`+ model.no +`</a></td>
                    <td>`+ model.itemName +`</td>
                    <td class="text-right bg-yellow">`+ number_format(model.qty, 2) +`</td>
                    <td class="text-right">`+ number_format(model.inv, 2) +`</td>         
                    <td class="text-center">`+ cons +`</td>         
                    <td class="text-center" style="width: 140px;"> 
                        <a href="#" class="btn btn-primary btn-cutting-produce">
                            <i class="fab fa-codepen text-yellow"></i> {$Yii::t('common','Produce')}
                        </a>
                    </td>                 
                    <td class="text-center"><i class="fas fa-trash text-red pointer btn-delete-line-production"></i></td>   
                </tr>
            `;
        });

        let html = `<table class="table table-bordered"> 
                    <thead>
                        <tr class="bg-gray">
                            <th class="text-center" style="width:50px;">#</th>
                            <th style="width:100px;">{$Yii::t('common','Date')}</th>
                            <th class="text-center" style="width: 82px;"><i class="fab fa-codepen"></i> {$Yii::t('common','Status')}</th>
                            <th style="width: 120px;">{$Yii::t('common','No')}</th>
                            <th>{$Yii::t('common','Item')}</th>
                            <th class="text-right bg-warning" style="width: 100px;"> {$Yii::t('common','Quantity')}</th>
                            <th class="text-right" style="width: 100px;"> {$Yii::t('common','Stock')}</th>
                            <th class="text-center" style="width: 140px;"><i class="fas fa-sitemap"></i>  {$Yii::t('common','Cutting raw materials')}</th>         
                            <th class="text-center" style="width: 140px;"><i class="fab fa-codepen"></i> {$Yii::t('common','Produce')}</th>                             
                            <th class="text-center" style="width:50px;"> {$Yii::t('common','Delete')}</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        `+tbody+`
                    </tbody>
                </table>`;


        callback({html:html});
    }



    const getPdrList = (callback) => {
        fetch("?r=Manufacturing/production-request/get-production", {
                method: "POST",
                body: JSON.stringify({limit:'true'}),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                }
        })
        .then(res => res.json())
        .then(response => {            
            callback(response);
            $('.loading-div').hide();
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', '.btn-show', function(){        

        getPdrList(res => {
            renderAll(res, response => {
                $('#modal-show-all').modal('show');
                $('#modal-show-all .modal-body .render').html(response.html);
            });
        });
        
    });

    const deleteLineProduction  = (obj, callback) => {
        fetch("?r=Manufacturing/production-request/delete-production", {
                method: "POST",
                body: JSON.stringify(obj),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                }
        })
        .then(res => res.json())
        .then(response => {            
            callback(response);
            $('.loading-div').hide();
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', '.btn-delete-line-production', function(){
        let el = $(this);
        let id = $(this).closest('tr').attr('data-key');
        if(confirm("Delete ?")){
            deleteLineProduction({id:id}, res =>{
                if(res.status==200){
                    el.closest('tr').remove();
                }            
            })
        }
    })


    const cutConsumption = (obj, callback) => {
        $('.loading-div').show();
        fetch("?r=Manufacturing/production-request/cutting-consumption", {
                method: "POST",
                body: JSON.stringify(obj),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                }
        })
        .then(res => res.json())
        .then(response => {            
            callback(response);
            $('.loading-div').hide();
        })
        .catch(error => {
            console.log(error);
        });
    }

    const outputCraft = (obj, callback) => {
        $('.loading-div').show();
        fetch("?r=Manufacturing/production-request/output-craft", {
                method: "POST",
                body: JSON.stringify(obj),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                }
        })
        .then(res => res.json())
        .then(response => {            
            callback(response);
            $('.loading-div').hide();
        })
        .catch(error => {
            console.log(error);
        });
    }

    
    $('body').on('click', '.btn-cutting-consumption', function(){  // In Line
        let id   = $(this).closest('tr').attr('data-key');
        if(confirm("Cut Stock ?")){       
            cutConsumption({id:id}, res => {
                
            })
        }

    });

    $('body').on('click', '.btn-cutting-consumption-file', function(){ // In Document
        let id   =  parseInt("{$id}");
        if(confirm("Cut Stock ?")){       
            cutConsumption({id:id}, res => {
                
            });
        }
    });


    $('body').on('click', '.btn-cutting-produce', function(){
        let id   = $(this).closest('tr').attr('data-key');
        if(confirm("Make Produce ?")){
            outputCraft({id:id}, res => {
                
            });
        }
    });

    $('body').on('click', '.btn-cutting-produce-file', function(){
        let id   = parseInt("{$id}");
        if(confirm("Make Produce ?")){
            outputCraft({id:id}, res => {
                
            });
        }
    });

    

JS;

$this->registerJS($js);


?>
