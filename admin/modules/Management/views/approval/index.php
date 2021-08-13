<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Management\models\ApprovalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Approvals');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="approval-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $layout = <<< HTML
    <div class="pull-right">
        <div class="row">
            <div class="col-xs-4">{summary}</div>
            <div class="col-xs-8">{$this->render('_search', ['model' => $searchModel])}</div>
        </div>
    </div>
    {custom}
    <div class="clearfix"></div>
    {items}
    <div class="row">
        <div class="pull-right">
            <div class="col-xs-12">
            {pager}
            </div>
        </div>
    </div>
HTML;
?>    

 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
             
            [
                'attribute' => 'document_type',
                'label' => Yii::t('common','Document'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->document_type,'#',['data-key' => $model->id,'class' => 'show-detail-on-modal']);
                }
            ],
            [
                'attribute' => 'detail',
                'label' => Yii::t('common','Detail'),
                'format' => 'raw',
                'value' => 'detail'
            ],
            [
                'attribute' => 'field_data',
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'value' => 'field_data'
            ],
            [
                'attribute' => 'balance',
                'label' => Yii::t('common','Discount'),
                'format' => 'raw',
                'value' => 'balance'
            ],
             
            //'source_id',
            //'ip_address',
            //'document_type',
            //'sent_by',
            //'sent_time',
            //'approve_date',
            //'approve_by',
            //'comp_id',
            //'approve_status',
            //'gps',
            //'balance',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:286px; max-width:286px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group"> {reject} {detail} {approve}  </div>',
                'buttons'=>[
                    'detail' => function($url,$model,$key){  
                        return Html::a('<i class="fas fa-eye"></i> ' .Yii::t('common','View'),'#',[
                            'class' => 'btn btn-info show-detail-on-modal',
                            'data-key' => $model->id,  
                            'data' => [
                                                               
                            ],
                        ]);                                                   
                    },
                    'reject' => function($url,$model,$key){  
                        return Html::a('<i class="fas fa-ban"></i> ' .Yii::t('common','Reject'),$url,[
                            'class' => 'btn btn-warning',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to Reject this?'),
                                'method' => 'post',
                            ],
                        ]);                                                   
                    },
                    'approve' => function($url,$model,$key){  
                        return Html::a('<i class="fas fa-check"></i> ' .Yii::t('common','Approve'),$url,[
                            'class' => 'btn btn-primary',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to Approve this?'),
                                'method' => 'post',
                            ],
                        ]);                                                   
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-success']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-primary']);
                    }
        
                ]
            ],        
        ],
        'layout' => $layout,
        'replaceTags' => [
            '{custom}' => function($widget) {
                // you could call other widgets/custom code here
                if ($widget->panel === false) {
                    return '';
                } else {
                    return '';
                }
            }
        ],
    ]); ?>
</div>
 
<div class="modal fade" id="modal-id-show-detail-approve">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Approval')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <div class="btn-group btn-group text-center" role="group"> 
                    <?=Html::a('<i class="fas fa-ban"></i> ' .Yii::t('common','Reject'),'#',[
                            'class' => 'btn btn-warning reject-button',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to Reject this?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                    <?=Html::a('<i class="fas fa-check"></i> ' .Yii::t('common','Approve'),'#',[
                            'class' => 'btn btn-primary approve-button',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to Approve this?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                     
                </div>
            </div>
        </div>
    </div>
</div>

<?php
 
$Yii = 'Yii';
$api = Yii::$app->params['api'];

$MyToken = \common\models\Authentication::findOne(['user_id' => Yii::$app->user->identity->id]);
$token = base64_encode($MyToken->token);

$js=<<<JS

    $('body').on('click','a.show-detail-on-modal',function(){
        $('#modal-id-show-detail-approve').modal('show');
        var id = $(this).data('key');
        var rejectBtn = 'index.php?r=Management/approval/reject&id=' + id;
        var approveBtn = 'index.php?r=Management/approval/approve&id=' + id;

        $('a.reject-button').attr('href',rejectBtn);
        $('a.approve-button').attr('href',approveBtn);

         
        $.ajax({
            url: '{$api}/approval/detail',
            type:'POST',
            data:{id:id},
            headers: {"token": '{$token}',"X-CSRF-Token": $('meta[name="csrf-token"]').attr('content')},
            dataType:'JSON',
            success:function(response){
                console.clear();
                console.warn('console.table()');
                console.table(response.detail.group);
           
                var html = '';
                if (response.status==200){

                    html += '<h4>' + response.detail.group[0].item_group + '</h4>';
                    html += '<table class="table">';
                    html += '<thead>';
                    html += '   <tr>';
                    //html += '       <th class=" ">Group</th>';
                    html += '       <th class=" ">{$Yii::t("common","Code")}</th>';
                    html += '       <th class=" ">{$Yii::t("common","Items")}</th>';
                    html += '       <th class="text-right">{$Yii::t("common","Balance")}</th>';
                    html += '       <th class="text-right">{$Yii::t("common","Discount")}</th>';
                    html += '   </tr>';
                    html += '</thead>';

                    (response.detail.items).forEach(element => {
                        
                        html += '<tr>';
                        //html += '   <td class=" ">' + element.group_name +'</td>';
                        html += '   <td class=" ">' + element.master_code +'</td>';
                        html += '   <td class=" ">' + element.items +'</td>';
                        html += '   <td class="text-right">' + number_format(response.detail.group[0].sale_amount) +'</td>';
                        html += '   <td class="text-right">' + number_format(response.detail.group[0].discount) +'</td>';
                        html += '</tr>';
                    });
                    html += '</table>';
                    
                }else{
                    html = '<div>' + response.message +'</div>';                
                }
                $('#modal-id-show-detail-approve').find('.modal-body').html(html);
            }
        })
    });

    

    $('body').on('click','button.approve-button',function(){
        console.log('approve');
    })

    $('body').on('click','button.reject-button',function(){
        console.log('reject');
    })
JS;
$this->registerJs($js);