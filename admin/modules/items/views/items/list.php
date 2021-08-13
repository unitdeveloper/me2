<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Unitofmeasure;

?>
<style>
    .content-wrapper{
        /* background-image: linear-gradient(45deg, rgba(46, 56, 66, 1), rgba(1, 1, 4, 1));
        color:#fff !important; */
    }
    .item-img img{
        width:50px;
    }

    .thead-item-img{
        width:50px;
    }

    @media (max-width: 767px) {
        .item-img {             
            float: left;
            margin-right: 10px;
            margin-top: -5px;
        }
        .item-img img{
            width:100px;
        }

    }
</style>

<div style="color:rgb(225,170,44);" id="item-title">
    <h3>รายการสินค้า</h3>
</div>

<div class="row search-box">
    <div class="col-sm-4 col-xs-8 ">
        <input id="search" class="form-control mb-10" type="text" placeholder="<?=Yii::t('common','Search')?>...">
    </div>  
    <div class="col-sm-8 col-xs-4 text-right">
        <a class="btn btn-default-ew btn-flat hidden-xs" data-toggle="modal" href='#modal-item-upload'><i class="fas fa-upload"></i>  <?=Yii::t('common','Upload')?> </a>
        <a class="btn btn-default-ew btn-flat btn-add-new" data-toggle="modal" href='#modal-create-item'><i class="fa fa-plus"></i> <?=Yii::t('common','Create')?></a>
    </div>           
</div>

 
<div class="items-renders"></div>
 



<?= $this->render('_craft'); ?>
<?= $this->render('_upload_excel'); ?>


<?php 

 
$Yii = 'Yii'; 
$jsh=<<<JS

let state = {
    progress : false,
    data : [],
    imgchange: false,
};

