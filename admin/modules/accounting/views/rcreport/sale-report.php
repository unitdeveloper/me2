<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use common\models\SaleHeader;

 
$this->title = Yii::t('common','Sales report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales report'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
 
<h4>รายงานนี้ (ยังไม่เสร็จ) </h4>
<div class="sales-report" >
<?php 
        $gridColumns = [
            ['class' => 'kartik\grid\SerialColumn'],
            
            //'update_date',
            [
            	'label' => Yii::t('common','Date'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		 //$strYear = date("Y",strtotime($strDate))+543;
            	 

            		 if($model->orderNo->update_date != ''){
                        $date = date('d/m/Y',strtotime($model->orderNo->update_date));
                    }else {
                        $date = date('d/m/Y',strtotime($model->orderNo->create_date));
                    } 
                    return $date;
            	}
            ],
            'order_no',
            [
            	'label' => Yii::t('common','Customer ID'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		 //$strYear = date("Y",strtotime($strDate))+543;
            		 
            		  
                    return $model->orderNo->customer->code;
            	}
            ],
            [
            	'label' => Yii::t('common','Customer'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		 //$strYear = date("Y",strtotime($strDate))+543;
            		 
            		  
                    return $model->orderNo->customer->name;
            	}
            ],
            'itemstb.master_code',
            'itemstb.Description',
            'quantity',
            'unit_price',
            [
            	'label' => yii::t('common','Total amount'),
            	'value' => function($model)
            	{
            		return $model->quantity * $model->unit_price;
            	}
            ],
             

            
        ]; ?>
<?php
 echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'columnSelectorOptions'=>[
                'label' => ' ',
                'class' => 'btn btn-warning'
            ],
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Export All',
                'class' => 'btn btn-primary'
            ],

    //         'exportConfig' => [
    //         	ExportMenu::FORMAT_HTML => false,
    //         	ExportMenu::FORMAT_PDF => [
				// 		        'label' => Yii::t('common', 'PDF'),
				// 		        'icon' =>  'file-pdf-o',
				// 		        'iconOptions' => ['class' => 'text-danger'],
				// 		        //'linkOptions' => [],
				// 		        'options' => ['title' => Yii::t('common', 'Portable Document Format')],
				// 		        'alertMsg' => Yii::t('common', 'The PDF export file will be generated for download.'),
				// 		        'mime' => 'application/pdf',
				// 		        'extension' => 'pdf',
				// 		        'writer' => 'PDF',
				// 		    ],
    //         ],
    //         'styleOptions' => [
				// ExportMenu::FORMAT_PDF => [
		  //           'font' => [
		  //           		'family' => ['garuda'],
				//             'bold' => true,
				//             'color' => [
				// 	                'argb' => 'FFFFFFFF',
			 //            	],
			 //        ],
		  //       ],
		  //   ],
        ]); 


?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,

         
        'columns' => $gridColumns,

         
    ]); ?>

</div>
 