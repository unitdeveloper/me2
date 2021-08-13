<form id="profile-user" action="" method="post" enctype="multipart/form-data">
    <div class="panel panel-warning">
        <div class="panel-heading"><h4>Convert PDF to Text</h4></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-2">
                    <input type="file" class="file" name="file" accept=".pdf"> 
                </div>
                <div class="col-sm-2">
                    <input type="submit" value="Submit" name="submit" class="btn btn-primary">  
                </div>
                <div class="col-sm-8">
                    <div class="panel panel-info">
                        <div class="panel-body">                                                
                            <button type="button" class="btn btn-warning data-validation"><i class="fas fa-file-archive"></i> Extract</button> 
                        </div>
                    </div>
                    <div id="json-data"></div>
                </div>
            </div>
        </div>
    </div>
 
    
</form>
<hr>
<?php //var_dump(Yii::$app->getRequest()->serverName);?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            
            <div class="panel-body" id="pdf-content">
                
                <?=$text;?> 
                
            </div>
        </div>

    </div>
    
</div>

<?php
$js =<<<JS

    $('button.data-validation').on('click',function(){
        
      
 
        console.log(getRowData(getPage()));
        
        
            
        
    });

    function getRowData(totalPage){

         
        var tagid   = 70;  
        var data    = [];
        var source  = [];
        var i       = 0;

        $.each(totalPage,function(key,page){

            // ถ้า Page 2 ให้เริ้มตั้งแต่ array ที่ 1
            if(page.no==2){
                tagid   = 0;
                i       = 0;
            }
            $.each( $('#'+page.id+' > p'), function(indexInArray, valueOfElement){
                
                // เริ่มนับตั้งแต่ แถวที่ 70
                if (indexInArray >= tagid){

                    i++;
                        
                    if(i == 1 ){

                        $($('#'+page.id+' > p')[indexInArray]).css('background','yellow');
                        source['no'] = $($('#'+page.id+' > p')[indexInArray]).text();
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
                        
                        
                        data.push({'no':source['no'],'qty':source['qty'],'price':source['price']});

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
        $($('p')[last]).css('background','red');

        var lastpage = $($('p')[last]).closest('div').attr('id');

       

        var pageNumber = lastpage.substring(4, 5);
        for(i = 1; i<= pageNumber; i++){
            totalPage.push({'id' : 'page'+i+'-div','no' : i});
        }
        //console.log(totalPage);
        // \. End ค้นหาจำนวน Page 


        return totalPage;
    }
JS;

$this->registerJS($js);