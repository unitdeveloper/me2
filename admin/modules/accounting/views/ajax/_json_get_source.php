<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
//use yii\grid\GridView;
use kartik\grid\GridView;
 
use yii\db\Expression;
 
use common\models\WarehouseMoving;

?>
 <?php Pjax::begin(['id' => 'ew-get-source-pjax', 
            'timeout' => 10000, 
            'enablePushState' => false, 
            'enableReplaceState' => false,
            'clientOptions' => ['method' => 'POST']]); ?>     
<!--row -->
<div class="row">
    <div class="col-sm-6">

        <div class="input-group">
            <input type="text" name="search-shipment" class="form-control" id='ew-search-ship' placeholder="<?=Yii::t('common','Search');?>..."/>
            <span class="input-group-btn">
              <button type='button' name='search' id='ew-search-ship-btn' class="btn btn-default btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
        </div>


    </div>

</div>
<!--/.row -->
 

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        //'pjax' => true,
        'rowOptions' => ['style' => 'cursor:pointer;'],
        'tableOptions' => ['id' => 'table-get-source','class' => 'table-hover'],
        'columns' => [
            [
                'format' => 'raw',
                'value' => function($model){
                    return '<input type="checkbox" class="ship ew-checked" name="ship[]" cust="'.$model->customer_id.'" value="'.$model->id.'">';
                }
            ],
        	[
        		'attribute' => 'PostingDate',
            	'format' => 'raw',
            	'value' => function($model){
            		 
            		return date('d/m/Y',strtotime($model->PostingDate));
            	}
            ],
            [

            	'attribute' => 'SourceDoc',
            	'format' => 'raw',
            	'value' => function($model){
            		return $model->SourceDoc;
            	}
            ],

            [

            	'attribute' => 'DocumentNo',
            	'format' => 'raw',
            	'value' => function($model){
            		// $data = '<div >';
            		// $data.= '<div><input type="checkbox" class="ship ew-checked" name="ship[]" cust="'.$model->customer_id.'" value="'.$model->id.'"> '.$model->DocumentNo.' </div>';
            		// $data.= '</div>';
            		return $model->DocumentNo;
            	}
            ],

            'customer.name',

            'status',
            [
                'label' => Yii::t('common','Transport'),
                'format' => 'raw',
                'value' => function($model)
                {
                 return '<i class="fa fa-truck  " aria-hidden="true"></i> '.$model->Description;
                }
            ],

             
        ],
    ]); ?>
<?php Pjax::end(); ?>

<div class="ew-render-getsource"></div>

<script type="text/javascript">
$(document).ready(function(){

    $('#table-get-source tr').click(function(event) {
      if (event.target.type !== 'checkbox') {

        $(':checkbox', this).trigger('click');
         
      }
    });
});
</script>