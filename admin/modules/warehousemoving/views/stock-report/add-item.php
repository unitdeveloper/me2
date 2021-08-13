<?php
use yii\helpers\Html;
use common\models\ItemgroupCommon;

function getTopParent($id){

    $html = '';
    if($id==0){
        $html = ' ';
    }else{
        $model = ItemgroupCommon::findOne($id);

        
        if($model->child > 0){
            
            $html.= getTopParent($model->child)." -> ";
            
        }
        $count = $model->countItem > 0 ? '<smal class="text-sm text-gray">('.$model->countItem.')</smal>' : '';
        $html.= Html::a($model->name.' '.$count,['add-item','id' => $model->id],['class' => 'btn btn-default-ew']);
    }

    return $html;
}



function getListMenu($id){

    $models = ItemgroupCommon::find()
    ->where(['child' => $id])
    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
    ->orderBy(['sequent' => SORT_ASC])
    ->all();

    $html = '';
    foreach ($models as $key => $model) {

        $count = $model->countItem > 0 ? '<smal class="text-sm text-gray">('.$model->countItem.')</smal>' : '';
        $html.= '<li style="list-style-type:none; margin: 5px 5px 5px 5px;">'.Html::a($model->name.' '.$count,['add-item','id' => $model->id],['class' => 'btn btn-default-ew']).'</li>';
        if($model->child > 0){            
            $html.= '<ul>'.getListMenu($model->id).'</ul>';            
        }
        
    }

    
    return $html;
}


function readTable($models){
    $html = ' ';
    $i      = 0;
    foreach ($models as $key => $model) {
        $i++;
        $html.='<li data-id="'.$model->items->id.'" data-code="'.$model->items->master_code.'" data-name="'.$model->items->description_th.'" class="'.($model->items->id == Yii::$app->request->get('item') ? 'bg-yellow' : ' ').'">
                    <div class="pull-right text-danger remove-item pointer" id="'.$model->id.'"><i class="far fa-minus-square"></i></div>
                    <div class="pull-right text-info add-item pointer" id=" "><i class="far fa-plus-square"></i></div>
                    <div class="row">
                        <div class="col-sm-4">'.$i.'.) 
                        '.Html::a($model->items->master_code,['/items/items/view','id' => $model->items->id],['target' => '_blank']).'</div>
                        <div class="col-sm-7"> '.$model->items->description_th.'</div>
                    </div>
                </li>';
    }
    $html.=' ';
    return $html;
}

?>

 
 
<div class="content">
    <div>
        <h3 id="group-id" data-key="<?=Yii::$app->request->get('id')?>"><?=Html::a('<i class="fab fa-elementor"></i> '.Yii::t('common','Groups'),['add-item','id' => 0],['class' => ''])?></h3>

        <div class="row">
            <div class="col-lg-12">
                <?=getTopParent(Yii::$app->request->get('id'))?>
                <ul>
                    <?=getListMenu(Yii::$app->request->get('id'))?>
                </ul>
            </div>
            
        </div>
    </div>
    <div class="row"  style="margin:10px -30px 20px -30px; border-bottom:1px solid #ccc;"></div>
    <div class="row" style="margin-bottom:30px;">
        <div class="col-lg-6 active-items"  >            
            <form class="form-inline text-right" role="form">
                <div class="form-group">
                    <label class="sr-only" for="">label</label>
                    <input type="text" class="form-control" id="text-search-in-group" placeholder="<?=Yii::t('common','Search')?>">
                </div>
                <button type="button" class="btn btn-primary" id="search-in-group"><?=Yii::t('common','Search')?></button>
            </form>
            <ol class="serialization vertical" data-key="0">
                <?=readTable($models)?>  
            </ol>   
        </div>
        <div class="col-lg-6" id="renders" >    
            <div class="pull-left">
                <span>ยังไม่จัดกลุ่ม : <?=Html::a(number_format($newItem),'#',['id' => 'item-no-group'])?></span>
                <?=$duplicate > 0 ?  ',<span class="text-red blink"> ซ้ำ : '.$duplicate.'</span>' : ''   ?>
            </div>        
            <form class="form-inline text-right" role="form">
                <div class="form-group">
                    <label class="sr-only" for="">label</label>
                    <input type="text" class="form-control" id="text-search" placeholder="<?=Yii::t('common','Search')?>">
                </div>
                <button type="button" class="btn btn-primary" id="search"><?=Yii::t('common','Search')?></button>
            </form>
            <ol class="items-list vertical"></ol>
        </div>
    </div>

 
