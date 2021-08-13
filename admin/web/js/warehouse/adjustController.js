 
app.controller('adjustController',['$scope', '$http', '$compile','$sce', '$filter',
function ($scope, $http, $compile,$sce,$filter) {

   
   $scope.dataTable = [];
  
   $scope.getProduct = function($event){
    
    var dataKey = angular.element($event.currentTarget).attr('data-key');
    console.log(dataKey);
    // Show Loading
 
    $('.data-render').fadeOut();
    $('.loading').show();     
   
    
    
    


    
    
    $http.get('index.php?r=items/ajax/list-menu&group='+dataKey)
    .then(function (response) {

        // Stop Loading  
        setTimeout(function(){
            $('.loading').hide();
            $('.data-render').fadeIn();
        },1000);
      
        $scope.dataTable    = response.data;


    });

 
   }

   



}]);

 