<div class="row search-box">
    <div class="col-sm-4 col-xs-12 pull-right mb-10">
        <input id="myInput" class="form-control" type="text" placeholder="<?=Yii::t('common','Search')?>...">
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div id="render-table"></div>
    </div>
</div>

<?php
$Yii    = 'Yii';
$today  = date('Y-m-d');
$js     =<<<JS

let state = {
    data: []
};


 
const getDataFromApi = () => {
    fetch("?r=items/stock/today-ajax", {
        method: "POST",
        body: JSON.stringify({now:"{$today}"}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        state.data = res.raw;
    })
    .catch(error => {
        console.log(error);
    });
}

$(document).ready(function(){
    getDataFromApi();
})

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
  