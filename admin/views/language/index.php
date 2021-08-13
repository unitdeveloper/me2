<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Language Setup');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<style media="screen">
  .editable{
    font-size: 15px;
  }
</style>
<div class="source-message-index" ng-init="Title='<?= Html::encode($this->title) ?>'" >

  <div class="row">
    <div class="col-sm-6 pull-right">
      <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
  </div>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'category',
            //'message:ntext',
            // [
            //   'label' => Yii::t('common','Type'),
            //   'value' => function($model){
            //     return $model->category;
            //   }
            // ],
            [
              'attribute' => 'message',
              'label' => Yii::t('common','Message'),
              'encodeLabel' => false,
              'value' => function($model){
                return $model->message;
              }
            ],
            [
              'label' => '<span class="flag-icon flag-icon-gb"></span> English',
              'encodeLabel' => false,
              'format' => 'raw',
              'value' => function($model){
                return '<input type="text" name="en"  value="'.$model->getLanguage('en')->text.'" data-key="'.$model->getLanguage('en')->id.'"
                class="form-control editable" style="background-color:#a9b1f9"/>';
              }
            ],
            [
              'label' => '<span class="flag-icon flag-icon-th"></span> ไทย',
              'encodeLabel' => false,
              'format' => 'raw',
              'value' => function($model){
                return '<input type="text" name="th"  value="'.$model->getLanguage('th')->text.'" data-key="'.$model->getLanguage('th')->id.'"
                class="form-control editable" style="background-color:#d8fcf4"/>';
              }
            ],
            [
              'label' => '<span class="flag-icon flag-icon-cn"></span> 中文',
              'encodeLabel' => false,
              'format' => 'raw',
              'value' => function($model){
                //return $model->getLanguage('zh');
                return '<input type="text" name="zh"  value="'.$model->getLanguage('zh')->text.'" data-key="'.$model->getLanguage('zh')->id.'"
                class="form-control editable" style="background-color:#fce7d8"/>';
              }
            ],

            [
              'label' => '<span class="flag-icon flag-icon-la"></span> ພາສາລາວ',
              'encodeLabel' => false,
              'format' => 'raw',
              'value' => function($model){
                //return $model->getLanguage('la');
                return '<input type="text" name="la" value="'.$model->getLanguage('la')->text.'" data-key="'.$model->getLanguage('la')->id.'"
                class="form-control editable" style="background-color:#d8d9fc"/>';
              }
            ],


            [
              'label' => Yii::t('common','Delete'),
              'format' => 'raw',
              'value' => function($model){
                return '<button data-key="'.$model->id.'" class="btn btn-danger btn-flat delete" ><i class="fa fa-trash"></i> '.Yii::t('common','Delete').'</button>';
              }
            ],
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=>Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>Yii::t('common','next'),    // Set CSS class for the "next" page button
            'prevPageCssClass'=>Yii::t('common','prev'),    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>Yii::t('common','first'),    // Set CSS class for the "first" page button
            'lastPageCssClass'=>Yii::t('common','last'),    // Set CSS class for the "last" page button
            'maxButtonCount'=>6,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
<?php Pjax::end(); ?></div>
<div class="textRes">

</div>
<?php

$language = (object)[

  'Accept' => 'ຍອມຮັບ',

];
foreach ($language as $key => $value) {

  // if(\common\models\SourceMessage::find()->where(['message' => $key])){
  //   // Update
  //   $models = \common\models\SourceMessage::find()->where(['message' => $key])->one();
  //
  //   $id = $models['id'];
  // }else {
  //   // Create
  //   $models = new \common\models\SourceMessage();
  //   $models->category = 'common';
  //   $models->message = $key;
  //   $models->save();
  //
  //   $id = $models->id;
  // }
  //
  // $mess = \common\models\Message::find()
  // ->where(['id' => $id])
  // ->andwhere(['language' => 'la'])
  // ->one();
  //
  // if($mess){
  //
  //   // Update
  //   // $message = \common\models\Message::find()->where(['id' => $models->id])->andWhere(['language' => 'th']);
  //   // $message->id = $models->id;
  //   // $message->language = 'th';
  //   // $message->translation = $value;
  //   // $message->save();
  //
  // }else {
  //   // Create
  //   $message = new \common\models\Message();
  //   $message->id = $id;
  //   $message->language = 'la';
  //   $message->translation = $value;
  //   $message->save();
  // }

}
$this->registerJs("
  $('body').on('keyup','input.editable',function(){
    console.log($(this).val());
    $.ajax({
      url:'index.php?r=language/index',
      method:'POST',
      data:{id:$(this).data('key'),language:$(this).attr('name'),text:$(this).val(),parent:$(this).closest('tr').data('key')},
      async:true,
      success:function(response){
        $('.textRes').html(response);
      }
    });
  });

  $('body').on('click','button.delete',function(){
    if(confirm(lang('common','Delete')+' ?')){
      $.ajax({
        url:'index.php?r=language/delete&id='+$(this).data('key'),
        method:'POST',
        data:{id:$(this).data('key')},
      });

    }
  })


");
?>
