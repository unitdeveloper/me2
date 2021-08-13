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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <script type="text/javascript" src="//admin.ewinl.com/js/jquery-2.1.3.min.js" ></script>
    <script type="text/javascript">$(document).ready(function () {$('script[type="text/javascript"]').remove();});</script>
    <style>
        label#file-input {
            cursor: pointer;
            margin-top:15px;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #000;
            color: #fff;
        }

        label#file-input > i {
            margin-right: 10px;
        }

        input#file {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }
    </style>
</head>
<body>
<div class="container" style="margin-top:30px;">
    <form id="import-file" action="" method="post" enctype="multipart/form-data" data-key="<?=$_GET['id']?>" >    
        <div id="smartwizard" class="sw-main sw-theme-arrows">
            <ul class="nav nav-tabs step-anchor">
                <li class="<?=($text)? 'done':'active'?>"><a href="#step-1">Upload PDF<br><small>เลือกไฟล์ PDF ที่ต้องการ</small></a></li>
                <li class="<?=($text)? 'active':''?>"><a href="#step-2">Verify Your Data<br><small>ตรวจสอบข้อมูล และยืนยัน</small></a></li>
            
            </ul>
            
            <div class="sw-container tab-content" style="height: 450px;">
                <div id="step-1" class="step-content" style="display: block;">
                    <h4><?php if($text==''){ echo "Upload File"; }?></h4>
                    <?php if($text==''){ ?>
                    <div class="row"> 
                        <div class="col-sm-3">
                            <label id="file-input" for="file"><i class="fas fa-file-upload fa-2x"></i> เลือกไฟล์ PDF</label>
                            <input type="file" id="file" class="file from-control" name="file" accept=".pdf"  />
                        </div>  
                                              
                    </div>
                    <?php } ?>
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
    </form>   
</div>


<script type="text/javascript">

var data = [];
let store   = 0;
 

let findCompany = (callback) =>{

    let store       = 1;
    let endpage     = 2;
    let storename   = '';
    let po          = '';

    $('#pdf-content').children('div').map((key,el) => {
       

           $(el).find('p').map((i,p) => { // List all <p> tag 
               
               let str = $(p).html();

                if(str.search("60004-CRC") > -1){
                    store = 1;
                    storename = 'CRC Thai Watsadu Limited';
                    $(p).css('background','gray');                       
                }else if(str.search("สยามโกลบอลเฮ้าส์") > -1){
                    store = 2;                  
                    storename = 'บริษัท สยามโกลบอลเฮ้าส์ จํากัด (มหาชน) สํานักงานใหญ่';
                    $(p).css('background','gray');  
                }else if(str.search("0115545007325") > -1){ // // ฮาร์ดแวร์เฮาส์  
                    store = 3;    
                    storename = 'บจก. ฮาร์ดแวร์เฮาส์ (สำนักงานใหญ่)';
                    $(p).css('background','gray');                
                }else if(str.search("โฮมฮับ") > -1){
                    store = 4;
                    storename = 'บริษัท โฮมฮับ จํากัด';
                    $(p).css('background','gray');  
                }else if(str.search("501383") > -1){
                    store = 5;
                    storename = 'บริษัท โฮมโปรดักส์ เซ็นเตอร์ จํากัด (มหาชน)';
                    $(p).css('background','gray');  
                }else if(str.search("บริษัท เมกา โฮม") > -1){
                    store = 6;
                    storename = 'บริษัท เมกา โฮม เซ็นเตอร์ จํากัด';
                    $(p).css('background','gray');  
                } 

                // หาจำนวนหน้า
                if(str.search("єѬјзҕѥѝѧьзҖѥ") > -1){
                    // CRC Thai Watsadu Limited
                    $(p).prevAll().slice(0, 1).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                    endpage = $(p).closest('div').index() -2;
                }else if(str.search("มูลค่ารวมทั้งสิ้น") > -1){ 
                    // บจก. ฮาร์ดแวร์เฮาส์ (สำนักงานใหญ่)
                    $(p).next().css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                    endpage = $(p).closest('div').index();
                }else if(str.search("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7%") > -1){
                    // บริษัท สยามโกลบอลเฮ้าส์ จํากัด (มหาชน) สํานักงานใหญ่
                    $(p).nextAll().slice(2, 3).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                    endpage = $(p).closest('div').index() -1;
                }else if(str.search("จํานวนเงินทังสิน") > -1){
                    // บริษัท โฮมฮับ จํากัด
                    $(p).nextAll().slice(3, 4).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'font-size': '20px'
                        });
                    endpage = $(p).closest('div').index() -1;
                }else if(str.search("รวมราคาสินค้าไม่รวมภาษีมูลค่าเพิ�ม") > -1){
                    // บริษัท เมกา โฮม เซ็นเตอร์ จํากัด
                    $(p).prevAll().slice(0, 1).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                    endpage = $(p).closest('div').index() -1;
                }else if(str.search("รวมราคาสินค้าไม่รวมภาษีมูลค่าเพิ�ม") > -1){
                    // บริษัท โฮมโปรดักส์ เซ็นเตอร์ จํากัด (มหาชน)
                    $(p).prevAll().slice(0, 1).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                    endpage = $(p).closest('div').index();
                }

                


            })
    })

    callback({
        store: store,
        page: endpage <=0 ? 1 : endpage,
        name: storename,
        po: po
    });
}

