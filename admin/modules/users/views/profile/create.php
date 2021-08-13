<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Profile */

$this->title = Yii::t('common', 'Create Profile');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-create">
<?= yii\widgets\Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
