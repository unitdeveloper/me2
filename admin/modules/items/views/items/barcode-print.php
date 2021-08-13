<?php

use yii\helpers\Html;


$this->title = Yii::t('common', 'Barcode');
$this->params['breadcrumbs'][] = $this->title;
 


?>

<?=$this->render('barcode-print-all')?>