<?php
use yii\helpers\Html;
use common\models\ItemgroupCommon;


function child($status,$id){
  $models = ItemgroupCommon::find()
  ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
  ->andWhere(['child' => $id])
  ->orderBy(['sequent' => SORT_ASC])
  ->all();

  $html = '<ol data-key="'.$id.'" class="child">';
   foreach ($models as $key => $model) {
    if($model->status===$status){
      $html.='<li data-id="'.$model->id.'" data-name="'.$model->name_en.'" class="menu-name">
                <i class="fas fa-arrows-alt text-black"></i> 
                <a class="text-white" href="index.php?r=warehousemoving%2Fstock-report%2Fadd-item&id='.$model->id.'" target="_blank"><span class="name">'.$model->name_en.'</span></a>
                <span class="add pull-right add-child" id="'.$model->id.'"><i class="far fa-plus-square"></i></span>
                <span class="add pull-right edit-child" id="'.$model->id.'" style="margin-right:5px;"><i class="far fa-edit"></i></span>
                <span class="pull-right text-danger remove-group" style="margin-right:10px;" id="'.$model->id.'"><i class="far fa-minus-square"></i></span>
                '.(ItemgroupCommon::find()->where(['child' => $model->id])->exists()  ? child($status,$model->id) : '<ol></ol>').'
              </li>';
    }
   }
   $html.= '</ol>';

   return $html;
}

function renders($status,$dataProvider){
  $html = '';
  $models = $dataProvider->getModels();
  foreach ($models as $key => $model) {
    if($model->status===$status){
      $html.='<li data-id="'.$model->id.'" data-name="'.$model->name_en.'" class="menu-name">
                <i class="fas fa-arrows-alt text-black"></i> 
                <a class="text-primary" href="index.php?r=warehousemoving%2Fstock-report%2Fadd-item&id='.$model->id.'" target="_blank"><span class="name">'.$model->name_en.'</span></a>
                <span class="add pull-right add-child" id="'.$model->id.'"><i class="far fa-plus-square"></i></span>
                <span class="add pull-right edit-child" id="'.$model->id.'" style="margin-right:5px;"><i class="far fa-edit"></i></span>
                <span class="pull-right text-danger remove-group" style="margin-right:10px;" id="'.$model->id.'"><i class="far fa-minus-square"></i></span>
                '.(ItemgroupCommon::find()->where(['child' => $model->id])->exists()  ? child($status,$model->id) : '<ol></ol>').'
              </li>';
    }
  }
  return $html;
}
?>
 
<div class="row">
  <div class="col-lg-12">
      <div class="pull-left"><h3>Manage menu <button class="btn"><i class="far fa-plus-square fa-2x add-new-gorup"></i> </button></h3></div>
       
  </div>
</div>
<div class="row">
  <div class="col-sm-7 render-menu-list">
    <ol class="serialization vertical" data-key="0">
      <?=renders(1,$dataProvider);?>
    </ol>
  </div>
  <div class="col-sm-5 trash-zone" style="border:1px solid #ccc; background-color:#b3b3b3; margin-top: 4px;">
    <div style="margin:15px 10px 10px 0px; color:#4c6ef5;" ><i class="fas fa-trash fa-2x" ></i> <?=Yii::t('common','Trash')?></div>
    <ol class="serialization vertical" data-key="0" style="min-height: 300px; ">
      <?=renders(0,$dataProvider);?>
    </ol>
  </div>
</div>

<div class="modal fade" id="modal-new-name">
  <div class="modal-dialog">
    <div class="modal-content modal-default">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Group')?></h4>
      </div>
      <div class="modal-body">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"  style="background-color:#ccc; border:1px solid #000;">
          <?=Yii::t('common','Name')?></span>
          <input type="text" class="form-control" placeholder="<?=Yii::t('common','Group Name')?>" id="group-name" aria-describedby="sizing-addon1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary add-new-group">Add</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal-new-child-name">
  <div class="modal-dialog">
    <div class="modal-content modal-info">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Group')?></h4>
      </div>
      <div class="modal-body">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"  style="background-color:#ccc; border:1px solid #000;">
          <?=Yii::t('common','Name')?></span>
          <input type="text" class="form-control" placeholder="<?=Yii::t('common','Group Name')?>" id="group-name-child" aria-describedby="sizing-addon1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary add-new-group-child">Add</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal-edit-name">
  <div class="modal-dialog">
    <div class="modal-content modal-warning">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Edit Group')?></h4>
      </div>
      <div class="modal-body">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"  style="background-color:#ccc; border:1px solid #000;">
          <?=Yii::t('common','Name')?></span>
          <input type="text" class="form-control" placeholder="<?=Yii::t('common','Group Name')?>" id="edit-group-name" aria-describedby="sizing-addon1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary click-edit-group">Edit</button>
      </div>
    </div>
  </div>
