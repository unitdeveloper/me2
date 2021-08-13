var app = angular.module("ewApp", ['ngRoute','ngAnimate', 'ngSanitize', 'ui.bootstrap','ngBarcode'], function($httpProvider) {
  // Use x-www-form-urlencoded Content-Type
  /*
  * ในการใช้งาน yii2 ต้องส่ง meta :scrf-token ไปให้ด้วย (ถ้าไม่ส่งไปจะเกิด Error 400)
  * แต่รูปแบบการส่งยังไม่สมบูรณ์ เนื่องจาก เป็นการส่งแบบ json ระบบยังไม่สามารถรับค่าได้
  * ดังนั้น ต้อง convert ค่าให้เป็น format ของ application/x-www-form-urlencoded (เหมือนฝั่ง JQuery)
  */
  $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  $httpProvider.defaults.headers.common['X-CSRF-Token'] = $('meta[name="csrf-token"]').attr('content');
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

  /**
   * The workhorse; converts an object to x-www-form-urlencoded serialization.
   * @param {Object} obj
   * @return {String}
   */
  var param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

    for(name in obj) {
      value = obj[name];

      if(value instanceof Array) {
        for(i=0; i<value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value instanceof Object) {
        for(subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value !== undefined && value !== null)
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }

    return query.length ? query.substr(0, query.length - 1) : query;
  };

  // Override $http service's default transformRequest
  $httpProvider.defaults.transformRequest = [function(data) {
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }];
});


app.service('dataService', function () {
    var property = 'First';

    return {
        getProperty: function () {
            return property;
        },
        setProperty: function(value) {
            property = value;
        }
    };
});

app.controller('headingCtrl',function($scope, $http, $interval, dataService){

  var tick = function() {
    $scope.clock = Date.now();
  }
  tick();
  $interval(tick, 1000);

  $scope.workdate = dataService.getProperty();

});


app.controller('workdateCtrl',function( $scope , $filter, $http, dataService ){
    $scope.dateToday = new Date();

    $scope.workDate = function($event){
      //$('button.save-workdate').addClass('btn-primary');

      if($scope.setDate==true){
        $('input[name="workingdate"]').val($('div.dateToday').attr('data-date'));
        $http.post('index.php?r=ajax/set-workdate',{date:$('input[name="workingdate"]').val()}).then(res => {
          angular.element($('button.save-workdate')).removeClass('btn-primary').addClass('btn-success').html('Auto');
          if(res.data.status===200){
            window.location.href = location.href;
          };
          //window.location.href = location.href;
        });        
      }else{
        $('input[name="workingdate"]').val($('div.dateToday').attr('data-date'));
        angular.element($('button.save-workdate')).removeClass('btn-success').addClass('btn-primary').html('Save');
      }
    }

    // $scope.$watch('workdateInput',function(newValue,oldValue){
    //   console.log(newValue);
    // });

    $scope.newWorkdate = function($event){

      $scope.workdate = dataService.setProperty($('input[name="workingdate"]').val());
      //
      $http.post('index.php?r=ajax/set-workdate',{date:$('input[name="workingdate"]').val()}).then(res => {
        //angular.element($event.currentTarget).attr('class','btn btn-success btn-flat ng-pristine ng-valid ng-empty ng-touched');
        angular.element($event.target).addClass('btn-success').html('Saved');
        if(res.data.status===200){
          window.location.href = location.href;
        };
        //console.log(angular.element($event.target).addClass('btn-success'));
      });
    }
});


function lang(source,text){
  var message = '';
  $.ajax({
    url:'index.php?r=language/translate&source='+source+'&text='+text,
    method:'POST',
    async:false,
    dataType:'JSON',
    success:function(response){
      message = response.text;
    }
  })
  return message;
}

// (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
// (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
// m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
// })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
//
// ga('create', 'UA-99888323-1', 'auto');
// ga('send', 'pageview');