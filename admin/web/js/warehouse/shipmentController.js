// 08/03/2019
  
app.controller('shipmentCtrl',function($scope,$http, $interval, $sce){

  

  $scope.openModal = function($event,sce){
    $('.loading').show();
    $('.btn-confirm').attr('class','btn btn-default btn-confirm').attr('disabled',true);
    $scope.selectedAll = false;

    

    $scope.modalTitle = $sce.trustAsHtml('<i class="fa fa-credit-card" aria-hidden="true"></i> รับชำระเงิน');



    var id = angular.element($event.currentTarget).attr('data-key');
    var no = angular.element($event.currentTarget).attr('data-no');

    // Barcode Translate
    if($scope.scanBarcode!=null){
      var dataArray = $scope.scanBarcode.split(' ');
          id        = dataArray[1];
          no        = dataArray[2]; 

      //console.log($scope.scanBarcode);
      // Clear Data
      $scope.scanBarcode = null;
      $('input.scanBarcode').val('').focus();
    }


    $http.post('index.php?r=warehousemoving/shipment/confirm-stock',{id:id,no:no})
    .then(function(response){
      $scope.modalTitle = $sce.trustAsHtml('<i class="fa fa-file-text-o" aria-hidden="true"></i> '+ response.data[0].order + ' : ['+ response.data[0].custcode + '] '+ response.data[0].custname+' ('+ response.data[0].province +')');
      $scope.itemList   = response.data;
      $scope.orderId    = response.data[0].orderid;
      $scope.orderNo    = response.data[0].order;
      $scope.custId     = response.data[0].custid;

      $scope.text       = response.data[0].text;
      
      $scope.printList  = $scope.itemList

      if(response.data[0].status==true){
        $('#shipModal').modal('show');
        
      }else {
        alert(response.data[0].text.message+'\r\n'+response.data[0].text.alert);
      }

      if(response.data[0].confirm == 0){
        $('body').find('.btn-confirm').show();
      }
      
      /**
       * Barcode Generator
       * 
       * Example Code
       * 
       *   |01 1384 SO1711-0001 1
       * 
       * -[|] => Start Barcode
       * --[01] => Company ID
       * -----[1384] => Order Id
       * ----------[SO1711-0001] => Document No
       * ----------------------[1] => Customer Id
       * 
       * 
       * Some problem, Some barcode scanner can't read ascii charector.
       * Change to 
       *   01 1383
       * -[01] => Company ID
       * ----[1384] => Order ID 
       */
      
      var compId      = response.data[0].comp_id;
      var custid      = response.data[0].custid; 
      var custcode    = response.data[0].custcode;
      var orderid     = response.data[0].orderid;
      var orderNo     = response.data[0].order;



      $scope.barcode  = compId+' '+orderid;


      // $scope.genBarcode = function(keyCode){
        
      //   JsBarcode("#barcode0", keyCode+"",{
      //     format:"code39",
      //     displayValue:true,
      //     fontSize:10,
      //     height:30,
      //   });

      // };
       
      // $scope.genBarcode($scope.barcode);

     // console.log($scope.barcode);
       
     $('.loading').hide();
    })


  }

  // ตั้งตัวแปล สำหรับเก็บ Event การเปลี่ยนตัวเลขสั่งผลิต
  // ถ้า เปลี่ยน ให้ set ค่าเป็น 1  
  $scope.session_change = 0;

  $scope.updateOutput = function (model){

    //console.log(model.id);
    for (var i = 0; i < $scope.printList.length; i++) {
      var newValue = event.target.value;
      if ($scope.printList[i].id === model.id) {

        // หน่วงเวลาไว้ (แก้ปัญหาทำให้ตัวเลขเปลี่ยนเร็วเกินไป จนใส่เลข 2 หลักไม่ทัน)  
        setTimeout(function(){
          $scope.printList[i].qty = newValue;
        },1000);
       

        // เมื่ีอเปลี่ยนจำนวนการผลิต set ค่าเป็น 1
        $scope.session_change = 1;

        break;
      }

    }
    //console.log($scope.printList);
    //$scope.printList = '';
  }

  $scope.nomalPrint = function(){

    $('.item-child').hide(); 
    $('.qty_per').hide(); 
    $(document).ready(function(){ 
      setTimeout(function(){
        window.print();
      },500); 
    });

    for (var i = 0; i < $scope.printList.length; i++) {
      
      $scope.printList[i].qtyprint = $scope.printList[i].need;

    }
  }

  $scope.bomPrint = function(){

    $('.item-child').show(); 
    $('.qty_per').show(); 
    $(document).ready(function(){ 
      setTimeout(function(){
        window.print();
      },500); 
    }); 

    for (var i = 0; i < $scope.printList.length; i++) {

      var model = $scope.printList[i];

      // ถ้าไม่ได้เปลี่ยนจำนวนการผลิต 
      // ให้ดึงค่าส่วนต่างมาปริ๊้น      
      if($scope.session_change == 0){

          // ถ้าจำนวนสินค้าในคลัง น้อยกว่า จำจวนที่ต้องการ
          if(model.inven < model.need){
            // (ไม่มีของ)
            // ให้แสดง (จำนวนที่ต้องการ - จำนวนสินค้าในคลัง)

            // ถ้าจำนวนสินค้าในคลัง ติดลบ ให้ใช้ค่าที่ต้องการ
            if(model.inven < 0){

              $scope.printList[i].qtyprint = model.qty;

            }else {

              $scope.printList[i].qtyprint = model.need - model.inven;

            }
            


    
          }else{
            // (มีของ)
            // ถ้าสินค้าในคลัง มีมากกว่า จำนวนที่ต้องการ
            // ให้แสดงจำนวนที่ต้องการ
            $scope.printList[i].qtyprint = $scope.printList[i].qty;
            
          }

      }else {
        // ถ้ามีการเปลี่ยนจำนวนการผลิต 
        $scope.printList[i].qtyprint = $scope.printList[i].qty;

      }
      
      

    }
  }

  $scope.validateAll = function(){
    var countItem = [];
    angular.forEach($scope.itemList, function(model){

        // Checked Id
        if(model.selected){

            countItem.push({
              'id':model.id
            })

        }
    });

    if(countItem.length === $scope.itemList.length){
      $('.btn-confirm').attr('class','btn btn-warning btn-confirm').attr('disabled',false);
      $scope.selectedAll = true;
    }else{
      $('.btn-confirm').attr('class','btn btn-default btn-confirm').attr('disabled',true);
    }
  }


  // ใส่เวลาเข้าไปตอนเลือกรายการ
  // เพื่อป้องกันการเลือกผิดพลาด
  var tick = function() {
    $scope.clock = Date.now();
  }
  tick();
  $interval(tick, 1000);

  $scope.checked = function(model,$event){
    model.selected = !model.selected;




    var el = $event.currentTarget;

    if(model.selected==true){
      $(el).closest('tr').attr('class','pointer bg-info');
      model.time = $scope.clock;
    }else{
      $(el).closest('tr').attr('class','pointer bg-default');
      $scope.selectedAll = false;
    }

    $scope.validateAll();

  }

  $scope.checkAll = function ($event) {

      var el = $event.currentTarget;

      if ($scope.selectedAll) {
          $scope.selectedAll = true;
      } else {
          $scope.selectedAll = false;
      }
      angular.forEach($scope.itemList, function(model) {
          model.selected = $scope.selectedAll;
          if(model.selected==true){
            $(el).closest('table').find('tbody').find('tr').attr('class','pointer bg-info');
            model.time = $scope.clock;
          }else{
            $(el).closest('table').find('tbody').find('tr').attr('class','pointer bg-default');
          }
      });

      $scope.validateAll();
  };

  $scope.confirmCheckList = function($event){
    var el = $event.currentTarget;
    
    if (confirm($scope.text.confirm)) {
      var quantity = 0;
      var qtyCheck = 0;
      angular.forEach($scope.itemList, function(model){
          quantity += model.qty;
          // Checked Id
          if(model.selected){
              qtyCheck += model.qty;
          }
      });
      if(qtyCheck != quantity){
        // Some checked
        //console.log('false');
        alert(lang('common','Please contact administrator.'));
      }else{
        // All checked
        //console.log('true');

        var qtytoship = $('form.Shipment input.need').serializeArray();
        var qtyOutput = $('form.Shipment input.output').serializeArray();
        //console.log('send'+qtytoship.length);
        var data = {
          'input'   : qtytoship,
          'output'  : qtyOutput,
          'id'      : $(el).closest('form').attr('data-key'),
         };

        $('#shipModal').modal('hide');
        $('body').find('.btn-confirm').hide();
        $('body').find('td[data-key="'+data.id+'"]').find('.status').html('<i class="fa fa-refresh fa-spin fa-2x"></i> <span class="blink">Loading...</span>');
        
        $http.post('index.php?r=warehousemoving/shipment/confirm-checklist&id='+data.id,data)
              .then(function(response){

                $('body').find('.btn-confirm').hide();
                if(response.data.status==200){
                  // swal(
                  //   response.data.text.Success,
                  //   '',
                  //   'success'
                  // );

                  $.notify({
                      // options
                      icon: 'fas fa-exclamation',
                      message: response.data.text.Success
                  },{
                      // settings
                      type: 'info',
                      delay: 1000,
                      z_index:3000,
                  });
                    
                    setTimeout(() => {
                      
                      $('body').find('td[data-key="'+data.id+'"]').find('.status').html('<i class="fa fa-check-square-o text-success"></i> Confirmed');
                      $('body').find('tr[data-key="'+data.id+'"]').removeClass('bg-pink');
                      $('body').find('tr[data-key="'+data.id+'"]').find('.serial-column').attr('class','bg-gray serial-column');
                    },800);
                 //setTimeout(function(){ window.location.href = 'index.php?r=warehousemoving'; },10000);
                }else {
                  swal(
                    response.data.text.Error,
                    response.data.message,
                    'warning'
                  );
                }
              });
      }
    }
  }



  $scope.postShipment   = function($event){

    var el = $event.currentTarget;

    if (confirm(lang('common','Do you want to confirm ?'))) {
      var quantity = 0;
      var qtyCheck = 0;
      angular.forEach($scope.itemList, function(model){
          quantity += model.qty;
          // Checked Id
          if(model.selected){
              console.log(model.id)
              qtyCheck += model.qty;
          }
      });
      if(qtyCheck != quantity){
        // Some checked
        console.log('false');
        alert(lang('common','Please contact administrator.'));
      }else{
        // All checked
        console.log('true');

        var qtytoship = $('form.Shipment input.qty').serializeArray();
        var appdata = { param:{
                apk:'ShipNow',
                id:$(el).closest('form').attr('data-key'),
                no:$(el).closest('form').attr('data-no'),
                cur:'Shiped',
                reson:'',
                qtytoship:qtytoship,
                addrid:'',
                custid:$(el).closest('form').attr('data-cust'),
                shipdate:$scope.workdate,

        }};
        //console.log(appdata);

        $http.post('index.php?r=approval/approve/sale-order',appdata)
              .then(function(response){

                if(response.data == 'Error !'){
                  //alert(lang('common','Already exists'));
                  swal({
                         title: lang('common','Already exists'),
                         type: 'info',
                         showCancelButton: false,
                         confirmButtonColor: '#3085d6',
                         cancelButtonColor: '#d33',
                         confirmButtonText: lang('common','Ok')
                      }).then(function (result) {
                         if (result) {
                           window.location.href = 'index.php?r=warehousemoving';
                         }
                       })
                }else {
                  //alert('Shipment No : '+response.data.no);
                  swal({
                         title: lang('common','Success'),
                         html: lang('common','Shipment No')+' : <a href="index.php?r=warehousemoving/header/view&id='+response.data.docid+'" target="_blank">'+response.data.no +'</a>',
                         type: 'success',
                         showCancelButton: false,
                         confirmButtonColor: '#3085d6',
                         cancelButtonColor: '#d33',
                         confirmButtonText: lang('common','Ok')
                      }).then(function (result) {
                         if (result) {
                           window.location.href = 'index.php?r=warehousemoving';
                         }

                       })

                }
              });
      }
     }
  }

  $scope.zoomImg = function(){
    console.log('test');
  }



  
});
