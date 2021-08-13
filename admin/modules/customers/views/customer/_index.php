
<style type="text/css">
    .ew-search
    {
        position: relative;
    }
    #ew-type-search-render{
        /*visibility: hidden;
        display: block;*/
        position: absolute;
        border: 1px solid #ccc;
        width: 100%;
        height: 200px;
        z-index: 1;
        margin-top: 38px;
        padding:5px;
        background-color: #fcfcfc;
    }


    .textbox-icon {
        padding: 7px;
        border-top:     1px rgb(15, 15, 15) solid;
        border-bottom:  1px rgb(15, 15, 15) solid;
        border-left:    1px rgb(15, 15, 15) solid;
      float:left;
      background: white;
    }
    .ew-search-text{
        width: 250px;
        height: 36px;
        padding: 3px;
        outline: 0;
      border: none;
      border-top:       1px rgb(15, 15, 15) solid;
        border-bottom:  1px rgb(15, 15, 15) solid;
        border-right:   1px rgb(15, 15, 15) solid;
      float:left;
    }
</style>

<script type="text/javascript">
    $('#ew-type-search-render').css('visibility','hidden');

    $('#ew-type-search').keyup(function(){
        $('#ew-type-search-render').css('visibility','');
    });


</script>
<div class="row">
<div class="col-md-4" >
    <div class="ew-search">
    <span class="textbox-icon"><i class="fa fa-search"></i> </span>
    <input type="text" class="form-control ew-search-text" name="Search" id="ew-type-search" ng-model="datasearch" value="">
        <div id="ew-type-search-render">{{ datasearch }}</div>
    </div>
</div>
</div>