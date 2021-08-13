<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */

$this->title = $model->no_;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-invoice-header-view">

<div id="ew-show-detail" class="modal modal-full fade" role="dialog" >
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close ew-inv-close-show-detail" >&times;</button>
        <h4 class="modal-title ew-title-pic-cust"><?=Yii::t('common','Select Customer') ?></h4>
      </div>
      <div class="Smooth-Ajax">
        <div class="modal-body">


            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'no_',
                    'cust_no_',
                    'cust_name_',
                    'cust_address:ntext',
                    'cust_address2:ntext',
                    'posting_date',
                    'order_date',
                    'ship_date',
                    'cust_code',
                    'sales_people',
                    'document_no_',
                    'doc_type',
                    [
                
                      'label' => Yii::t('common','Users'),
                      'format' => 'raw',
                      'headerOptions' => ['class' => 'text-left'],
                      'contentOptions' => ['class' => 'font-roboto text-left'],
                      'value' => function($model){                   
                          return $model->users ? $model->users->username : '';
                      }
      
                  ],
                    
                ],
            ]) ?>

 

        
        </div>
      </div>

      <div class="modal-footer" >

          <button type="button" class="btn btn-default-ew pull-left ew-inv-close-show-detail" ><i class="fa fa-power-off" aria-hidden="true"></i>  <?=Yii::t('common','Close')?></button>    


      </div>
    </div>
    
  </div>

  
</div>
</div>
<script type="text/javascript">

    $(document).ready(function(){
        $('#ew-show-detail').modal('show');
    });

    $('body').on('click','.ew-inv-close-show-detail',function(){
        event.preventDefault();
        history.back(1); 
    });
     
</script>