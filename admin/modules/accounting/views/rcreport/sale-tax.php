<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

 
$this->title = Yii::t('common','Sales tax');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales tax'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h4>รายงานนี้ ยังไม่ใช่รายงานภาษีขาย (เป็นเพียงใบสั่งขายที่ออกบิล Vat เท่านั้น)</h4>
<div class="sales-tax">
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

            		 if($model->update_date != ''){
                        $date = date('d/m/Y',strtotime($model->update_date));
                    }else {
                        $date = date('d/m/Y',strtotime($model->create_date));
                    } 
                    return $date;
            	}
            ],
            'no',
            'customer.name',
            [
            	'label' => Yii::t('common','Value of goods'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		return $model->balance;
            	}
            ],
            [
            	'label' => Yii::t('common','Vat'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		return $model->balance * 7 /100;
            	}
            ],

            [
            	'label' => Yii::t('common','Amount'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		return $model->balance + ($model->balance * 7 /100);
            	}
            ],


            
        ]; ?>
<?php
 echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'columnSelectorOptions'=>[
                'label' => 'Columns',
                'class' => 'btn btn-danger'
            ],
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Export All',
                'class' => 'btn btn-primary'
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
             // ExportMenu::FORMAT_PDF => [
             //                 'label' => Yii::t('common', 'PDF'),
             //                 'icon' =>  'file-pdf-o',
             //                 'iconOptions' => ['class' => 'text-danger'],
             //                 //'linkOptions' => [],
             //                 'options' => ['title' => Yii::t('common', 'Portable Document Format')],
             //                 'alertMsg' => Yii::t('common', 'The PDF export file will be generated for download.'),
             //                 'mime' => 'application/pdf',
             //                 'extension' => 'pdf',
             //                 'writer' => 'PDF',
             //             ],
            ],
            'styleOptions' => [
                ExportMenu::FORMAT_PDF => [
                    'font' => [
                         'family' => ['THSarabunNew','garuda'],
                            'bold' => true,
                            'color' => [
                                 'argb' => 'FFFFFFFF',
                         ],
                    ],
                ],
            ],
        ]); 
?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,

         
        'columns' => $gridColumns,

         
    ]); ?>

</div>
 