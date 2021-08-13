<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\users\models\SearchProfile */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Profiles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Profile'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user_id',
            'name',
            'public_email:email',
            'gravatar_email:email',
            'gravatar_id',
            // 'location',
            // 'website',
            // 'bio:ntext',
            // 'timezone',
            // 'address:ntext',
            // 'province:ntext',
            // 'district:ntext',
            // 'amphur:ntext',
            // 'postcode:ntext',
            // 'city:ntext',
            // 'mobile_phone:ntext',
            // 'company:ntext',
            // 'avatar:ntext',
            // 'firstname',
            // 'lastname',
            // 'email:email',
            // 'tax_id',
            // 'auth_id',
            // 'auth_source_name',
            // 'user_birthday',
            // 'country',
            // 'user_location',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
