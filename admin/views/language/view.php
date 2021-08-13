<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SourceMessage */

$this->title = $model->message;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Source Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="source-message-view" ng-init="Title='<?= Html::encode($this->title) ?>'">



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
          [
            'label' => Yii::t('common','Type'),
            'value' => function($model){
              return $model->category;
            }
          ],
          [
            'attribute' => 'message',
            'label' => '<span class="flag-icon flag-icon-us"></span> English',
            'encodeLabel' => false,
            'value' => function($model){
              return $model->message;
            }
          ],
          [
            'label' => '<span class="flag-icon flag-icon-th"></span> ไทย',
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model){
              return $model->getLanguage('Th')->text;
            }
          ],
          [
            'label' => '<span class="flag-icon flag-icon-cn"></span> 中文',
            'encodeLabel' => false,
            'value' => function($model){
              return $model->getLanguage('zh')->text;
            }
          ],

          [
            'label' => '<span class="flag-icon flag-icon-la"></span> ພາສາລາວ',
            'encodeLabel' => false,
            'value' => function($model){
              return $model->getLanguage('la')->text;
            }
          ],
        ],
    ]) ?>

</div>
