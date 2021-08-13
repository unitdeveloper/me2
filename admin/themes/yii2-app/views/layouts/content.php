<?php
use dmstr\widgets\Alert;

?>
<div class="content-wrapper">
    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<!-- <footer class="main-footer hidden-xs">
    <div class="pull-right ">
        <b>Version</b> 3.3.0
    </div>
    <strong>Copyright &copy; 2016-2019 <a href="http://www.ewinl.com">EWIN LASTEST SYSTEM</a>.</strong> All rights
    reserved.
</footer> -->

<?= $this->render('right');?>
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>



