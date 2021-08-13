<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\BomHeader */

use admin\modules\Itemset\models\FunctionItemset;
$Fnc = new FunctionItemset;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bom Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php

        
// $CalInven = $Fnc->ProMinBomLine($model->id);
// var_dump($CalInven);

?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>



<div class="bom-header-view" ng-init="Title='<?= Yii::t('common','Production Bom') ?>'">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            'code',
            'name',

            'description:ntext',
            [
                'label' => Yii::t('common','Inventory'),
                'format' => 'raw',
                'value' => function($model){
                    $items = \common\models\Items::find()->where(['ProductionBom' => $model->id])->all();
                    $data = '<ul style="  margin-left:-20px;">';
                    foreach ($items as $key => $item) {
                            $i = $key +1;
                            $data.= '<li ><a href="index.php?r=items/items/view&id='.$item->id.'" target="_blank">'.$i.'. '.$item->master_code.'</a></li>'."\r\n";
                                $data.= '<ul style="list-style-type:none;">';
                                    $data.= '<li> |---> '.Yii::t('common','Stock').' : '.$item->getInven().' '.$item->UnitOfMeasure.' </li>'."\r\n";
                                $data.= '</ul>';
                        
                    }
                    $data.= '</ul>';

                    return $data;
                }
            ],
            [
                'label' => Yii::t('common','Sale Price'),
                'value' => function($model){
                    return $model->items ? number_format($model->items->salePrice->avg,2) : 0;
                }
            ],           
           
            'create_date',
        ],
    ]) ?>

</div>
<hr class="style2">
<div class="row">
    <div class="col-md-12">
    <label><?=Yii::t('common','Bom Line') ?></label>
   
    <div class="BomLine">
        <?=$this->render('__bom_line_readonly',['model' =>$model,'dataProvider' => $dataProvider]); ?>
    </div>
    </div>
</div>
 
