<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <title>ตรวจนับสินค้า</title>
    <style> 
        @media print {
            @page {
                size: A4 landscape;
            }
 
            body{
                background-color:#fff !important;
            }
        }
    </style>
  </head>
  <body style="background-color:#ccc;">
    <div class="container" style="background-color:#fff; height:100%;"> 
        <div class="row">
            <div class="col-12">
                <h1 class="mt-5">ตรวจนับสินค้า</h1>

                <p class="mt-3">วันที่ : <?=date('d/m/Y')?></p>
                <p>กลุ่มสินค้า : <?=$model->name?></p>

                <table class="table table-bordered renders-table">
                    <thead>
                        <tr class="bg-light">
                            <th width="50"><?=Yii::t('common','#')?></th>
                            <th width="170"><?=Yii::t('common','Code')?></th>
                            <th><?=Yii::t('common','Name')?></th>
                            <th width="100"><?=Yii::t('common','Stock')?></th>
                            <th width="100"><?=Yii::t('common','Quantity')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>01-CT02-000000</td>
                            <td>LOADING</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>                         
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">รวม</td>
                            <td id="sum-remain" class="text-right"> </td>
                            <td > </td>
                        </tr>
                    </tfoot>
                </table>                
            </div>
             
        </div>
        <footer class="text-center">
            <div class="row signature mt-5 mb-5"> 
                <div class="col">
                        <div>..............................</div>
                        <div>(ผู้ตรวจนับ)</div>
                        <div>Creator</div>
                        <div>(........./........./.........)</div>
                </div>
                    
                <div class="col">
                        <div>..............................</div>
                        <div>(ทบทวน)</div>
                        <div>Approve</div>
                        <div>(........./........./.........)</div>
                </div>
            </div>
        </footer>
        
    </div>

    <script>

        let renderTable = (response) => {
            let sum_remain = 0;
            let html =' ';

                response.map((model,key) => {
             
                    html+='<tr key="'+key+'" data-key="'+model.id+'" class="data-tr">';
                        html+= '<td>'+(key + 1)+'</td>';
                        html+= '<td>'+model.code+'</td>';
                        html+= '<td>'+model.name+'</td>';
                        html+= '<td class="text-right">'+(model.inven <= 0 ? 0 : model.inven)+'</td>';
                        html+= '<td> </td>';
                    html+='</tr>';
                    
                    sum_remain+= model.inven <= 0 ? 0 : model.inven;
                })
      
    
            return {
                'html':html,
                'sum_remain': sum_remain <= 0 ? 0 : sum_remain
            };
        }

        $('document').ready(function(){

            $('body').find('table.renders-table tbody').html('');
            let list = JSON.parse(localStorage.getItem('buffers'));

            let filter = [];

            Object.values(list).map(group => {          // นำข้อมูลทั้งหมดมาวน
                group.data.map(model => {               // นำเฉพาะ data มาวนอีกรอบ
                    model.group_id === parseInt(<?=$model->id?>) ?   // ถ้าเจอกลุ่มที่คลิก   
                        filter.push(model)              // สร้าง array ชุดใหม่ 
                    : null;
                });
            })

            console.log(filter);

            let renders = renderTable(filter);
             
            $('body').find('table.renders-table tbody').html(renders.html);
            $('body').find('#sum-remain').html(renders.sum_remain);
       
        })
    </script>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>