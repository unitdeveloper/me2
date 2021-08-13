<?php 
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

?>

<style>
.content-wrapper{
    background-color:#ecf0f5 !important;
}
#div1 {
    width: 100%;
    height: 70vh;
    padding: 10px;
    border: 1px solid #ccc;
    background-color:#fff;  
    /* resize: both;
    overflow: auto; */
}
.obj{
    border:1px solid green;
    list-style-type:none;
    padding:5px;
    height:50px;
    margin:5px;
    background-color:orange;  
    color:#fff;
}
 
</style>
<div class="hidden-xs">
	<?=Breadcrumbs::widget([
		'itemTemplate' => "<i class=\"fas fa-home\"></i> <li>{link}</li>\n", // template for all links
		'links' => [
            [
                'label' => Yii::t('common','Sales & Marketing'),
                'url' => ['/SaleOrders/saleorder', 'uid' => Yii::$app->user->identity->id],
                'template' => "<li>{link}</li>\n", // template for this link only
            ],
            [
                'label' => Yii::t('common','Product Report'),
                'url' => ['/items/report/index','t' => date('s')],
                'template' => "<li>{link}</li>\n", // template for this link only
            ]

        ],
        
	]);?>
</div>

<div class="row">
    <div class="col-xs-12 text-right">
        <div class="well">
        <?=Html::a('<i class="far fa-chart-bar"></i> Report' ,['/items/report/group'],['class'=>'btn btn-primary'])?>
        </div>
    </div> 
</div>
 

<div class="row">
    <div class="col-xs-6 col-sm-4 ">
        <ul id="div1" ondrop="drop(event)" ondragover="allowDrop(event)" data-id="D1">
            <li id="d1" draggable="true" ondragstart="drag(event)"  class="obj" data-key="1">FIRST</li>

            <li id="d2" draggable="true" ondragstart="drag(event)"  class="obj" data-key="2">SECOND</li>

            <li id="d3" draggable="true" ondragstart="drag(event)" class="obj" data-key="3">THIRD</li>        
        </ul>
    </div>
    <div class="col-xs-6 col-sm-4 no-padding">
        <ul id="div1" ondrop="drop(event)" ondragover="allowDrop(event)" data-id="D2">

        </ul>
    </div>
    <div class="col-xs-12 col-sm-4 ">
        <ul id="div1" ondrop="drop(event)" ondragover="allowDrop(event)" data-id="D3">

        </ul>
    </div>
</div>
 

 
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body" ondrop="drop(event)" ondragover="allowDrop(event)">
                <div class="throw pull-left"></div>
                <button type="button" class="btn btn-info btn-lg pull-right"> <i class="ion ion-ios-arrow-forward"></i> Enter</button>
            </div>
        </div>
    </div>
</div>


<?php $this->registerJsFile('js/DragDropTouch.js');?>

<?php
$js=<<<JS
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
    $(ev.target).find('.obj').attr('data-id',ev.target.getAttribute('data-id'));
    getChild(ev.target);

}

function getChild(el){
    var box = [];
    var data = [];

    $( $(el).find('.obj') ).each(function( key,target ) {
        data.push($(target).attr('data-key'));
    });

    box.push({
        parent:el.getAttribute('data-id'),
        child:data
    });

    $('.throw').html(JSON.stringify(box[0]));
     
}
JS;
$this->registerJs($js,\yii\web\view::POS_HEAD);

?>
 


