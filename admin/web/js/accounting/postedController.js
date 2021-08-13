app.controller('creditnoteCtrl',function($scope,$http, $interval, $sce){

    $scope.selected     = true;
    $scope.received     = true;
    $scope.receivedAll  = true;
    $scope.selectedAll  = true;
    sumTotal();
    
    $scope.checkAll = function ($event) {
        var el = $event.currentTarget;
        var row = $('tr.credit-note-content');
        if ($scope.selectedAll) {
            $scope.selectedAll = true;
        } else {
            $scope.selectedAll = false;
        }
        angular.forEach(row, function(model) {
            $scope.selected = $scope.selectedAll;
            let item = parseInt($(model).attr('data-id'));
            if($scope.selected==true){
              $(el).closest('table').find('tbody').find('tr').attr('class','credit-note-content pointer bg-info');
              $(el).closest('table').find('tbody').find('tr').find('input[name="chk[]"]').prop('checked', true);

              if(item !== 1414){
                $(model).find('input.receive-checked').prop('checked',true);
              }else{
                $(model).find('input.receive-checked').prop('checked',false);
              }
              
              //$(el).closest('table').find('tbody').find('tr').find('input.receive-checked').attr('disabled',false);
              $('tr.credit-note-content').find('input[type=number]').attr('disabled',false);
              $('input#receive-all').prop('checked', true);
            }else{
              $(el).closest('table').find('tbody').find('tr').attr('class','credit-note-content pointer bg-default');
              $(model).find('input[name="chk[]"]').prop('checked', false);
              //$(el).closest('table').find('tbody').find('tr').find('input.receive-checked').attr('disabled',true);
              $('tr.credit-note-content').find('input[type=number]').attr('disabled',true);
              $(model).find('input.receive-checked').prop('checked',false);
              $('input#receive-all').prop('checked', false);
            }             
        });  
        sumTotal();     
    };

    $scope.receiveAll = function ($event) {
        var el = $event.currentTarget;
        var row = $('tr.credit-note-content');
        if ($scope.receivedAll) {
            $scope.receivedAll = true;
        } else {
            $scope.receivedAll = false;
        }
        angular.forEach(row, function(model) {
            $scope.received = $scope.receivedAll;
            let items       = parseInt($(model).attr('data-id'));
            if($scope.received==true){


              if(items !== 1414){
                $(model).find('input.receive-checked').prop('checked',true);
              }else{
                $(model).find('input.receive-checked').prop('checked',false);
              }
              
              //$(el).closest('table').find('tbody').find('tr').find('input.receive-checked').prop('checked', true);
            }else{
              $(el).closest('table').find('tbody').find('tr').find('input.receive-checked').prop('checked', false);
              //$(el).closest('table').find('tbody').find('tr').find('input.receive-checked').val(0);
            }             
        });   
    };

    $scope.checked = function($event){
        //$scope.selected = !$scope.selected;
        var el = $event.currentTarget;
        var me = $('tr[data-key="'+$(el).closest('tr').attr('data-key')+'"]').find('input[name="chk[]"]');
        if(me.is(":checked")){
            $(el).closest('tr').attr('class','credit-note-content pointer bg-default');
            me.prop('checked', false);
            $('tr[data-key="'+$(el).closest('tr').attr('data-key')+'"]').find('input[type=number]').attr('disabled',true);
            $('tr[data-key="'+$(el).closest('tr').attr('data-key')+'"]').find('input.receive-checked').prop('checked',false);
        }else{
            $(el).closest('tr').attr('class','credit-note-content pointer bg-info');
            me.prop('checked', true);
            $('tr[data-key="'+$(el).closest('tr').attr('data-key')+'"]').find('input[type=number]').attr('disabled',false);
            $('tr[data-key="'+$(el).closest('tr').attr('data-key')+'"]').find('input.receive-checked').prop('checked',true);
        }
        $scope.validateAll()
        sumTotal();
    }

    $scope.validateAll = function(){
        var row = $('tr.credit-note-content').find('input[type="checkbox"]');  
        var countItem = [];
        angular.forEach(row, function(model){    
            // Checked Id
            if($(model).is(":checked")){    
                countItem.push({
                  'id':$(model).attr('data')
                })    
            }
        });
    
        if(countItem.length === row.length){
            $('tr.credit-note-content').find('input[type="checkbox"]').prop('checked', true);
            $scope.selectedAll = true;            
        }else{            
            $scope.selectedAll = false;            
        }
      }

    $scope.confirmCn = function($event){       
        
        var row = $('tr.credit-note-content').find('input[type="checkbox"]');         
        var countItem = [];
        angular.forEach(row, function(model) {             
            if($(model).is(":checked")){
                countItem.push({
                    'id':$(model).attr('data')
                })
            }                 
        });   

        // if(countItem.length<=0){
             
        // }
        // console.log(countItem);     
    }
});
