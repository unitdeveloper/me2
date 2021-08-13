
<!-- Modal Add-series-->
<div id="ew-add-series" class="modal modal-full fade" role="dialog" data-backdrop="static" data-keyboard="true">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close add-series-close-modal">&times;</button>

        <h4 class="modal-title pull-left" style="padding-right: 20px;"><?=Yii::t('common','Create number series')?></h4>

        <!-- <a href="#" class="link" id="modal-back" >< Back</a> -->
      </div>
      <ew class="ew-condition"></ew>
      <div class="modal-body ew-series-body"  >

        <div class="loading"></div> Loading...

      </div>

      <div class="modal-footer" >

        <button type="button" class="btn btn-default add-series-close-modal pull-left" ><i class="fa fa-power-off" aria-hidden="true"></i> Close</button>

        <button type="button" class="btn btn-default ew-save-modal-common" onclick="" ><i class="fa fa-save" aria-hidden="true"></i> Save</button>

       <!--  <div class="ew-menu-center text-center hidden-xs hidden-sm">

          <a href="#" class="link"><i class="fa fa-print" aria-hidden="true"></i> Print</a>

          <a href="#" class="link"><i class="fa fa-download" aria-hidden="true"></i> Download</a>

        </div> -->



      </div>
    </div>

  </div>

</div>




<!-- Modal data-keyboard="false" data-backdrop="static"-->
<div id="RunNoSeries" class="modal fade" role="dialog" >
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Number Series</h4>
      </div>
      <div class="data-body">
        <p>Loading...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off" aria-hidden="true"></i> Close</button>
      </div>
    </div>

  </div>
</div>

<!-- /.Modal Add-series-->
