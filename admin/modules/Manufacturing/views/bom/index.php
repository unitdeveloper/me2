<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Manufacturing\models\KitbomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'KIT BOM');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .space{
        margin:5px;
    }
    .space-toggle .toggle{
        margin:5px;
    }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>


<h4>สร้างชุด Kit และผูกเข้ากับกลุ่มตามต้องการ</h4>
<p>
	<a  data-toggle="collapse" href="#contentId" aria-expanded="false" aria-controls="contentId">
	คำอธิบาย <i class="fa fa-info-circle"></i>
	</a>
</p>
<div class="collapse" id="contentId">    
    <ul>
        <li>สร้างชุด Kit แล้วทำการเลือกกลุ่มสินค้าที่ต้องการจับคู่</li>
        <li>สามารถจับคู่ได้ไม่จำกัด</li>
        <li>การจับคู่ สามารถเลือกได้ทั้ง [ชุด Fixed] และ [ชุด Multiple]</li>
         
        <li>เมื่อสินค้าเหล่านี้ถูกเลือกจาก sale order
            <ul>                
                <li> หากยังไม่มีชื่อสินค้านั้น ระบบจะสร้างสินค้าขึ้นมาใหม่ จากชื่อที่ถูกเลือกไว้ และ "โค๊ดสินค้า" จะถูกสร้างจากค่าที่กำหนดไว้ใน [ชุด Fixed]</li>
                <li> หากมีชื่อสินค้านั้นอยู่แล้ว ระบบจะดึงสินค้านั้นไปใช้ (สินค้าจะไม่ถูกสร้างขึ้นมาใหม่)</li>
            </ul>
        </li>
    </ul>
    <div class="text-right" style="font-size:23px;"><a  data-toggle="collapse"  aria-expanded="false" aria-controls="contentId" href="#contentId" >[ X ]</a></div>
	<img src="images/KIT_BOM.jpg" class="img-responsive" />
</div>
<div class="kitbom-line-index" ng-init="Title='<?=$this->title;?>'">

 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'photo',
                'format' => 'html',
                'contentOptions'=>['style'=>'width:50px;'],
                'value' => function($model){
                    return  Html::a(Html::img($model->photoViewer,['class'=>'img-responsive','style'=>'width:50px;']),['/Manufacturing/bom/view/' ,'id' => $model->id]);
                }
            ],
           

            //'itemset.name',
            //'code',
            //'format_gen',
            [
                'attribute' => 'format_gen',
                'format' => 'raw',
                'label' => 'Code ที่จะสร้าง',
                'headerOptions' => ['style'=>'width:250px;'],
                'value' => function($model){
                    $html = ' ';
                    if($model->format_gen!='')
                    {

                        $html.= '<div class="input-group">';
                        $html.= '<input class="form-control text-right" value="'.$model->format_gen.'" style="background-color:#ccc; padding:5px; width:100%;" readonly>
                                    <span class="input-group-addon"  style="background-color:#a0bbe5; padding:5px;">1234</span>';
                        $html.= '</div>';
                        
                    } 

                    return $html;
                    
                }
            ],
            // [
            //     'attribute' => 'format_gen',
            //     'label' => 'Code ที่จะสร้าง',
            //     'value' => 'format_gen'
            // ],
            [
                'attribute' => 'code',
                'label' => 'ชื่อที่จะถูกสร้าง',
                'value' => 'code'
            ],
            [
                'attribute' => 'name',
                'format' => 'html',
                'label' => 'ชื่อเรียก' ,
                'value' => function($model){ 
                    $html = '<div>'.$model->name.'</div>';
                    $html.= '<div>'.$model->description.'</div>';

                    return $html;
                }
            ],
            //'description',
            
            //'name',
            
            
            //'fixed',
            // [
            //     'label' => yii::t('common','Multiple'),
            //     'format' => 'raw',
            //     'contentOptions' => ['class' => 'text-right'],
            //     'value' => function($model)
            //     {
            //         if($model->multiple == 1)
            //         {
            //             $status = 'checked';
            //         }else {
            //             $status = NULL;
            //         }
            //         $data = '<input id="ew-mutiple" type="checkbox" '.$status.'   data-toggle="toggle" data-style="android" data-onstyle="info" value="'.$model->multiple.'" ew-id="'.$model->id.'" data-on="'.Yii::t('common','Multiple').'" data-off="'.Yii::t('common','Fix').'">';

            //         return $data;
            //     }
            // ],

            
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right','style'=>'width:120px;'],
                'contentOptions' => ['class' => 'text-right space-toggle'],
                'value' => function($model){

                    return SwitchInput::widget([
                        'name' => 'mutiple',
                        'value' => $model->multiple,
                        'pluginOptions' => [
                            'onColor' => 'success',
                            'offColor' => 'info',
                            'size' => 'small',
                            'onText' => Yii::t('common','Multi (ตัวมิกซ์)'),
                            'offText' => Yii::t('common','Fix (ตัวหลัก)'),
                            'class' => 'pull-left'
                        ] 
                    ]);
                     
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'options'=>['style'=>'width:90px;'],
                'contentOptions' => ['class'=>' text-center'],
                'buttonOptions'=>['class'=>'btn btn-default','title' => ''],
                'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update}  </div>'
            ],
            // 'quantity',
            // 'color_style',
            // 'comp_id',
            // 'user_id',
            //'running_digit',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>


<?php 

$js=<<<JS

$('input[name="mutiple"]').on('switchChange.bootstrapSwitch', function (event, state) {
    let id      = $(this).closest('tr').attr('data-key');
    let value   = state;
    $.ajax({
        url:'index.php?r=Manufacturing/ajax/update-multiple',
        type:'POST',
        data: {param:{id:id,val:value}},
        dataType:'JSON',
        success:function(response){

            if(response.status===200){
                $.notify({
                    // options
                    icon: 'far fa-save',
                    message: 'Saved',                         
                },{
                    // settings
                    type: 'success',
                    delay: 1500,
                    z_index:3000,
                    placement: {
                        from: "top",
                        align: "center"
                    }
                });
            }
           
        }
    })
  });

//   $(function() {     
//     $('.ew-status').change(function() {
//       route('index.php?r=Manufacturing/ajax/update-status','POST',{param:{id:$(this).attr('ew-enable'),val:$(this).prop('checked')}},'Navi-Title');
//     })
//   });

 
 
  

JS;

$this->registerJs($js,\yii\web\View::POS_END,'JS');
?>