$(document).ready(function(){

    findCompany(res => {    

        store = res.store;
        console.log(res);


        $('#pdf-content').children('div').map((key,el) => {
        
            if(key <= (res.page)){ // เอาแค่ 2 หน้า


                $(el).find('p').map((i,p) => { // List all <p> tag 
                    
                    let str = $(p).html();

                    
                                         
                        switch (store) {
                            case 1: // Home work ไทวัสดุ
                               
                                // ค้นหา PO
                                if(str.search("P/O Number") > -1){ 
                                    // CRC Thai Watsadu Limited
                                    $(p).next().next().css('background','pink');
                                    let po = $(p).next().next().text();
                                }
                                
                                if(str.search("8859042") > -1){ // find string 885 from p
                                    $(p).css('background','#51ff00');
                                    $(p).nextAll().slice(4, 5).css('background','red');
                                    $(p).nextAll().slice(10, 11).css('background','orange');

                                    let qty = Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g,""));
                                    let sumline = Number($(p).nextAll().slice(10, 11).text().replace(/[^0-9\.-]+/g,""));
                                    let price = sumline / qty

                                    data.push({
                                        'item': $(p).text(),
                                        'qty': qty,
                                        'price': Number(price.toFixed(4)),
                                        'sumline': sumline
                                    });
                                }
                            break;

                            case 2: // GOBAL HOUSE
                                
                                if(str.search("PODC") > -1){ // ค้นหา PO
                                    $(p).first().css('background','pink');
                                }

                                if(str.search("8859042") > -1){ // find string 885 from p
                                    $(p).css('background','#51ff00');
                                    $(p).nextAll().slice(3, 4).css('background','red');
                                    $(p).nextAll().slice(6, 7).css('background','orange');

                                    let qty = Number($(p).nextAll().slice(3, 4).text().replace(/[^0-9\.-]+/g,""));
                                    let sumline = Number($(p).nextAll().slice(6, 7).text().replace(/[^0-9\.-]+/g,""));
                                    let price = sumline / qty

                                    data.push({
                                        'item': $(p).text(),
                                        'qty': qty,
                                        'price': Number(price.toFixed(4)),
                                        'sumline': sumline
                                    });
                                }
                            break;

                            case 3: // HARDWARE HOUSE
                                
                                if(str.search("เลขที่เอกสาร :") > -1){ // ค้นหา PO
                                    $(p).next().css('background','pink');
                                }

                                if(str.search("8859042") > -1){ // find string 885 from p
                                    $(p).css('background','#51ff00');
                                    $(p).nextAll().slice(2, 3).css('background','red');
                                    $(p).nextAll().slice(4, 5).css('background','orange');

                                    data.push({
                                        'item': $(p).text(),
                                        'qty': Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g,"")),
                                        'price': Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g,"")) / Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g,"")),
                                        'sumline': Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g,""))
                                    });
                                }
                            break;

                            case 4: // HOME HUB
                                
                                if(str.search("PO-D") > -1){ // ค้นหา PO
                                    $(p).first().css('background','pink');
                                }

                                if(str.search("8859042") > -1){ // find string 885 from p
                                    $(p).css('background','#51ff00');
                                    $(p).nextAll().slice(2, 3).css('background','red');
                                    $(p).nextAll().slice(0, 1).css('background','orange');

                                    let qty = Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g,""));
                                    let sumline = Number($(p).nextAll().slice(0, 1).text().replace(/[^0-9\.-]+/g,""));
                                    let price = sumline / qty

                                    data.push({
                                        'item': $(p).text(),
                                        'qty': qty,
                                        'price': Number(price.toFixed(4)),
                                        'sumline': sumline
                                    });
                                }
                            break;


                            case 5: // HOME PRO
                                
                                if(str.search("PO #") > -1){ // ค้นหา PO
                                    $(p).first().css('background','green');    
                                    $(p).prev().css('background','pink');                          
                                }

                                if(str.search("/1 EA") > -1){ // find string 885 from p

                                    $(p).prevAll().slice(1, 2).css('background','#51ff00');
                                    $(p).next().css('background','red');                            
                                    $(p).nextAll().slice(1, 2).css('background','orange');

                                    let qty = Number($(p).next().text().replace(/[^0-9\.-]+/g,""));
                                    let sumline = Number($(p).nextAll().slice(1, 2).text().replace(/[^0-9\.-]+/g,""));
                                    let price = sumline / qty

                                    data.push({
                                        'item': $(p).prevAll().slice(1, 2).text(),
                                        'qty': qty,
                                        'price': Number(price.toFixed(4)),
                                        'sumline': sumline
                                    });
                                }
                            break;

                            case 6: // MAGA PRO
                                
                                if(str.search("PO #") > -1){ // ค้นหา PO 
                                    $(p).prev().css('background','pink');                          
                                }

                                if(str.search("/1 EA") > -1){ // find string 885 from p

                                    $(p).prevAll().slice(1, 2).css('background','#51ff00');
                                    $(p).next().css('background','red');                            
                                    $(p).nextAll().slice(1, 2).css('background','orange');

                                    let qty = Number($(p).next().text().replace(/[^0-9\.-]+/g,""));
                                    let sumline = Number($(p).nextAll().slice(1, 2).text().replace(/[^0-9\.-]+/g,""));
                                    let price = sumline / qty

                                    data.push({
                                        'item': $(p).prevAll().slice(1, 2).text(),
                                        'qty': qty,
                                        'price': Number(price.toFixed(4)),
                                        'sumline': sumline
                                    });
                                }
                            break;
                        
                        
                            default:
                                break;
                        }

                    
                    
                })
            }
        })

        console.log(data);

    }); // findCompany
});






$('button.confirm-data').on('click',function(){

    

    // $.ajax({
    //     url:'index.php?r=SaleOrders/ajax/create-sale-line',
    //     type: 'POST',
    //     data: {id:$('form#import-file').attr('data-key'),data:dataset},
    //     success:function(response){
    //         var obj = $.parseJSON(response);
    //         if(obj.status == 200){
    //             window.opener.location.reload();
    //             window.close();
    //         }else if(obj.status == 404){
    //             alert(obj.message);
    //         }else {
    //             alert(obj.message);
    //         }
    //     }

    // })
   //window.close();

})
 
</script>
</body>
</html>



 