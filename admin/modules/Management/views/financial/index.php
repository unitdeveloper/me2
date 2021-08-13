<?php
/* @var $this yii\web\View */
?>
<h1>financial/index</h1>

<p>
    You may change the content of this page by modifying
    the file <code><?= __FILE__; ?></code>.
</p>
<a href="#" class="calculate" >TEST</a>

<?php
$js=<<<JS
    

    let searchData = (data,callback) => {
        fetch("?r=Management/financial/tax-invoice-ajax", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {            
            callback(response);            
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', 'a.calculate', function(){

        searchData({id:0}, res => {
            console.log(res);
        })
    })
 
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>