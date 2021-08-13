<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\express\models\VatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'ข้อมูลวันที่ 14/05/2019');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="isvat-index font-roboto">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('common', 'Home'), ['index'], ['class' => 'btn btn-primary']) ?>
         
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
            echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' =>  [
                            'VATDAT',
                            'DOCNUM',
                            [
                                'label' => 'EWIN',
                                'value' => function($model){
                                    $inv = \common\models\RcInvoiceHeader::find()->where(['no_' => $model->DOCNUM])->one();
                                    if($inv!=null){
                                        return $inv->no_;
                                    }else{
                                        return '';
                                    }
                                }
                            ],
                            'DESCRP:text',                            
                            'AMT01',
                            'VAT01',
                            'TAXID',
                        ],
                        'columnSelectorOptions'=>[
                            'label' => Yii::t('common','Columns'),
                            'class' => 'btn btn-success-ew'
                        ],
                        'fontAwesome' => true,
                        'dropdownOptions' => [
                            'label' => Yii::t('common','Export All'),
                            'class' => 'btn btn-primary-ew'
                        ],
                        'exportConfig' => [
                            ExportMenu::FORMAT_HTML => false,
                        ],
                        'styleOptions' => [
                            ExportMenu::FORMAT_PDF => [
                                'font' => [
                                    'family' => ['garuda'],
                                        //'bold' => true,
                                        'color' => [
                                            'argb' => 'FFFFFFFF',
                                    ],
                                ],
                            ],
                        ],
                        'filename' => Yii::t('common','Inv'),
                        //'encoding' => 'utf8',
                    ]);
        ?>
    <?php Pjax::begin();?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'TREC',
            //'VATTYP',
            //'RECTYP',
            //'VATPRD',
            //'LATE',
            'VATDAT',
            //'DOCDAT',
            
            [
                'attribute' => 'DOCNUM',
                'label' => 'EXPRESS',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->DOCNUM,['view','id' => $model->id], ['target'=>'_blank', 'data-pjax'=>"0"]);
                }
            ],

            [
                'label' => 'EWIN',
                'format' => 'html',
                'value' => function($model){
                    $inv = \common\models\RcInvoiceHeader::find()->where(['no_' => $model->DOCNUM])->one();
                    if($inv!=null){
                        return Html::a($inv->no_,['/accounting/posted/posted-invoice','id' => base64_encode($inv->id)], ['target'=>'_blank', 'data-pjax'=>"0"]);
                    }else{
                        return '';
                    }
                }
            ],
            //'REFNUM',
            //'NEWNUM',
            //'DEPCOD',
            'DESCRP:text',
            'AMT01',
            'VAT01',
            //'AMT02',
            //'VAT02',
            //'AMTRAT0',
            //'REMARK',
            //'SELF_ADDED',
            //'HAD_MODIFY',
            //'DOCSTAT',
            'TAXID',
            //'ORGNUM',
            //'PRENAM',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end();?>
</div>
