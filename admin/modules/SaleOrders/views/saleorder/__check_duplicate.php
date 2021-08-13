<li class="dropdown notifications-menu open">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning">10</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have 10 notifications</li>
        <li>
        <!-- inner menu: contains the actual data -->
        
        </li>
        <li class="footer"><a href="#">View all</a></li>
    </ul>
</li>
<?php
$Yii = 'Yii';
 
$js =<<<JS
       
    // ตรวจบิลซ้ำ
    const checkDuplicateBill = (callback) => {
        fetch("?r=accounting/ajax/duplicate-bill", {
            method: "POST",
            body: JSON.stringify({limit:0}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            if(response.status===200){
                callback(response);                
            }else{
                $.notify({
                    // options
                    icon: "fas fa-exclamation-circle",
                    message: response.error
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
            }
            
        })
        .catch(error => {
            console.log(error);
        });
    }

    $(document).ready(function(){
        checkDuplicateBill(res => {
            let body = ``;
            res.raws.map((model,key) => {

            })

            let ul = `<ul class="menu">
                        <li>
                        <a href="#">
                            <i class="fa fa-users text-aqua"></i> 5 new members joined today
                        </a>
                        </li>
                        <li>
                        <a href="#">
                            <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                            page and may cause design problems
                        </a>
                        </li>
                        <li>
                        <a href="#">
                            <i class="fa fa-users text-red"></i> 5 new members joined
                        </a>
                        </li>
                        <li>
                        <a href="#">
                            <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                        </a>
                        </li>
                        <li>
                        <a href="#">
                            <i class="fa fa-user text-red"></i> You changed your username
                        </a>
                        </li>
                    </ul>`;
            
            $('body').find('li.notifications-menu').find('.dropdown-menu > li.notice-body').html(ul)
            setTimeout(() => {
                $('body').find('li.notifications-menu').find('.warning-amount').html(15)
            }, 1000);
            
            
        });
    })
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>