</div>



<?php
$jsonModel = [];
foreach ($models as $key => $model) {
    $jsonModel[] = (Object)[
        'id' => $model->items->id,
        'code' => $model->items->master_code,
        'name' => $model->items->description_th,
        'name_en' => $model->items->Description
    ];
}
$json = addslashes(json_encode($jsonModel));
 
$js=<<<JS
    
    let json = JSON.parse('{$json}');
 
   
    let data = '';
    let oldContainer;

    // $("ol.serialization").sortable({
    //     group: 'no-drop',
    //     delay: 200,
    //     onDrop: function (item, container, _super) {
    //         container.el.removeClass("active");
    //         _super(item, container);
    //         lisItem();
    //     }         

    // });

    // $("ol.items-list").sortable({
    //     group: 'no-drop',
    //     delay: 200,
        
    //     // drop: false
    // });


    let updateData = (data,callback) => {
        fetch("?r=warehousemoving/stock-report/set-item-to-group", {
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

    let lisItem = () => {
        let groupId = $('#group-id').attr('data-key');
        let data = [];
        $('ol.serialization > li').map((key,el) => {
            data.push({
                id:$(el).attr('data-id'),
                code:$(el).attr('data-code')
            })
            
        })
        
        updateData({data:data,group:groupId},res => {
            console.log(res);
        })
    }


    let removeItem = (that) => {
        let id = $(that).closest('li').attr('data-id');
        let groupId = $('#group-id').attr('data-key');
        
        fetch("?r=warehousemoving/stock-report/remove-item-from-group", {
            method: "POST",
            body: JSON.stringify({id:id,group:groupId}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(respose => {            
            //console.log(respose);
        })
        .catch(error => {
            console.log(error);
        });
        
    }

    let addItem = (that) => {
        let id = $(that).closest('li').attr('data-id');
        let groupId = $('#group-id').attr('data-key');
        
        fetch("?r=warehousemoving/stock-report/add-item-to-group", {
            method: "POST",
            body: JSON.stringify({id:id,group:groupId}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(respose => {            
            //console.log(respose);
        })
        .catch(error => {
            console.log(error);
        });
        
    }

    let listAfterSearch = (calback) => {
        let groupId = $('#group-id').attr('data-key');
        let data = [];
        $('ol.serialization > li').map((key,el) => {
            data.push(parseInt($(el).attr('data-id')))
            
        })

        calback(data);
    }

 
    $.fn.animateTo = function(appendTo, destination, duration, easing, complete) {
        if(appendTo !== 'appendTo'     &&
            appendTo !== 'prependTo'    &&
            appendTo !== 'insertBefore' &&
            appendTo !== 'insertAfter') return this;
        var target = this.clone(true).css('visibility','hidden')[appendTo](destination);
        this.css({
            'position' : 'relative',
            'top'      : '0px',
            'left'     : '0px'
        }).animate({
            'top'  : (target.offset().top - this.offset().top)+'px',
            'left' : (target.offset().left - this.offset().left)+'px'
        }, duration, easing, function() {
            target.replaceWith($(this));
            $(this).css({
            'position' : 'static',
            'top'      : '',
            'left'     : ''
            });
            if($.isFunction(complete)) complete.call(this);
        });
    }

    $('body').on('click','.add-item',function(e){
        $(this).closest('li').animateTo('appendTo','ol.serialization','','',res => {
            //lisItem();
            addItem(this);
        });
    })


    $('body').on('click','.remove-item',function(e){
        
        $(this).closest('li').animateTo('prependTo','ol.items-list','','',res => {
            //lisItem();
            removeItem(this);
        });
    })

    let renderItem = (data,div) => {
        let node = '';
        let i = 0;
        $('body').find(div).hide();
        listAfterSearch(res => { 
            //let newdata = data.filter(el =>res.indexOf(el.id) === -1 ? el : null );
            data.length > 0 ? 
                data.map(model => {
                        i++;
                        node +=  '<li data-id="'+model.id+'" data-code="'+model.code+'" data-name="'+model.name+'" class="'+(model.exists===true? '' : 'highlight')+'">'+
                                    '<div class="pull-right text-danger remove-item pointer" id=" "><i class="far fa-minus-square"></i></div>'+
                                    '<div class="pull-right text-aqua add-item pointer" id=" " '+(model.exists.length > 0 ? 'style="display:none;"' : '')+'><i class="far fa-plus-square"></i></div>'+
                                    '<div class="row">'+
                                        '<div class="col-sm-4">'+i+'.)<a href="?r=items%2Fitems%2Fview&id=' + model.id + '" target="_blank" > ' + model.code + '</a></div>'+
                                        '<div class="col-sm-7">' + model.name + ' ' + (model.exists.length > 0 ? `<div class="text-yellow">
                                                                                                                <a href="?r=warehousemoving%2Fstock-report%2Fadd-item&id=` + model.exists[0].id + `&item=` + model.id + `" class="btn btn-default">
                                                                                                                 ` + model.exists[0].name + `
                                                                                                                </a>
                                                                                                            </div>` : '') + 
                                        '</div>'+
                                    '</div>'+
                                '</li>';
                }) : node+= '<div class="text-center text-warning" style="margin-top:10px;">No data found</div>';
            $('body').find(div).html(node);
            $('body').find(div).slideDown('slow');
        })
    }

    $('body').on('click','#search',function(){
        let text = $('input#text-search').val();
        search({search:text},res => {
            renderItem(res.data,'.items-list');            
        })
    })

    // เมื่อ ENTER
    $('body').on('keypress','input#text-search', function(e) {
        let text = $('input#text-search').val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {  
            search({search:text},res => {             
                renderItem(res.data,'.items-list');                 
            })
        }
    })

    let search = (data,callback) => {
        fetch("?r=warehousemoving/stock-report/find-item", {
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

    $('body').on('click','#search-in-group',function(){
        let search  = $('input#text-search-in-group').val();
        let data    = [];
        let html    = '';
        let i       = 0;
        json.map((model,el) => {
            if((model.name.indexOf(search) !== -1) || (model.code.indexOf(search) !== -1)){
                i++;
                html +=  '<li data-id="'+model.id+'" data-code="'+model.code+'" data-name="'+model.name+'" >'+
                                '<div class="pull-right text-danger remove-item pointer" id=" "><i class="far fa-minus-square"></i></div>'+
                                '<div class="pull-right text-info add-item pointer" id=" "><i class="far fa-plus-square"></i></div>'+
                                '<div class="row">'+
                                    '<div class="col-sm-4">'+i+'.) '+model.code+'</div>'+
                                    '<div class="col-sm-7">'+model.name+'</div>'+
                                '</div>'+
                            '</li>';
            }
        })
        $('body').find('ol.serialization').html(html);
    })

    $('body').on('keypress','input#text-search-in-group', function(e) {
         
        var keyCode = e.keyCode || e.which;
        let search  = $(this).val();
        let data    = [];
        let html    = '';
        let i       = 0;

        if (keyCode === 13) {  
            json.map((model,el) => {
                if((model.name.indexOf(search) !== -1) || (model.code.indexOf(search) !== -1)){
                    i++;
                    html +=  '<li data-id="'+model.id+'" data-code="'+model.code+'" data-name="'+model.name+'" >'+
                                    '<div class="pull-right text-danger remove-item pointer" id=" "><i class="far fa-minus-square"></i></div>'+
                                    '<div class="pull-right text-info add-item pointer" id=" "><i class="far fa-plus-square"></i></div>'+
                                    '<div class="row">'+
                                        '<div class="col-sm-4">'+i+'.) '+model.code+'</div>'+
                                        '<div class="col-sm-7">'+model.name+'</div>'+
                                    '</div>'+
                                '</li>';
                } 
            })

            if(i <= 0){
                html = '<div class="text-center text-warning" style="margin-top:10px;">No data found</div>';
            }

            $('body').find('ol.serialization').html(html);
        }
    })


    $('body').on('click','a#item-no-group',function(){
        $('.items-list').html('<div class="text-center" style="margin-top:15px;"><i class="fas fa-sync-alt fa-spin fa-2x"></i></div>');
        $('input#text-search').val('');
        fetch("?r=warehousemoving/stock-report/find-item-not-in-group", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => { 
            renderItem(response.data,'.items-list');    
        })
        .catch(error => {
            console.log(error);
        });
    });
    

JS;
$this->registerJs($js,Yii\web\View::POS_END);
?>
<?php $this->registerCssFile('css/sortable.css?v=4.3.7',['rel' => 'stylesheet','type' => 'text/css']);?>
<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('@web/js/jquery-sortable.js', $Options);?>
