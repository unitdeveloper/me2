<?php 

use yii\helpers\Html;
use common\models\ViewInvoiceLine;
use dosamigos\chartjs\ChartJs;

$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");

$LastTime       = 5;

$invoice = ViewInvoiceLine::find()
->where(['item' => $model->id])
->andWhere(['between', 'posting_date', date('Y').'-01-01 00:00:00', date('Y-m-d').' 23:59:59'])
->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
->orderBy([
    'posting_date' => SORT_DESC, 
    'doc_no_' => SORT_DESC
]);

$date_of_sale   = '';
$chart_labels   = [];
$chart_data     = [];
$chart_color    = [];
$sum_quantity   = 0;
$sum_price      = 0;
foreach ($invoice->limit($LastTime)->all() as $key => $rc) {
    if($key == 0){
        $date_of_sale = $rc->header->posting_date;
    }
    $Date        = date('d',strtotime($rc->header->posting_date));
    $Month       = $strMonthCut[date('n',strtotime($rc->header->posting_date))];
    $chart_labels[] = '['.Yii::t('common',$rc->header ? $rc->header->customer->code : '') ."]  ". $Date. '-' .$Month ."\r\n";
    $chart_data[]   = $rc->quantity;

}

foreach ($invoice->all() as $key => $rc) {
    $sum_quantity+= $rc->quantity;
    $sum_price+= ($rc->quantity * $rc->unit_price)  - $rc->line_discount;
}

?>
<?= Html::a('<h4>'.Yii::t('common','Last {:time} Sale', [':time' => $LastTime]).' <small>('.date('Y').')</small></h4> ',['/accounting/inv-line','InvLineSearch[item]' => $model->id,],['target' => '_blink']); ?>

<div class="row">
    <div class="col-md-3 col-sm-12">
        <div class="info-box">
        <span class="info-box-icon bg-red"><i class="far fa-clock"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">ล่าสุด</span>
            <span class="info-box-number" style="font-size:13px;"><?=date('d M Y', strtotime($date_of_sale));?></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fas fa-box"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">ขายไปแล้ว (ครั้ง)</span>
            <span class="info-box-number" title="เฉพาะปี <?= date('Y') ?>"><?=$invoice->count();?></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-12">
        <div class="info-box">
        <span class="info-box-icon bg-yellow"><i class="fas fa-cubes"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">ทั้งหมด (หน่วย)</span>
            <span class="info-box-number"><?= number_format($sum_quantity)?></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
<!-- /.col -->
    <div class="col-md-3 col-sm-12">
        <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fas fa-receipt"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">ขายไป (บาท)</span>
            <span class="info-box-number"><?= number_format($sum_price)?></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    
</div>
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
                    'label' => $model->description_th,
                    'backgroundColor' => "rgba(0, 192, 239, 0.44)",
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
<div>*** วันที่เปิดบิล</div>