<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="company-view">

    <div class="row">
        <div class="col-sm-6">
            <h1><?= Html::encode($this->title) ?></h1> 
        </div>
        <div class="col-sm-6">
            <span class="pull-right mt-5">
                <button type="button" class="btn btn-primary access-to-this-company" data-key="<?=$model->id?>" ><i class="fas fa-sign-in-alt"></i> <?= Yii::t('common','Access')?></button>
            </span>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-2">
                
            <div class="my-10">
                <h4><?= Yii::t('common','Icon')?></h4>         
                <?= Html::img($model->logoViewer,['class' => 'img-responsive img-rounded img-thumbnail'])?>
            </div>

            <div class="my-10">
                <h4><?= Yii::t('common','Brand')?></h4>
                <?= Html::img($model->brandViewer,[
                                'alt'       => Yii::t('common','Maps'),       
                                'id'        => 'brand',                          
                                'class'     => 'img-responsive',                                
                                'data-zoom-image' => $model->brandViewer
                            ])?>
            </div>
 
        </div> 

        <div class="col-sm-10">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'acronym',
                    'name_en',
                    'address_en',
                    'address',
                    'address2',
                    [
                        'attribute' => 'maps',
                        'format' => ['image',[
                            'alt'       => Yii::t('common','Maps'),   
                            'class'     => 'img-responsive', 
                            'id'        => 'maps',
                            'data-zoom-image' => $model->mapsViewer]
                        ],
                        'visible' => $model->maps ? true : false,
                        'value' => function($model){
                            return $model->mapsViewer;
                        }
                    ],
                    [
                        'attribute' => 'maps',
                        'format' => 'html',
                        'visible' => $model->maps ? true : false,
                        'value' => function($model){
                            return Html::a('<i class="fa fa-download"></i> '.Yii::t('common','Download'),"images/company/maps/{$model->maps}");
                        }
                    ],
                    'city',
                    'location',
                    'postcode',
                    'country',
                    'mobile',
                    'phone',
                    'fax',
                    'vat_register',
                    'vat_address',
                    'vat_city',
                    'vat_location',
                    'headofficetb.name',
                    'create_time',
                    'update_time',
                    'brand',
                ],
            ]) ?>

        </div>

    </div>

</div>
 

<?php 
$Yii = 'Yii';
// https://igorlino.github.io/elevatezoom-plus/examples.htm#
echo $this->registerJsFile('https://cdn.rawgit.com/igorlino/elevatezoom-plus/1.1.6/src/jquery.ez-plus.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$js=<<<JS

$("#maps").ezPlus({
    zoomType: 'lens',
    lensShape: 'round',
    lensSize: 200,
    //scrollZoom: true
});

$("#brand").ezPlus({easing: true});

$('.btn-app-print').remove();


$('.access-to-this-company').on('click',function(){
    let comp = $(this).data('key');
    if(confirm('Confirm ?')){
        fetch("?r=ajax/change-company", {
            method: "POST",
            body: JSON.stringify({comp:comp}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {   
            if(res.status===200){  

                
            }else{
                swal('{$Yii::t("common","Warning")} : ' + res.message, res.count, "warning");
            }
            
        })
        .catch(error => {
            console.log(error);
        });
    }
})
JS;

$this->registerJS($js,\yii\web\View::POS_END);