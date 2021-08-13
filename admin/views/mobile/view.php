<?php 
//ALTER TABLE `items` ADD `last_update_stock` VARCHAR(50) NULL DEFAULT NULL AFTER `last_possible`; 
if(!Yii::$app->session->get('Rules')){
    \Yii::$app->session->set('Rules', ['comp_id' => 1]);
}

if(!$model->last_update_stock){
    
    $Stock = $model->ProductionBom != 0
                ? $model->updateQty->model->last_possible
                : $model->updateQty->model->last_stock ;
}else{
    $Stock = $model->ProductionBom != 0
                ? $model->last_possible
                : $model->last_stock ;
}


?>

<div style="padding:20px;">

    <div style="width:100%; float:left;">
        <img src="<?=$model->picture;?>" style="width:100%;"/>
    </div>

    <div style="width:100%; float:left; margin-top: 50px;">
        <div style="font-size: 5vw;"><?=$model->master_code; ?></div>
        <div style="border:1px solid #ccc; padding:20px; margin-top:50px;"><h1  style="font-size: 8vw; margin: 0px;"><?=number_format($Stock);?></h1></div>
    </div>

    <div style="width:100%; float:left; margin-top:10px;">
        <p style="font-size: 8vw;"><?=$model->description_th; ?></p>
    </div>

    <div style="width:100%; float:left; margin-top:10px;">
        <p style="font-size: 7vw; color: #8a8a8a;"><?=$model->Description == $model->description_th ? '' : $model->Description; ?></p>
    </div>
</div>
 