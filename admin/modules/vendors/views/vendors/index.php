<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\vendors\models\VendorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Vendors');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="vendors-index" ng-init="Title='<?=$this->title?>'" style="padding-top: 15px !important;">
<?php if(Yii::$app->session->hasFlash('alert')):?>
    <?= \yii\bootstrap\Alert::widget([
    'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
    'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
    ])?>
<?php endif; ?>

<style type="text/css">
    .bg-dark{
        margin: 10px 0px 10px 0px;
        background-color: #4b646f;

    }
    .bg-dark:hover{
        color: #f39c12;
    }
    .text-vendor-header{
        font-size: 16px;
    }
</style>
<div class="row" style="font-family: saraban;">
    <div class="col-md-12">
    <?= $this->render('_search',['model' => $searchModel]) ?>


    <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['class' => 'text-center'],
                ],
                //'code',
                //'id',
                [
                    //'attribute' => 'name',
                    //'label' => Yii::t('common','Vendors'),


                    'format' => 'raw',
                    'value' => function($model){

                        $html = '<div class="row" style="position:relative;">';

                        $html.= '   <div class="col-sm-2 col-xs-5">';
                        $html.=          Html::a(Html::img($model->getPhotoViewer('logo'),[
                                            'class' => 'img-responsive img-rounded img-thumbnail',
                                            'style' => 'max-width:50px;']),['view','id' => $model->id]);
                        $html.= '   </div>';

                        $html.= '   <div class="col-sm-10 col-xs-7">';

                        $html.= '   <div class="text-vendor-header">
                                        <b>'.Html::a($model->name,['view','id' => $model->id]).'</b>
                                    </div>';

                        $html.= '   <div class="btn btn-xs bg-dark">'.$model->code.'</div>  <div class="btn btn-xs bg-dark text-warning">'.$model->business->detail.'</div>';

                        $html.= '   <div>'.$model->address.'</div>';
                        $html.=         Html::a('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit',
                                            ['update','id' => $model->id ],
                                            [
                                                //'class' => 'btn btn-default-ew btn-xs ',
                                                'class' => 'btn btn-default-ew btn-xs pull-right pull-down',

                                            ]);
                        $html.= '   </div>';

                        $html.= '</div>';


                        return $html;
                    }
                ],
                
                //'name',
                //'address',
                //'address2',
                //'district',
                // 'city',
                // 'province',
                // 'postcode',
                // 'country',
                // 'phone',
                // 'fax',
                // 'contact',
                // 'vendor_posting_group',
                // 'batbus_posting_group',
                // 'email:ntext',
                // 'homepage:ntext',
                // 'headoffice',
                // 'user_id',
                // 'comp_id',

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    </div>
    <div class="col-md-3 hidden-xs">
        <!-- <div class="panel panel-default">


            <img src="images/Stock-Notification.png" class="img-responsive ">

            <img src="images/setup-stock.png" class="img-responsive ">

            <img src="images/right-banner.jpg" class="img-responsive">
        </div> -->

    </div>
</div>
</div>
<script type="text/javascript">
    $('.pull-down').each(function() {
      var $this=$(this);
        //$this.css('margin-top', $this.parent().height()-$this.height())
    });
</script>
