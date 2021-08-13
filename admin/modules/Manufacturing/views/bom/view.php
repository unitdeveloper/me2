<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\KitbomLine */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Kitbom Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="kitbom-line-view" ng-init="Title='<?=$this->title;?>'" id="ew-bom-id" ew-bom-id="<?=$model->id ?>">


<p>
         
        <?= Html::a('<i class="fa fa-trash"></i> '.Yii::t('common', 'Delete'), ['delete-header', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
</p>



<div class="row">
    <div class="col-sm-12 mt-10">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'code',
            'name',
            'priority',
            //'description',
           // 'max_val',
            //'itemset.name',
            //'company.name',
            //'user.username',
            //'format_gen',
            // [
            //     'attribute' => 'format_gen',
            //     'format' => 'html',
            //     'contentOptions' => ['style' => 'border: 1px solid #ccc; height: 40px; background-color: #000; color: #FFF; text-align: center; font-size: 25px; padding-top: 2px;'],
            //     'value' => $model->format_gen


            // ],
            [
                'attribute' => 'format_gen',
                'format' => 'raw',
                'value' => function($model){

                    //$html = '<div>'.$model->description.'</div>';
                    $html = ' ';
                    if($model->format_gen!='')
                    {

                        $html.= '<div class="input-group" style="max-width:200px;">';
                        $html.= '<input class="form-control text-right" value="'.$model->format_gen.'" style="background-color:#ccc; padding:5px; width:100%;" readonly>
                                    <span class="input-group-addon"  style="background-color:#a0bbe5; padding:5px;">1234</span>';
                        $html.= '</div>';
                        
                    } 

                    return $html;
                    
                }
            ],
            // [
            //     'attribute' => 'photo',
            //     'format' => 'html',
            //     'value' => Html::img($model->photoViewer,['class'=>'img-thumbnail','style'=>'width:200px;'])
            // ],
        ],
    ]) ?>
    </div>
</div>

</div>

<p>
	<a  data-toggle="collapse" href="#contentId" aria-expanded="false" aria-controls="contentId">
	คำอธิบาย <i class="fa fa-info-circle"></i>
	</a>
</p>
<div class="collapse" id="contentId">    
    <ul>
        <li>เพิ่มรายการสินค้าเข้าไปในชุด เพื่อให้รู้ว่า ในชุดนี้มีสินค้าอะไรให้เลือกบ้าง</li>
        <li>*** หากเป็น ​Fix จะไม่สามารถเลือกได้ (ส่วนใหญ่จะเป็นส่วนประกอบหลัก เช่น ตู้เปล่า)</li>
        <li>*** หากเป็น Multi จะสามารถเลือกได้จากหน้า Sale order (เช่น ลูกเซอร์กิต สามารถเลือกได้เองว่าจะเอากี่ Amp)</li>
        <li><?= Yii::t('common','Default quantity')?> ถ้าเป็น Multi ไม่ต้องใส่ (ให้เลือกเองจาก sale order)</li>
    </ul>
    <div class="text-right" style="font-size:23px;"><a  data-toggle="collapse"  aria-expanded="false" aria-controls="contentId" href="#contentId" >[ X ]</a></div>
	<img src="images/KIT_BOM_MIX.jpg" class="img-responsive" />
</div>

<div class="row">
    <div class="col-sm-12 ew-bom-line">
    <h4 >รายการที่จะแสดงบนตัวเลือก  </h4>
    <?php echo  $this->render('_bom_line',[
            'model' => $model,
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]) ?>
    </div>
</div>

<?= $this->render('_script_js') ?>