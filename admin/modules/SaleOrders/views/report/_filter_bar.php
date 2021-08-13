<?PHP 
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\form\ActiveForm;
?>
<style>
.border-gray{
    border:1px solid #ccc;
}
</style> 
<div class="row">   
    <div class="col-sm-12">
        
        <?php $form = ActiveForm::begin([           
            'options' => [               
                'class' => 'well',
                ]]);
            ?>
            <?php 
                $model->fdate = (@$_POST['BestsaleSearch']['fdate'])? date('Y-m-d',strtotime($_POST['BestsaleSearch']['fdate'])) : date('Y').'-01-01'; 
                $model->tdate = (@$_POST['BestsaleSearch']['tdate'])? date('Y-m-d',strtotime($_POST['BestsaleSearch']['tdate'])) : date('Y-m-d'); 
            ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">   
                        <div class="col-sm-6">
                            <?=$form->field($model,'fdate',[
                                'addon' => [
                                    'prepend' =>  
                                        ['content' => Yii::t('common','From Date')]                                                    
                                ]
                            ])->textinput([
                                'type'          => 'date',
                                'class'         => 'form-control',
                                'pattern'       => '\d{4}-\d{1,2}-\d{1,2}',
                                'placeholder'   => 'yyyy-mm-dd',
                                'required'      => true
                                ])->label(false)?>
                        </div>
                        <div class="col-sm-6">
                            <?=$form->field($model,'tdate',[
                                'addon' => [
                                    'prepend' => ['content' => Yii::t('common','To Date')],                                
                                ]
                            ])->textinput([
                                'type'          => 'date',
                                'class'         => 'form-control',
                                'pattern'       => '\d{4}-\d{1,2}-\d{1,2}',
                                'placeholder'   => 'yyyy-mm-dd',
                                'required'      => true
                                ])->label(false)?>
                        </div>
                    </div>       

                </div>
                <div class="col-sm-4">                     
                    
                    <?= Html::submitButton('<i class="fas fa-search"></i> '.Yii::t('common', 'Filter'),
                    [
                        'class' => 'btn btn-default-ew btn-flat pull-right',
                        'data-rippleria' => true,
                    ]) ?>

                   
                </div>
                <div class="col-sm-2 text-right" >
                    <?= Html::a('<i class="fas fa-sync-alt text-warning"></i> '.Yii::t('common', 'Clear Filter'),['/SaleOrders/report/best-sale'],
                    [
                        'class' => 'btn btn-info-ew btn-flat hidden-xs',
                        'data-rippleria' => true,
                    ]) ?>
                    
                </div>
            </div> <!-- /.row -->
        <?php ActiveForm::end(); ?> <!-- /.well -->
    </div>
</div>