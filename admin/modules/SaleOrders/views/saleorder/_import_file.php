<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Convert PDF to Text</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link href="css/smart_wizard.css" rel="stylesheet" type="text/css">
    <link href="css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
    <script type="text/javascript" src="//admin.ewinl.com/js/jquery-2.1.3.min.js" ></script>
    <script type="text/javascript">$(document).ready(function () {$('script[type="text/javascript"]').remove();});</script>
</head>
<body>
<div class="container" style="margin-top:30px;">
<form id="import-file" action="" method="post" enctype="multipart/form-data" data-key="<?=$_GET['id']?>" >    
    <div id="smartwizard" class="sw-main sw-theme-arrows">
        <ul class="nav nav-tabs step-anchor">
            <li class="<?=($text)? 'done':'active'?>"><a href="#step-1">Upload PDF<br><small>เลือกไฟล์ PDF ที่ต้องการ</small></a></li>
            <li class="<?=($text)? 'active':''?>"><a href="#step-2">Verify Your Data<br><small>ตรวจสอบข้อมูล และยืนยัน</small></a></li>
        
        </ul>
        
        <div class="sw-container tab-content" style="height: 550px;">
            <div id="step-1" class="step-content" style="display: block;">
        
                <h4><?php if($text==''){ echo "Upload File"; }?></h4>
                <div class="row">
                    <?php if($text==''){ ?>
                    <div class="col-sm-6">
                        <label for="Homeworks"><h4>ไทวัสดุ</h4></label>
                        <input type="file" id="Homeworks" class="file" name="file" accept=".pdf"> 
                    </div>
                    <div class="col-sm-6 text-right">
                        
                    </div>
                    <?php } ?>
                    
                </div>
            

            </div>
            <div id="step-2" class="step-content" style="display: <?=($text)? 'block':'none'?>;">
                 
                 
                <div class="row">
                    <div class="col-sm-12 text-right">
                                                                    
                        <button type="button" class="btn btn-warning confirm-data"><i class="fas fa-arrow-circle-right"></i> 
                            <?=Yii::t('common','Confirm')?> <?=Yii::t('common','And')?> <?=Yii::t('common','Close')?>
                        </button> 

                    </div>
                </div>
                 
                <div class="row">
                    <div class="col-sm-12" id="pdf-content">
                        <?=$text;?>
                         
                    </div>
                    
                </div>
            </div>
            
        </div>
        <?php if($text==''){ ?>
        <nav class="navbar btn-toolbar sw-toolbar sw-toolbar-bottom">
             
            <div class="btn-group navbar-btn sw-btn-group pull-right" role="group">
                <button class="btn btn-default sw-btn-prev disabled" type="button">Previous</button>
                <button type="submit"   name="submit" class="btn btn-primary"><i class="fa fa-upload" ></i>  <?=Yii::t('common','Upload')?></button>
            </div>
        </nav>
        <?php } ?>
    </div>
 

    <?php //var_dump(Yii::$app->getRequest()->serverName);?>

    </form>   
</div>


<script type="text/javascript">

var dataset = [];


$(document).ready(function(){
    <?php if($text!=''){ ?>
    dataset = getRowData(getPage());  
    <?php } ?>

    
});

$('button.confirm-data').on('click',function(){
    $.ajax({
        url:'index.php?r=SaleOrders/ajax/create-sale-line',
        type: 'POST',
        data: {id:$('form#import-file').attr('data-key'),data:dataset},
        success:function(response){
            var obj = $.parseJSON(response);
            if(obj.status == 200){
                window.opener.location.reload();
                window.close();
            }else if(obj.status == 404){
                alert(obj.message);
            }else {
                alert(obj.message);
            }
        }

    })
   //window.close();

})

function getRowData(totalPage){

     
    var tagid   = 70;  
    var data    = [];
    var source  = [];
    var i       = 0;

    

    $.each(totalPage,function(key,page){

        // ถ้า Page 2 ให้เริ้มตั้งแต่ array ที่ 1
        //if(page.no==2){            
            // tagid   = 0;
            // i       = 0;
        //}
        //$('#page'+page.no+'-div').show();

         

        // ค้นหาตำแหน่งที่ 885 อยู่ 
        var row  = findNextRow(page);
        console.log(row);        
        tagid   = row;
        i       = 0;

        $.each( $('#'+page.id+' > p'), function(indexInArray, valueOfElement){
            
            // เริ่มนับตั้งแต่ แถวที่ 70  page1-div
            
            
            if (indexInArray >= tagid){

                
                                
                            
                i++;
                    
                if(i == 1 ){

                    

                    
                    source['no'] = $($('#'+page.id+' > p')[indexInArray]).text();
                    if(findBarcode(source['no'])){
                        $($('#'+page.id+' > p')[indexInArray]).css('background','#51ff00').css('color','#cc7a79').css('padding','0px 5px 0px 5px;');
                    }else{
                        $($('#'+page.id+' > p')[indexInArray]).css('background','#ff4b4b');
                    }

                    
                }else if(i ==6){
                        
                    $($('#'+page.id+' > p')[indexInArray]).css('background','yellow');
                    source['qty'] = $($('#'+page.id+' > p')[indexInArray]).text();
                }else if(i ==11){
                                            
                    $($('#'+page.id+' > p')[indexInArray]).css('background','yellow');
                    source['price'] = $($('#'+page.id+' > p')[indexInArray]).text();
                }
                
                
                //console.log($('p[text="PONOTE:&nbsp;"]').last().css('background','red'));    
                // ครบคอลัมน์ที่ 19   ให้นับแถวใหม่    
                if(i == 19){
                    
                    
                    
                    data.push({'no':source['no'],'qty':Number(source['qty'].replace(/[^0-9\.-]+/g,"")),'price':Number(source['price'].replace(/[^0-9\.-]+/g,""))});

                    i       = 0;
                    source  = [];
                    
                }
                 

            }
        }); 

    })
    //console.log(data);
    return data;
}

 

function getPage(){
    var totalPage = [];
    // ค้นหาจำนวน Page 
    // โดยหาจากคำว่า "PONOTE:" ตัวสุดท้าย
    var last = 0;
    for (var i = 0; i < $('p').length; i++) {
        var endWord = $($('p')[i]).text();
        //console.log(endWord.trim());
        if(endWord.trim()=="PONOTE:"){
            last = i;
        }
    }
    $($('p')[last]).css('background','#3dd3ff').css('color','#fff');

    var lastpage = $($('p')[last]).closest('div').attr('id');

    var pageNumber = lastpage.substring(4, 5);
    for(i = 1; i<= pageNumber; i++){
        totalPage.push({'id' : 'page'+i+'-div','no' : i});
    }
    //console.log(totalPage);
    // \. End ค้นหาจำนวน Page 

    return totalPage;
}


function findNextRow(page){

 
    var data = 0;
    $.each( $('#'+page.id+' > p'), function(indexInArray, valueOfElement){ 
        var str = $($('#'+page.id+' > p')[indexInArray]).text();
        // ถ้าเจอข้อความ 885(ตัวแรก) ให้ส่งตำแหน่ง p กลับไป
        if(findBarcode(str)){
            data = indexInArray;
            return false;
        }
    });

    return data;
}

function findBarcode(word){    
    var str = word;
    var patt = new RegExp("885");
    var res = patt.test(str);
    return res;
}


</script>
</body>
</html>



 