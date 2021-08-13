<a href="#" class="btn btn-primary-ew get-source-company hidden"  ><i class="fa fa-plus"></i> <?=Yii::t('common','Get Sale Order')?></a> 

<a href="#" id="ew-add-new-line" class="btn btn-success-ew hidden"  ><i class="fa fa-plus"></i> <?=Yii::t('common','Get source document')?></a> 

<div class="modal fade modal-full" id="modal-get-source-company">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Sale Order')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


<?php

$Yii = 'Yii';

$js =<<<JS
  
    $('body').on('click', '.get-source-company', function(){
        $('#modal-get-source-company').modal('show');
    })

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>