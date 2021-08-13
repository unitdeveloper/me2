<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\models\SalesPeople;
/* @var $this yii\web\View */
/* @var $model common\models\AppsRules */

$this->title = $model->profiletb->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Apps Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="row">
<div class="col-md-6">
  <!-- Widget: user widget style 1 -->
  <div class="box box-widget widget-user-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-aqua">
      <div class="widget-user-image">
        <?php 

        $AvatarDefault = 'images/logo-ew-x.jpg';

        if($model->profiletb->avatar!='') $AvatarDefault = $model->profiletb->picture; 

        ?>
        <img class="img-circle" src="<?=$AvatarDefault?>" alt="User Avatar">
      </div>
      <!-- /.widget-user-image -->
      <h3 class="widget-user-username"><?= Html::encode($this->title) ?></h3>
      <h5 class="widget-user-desc">
      
          
      </h5>
    </div>
    <div class="box-footer no-padding">
        <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            //'user_id',
            [
                'attribute' => 'user_id',
                'label' => Yii::t('common','User ID'),
                'value' => function($model)
                {
                    return $model->sprit_code.'-'.$model->user_id;
                }
            ], 
            // [
            //     'attribute' => 'name',
            //     'label' => 'Book Account',
            //     'value' => function($model){
            //         return $model->name;
            //     },
            // ],
            'usertb.username', 
            
            [
                'attribute' => 'profiletb.name',
                'label' => Yii::t('common','Name'),
                'value' => function($model)
                {
                    return $model->profiletb->name;
                }
            ],
            //'permission_id',
            'company.name',
            'date_created:Date',
            //'sales_people',
            'rulesetup.name',
            'sale.name',
             
            
        ],
    ]) ?>

      <!-- <ul class="nav nav-stacked">
        <li><a href="#">Projects <span class="pull-right badge bg-blue">31</span></a></li>
        <li><a href="#">Tasks <span class="pull-right badge bg-aqua">5</span></a></li>
        <li><a href="#">Completed Projects <span class="pull-right badge bg-green">12</span></a></li>
        <li><a href="#">Followers <span class="pull-right badge bg-red">842</span></a></li>
      </ul> -->
    </div>
  </div>
  <!-- /.widget-user -->
</div>
</div>
<div class="apps-rules-view">

     

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

    

</div>
