<?php
use yii\helpers\Html;
?>
<div class="row">
    <div class="col-sm-12">
        <h4 class="ml-5">
            <?=Yii::t('common','Tax Invoice')?> : <?= Html::a('',
                                            ['/accounting/posted/posted-invoice', ['id' => '']],
                                            ['class' => 'INVOICE-NUMBER', 'target' => '_blank']) ?>
        </h4>
    </div>
</div>