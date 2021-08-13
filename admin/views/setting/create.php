<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<?=\Yii::t('common','Not available')?>



<?php 
$js=<<<JS

$(document).ready(function(){

    //$('.ew-bt-app-home').attr('href','index.php?r=setting%2Fprinter-index');
 
});
 

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');