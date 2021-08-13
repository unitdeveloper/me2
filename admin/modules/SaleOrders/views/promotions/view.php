<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Promotions */

$this->title = $model->item_group;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Promotions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="promotions-view" ng-init="Title='<?= Html::encode($this->title) ?>'">



    <div class="row">
            <div class="col-sm-4">
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
                        //'id',
                        'item_group',
                        //'items',
                        [
                            'label' => Yii::t('common','Sale Amount'),
                            'value' => function($model){
                                return number_format($model->sale_amount,2);
                            }
                        ],
                        [
                            'label' => Yii::t('common','Discount'),
                            'value' => function($model){
                                return number_format($model->discount,2);
                            }
                        ],
                        // 'sale_amount',
                        // 'discount',
                        [
                            'label' => Yii::t('common','Send date'),
                            'value' => function($model){
                                return $model->approved->sent_time;
                            }
                        ],
                        [
                            'label' => Yii::t('common','Approved By'),
                            'value' => function($model){
                                return $model->approved->approveby->name;
                            }
                        ],
                         
                        'approved.approve_date',
                        //'createBy.profile.name',
                        //'create_date',
                        //'approveBy.profile.name',
                        //'approve_date',
                        //'status',
                        //'approve_id',
                        'start_date',
                        'end_date'
                    ],
                ]) ?>
            </div>
            <div class="col-sm-8">
                <h4><?=Yii::t('common','Items')?></h4>
                <div id="dataTable" data-key="<?=$model->id?>"></div>
            </div>
    </div>
    
         
</div>


<?php
        
    $Yii = 'Yii';
    $id = Yii::$app->request->get('id');
    $api = Yii::$app->params['api'];

    $MyToken = \common\models\Authentication::findOne(['user_id' => Yii::$app->user->identity->id]);
    $token = base64_encode($MyToken->token);


    $js=<<<JS
    const token = '{$token}';
    const url = '{$api}';
    const id = '{$id}';
    const t_code = '{$Yii::t("common","Code")}';
    const t_name = '{$Yii::t("common","Name")}';
    const t_product = '{$Yii::t("common","Product Name")}';
    const t_manage = '{$Yii::t("common","Manage")}';
    const t_edit = '{$Yii::t("common","Edit")}';
JS;
    $this->registerJs($js,\yii\web\view::POS_HEAD);

    $Option = ['depends' => [admin\assets\ReactAsset::className()]];

    $Options =  ['depends' => [\admin\assets\ReactAsset::className()],'type'=>'text/jsx'];

    $this->registerJsFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table.min.js',$Option);
    $this->registerCssFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table-all.min.css',$Option); 
    $this->registerJsFile('@web/js/approval/approved.jsx?v=3.08.05',$Options); 	
    
?>
    