<?php

use yii\helpers\Html;
use yii\helpers\Url;
 
$this->title = Yii::t('common', 'Payment');
$this->params['breadcrumbs'][] = $this->title;

?>
<div ng-init="Title='<?=$this->title?>'">
    <?= $this->render('_form', ['model' => $model])?>
</div>