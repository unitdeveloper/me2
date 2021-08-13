<?php

use yii\helpers\Html;
 

/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */

$this->title = Yii::t('common', 'Tax Invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Tax Invoice'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
 
<div class="row">
    <div class="col-xs-12 mb-2">
        <div class="pull-right">
            <div class="btn-group">
                <button id=" " type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-download"></i> <?=Yii::t('common','Download')?>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">                    
                    <li><a href="#" class="export-to-word  text-aqua" id="export_word" ><i class="fas fa-file-word"></i> <?=Yii::t('common','Microsoft Word')?></a>  </li>
                    <li><a href="#" class="ew-print-btn  text-green"><i class="fas fa-file-excel"></i> <?=Yii::t('common','Microsoft Excel')?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>