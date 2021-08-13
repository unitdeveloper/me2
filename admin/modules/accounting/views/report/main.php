<style>
    .content-wrapper {
        background-color: #ecf0f5 !important;
    }
</style>
<div class="row">
    <div class="col-sm-3">
        <a class="info-box" href="index.php?r=accounting/report/bank-list">
            <span class="info-box-icon bg-aqua"><i class="fas fa-book-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cash Receipt</span>
                <span class="info-box-number"><?= Yii::t('common','By Book Bank')?></span>
            </div>             
        </a>
    </div>        
    <div class="col-sm-3 ">
        <a class="info-box" href="index.php?r=accounting/cheque">
            <span class="info-box-icon bg-green-active"><i class="fas fa-shopping-bag"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Cash Receipt Journals</span>
            <span class="info-box-number"><?= Yii::t('common','Cash Receipt Journals')?></span>
            </div>
            <!-- /.info-box-content -->
        </a>
    </div>

    <div class="col-sm-3">
        <a class="info-box" href="index.php?r=accounting/cheque/index-ajax-detail">
            <span class="info-box-icon bg-info"><i class="fas fa-shopping-bag"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Cash Receipt Journals</span>
            <span class="info-box-number"><?= Yii::t('common','Cash Receipt Journals')?> (Show Detail)</span>
            </div>
            <!-- /.info-box-content -->
        </a>

        <a class="info-box" href="index.php?r=accounting/cheque/index-ajax">
            <span class="info-box-icon bg-orange-active"><i class="fas fa-shopping-bag"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Cash Receipt Journals</span>
            <span class="info-box-number"><?= Yii::t('common','Cash Receipt Journals')?> (NEW)</span>
            </div>
            <!-- /.info-box-content -->
        </a>

       
    </div>

    <div class="col-sm-3">
        <a class="info-box" href="index.php?r=accounting/default/50tw">
            <span class="info-box-icon bg-red-active"><i class="fas fa-print"></i></span>
            <div class="info-box-content">
            <span class="info-box-text"><?=Yii::t('common', 'Withholding tax')?></span>
            <span class="info-box-number">50 ทวิ</span>
            </div>
            <!-- /.info-box-content -->
        </a>
    </div>
</div>

<div class="row hidden">
    <div class="col-sm-3">
        <a class="info-box" href="#">
            <span class="info-box-icon bg-orange-active"><i class="far fa-credit-card"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Sale Invoice </span>
            <span class="info-box-number">Sales conflict</span>
            </div>
            <!-- /.info-box-content -->
        </a>
    </div>

    <div class="col-sm-3">
        <a class="info-box" href="#">
            <span class="info-box-icon bg-red"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Posted Invoice </span>
            <span class="info-box-number">Sales conflict</span>
            </div>
            <!-- /.info-box-content -->
        </a>
    </div>         
</div>