

<?php
$Yii        = 'Yii';

$js =<<<JS



const renderTableSaleReturn = (obj) => {
    let body = ``;
    
    obj.map(model => {
        body+= `
                <tr>
                    <td>` + model.id + `</td>
                    <td>` + model.no + `</td>
                    <td>` + model.no + `</td>
                    <td>` + model.no + `</td>
                    
                </tr>
        `;
    });

    $('body').find('table#export_table tbody').html(body);
}



const getReturnHeader = (obj,callback) => {
    fetch("?r=SaleOrders/return/ajax-list", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
}

$(document).ready(function(){
    getReturnHeader(state, res => {
        renderTableSaleReturn(res.raws);
    })
});

$('body').on('click', '.btn-search-return', function(){
    getReturnHeader(state, res => {
        renderTableSaleReturn(res.raws);
    })
     
})
 
const UpdateOrderStatus = (obj,callback) => {
    fetch("?r=SaleOrders/return/update-status", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
}

const postStock = (obj, callback) => {
    fetch("?r=SaleOrders/return/post-stock", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
}

$('body').on('click','.change-order-status > a', function(){
        let id      = $(this).closest('tr').attr('data-key');
        let status  = $(this).attr('data-key');
        let that    = $(this);
        let text    = $(this).html();
        let caret   = ' <span class="caret"></span>';
        let oldVal  = $(this).closest('.line-group').attr('data-status');

        if(oldVal!==status){
            if(confirm("Are you sure?")){
                UpdateOrderStatus({id:id,status:status}, res =>{
                    console.log(text);
                    if(res.status===200){
                        that.closest('.btn-group').find('button').html(text + caret);
                        $.notify({
                            // options
                            icon: "fas fa-check-circle",
                            message: res.message
                            },{
                            // settings
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });  
                        that.closest('.line-group').attr('data-status', status);
                    }else{
                        
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.message
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: res.status===301 ? "right" : "center"
                            },
                            type: res.status===301 ? "info" : "error",
                            delay: 3000,
                            z_index: 3000
                        }); 
                    }
                    
                })
            }
        }
    })


    $('body').on('click', 'a.post-adjust', function(){
        let el      = $(this);
        let id      = $(this).closest('tr').attr('data-key');
        let oldVal  = $(this).closest('.line-group').attr('data-status');

        if(oldVal=='Posted'){
            alert('Posted');
        }else{
            if(confirm('Are you sure, Do you want to post?')){
                postStock({id:id}, res => {
                    if(res.status===200){
                        $.notify({
                            // options
                            icon: "fas fa-check-circle",
                            message: res.message
                            },{
                            // settings
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });   

                        el.closest('.line-group').find('button').html(' <i class="fa fa-server text-red"></i> Posted <span class="caret"></span>');
                        el.closest('.line-group').attr('data-status','Posted');
                    }else{
                        
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.message
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: res.status===301 ? "right" : "center"
                            },
                            type: res.status===301 ? "info" : "error",
                            delay: 3000,
                            z_index: 3000
                        }); 
                    }
                })
            }
        }
    })
JS;

$this->registerJS($js,\Yii\web\View::POS_END);

?>