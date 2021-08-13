<?php
    $options = ['depends' => [\yii\web\JqueryAsset::className()]];

    $this->registerJsFile('@web/js/jquery.animateNumber.min.js', $options);
    $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js',$options);
    $this->registerJsFile('@web/js/jquery.rippleria.min.js',$options);
    $this->registerJsFile('@web/js/saleorders/saleorder_index.js?v=3.06.21'); 
 
?>

<?php
$Yii = 'Yii';
 
 
$js =<<<JS
       
    const UpdateOrderStatus = (obj,callback) => {
        fetch("?r=SaleOrders/ajax/update-status", {
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

    $('body').on('click', '.plus-to-ship', function(){
        let thisBtn = $(this);
        let id      = $(this).closest('tr').attr('data-key');

        fetch("?r=SaleOrders/report/make-bill-to-ship", {
            method: "POST",
            body: JSON.stringify({ id: parseInt(id) }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.status===200){
                thisBtn.attr('class','btn btn-default-ew btn-sm minus-from-ship');
                thisBtn.find('i').attr('class','fas fa-truck text-info');
            }else{
                thisBtn.attr('class','btn btn-default-ew btn-sm plus-to-ship');
                alert('Error! Something wrong');
            }            
        })
        .catch(error => {
            console.log(error);
        });
    });

    $('body').on('click', '.minus-from-ship', function(){
        let thisBtn = $(this);
        let rows    = $(this).closest('tr').attr('data-key');

        fetch("?r=SaleOrders/report/remove-bill-from-ship", {
            method: "POST",
            body: JSON.stringify({ id: rows }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.status===200){
                thisBtn.attr('class','btn btn-default-ew btn-sm plus-to-ship');
                thisBtn.find('i').attr('class','fas fa-truck text-gray');
            }else{
                thisBtn.attr('class','btn btn-success-ew btn-sm minus-from-ship');
                alert('Error! Something wrong');
            }            
        })
        .catch(error => {
            console.log(error);
        });
    })


    $('body').on('click','.change-order-status > a', function(){
        let id      = $(this).closest('tr').attr('data-key');
        let status  = $(this).attr('data-key');
        let that    = $(this);
        let text    = $(this).html();
        let caret   = ' <span class="caret"></span>';
         
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
    })

    $('body').on('click', 'a.delete-order', function(){
        let row = $(this);
        let tr  = row.closest('tr');
        let id  = parseInt(tr.attr('data-key'));
        let no  = $(this).closest('tr').find('a.order-no').text();        
        
        if (confirm('ต้องการลบรายการ "' + no + '" ?')) {

            fetch("?r=SaleOrders/ajax/delete", {
                method: "POST",
                body: JSON.stringify({id:id}),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
                }
            })
            .then(res => res.json())
            .then(response => {
                if(response.status===200){
                    tr.remove();
                    $.notify({
                        // options
                        icon: "fas fa-check-circle",
                        message: response.message
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
                }else{
                    $.notify({
                        // options
                        icon: "fas fa-exclamation-circle",
                        message: response.suggestion
                    },{
                        // settings
                        placement: {
                            from: "top",
                            align: "center"
                        },
                        type: "error",
                        delay: 5000,
                        z_index: 3000
                    }); 

                    if(response.status===501){                    
                        $.notify({
                            // options
                            icon: "fas fa-exclamation",
                            message: response.message
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: "center"
                            },
                            type: "warning",
                            delay: 7000,
                            z_index: 3000
                        }); 
                    }
                }
                
            })
            .catch(error => {
                console.log(error);
            });
        }
    });

     
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>

