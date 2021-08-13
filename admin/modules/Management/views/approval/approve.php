<?php

use yii\helpers\Html;

$this->title = Yii::t('common', 'Approved');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="approval-approve" ng-init="Title='<?= Html::encode($this->title) ?>'">




<div class="panel panel-primary">
      <div class="panel-heading">
        <h4><?=$source->item_group;?></h4>
      </div>
      <div class="panel-body">
        <div class="row">
            <div class="col-sm-6">
                <label><?=Yii::t('common','Sale Amount')?></label>
                <div class="well">
                    <?=number_format($source->sale_amount,2);?>
                </div>
            </div>
            <div class="col-sm-6">
                <label><?=Yii::t('common','Discount')?></label>
                    <div class="well">
                        <?=number_format($source->discount,2);?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div id="dataTable" data-key="<?=$model->source_id?>">
                </div>
            </div>
        </div>
      </div>
      
</div>



</div>

<?php
        
    $Yii = 'Yii';
    $id = @$_GET['id'];
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
