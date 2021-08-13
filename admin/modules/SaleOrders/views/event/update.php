<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SaleEventHeader */

$this->title = $model->no;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Event Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?php //$this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-event-header-update" ng-init="Title='<?=$this->title?>'">
 

<?= $this->render('_pos', [
                            'model' => $model,
                        ]) ?>
<!-- Modal -->
<div class="modal fade modal-clear" id="point-of-sale" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-modal"  aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modelTitleId">Point Of Sale</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-ew close-modal pull-left"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                 
            </div>
        </div>
    </div>
</div>

<script>
    $('document').ready(function(){
        $('input[ng-model=\"search\"]').focus().val('');
        
        setTimeout(function(e){
            //$('#point-of-sale').modal('show');
            
        }, 1000);
        
    });

    $('#point-of-sale').on('show.bs.modal', event => {
        var button = $(event.relatedTarget);
        var modal = $(this);
       
        // Use above variables to manipulate the DOM
        
    });

    $('.close-modal').on('click',function(){
        window.location.href = "index.php?r=SaleOrders%2Fevent%2Findex";
    });
</script>

    

</div>