const loading = ` 
                    <div class="loading" style="position:absolute; left:-3px; top:-4px; width:100%; height: 100%; background: rgba(29, 29, 29, 0.42); z-index: 10;"> 
                        <div style="text-align: center; margin-top: 75px; color: #fff; font-size:28px; color: green;">Loading <i class="fas fa-sync-alt fa-spin"></i></div>
                    </div>
                `;

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Please wait a minute")} .... </div>
        </div>`;


const filterTable  = (search) => {

    Table = $('#export_table').DataTable();
     
    Table.search(search).draw() ;
     
/*
    $("#export_table  tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
    });

    $('#export_table tbody tr').each((key,value) => {
        $(value).find('.key').html(key + 1);
    });

    $('#export_table').attr('style','width:100%');
*/
}


const getDataFromUrl = (obj, callback) => {
    fetch("?r=items/items/list-ajax", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {        
        callback(res);        
    })
    .catch(error => {
        console.log(error);        
    });
}

const getCountStock = (obj, callback) => {
    // ถ้านับแล้วไม่ต้องนับอีก
    let countOrNot = localStorage.getItem('count-or-not') ? JSON.parse(localStorage.getItem('count-or-not')) : [];

    if(countOrNot.length <= 0){
        // Increase expiration time after save  
        localStorage.setItem('saved', new Date().getTime())
        localStorage.setItem('count-or-not', JSON.stringify({id:1}))     

        fetch("?r=items/items/count-stock-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {        
            
        })
        .catch(error => {
            console.log(error);        
        });
    }
}

 
const renderTable = (data) => {
    let rows  = ``;
    
    let newData = data.raw;
    function compare( a, b ) {
        if ( a.code < b.code ){
            return -1;
        }
        return 0;
    }

    newData.sort( compare );   

    newData.map((model,key) => {
        rows+= `<tr id="`+key+`" data-key="` + model.id + `">
                    <td class="hidden-xs">` + (key + 1) + `</td>
                    <td>                       
                        <div class="item-img edit-item-modal"><img class="td-item-img pointer" src="` + model.img + `"/></div>
                        <div>
                            <div class="hidden-sm hidden-md hidden-lg mt-10">
                                <span class="item-code font-roboto">
                                    <a href="#" class="edit-item-modal font-roboto" >` + model.code + `</a>
                                </span>
                            </div>
                            <div class="hidden-sm hidden-md hidden-lg mt-5" style="font-family: saraban; font-size:13px;">
                                <div><span class="item-name text-info">` + model.name + `</span></div> 
                                <div>` + (model.detail != null ? model.detail : ``)  + `</div> 
                                <div>` + (model.size != null ? model.size : ``) + `</div>
                            </div>
                        </div>                        
                    </td>                    
                    <td class="hidden-xs font-roboto"><a href="#" class="td-item-code edit-item-modal">` + model.code + `</a></td>
                    <td class="hidden-xs" style="font-family: saraban; font-size:13px;">
                        <div class="td-item-name" >` + model.name + `</div>
                        <div class="td-item-detail" >` + (model.detail != null ? model.detail : ``)  + `</div> 
                        <div class="td-item-size">` + (model.size != null ? model.size : ``) + `</div>
                    </td>
                    <td class="hidden">` + model.name + `</td>
                    <td class="text-right font-roboto">` + number_format(model.stock) + `</td>
                    <td class="hidden-xs td-item-unit" data-id="` + model.unit + `">` + model.measure + `</td>
                    <td class="hidden-xs text-center">
                        <a href="#" class="btn btn-flat btn-warning-ew btn-sm edit-item-modal"><i class="fa fa-pencil text-warning"></i></a>
                        <a href="#" class="btn btn-flat btn-danger-ew btn-sm delete-item"><i class="fa fa-trash text-red"></i></a>
                    </td>
                </tr>`;
    });

    let html = `<table class="table table-bordered" id="export_table">
                    <thead>
                        <tr class="bg-gray">
                            <th class="hidden-xs" style="width:10px;">#</th>
                            <th class="hidden-xs font-roboto thead-item-img">{$Yii::t('common','Images')}</th>     
                            <th class="font-roboto hidden-xs" style="width:120px;" >{$Yii::t('common','Code')}</th>                             
                            <th class=" ">{$Yii::t('common','Description')}</th>
                            <th class="hidden">{$Yii::t('common','Description')}</th>
                            <th class="text-right">{$Yii::t('common','Stock')}</th>
                            <th class="hidden-xs">{$Yii::t('common','Unit')}</th>
                            <th class="hidden-xs text-center" style="width:80px;">{$Yii::t('common','Delete')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        ` + rows + `
                    </tbody>
                </table>`;

    $('.items-renders').html(html);
    var table = $('#export_table').DataTable({
            "paging": false,
            'pageLength' : 50,
            "searching": true
        });

    
         

    // var data = table
    //     .column( 3 )
    //     .data()
    //     .sort();
    
}

const readURL = (input,div) => {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) { $(div).fadeOut(400, function() { $(div).attr('src', e.target.result); }).fadeIn(400); }
        reader.readAsDataURL(input.files[0]);
    }
}

const deleteItem = (id, callback) => {
    fetch("?r=items/items/delete-ajax", {
        method: "POST",
        body: JSON.stringify({id:id}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        callback(res);
    })
    .catch(error => {
        console.log(error);        
    });
}

 
JS;
$this->registerJS($jsh,\yii\web\View::POS_HEAD);
?>




<?php 
 
 
$js=<<<JS

 
    
$("#search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    filterTable(value);
    // oTable = $('#export_table').DataTable();   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    // $('#myInputTextField').keyup(function(){
    //     oTable.search(value).draw() ;
    // })
});



$(document).ready(function(){
    setTimeout(() => {
        console.log('try-count')
        getCountStock({limit:0})
        // Clear on startup if expired
        if(!localStorage.getItem('hours')){
            localStorage.setItem('hours',2)
        }
        let hours   = localStorage.getItem('hours');
        let saved   = localStorage.getItem('saved')
        let now     = new Date().getTime() - saved;
        let compaire= hours * 60 * 60 * 1000;
        let inTime  = (((compaire - now)  ) ) / 1000 ;
        if (saved && (now > compaire)) {
            localStorage.removeItem('count-or-not')
        }else{
            console.log('Count next in '+ number_format(inTime.toFixed(0)) +' sec')
        }
    }, 3000);

    $('.items-renders').html(loadingDiv);
    $('#search').attr('readonly',true);
    getDataFromUrl({limit:10, offset:0}, res => {
        state.data = res;
        renderTable(res);
        if(res.status==200){
            setTimeout(() => {
                $('#search').attr('placeholder', 'กำลังดึงข้อมูลเพิ่มเติม...');
                getDataFromUrl({limit:false,offset:10}, res => {
                    let oldData  = state.data.raw;
                    res.raw.map(model => oldData.push(model));                    

                    let newState = {
                        raw: oldData
                    }                     
                   
                    renderTable(newState);    
                    $('#search').attr('placeholder', '{$Yii::t("common","Search")}...').attr('readonly',false);        
                    setTimeout(() => {
                        $("#export_table").tableExport({
                            headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
                            footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
                            formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
                            fileName: "{$this->title}",         // (id, String), filename for the downloaded file
                            bootstrap: true,                    // (Boolean), style buttons using bootstrap
                            position: "top" ,            	// (top, bottom), position of the caption element relative to table
                            ignoreRows: null,     			// (Number, Number[]), row indices to exclude from the exported file
                            ignoreCols: [1,3,7],                   // (Number, Number[]), column indices to exclude from the exported file
                            ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file         
                            footers: false 
                        });

                        
                    }, 1000);        
                });
            }, 1500);
        }
    });

    // Fixed table width not full max
    setTimeout(() => {
        $('#export_table').attr('style',' ');
    }, 800);
   
})


$(document).ready(function(){

    $('div.item-image-change').fadeOut(500, function() {
        $('div.item-image-change').append('<img class=\"img-responsive img-rounded img-thumbnail item-img\" src="" id=\"img-preview-logo\">');
    }).fadeIn(500);

});


$("#item-image").change(function(){
    readURL(this,'#img-preview-logo');
    state.imgchange = true;
});


const createItem = (data, callback) => {
    fetch("?r=items/items/create-ajax", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        callback(res);
    })
    .catch(error => {
        console.log(error);        
    });
}

const updateItem = (data, callback) => {
    fetch("?r=items/items/update-ajax", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        callback(res);
    })
    .catch(error => {
        console.log(error);        
    });
}

$('#modal-create-item').on('show.bs.modal',function(){   
    $('body').attr('style', ' ');
    $('.loading').remove();    
});

$('body').on('click','.btn-add-new', function(){
    // Clear image
    $('#img-preview-logo').attr('src','');
    $('#item-image').val('');

    // Clear data
    $('#item-code').val('');
    $('#item-name').val('');
    $('#item-detail').val('');
    $('#item-size').val('');
    $('#btn-modal-action').attr('class','btn btn-success btn-save-item');
    $('#btn-modal-openlink').attr('href','#');

    // Hide btn
    $('#craft-item').hide();
    
})

$('body').on('click', '.btn-save-item', function(){
    let el  = $(this);
        el.hide();
    let data = {
        code: $('#item-code').val(),
        name: $('#item-name').val(),
        detail: $('#item-detail').val(),
        size: $('#item-size').val(),
        unit: parseInt($('#item-measure').val()),
        measure: $('#item-measure option:selected').text(),
        img: $('#img-preview-logo').attr('src'),
        imgchange: state.imgchange
    };

    
    
    if(data.code && data.name != ''){
        $('#modal-create-item .modal-body').prepend(loading);
        createItem(data, res => {
            el.show();
            if(res.status===200){
                // Clear image
                $('#img-preview-logo').attr('src','');
                $('#item-image').val('');

                // Clear data
                $('#item-code').val('');
                $('#item-name').val('');
                $('#item-detail').val('');
                $('#item-size').val('');

                // Hide modal
                $('#modal-create-item').modal('hide');
                $('#btn-modal-action').attr('class','btn btn-success btn-save-item');

                // Insert to cache
                state.data.raw.push({
                    code: res.raw.code,
                    detail: res.raw.detail,
                    id: res.raw.id,
                    img: res.raw.img,
                    name: res.raw.name,
                    stock: res.raw.stock,
                    size: res.raw.size,
                    unit: data.unit,
                    measure: res.raw.measure
                });

                // Render
                renderTable(state.data);
            }else{
                $('.loading').remove();  
                $.notify({
                    // options
                    icon: 'fas fa-clock',
                    message: res.message
                },{
                    // settings
                    type: 'warning',
                    delay: 10000,
                    z_index:3000,
                });    
            }
        })

        $('#item-name').closest('div').removeClass('has-error');
        $('#item-code').closest('div').removeClass('has-error');
    }else{
        $('#item-name').closest('div').addClass('has-error');
        $('#item-code').closest('div').addClass('has-error');
    }
 
});

$('body').on('change', '#item-name', function(){
    $('#item-name').closest('div').removeClass('has-error').addClass('has-success');
})


$('body').on('click','.delete-item', function(){
    let thisRow = $(this).closest('tr');
    let id      = parseInt(thisRow.attr('data-key'));
    if(confirm('คุณแน่ใจ! ว่าต้องการลบรายการนี้หรือไม่?')){
        deleteItem(id, res => {
            
            if(res.status===200){
                thisRow.remove(); // Remove row
                let newData     = state.data.raw.filter(model => model.id !== id ? model : null); // Filter removed id
                state.data.raw  = newData; // new data to state
        
            }else{
                $.notify({
                    // options
                    icon: 'fas fa-clock',
                    message: res.message
                },{
                    // settings
                    type: 'warning',
                    delay: 10000,
                    z_index:3000,
                });    
            }
        })
    }
})


$('body').on('click', '.edit-item-modal', function(){
    let rows = $(this).closest('tr');

 
    $('#img-preview-logo').attr('src',rows.find('.td-item-img').attr('src'));

    $('#item-code').val(rows.find('.td-item-code').html()).attr('data-id',rows.attr('data-key'));
    $('#item-name').val(rows.find('.td-item-name').html());
    $('#item-detail').val(rows.find('.td-item-detail').html());
    $('#item-size').val(rows.find('.td-item-size').html());
    $('#item-measure').val(rows.find('.td-item-unit').data('id'));

    // Show modal
    $('#modal-create-item').modal({backdrop: 'static', keyboard: false});
    $('#modal-create-item').attr('data-key', rows.attr('data-key'));
    $('#btn-modal-action').attr('class','btn btn-success btn-edit-item');
    $('#btn-modal-openlink').attr('href','?r=items%2Fitems%2Fview&id=' + rows.attr('data-key'));
    $('#craft-item').show();
})

$('body').on('click','.btn-edit-item', function(){
    let data = {
        id: $('#item-code').attr('data-id'),
        code: $('#item-code').val(),
        name: $('#item-name').val(),
        detail: $('#item-detail').val(),
        size: $('#item-size').val(),
        unit: parseInt($('#item-measure').val()),
        measure: $('#item-measure option:selected').text(),
        img: $('#img-preview-logo').attr('src'),
        imgchange: state.imgchange
    };
    
    

    $('#modal-create-item .modal-body').prepend(loading);

    updateItem(data, res => {
        if(res.status===200){
           

            // Insert to cache
            let newData = [];
                newData = { 
                    raw : state.data.raw.map(model => parseInt(model.id) === parseInt(data.id) 
                            ? Object.assign({}, model, {
                                            code: data.code,
                                            detail: data.detail,
                                            id: parseInt(data.id),
                                            img: data.img,
                                            name: data.name,
                                            stock: res.raw.stock,
                                            size: data.size, 
                                            unit: data.unit,
                                            measure: res.raw.measure
                                    })
                            : model
                        ),
                    status: 200
                }
            
            state.data = newData;
           
            

            setTimeout(() => {
                // Hide modal
                $('#modal-create-item').modal('hide'); 

                // Clear image
                $('#img-preview-logo').attr('src','');
                $('#item-image').val('');

                // Clear data
                $('#item-code').val('');
                $('#item-name').val('');
                $('#item-detail').val('');
                $('#item-size').val('');               
            }, 500);
            
            $('#btn-modal-action').attr('class','btn btn-success btn-save-item');

             

            

            // Render
            renderTable(newData);
        }else{
            $.notify({
                    // options
                    icon: 'fas fa-clock',
                    message: res.message
                },{
                    // settings
                    type: 'warning',
                    delay: 10000,
                    z_index:3000,
                });  
        }
    })
});
 

 
JS;
$this->registerJS($js,\yii\web\View::POS_END);
$this->registerJsFile('@web/js/jquery.animateNumber.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 