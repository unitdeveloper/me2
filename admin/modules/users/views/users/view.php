<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->profile? $model->profile->name : '';
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="user-view" ng-init="Title='<?= Html::encode($this->title) ?>'">
 
<div class="row">
    <div class="col-md-4">
    
        <div class="panel panel-info">
            <div class="panel-heading">
                    <h3 class="panel-title">Panel title</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                        <div class="col-xs-6">
                            <img src="<?=$model->profile ? $model->profile->picture : ''?>" class="img-responsive">
                        </div>
                        <div class="col-xs-6">
                            <div><?=$model->profile? $model->profile->name : '';?></div>                            
                            <div>User : <?=$model->username;?></div>
                            <hr>
                            <div>Email : <?=$model->email;?></div>
                        </div>
                </div>
            </div>
        </div>
        <?= Html::a('<i class="far fa-edit"></i> '.Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fab fa-expeditedssl"></i> '.Yii::t('common', 'Rules Update'), ['/apps_rules/rules/update', 'id' => $model->rules->id], ['class' => 'btn btn-info','target' => '_blank']) ?>
        <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
  
    </div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><?=$appRule->company->name?></h4></div>
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-sm-3">
                        <label><?=Yii::t('common','Sale People')?></label> 
                    </div>
                    <div class="col-sm-9">
                        
                        <?= $appRule->sale->name?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label><?=Yii::t('common','Status')?></label> 
                    </div>
                    <div class="col-sm-9">
                    <?= $appRule->status == 1 
                            ? '<div class="text-green">'.Yii::t('common','Enabled').'</div>' 
                            :  '<div class="text-yellow">'.Yii::t('common','Disabled').'</div>'  ?>    
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label><?=Yii::t('common','Department')?></label> 
                    </div>
                    <div class="col-md-9">
                    <?=$appRule->rulesetup->name?>
                    </div>
                </div>
            </div>
        </div>

        <a href="#detail" class="mt-10" data-toggle="collapse" title="help" aria-expanded="true"><i class="fas fa-question-circle"></i> Source</a>
        <div id="detail" class="collapse  " aria-expanded="true" style="">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'email:email',
                    'password_hash',
                    'auth_key',
                    'confirmed_at',
                    'unconfirmed_email:email',
                    'blocked_at',
                    'registration_ip',
                    'created_at:datetime',
                    'updated_at:datetime',
                    'flags',
                    [
                        'attribute' => 'last_login_at',
                        'contentOptions' => ['class' => 'text-green'],
                        'value' => function($model){ return date('M d, Y H:i:s A',$model->last_login_at); }
                    ],
                    'status',
                    'password_reset_token',
                    
                ],
            ]) ?>
        </div>
    </div>
</div>
   

</div>
