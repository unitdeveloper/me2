<?php 

use yii\helpers\Html;
use common\models\WarehouseMoving;
use dosamigos\chartjs\ChartJs;

$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");

$LastTime       = 5;

$movement = WarehouseMoving::find()
    ->where(['item' => $model->id])
    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
    ->orderBy([
        'PostingDate' => SORT_DESC,
        'SourceDocNo' => SORT_DESC
    ])
    ->limit($LastTime)
    ->all();

$chart_labels   = [];
$chart_data     = [];
$chart_color    = [];
foreach ($movement as $key => $wh) {
    $Date        = date('d',strtotime($wh->PostingDate));
    $Month       = $strMonthCut[date('n',strtotime($wh->PostingDate))];
    $Years       = date('Y',strtotime($wh->PostingDate));

    $chart_labels[] = Yii::t('common',$wh->TypeOfDocument) ."\r\n". $Date. "-" .$Month . "-" . $Years ."\r\n";
    $chart_data[]   = $wh->Quantity;
    $chart_color[]  = $wh->Quantity > 0 ? "rgba(88, 228, 157, 0.51)" : "rgba(224, 114, 114, 0.51)";
}
?>
<h4><?= Html::a(Yii::t('common','Last {:time} Movement', [':time' => $LastTime]),['/warehousemoving/warehouse', 'WarehouseSearch[ItemNo]' => $model->master_code],['target' => '_blank'])?> </h4>
<?= ChartJs::widget([
        'type' => 'bar',
        'options' => [
            'height' => 150,
            'width' => 400
        ],
        'data' => [
            'labels' => $chart_labels,
            'datasets' => [                 
                [
                    'label' => $model->master_code,
                    'backgroundColor' => $chart_color,
                    'borderColor' => "rgba(27, 114, 222, 0.1)",
                    'pointBackgroundColor' => "rgba(255,99,132,1)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(255,99,132,1)",
                    'data' => $chart_data
                ]
            ]
        ]
    ]);
?>
<div>*** วันที่ตัดสต๊อก</div>