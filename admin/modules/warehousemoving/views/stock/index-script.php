<?php
$Yii = 'Yii';
$Uri = Yii::getAlias('@web');
$digit_stock = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
 
$js=<<<JS

let digit_stock = parseInt('{$digit_stock}');

$(document).ready(function() {
    setTimeout(() => {
        $("body")
            .addClass("sidebar-collapse")
            .find(".user-panel")
            .hide();        
    }, 100);
    setTimeout(() => { 
          $('body').find('.user-panel').hide(); 
      }, 1500);
    localStorage.removeItem('buffers');
});

const formatDate = (date) => {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return date.getMonth()+1 + "/" + date.getDate() + "/" + date.getFullYear() + "  " + strTime;
}


$('body').on('click',function(e){
    if(($(e.target).attr('id')!=='itemgroup-menu') || $(e.target).attr('id')!=='modal-loading'){
        if($(e.target.offsetParent).attr('id')!=='popup-list'){
            $('body').find('.popup-list').remove();
        } 
    } 
})



let getData = async (id,calback) => {
    
    await fetch("?r=warehousemoving/stock/get-item-group", {
        method: "POST",
        body: JSON.stringify({id:id}),
        async: false,
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(response => response.json())
    .then(obj => {            
        calback(obj);
    })
    .catch(error => {
        console.log(error);
    });

    
}

let child = obj => {
    let html = '';
    obj.map(model => {
        html += '<li> ↳ <span id="count"> </span><a href="javascript:void(0)" class="click-item-in-group" data-key="'+model.id+'"  data-name="'+model.name_en+'"  >'+model.name_en+'</a>';
        if(model.child.length > 0){
            html += '<ul>';
            html += child(model.child)
            html += '</ul>';
        } 
        html += '</li>';        
    })
    return html;
}


$('body').on('click','.itemgroup-menu',function(){

    let id = $(this).attr('data-id');  
    let el = $(this);
    let renders =  '';
    
    $('#modal-loading').modal('show');        

    getData(id,res => {
        $('body').find('.popup-list').remove();
        res.length > 0 ? 
        res.map(model => {
            renders += '<div class="col-xs-6" id="child">';
            if(model.child.length > 0){
                
                renders += '<li ><span id="count"> </span><a href="javascript:void(0)" style="color:#999;" data-key="'+model.id+'"   data-name="'+model.name_en+'" >'+model.name_en+'</a>';
                renders += '<ul class="child-menu">';
                renders += child(model.child);
                renders += '</ul>';
            }else{      
                renders += '<li ><span id="count"> </span><a href="javascript:void(0)" class="click-item-in-group" data-key="'+model.id+'" data-name="'+model.name_en+'"  >'+model.name_en+'</a>';
            }
                renders += '</li>';   
            renders += '</div>';          
        }) :  '';

        var html = '<div class="text-left popup-list" id="popup-list" >'+
                        '<ul class="menu row">'+
                            renders+                       
                        '</ul>'+
                    '</div>';
    
        if(renders!==''){
            $(this).after(html);
            $('body').find('.popup-list').hide();
            $('body').find('.popup-list').slideDown('fast');
            $('#modal-loading').modal('hide');
        }

    });

})

$('body').on('click','.click-item-in-group, a#reload-data',function(){
    let id = $(this).attr('data-key');
    let name = $(this).attr('data-name');
    let refresh = $(this).attr('data-refresh');
    
    $('body').find('#renders-panel-body').html('<div class="panel-body text-center"><i class="fas fa-redo-alt fa-2x fa-spin"></i></div>');
    $('body').find('#renders-header').html("<h3>"+name+"</h3>");    
    $('body').find('#renders-panel-heading').html('<h3 class="panel-title">'+name+'</h3><a type="text" href="javascript:void(0)" data-key="'+id+'" data-refresh="true" data-name="'+name+'" class="pull-right text-white" style="margin-top:-15px;" id="reload-data"><i class="fas fa-sync-alt"></i> {$Yii::t("common","Reload")}</a>');
    $('body').find('#text-remark').fadeIn();
    $('body').find('.active-bottom').slideDown('fast'); // ปุ่ม print & save


    let renderTable = (response) => {
        let rows = ``;
        response.map((model,key) => {    
            let name        = (model.alias != null && model.alias != '')
                                ? ` <div  title="` + model.name + `"  data-toggle="tooltip">
                                        <div> ` + model.alias + ` </div>
                                        <small class="text-gray"> ` + model.name + ` </small>
                                    </div>`
                                : ` <div  title="` + model.name + `"  data-toggle="tooltip"><div> ` + model.name + ` </div> </div>`;

            let stock       = model.inven;
            let quantity    = `<a href="?r=warehousemoving%2Fwarehouse&WarehouseSearch[ItemId]=` + btoa(model.id) + `" target="_blank">` + number_format(stock.toFixed(digit_stock)) + `</a>`;    

            rows+=`<tr key="` + key + `" data-key="` + model.id + `" class="data-tr">
                        <td width="50" class="hidden-xs">` + (key + 1) + `</td>
                        <td>
                            <a href="index.php?r=items/items/view&id=` + model.id + `" target="_blank" title="` + model.code + `">
                                <img class="pull-left" style="width:30px; margin-right:5px;" src="` + model.photo + `" />
                            </a>
                            ` + name + `
                        </td>
                        <td class="text-right font-roboto inven" data-val="` + model.inven + `">` + quantity + `</td>
                        <td class="data"><input type="text" name="diff" key="` + model.id + `"  class="input-change form-control text-right numbers" value="` + model.diff + `" autocomplete="off" /></td>
                        <td class="data"><input type="text" name="remain" key="` + model.id + `" class="input-change form-control text-right numbers" value="` + model.remain + `" autocomplete="off" /></td>
                    </tr>`;                            
        });

        let html = `<table class="table table-bordered" id="render-table" style="display:none">
                        <thead>
                            <tr class="bg-gray">
                                <th class="hidden-xs">#</th>
                                <th style="min-width:350px;">{$Yii::t("common","Items")}</th>
                                <th class="text-center" width="80">{$Yii::t("common","Stock")}</th>
                                <th class="text-center" style="min-width: 100px; max-width: 101px;">+/-</th>
                                <th class="text-center" style="min-width: 100px; max-width: 101px;">{$Yii::t("common","After adjust")}</th>
                            </tr>
                        </thead>
                        <tbody id="data-tbody" group-id="` + id + `">
                            ` + rows + `
                        </tbody>
                    </table>`;

        return html;
    }

    let getNewData = (obj,act) =>{
        let id = obj.id
        fetch("?r=warehousemoving/stock/get-item-list", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            let d = new Date();
            if(act!==null){                
                let merge = Object.assign({},act,{ [id] : { 
                    'data': response ,
                    'time': formatDate(d)
                }});                 
                localStorage.setItem("buffers",JSON.stringify(merge));
            }else{
                localStorage.setItem("buffers",JSON.stringify({ [id] : { 
                    'data': response,
                    'time': formatDate(d)
                 }}));
            }

            $('body').find('#renders-panel-body').html(renderTable(response));
            $('body').find('#render-table').fadeIn('slow');
            $('body').find('[data-toggle="tooltip"]').tooltip(); 
            location.hash = "#renders-header";
            $('body').find('a.add-to-group').attr('href','?r=warehousemoving/stock-report/add-item&id='+id);
            $('body').find(".panel-title").html($('body').find('#renders-header h3').text() + ' [' + response[0].workdate + ']');
            $("body").find('input[name="diff"]').first().focus(); 
        })
        .catch(error => {
            console.log(error);
        });
    }

    
        
    
    if(localStorage.getItem('buffers')){
        let list = JSON.parse(localStorage.getItem('buffers'));
        let keys    = Object.keys(list);       

        let filter = [];
                            
        Object.values(list).map(group => {          // นำข้อมูลทั้งหมดมาวน
            group.data.map(model => {               // นำเฉพาะ data มาวนอีกรอบ
                model.group_id === parseInt(id) ?   // ถ้าเจอกลุ่มที่คลิก   
                    filter.push(model)              // สร้าง array ชุดใหม่ 
                : null;
            });
        })

        if(keys.indexOf(id) === -1){ // Already exist
            getNewData({id:id},list);
            
        }else {
            if(refresh){ // ถ้าคลิก refresh ให้ดึงข้อมูลจาก api อีกรอบ
                if(confirm("ข้อมูลที่มีการเปลี่่ยนแปลง/แก้ไข จะลูกลบ! คุณต้องการดำเนินการต่อหรือไม่?")){
                    getNewData({id:id, force:'true'},null);
                }else{                    
                
                    $('body').find('#renders-panel-body').html(renderTable(filter));
                    $('body').find('#render-table').fadeIn('slow');
                    $('body').find('[data-toggle="tooltip"]').tooltip(); 
                    location.hash = "#renders-header";
                    $('body').find('a.add-to-group').attr('href','?r=warehousemoving/stock-report/add-item&id='+id);
                }
            }else{
                $('body').find('#renders-panel-body').html(renderTable(filter));
                $('body').find('#render-table').fadeIn('slow');
                $('body').find('[data-toggle="tooltip"]').tooltip(); 
                location.hash = "#renders-header";
                $('body').find('a.add-to-group').attr('href','?r=warehousemoving/stock-report/add-item&id='+id);
            }
            $('body').find(".panel-title").html($('body').find('#renders-header h3').text() + ' [' + filter[0].workdate + ']');
        }
        

    }else {       
        getNewData({id:id},null);
    }
     

    

    
})


let formData = () => {
    let data = $('.data-tr').map(function(){
                if(parseInt($(this).find('input[name="diff"]').val())){
                    return {
                        'id':$(this).attr('data-key'),
                        'diff': $(this).find('input[name="diff"]').val(),
                        'remain': $(this).find('input[name="remain"]').val(),
                    }
                }else{
                    // ถ้าข้อมูลไม่มีการเปลี่ยนแปลง(ไม่ส่งไปแก้ไข) จะแสดง background #ccc;
                    let key = $(this).attr('data-key');
                    $('body').find('tr[data-key="'+key+'"]').attr('style','background-color:#ccc;');     
                }
        }).get();

    return data;
}


let postData = (postDate) => {

    let groupId     = $('body').find('#data-tbody').attr('group-id');
    let remark      = $('body').find('textarea#remark').val();
    let inspector   = $('body').find('input.inspector').val();
    let timeStamp   = $('body').find('input[name="document-time"]').val();

    // var formData= $('.data-tr').map(function(){
    //             if(parseInt($(this).find('input[name="remain"]').val())){
    //                 return {
    //                     'id':$(this).attr('data-key'),
    //                     'diff': $(this).find('input[name="diff"]').val(),
    //                     'remain': $(this).find('input[name="remain"]').val(),
    //                 }
    //             }else{
    //                 // ถ้าข้อมูลไม่มีการเปลี่ยนแปลง(ไม่ส่งไปแก้ไข) จะแสดง background #ccc;
    //                 let key = $(this).attr('data-key');
    //                 $('body').find('tr[data-key="'+key+'"]').attr('style','background-color:#ccc;');     
    //             }
    //     }).get();


    let data =  { [groupId] : { 'data': formData() }};
    
    localStorage.setItem("form",JSON.stringify(data));

    if(formData().length > 0){
        $('#modal-loading').modal('show');      
        fetch("?r=warehousemoving/stock/post-update", {
            method: "POST",
            body: JSON.stringify({
                json:localStorage.getItem('form'),
                postDate:postDate,
                groupId:groupId,
                remark:remark,
                inspector:inspector,
                times:timeStamp
            }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            
            // Update Buffers
            let loadData = JSON.parse(localStorage.getItem('buffers'));     // ดึง buffer เพื่อลบข้อมูล                
            const allowed = [response.groupId];
            Object.keys(loadData)
            .filter(key => allowed.includes(key))
            .forEach(key => delete loadData[key]);            
            localStorage.setItem("buffers",JSON.stringify(loadData));

            // Clear text box
            const promises = response.data.map((model, i) =>
                new Promise(resolve =>
                    setTimeout(() => {
                        $('tr[data-key="'+model.id+'"]').find('td.inven').attr('data-val',model.inven).html(number_format(model.inven));
                        $('tr[data-key="'+model.id+'"]').find('input').val('');
                        if(model.status===1){
                            $('body').find('tr[data-key="'+model.id+'"]').attr('style','background-color:#d0ffdc;');                        
                        }else{
                            $('body').find('tr[data-key="'+model.id+'"]').attr('style','background-color:#fdd9eb');
                        }

                        resolve()
                    }, 500 * response.data.length - 500 * i)

                
                )

            )
            Promise.all(promises).then(() => {
                // console.log('done');
                // Show Print resault
                setTimeout(() => {
                    window.open('{$Uri}/index.php?r=warehousemoving/stock/print-report&id='+ response.id +'&group=' + groupId, '_blank');
                }, 1000);
                
            });

            // Clear 'formdata' from storage
            localStorage.removeItem('form');

            $('#modal-loading').modal('hide');      
        })
        .catch(error => {
        console.log(error);
        });
    }else{
        alert('No Data');
    }
   
}


$('body').on('click','.click-btn-save, .btn-back-to-date',function(){

    if(formData().length <= 0){
        alert('No Data');
    }else{
        // ยกเลิกการแก้ไขวันที่ 13/01/2020 (เอากลับมาเหมือนเดิม พี่หมีต้องการทำย้อนหลัง 29/01/63)
        $('#modal-pick-date').modal('show');
        $('#modal-inspector').modal('hide');

        // $('#modal-inspector').modal('show');
        // $('#modal-pick-date').modal('hide');
        setTimeout(() => {
            $('input.inspector').focus();
        }, 500);
       
    }

    $('a.btn-back-to-date').hide();
    
    var today   = new Date(); 
    var time    = ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2) + ":" + ('0' + today.getSeconds()).slice(-2);     
    $('input[name="document-time"]').val(time);
    
})

$('body').on('click','.btn-save-adjust',function() {

    let postDate = $('input[name="posting_date"]').val();
    $('#modal-pick-date').modal('hide');
    $('#modal-inspector').modal('show');
    setTimeout(() => {
        $('body').find('input.inspector').focus();
    }, 500);
    

    $('body').find('#modal-inspector').find('#date').html(postDate);
    
    
})

$('body').on('click','.btn-save-inspector',function() {
    let postDate = $('input[name="posting_date"]').val();

    if($('body').find('input.inspector').val().length > 0){
        postData(postDate);
        $('#modal-inspector').modal('hide');         
    }else{
        $('body').find('input.inspector').focus().attr('style','border: 1px solid red;');
    }
    
})

$('body').on('keypress','input.inspector', function(e) {
    let postDate = $('input[name="posting_date"]').val();
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {       
        if($('body').find('input.inspector').val().length > 0){
            postData(postDate);
            $('#modal-inspector').modal('hide');         
        }else{
            $('body').find('input.inspector').focus().attr('style','border: 1px solid red;');
        }
    }
});




$('body').on('change','input.input-change',function(){
    let name    = $(this).attr('name');
    let groupId = $('body').find('#data-tbody').attr('group-id');
    let inven   = $(this).closest('tr').find('td.inven').attr('data-val');
    let pk      = $(this).attr('key');
    let value   = $(this).val() ? $(this).val() : 0;
    let remain  = 0;
    let diff    = 0;

    
    if(name=='diff'){
        // ถ้าเปลี่ยนค่า diff ให้คำนวนคงเหลืออัตโนมัติ
        remain  = parseFloat(inven) + parseFloat(value);
        diff    = parseFloat(value);
        $(this).closest('tr').find('input[name="remain"]').val(remain.toFixed(digit_stock));
    }else{
        // ถ้าเปลี่ยน จำนวนคงเหลือ ให้คำนวนจำนวน diff อัตโนมัติ
        diff    = parseFloat(value) - parseFloat(inven);
        remain  = parseFloat(value);
        $(this).closest('tr').find('input[name="diff"]').val(diff.toFixed(digit_stock));
    }
    

    if(localStorage.getItem('buffers')){
        let loadData = JSON.parse(localStorage.getItem('buffers'));     // ดึง buffer มาวนเพื่อเปลี่ยนแปลงข้อมูล
        
        let filters = {};
        Object.values(loadData).map((group,key) => (
            filters[group.data[key].group_id] = { 'data' : group.data.map(model => 
                    model.id === parseInt(pk)?
                        Object.assign({}, model, {
                            'remain': remain.toFixed(digit_stock),
                            'diff': diff.toFixed(digit_stock),
                            'digit' : digit_stock             
                        })
                    : model
                )}
            )
        )
        localStorage.setItem("buffers",JSON.stringify(filters));
        
    }
})


$('body').on('click','.click-btn-print',function(){
    let id = $('body').find('#data-tbody').attr('group-id');
    window.open('{$Uri}/index.php?r=warehousemoving/stock/print&id='+ id, '_blank');
})

$('body').on('click', 'button#reset-time', function(){	
    var today   = new Date(); 
    var time   = ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2) + ":" + ('0' + today.getSeconds()).slice(-2);     
    $('input[name="document-time"]').val(time);
})


$('body').on('keypress','input[name="diff"]', function(e) {
    $(this).closest('tr').addClass('bg-info'); 
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {       
        $(this).closest('tr').find('input[name="remain"]').focus();
        $(this).closest('tr').prevAll().eq(0).removeClass('bg-info');        
    }
});

$('body').on('keypress','input[name="remain"]', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {       
        $(this).closest('tr').nextAll().eq(0).find('input[name="diff"]').focus();
        $(this).closest('tr').nextAll().eq(0).addClass('bg-info');
        $(this).closest('tr').removeClass('bg-info');   
    }
});



JS;


$this->registerJs($js,Yii\web\View::POS_END);
?>