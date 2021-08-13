<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\users\models\SearchUsers */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="user-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

 
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
 
    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            
            [
                'label' => Yii::t('common','Photo'),
                'format' => 'html',
                'value' => function($model){
                    return Html::a(Html::img($model->profile['picture'],['class' => 'img-responsive','style' => 'width:50px;']),
                    ['/users/users/view','id' => $model->id],
                    ['target' => '_blank']);
                }
            ],
            //'id',
            [
                'attribute' => 'username',
                'label' => Yii::t('common','Username'),
                'format' => 'raw',
                'value' => function($model){
                    return '<div>
                                <div>'.$model->username.'</div>
                                <div>'.$model->email.'</div>
                            </div>';
                }
            ],
            'profile.name',
            //'profile.sales.name',
            //'email',
            [
                'label' => Yii::t('common','Company'),
                'format' => 'html',
                'value' => function($model){
                    $html = '<div class="text-aqua">'.($model->rule ? ($model->rule->company ? $model->rule->company->name : '') : '').'</div>';

                    $html.= '<div><span class="text-info">'.Yii::t('common','Sale People').' :</span> '.$model->profile->sales->name.'</div>';
                    return $html;
                }
            ],
            //'profile.salepeople.company.name',
            // 'auth_key',
            // 'confirmed_at',
            // 'unconfirmed_email:email',
            // 'blocked_at',
            // 'registration_ip',
            // 'created_at',
            // 'updated_at',
            // 'flags',
            // 'last_login_at',
            // 'status',
            // 'password_reset_token',
            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:230px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{updaterule} {passwd} {view}  {update} {delete}</div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    'passwd' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-key"></i> ','javascript:void(0)',[
                            'class'=>'btn btn-warning',       
                            'id' => 'change-password',                     
                            'data-key' => $model->id,
                            'alt' => Yii::t('common','Change Password'),
                            'Title' => Yii::t('common','Change Password')
                            ]);
                    },

                    'updaterule' => function($url,$model,$key){
                        return Html::a('<i class="fab fa-expeditedssl"></i> ',
                        ['/apps_rules/rules/update', 'id' => $model->rules->id],
                        [
                            'target' => '_blank',
                            'class'=>'btn btn-default',
                            'alt' => Yii::t('common','Rules'),
                            'Title' => Yii::t('common','Rules')
                        ]
                        );
                    },
                    
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',$url,[
                            'class'=>'btn btn-info',
                            'alt' => Yii::t('common','View'),
                            'Title' => Yii::t('common','View')
                            ]);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                            'alt' => Yii::t('common','Delete'),
                            'Title' => Yii::t('common','Delete')
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',$url,[
                            'class'=>'btn btn-success',
                            'alt' => Yii::t('common','Edit'),
                            'Title' => Yii::t('common','Edit')
                            ]);
                    }

                  ]
              ],
        ],
    ]); ?>
    </div>
</div>

 
<div class="modal fade" id="modal-password-reset">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Change Password : <span id="user-profile">?</span> </h4>
            </div>
            <div class="modal-body">
                <form id="change-pass">
                    <div class="row margin-top">
                        <div class="col-xs-3">
                            Username : 
                        </div>
                        <div class="col-xs-9">
                           <input type="text" name="username" class="form-control" readonly="readonly">
                        </div>
                    </div>
                    <div class="row margin-top">
                        <div class="col-xs-3">
                            Password : 
                        </div>
                        <div class="col-xs-9">
                           <input type="text" name="password" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="row margin-top">
                        <div class="col-xs-3">
                            Re password : 
                        </div>
                        <div class="col-xs-9">
                           <input type="password" name="re-password" class="form-control" autocomplete="off" ng-model="passwd">
                        </div>
                    </div>
                    
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i> Close</button>
                <button type="button" class="btn btn-primary" id="save-change" disabled="disabled"><i class="far fa-save"></i> Save changes</button>
            </div>
        </div>
    </div>
</div>


<?php
$js=<<<JS
$('body').on('click','#change-password',function(){
    
    var id = $(this).data('key');
    $.ajax({
        url:'index.php?r=users/users/ajax-get&id='+id,
        type:'POST',
        data:{id:id},
        dataType:'JSON',
        success:function(response){
            if(response.status==200){
                $('#user-profile').text(response.data.name);
                $('form#change-pass').attr('data-key',response.data.id);
                $('input[name="username"]').val(response.data.username).attr('data-value',response.data.username);
                $('input[name="password"]').val('').attr('style','border:1px solid #ccc;');
                $('input[name="re-password"]').val('').attr('style','border:1px solid #ccc;');

                $('#modal-password-reset').modal('show');
            }
        }
    })
});

$('body').on('change','input[name="password"]',function(){
    $('input[name="password"]').attr('style','border:1px solid green;');
});

$('body').on('keyup','input[name="re-password"]',function(){
    if($('input[name="password"]').val() === $('input[name="re-password"]').val()){
        $('input[name="password"]').attr('style','border:1px solid green;');
        $('input[name="re-password"]').attr('style','border:1px solid green;');
        $('button#save-change').attr('disabled',false);
    }else{
        $('input[name="re-password"]').attr('style','border:1px solid red;');
        $('button#save-change').attr('disabled',true);
    }
});

$('body').on('click','button#save-change',function(){
    let id = $('form#change-pass').attr('data-key');
    let password1 = $('input[name="password"]').val();
    let password = $('input[name="re-password"]').val();

    $.ajax({
        url:'index.php?r=users/users/ajax-change-passwd&id='+id,
        type:'POST',
        data:{id:id,password:password,password1:password1},
        dataType:'JSON',
        success:function(response){
            if(response.status==200){
                 
                $('#modal-password-reset').modal('hide');

                swal(
                    "เปลี่ยนรหัสผ่านแล้ว",
                    'The password has been successfully changed.',
                    'success'
                    );
            }else{
                swal(
                    "'มีปัญหาบางอย่างเกิดขึ้น...'",
                    'Error : ' + response.message,
                    'success'
                    );
            }
        }
    })
});
JS;
$this->registerJs($js,\yii\web\view::POS_END,'Yii');