</div>

<?php
$js=<<<JS
 
  let childId = '';
  let oldContainer;

  $(document).ready(function() {
    setTimeout(() => {
      $("body")
        .addClass("sidebar-collapse")
        .find(".user-panel")
        .hide();
    }, 1500);
  });

  let group = $("ol.serialization").sortable({
    group: 'serialization',
    handle: 'i.fa-arrows-alt',
    delay: 200,
    afterMove: function (placeholder, container) {
      if(oldContainer != container){
        if(oldContainer)
          oldContainer.el.removeClass("active");
        container.el.addClass("active");

        oldContainer = container;
          
      }
    },
    onDrop: function (item, container, _super) {
      container.el.removeClass("active");
      _super(item, container);

      let data        = group.sortable("serialize").get();
      // let jsonString  = JSON.stringify(data, null, ' ');
      // localStorage.setItem('menu',jsonString);


      let MoveItemGroup = (data,callback) => {
        fetch("?r=warehousemoving/stock-report/move-item-group", {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                },
            })
            .then(res => res.json())
            .then(response => {            
              callback(response);            
            })
            .catch(error => {
              console.log(error);
            });
      }

      MoveItemGroup(data,res => {
        if(res.status===200){
          
        }
      })
       
    }
  });



  let addGroup = (data,callback) => {
    fetch("?r=warehousemoving/stock-report/add-group", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {            
          callback(response);            
        })
        .catch(error => {
          console.log(error);
        });
  }

  let RemoveGroup = (data,callback) => {
    fetch("?r=warehousemoving/stock-report/remove-group", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {            
          callback(response);            
        })
        .catch(error => {
          console.log(error);
        });
  }

  let EditGroup= (data,callback) => {
    fetch("?r=warehousemoving/stock-report/edit-group", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {            
          callback(response);            
        })
        .catch(error => {
          console.log(error);
        });
  }

  // คลิกปุ่ม  Manage Menu [+]
  $('body').on('click','.add-new-gorup',function(){
    $('#modal-new-name').modal('show');
    setTimeout(() => {
      $('input#group-name').val('').focus();
    }, 500);
    
  })

  // เพิ่มกลุ่มหลัก เมื่อ ENTER
  $('body').on('keypress','input#group-name', function(e) {
    let name    = $(this).val();    
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {   
        if(name){ 
          let data = {name:name,child:0};  
          addGroup(data,res => {
            let id      = res.id;
            if(res.status===200){
              let node =  '<li data-id="'+id+'" data-name="'+name+'" class="menu-name">'+
                            '<i class="fas fa-arrows-alt text-black"></i> <a class="text-primary" href="index.php?r=warehousemoving%2Fstock-report%2Fadd-item&id='+id+'" target="_blank"><span class="name">'+ name +'</span></a>'+
                            '<span class="add pull-right add-child" id="'+id+'"><i class="far fa-plus-square"></i></span>'+
                            '<span class="add pull-right edit-child" id="'+id+'" style="margin-right:5px;"><i class="far fa-edit"></i></span>'+
                            '<span class="pull-right text-danger remove-group" style="margin-right:10px;" id="'+id+'"><i class="far fa-minus-square"></i></span>'+
                            '<ol></ol>'+
                          '</li>';

              $('.render-menu-list .serialization').prepend(node);
              $('#modal-new-name').modal('hide');
            }
          });
      }else{
        alert('Please input group name.');
      }
    }
  });

  // เพิ่มกลุ่มหลัก
  $('body').on('click','.add-new-group', function(e) {
    let name    = $('input#group-name').val();
    
    if(name){     

      let data = {name:name,child:0};
      addGroup(data,res => {
        let id      = res.id;
        if(res.status===200){
          let node =  '<li data-id="'+id+'" data-name="'+name+'" class="menu-name">'+
                        '<i class="fas fa-arrows-alt text-black"></i> <a class="text-white" href="index.php?r=warehousemoving%2Fstock-report%2Fadd-item&id='+id+'" target="_blank"><span class="name">'+ name +'</span></a>'+
                        '<span class="add pull-right add-child" id="'+id+'"><i class="far fa-plus-square"></i></span>'+
                        '<span class="add pull-right edit-child" id="'+id+'" style="margin-right:5px;"><i class="far fa-edit"></i></span>'+
                        '<span class="pull-right text-danger remove-group" style="margin-right:10px;" id="'+id+'"><i class="far fa-minus-square"></i></span>'+
                        '<ol></ol>'+
                      '</li>';

          $('.render-menu-list .serialization').prepend(node);
          $('#modal-new-name').modal('hide');
        }
      });
    }else{
      alert('Please input group name.');
    }
  });

  // ลบรายการ [-]
  $('body').on('click','.remove-group',function(){
    let id = $(this).closest('li').attr('data-id');
    if(confirm('Do you want to remove ? ')){
      RemoveGroup({id:id},res => {
        $(this).closest('li').remove();
      })
    }
  })


  // คลิกที่ [+] เพื่อเพิ่มกลุ่มย่อย
  $('body').on('click','.add-child',function(){

    $('#modal-new-child-name').modal('show');
    childId = $(this);
    setTimeout(() => {
      $('input#group-name-child').val('').focus();
    }, 500);

  })

  // เพิ่มกลุ่มย่อย
  $('body').on('click','.add-new-group-child', function(e) {
    let name    = $('input#group-name-child').val();
    let child   = childId.closest('li').attr('data-id');
    if(name){ 
      let data = {name:name,child:child};
        addGroup(data,res => {
          let id      = res.id;
          if(res.status===200){        
            let node =  '<ol data-key="'+id+'" class="child">'+
                          '<li data-id="'+id+'" data-name="'+name+'" class="menu-name">'+
                            '<i class="fas fa-arrows-alt text-black"></i> <a class="text-white" href="index.php?r=warehousemoving%2Fstock-report%2Fadd-item&id='+id+'" target="_blank"><span class="name">'+ name +'</span></a>'+
                            '<span class="add pull-right add-child" id="'+id+'"><i class="far fa-plus-square"></i></span>'+
                            '<span class="add pull-right edit-child" id="'+id+'" style="margin-right:5px;"><i class="far fa-edit"></i></span>'+
                            '<span class="pull-right text-danger remove-group" style="margin-right:10px;" id="'+id+'"><i class="far fa-minus-square"></i></span>'+
                            '<ol></ol>'+
                          '</li>'+
                        '</ol>';

            childId.closest('li').append(node);
            $('#modal-new-child-name').modal('hide'); 
          }
        });     
    }else{
      alert('Please input group name.');
    }
  });

  // เพิ่มกลุ่มย่อย เมื่อ ENTER
  $('body').on('keypress','input#group-name-child', function(e) {
    let name    = $('input#group-name-child').val();
    let child      = childId.closest('li').attr('data-id');
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {   
      if(name){    
        let data = {name:name,child:child};
        addGroup(data,res => {
          let id      = res.id;
          if(res.status===200){  
            let node =  '<ol data-key="'+id+'" class="child">'+
                          '<li data-id="'+id+'" data-name="'+name+'" class="menu-name">'+
                            '<i class="fas fa-arrows-alt text-black"></i> <a class="text-white" href="index.php?r=warehousemoving%2Fstock-report%2Fadd-item&id='+id+'" target="_blank"><span class="name">'+ name +'</span></a>'+
                            '<span class="add pull-right add-child" id="'+id+'"><i class="far fa-plus-square"></i></span>'+
                            '<span class="add pull-right edit-child" id="'+id+'" style="margin-right:5px;"><i class="far fa-edit"></i></span>'+
                            '<span class="pull-right text-danger remove-group" style="margin-right:10px;" id="'+id+'"><i class="far fa-minus-square"></i></span>'+
                            '<ol></ol>'+
                          '</li>'+
                        '</ol>';
            childId.closest('li').append(node);
            $('#modal-new-child-name').modal('hide');      
          }
        })
      }else{
        alert('Please input group name.');
      }
    }
  });

  $('body').on('click','.edit-child',function(){
    $('#modal-edit-name').modal('show');
    childId = $(this);
    setTimeout(() => {
      $('#edit-group-name').val($(this).closest('li').attr('data-name')).select();
    }, 500);
    
  });

  // Click Edit
  $('body').on('click','.click-edit-group',function(){
    let id = childId.closest('li').attr('data-id');
    let name = $('#edit-group-name').val();
    EditGroup({id:id,name:name},res => {
      if(res.status===200){
        $('body').find('li[data-id='+res.data.id+']').attr('data-name',res.data.name);
        $('body').find('li[data-id='+res.data.id+']').children('a').children('span.name').html(res.data.name);
        $('#modal-edit-name').modal('hide');
      }
    })
  });

  $('body').on('keypress','input#edit-group-name', function(e) {
    let id = childId.closest('li').attr('data-id');
    let name = $('#edit-group-name').val();
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {   
      if(name){    
        EditGroup({id:id,name:name},res => {
          if(res.status===200){
            $('body').find('li[data-id='+res.data.id+']').attr('data-name',res.data.name);
            $('body').find('li[data-id='+res.data.id+']').children('a').children('span.name').html(res.data.name);
            $('#modal-edit-name').modal('hide');
          }
        })
      }

    }

  });



 

JS;
$this->registerJs($js,Yii\web\View::POS_END);
?>
<?php $this->registerCssFile('css/sortable.css?v=2',['rel' => 'stylesheet','type' => 'text/css']);?>
<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('@web/js/jquery-sortable.js', $Options);?>
