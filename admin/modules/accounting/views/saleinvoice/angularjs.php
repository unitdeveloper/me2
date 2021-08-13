<h3>Yii Encrypt </h3><br>
<?php
$id = '698';
echo 'Key : [' .\Yii::$app->request->cookieValidationKey.']<br>';


$encrypt = \Yii::$app->security->encryptByKey($id, \Yii::$app->request->cookieValidationKey);

$decrypt = \Yii::$app->security->decryptByKey($encrypt, \Yii::$app->request->cookieValidationKey);


echo 'Encrypt : ['.$encrypt.']<br>';
echo 'Decrypt : ['.$decrypt.']<br>';

?>
<hr>
<h3>Base 64 </h3><br>
<?php

    $password = $id;
    
    $base64_encode = base64_encode($password);
    
    echo "Original = ".$password;
    echo "<br/>";
    echo "Encode with Base64 = ".($base64_encode);
    echo "<br/>";
    
    $base64_decode = base64_decode($base64_encode);
    echo "Decode with Base64 = ".($base64_decode);


?>
<script type="text/javascript">
  $(function(e){
     
     $('body').keydown(function(event) {
      //alert(event.which);
       
        if(event.which == 27) { // ESC

          alert(event.key);
        }else if(event.which == 116) { // F5

          window.location = "index.php?r=accounting%2Fsaleinvoice/angular";
        }
      });
    
  });
</script>
<hr><br> 
<div ng-controller="ModalDemoCtrl as $ctrl" class="modal-demo">

    <script type="text/ng-template" id="myModalContent.html" class="modal-full">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">I'm a modal! 1</h3>
        </div>
        <div class="modal-body" id="modal-body">
            <ul>
                <li ng-repeat="item in $ctrl.items">
                    <a href="#" ng-click="$event.preventDefault(); $ctrl.selected.item = item">{{ item }}</a>
                </li>
            </ul>
            Selected: <b>{{ $ctrl.selected.item }}</b>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" ng-click="$ctrl.ok()">OK</button>
            <button class="btn btn-warning" type="button" ng-click="$ctrl.cancel()">Cancel</button>
        </div>
    </script>


    <script type="text/ng-template" id="stackedModal.html">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title-{{name}}">The {{name}} modal!</h3>
        </div>
        <div class="modal-body" id="modal-body-{{name}}">
            Having multiple modals open at once is probably bad UX but it's technically possible.  11111
        </div>
    </script>


    <button type="button" class="btn btn-default" ng-click="$ctrl.open()">Open me!</button>
    <button type="button" class="btn btn-default" ng-click="$ctrl.open('lg')">Large modal</button>
    <button type="button" class="btn btn-default" ng-click="$ctrl.open('sm')">Small modal</button>
    <button type="button" 
        class="btn btn-default" 
        ng-click="$ctrl.open('sm', '.modal-parent')">
            Modal appended to a custom parent
    </button>

    <button type="button" class="btn btn-default" ng-click="$ctrl.toggleAnimation()">Toggle Animation ({{ $ctrl.animationsEnabled }})</button>

    <button type="button" class="btn btn-default" ng-click="$ctrl.openComponentModal()">Open a component modal!</button>

    <button type="button" class="btn btn-default" ng-click="$ctrl.openMultipleModals()">
        Open multiple modals at once 
    </button>

    <div ng-show="$ctrl.selected">Selection from a modal: {{ $ctrl.selected }}</div>
    <div class="modal-parent">

    </div>
</div>
<script>
 
    // app.controller("accController", function($scope) {
    //     $scope.test = 'x-x';
    // });

    app.controller('ModalDemoCtrl',
        function ($uibModal, $log, $document) {
          var $ctrl = this;
          $ctrl.items = ['item1', 'item2', 'item3'];

          $ctrl.animationsEnabled = true;

          $ctrl.open = function (size, parentSelector) {
            var parentElem = parentSelector ? 
              angular.element($document[0].querySelector('.modal-demo ' + parentSelector)) : undefined;
            var modalInstance = $uibModal.open({
              animation: $ctrl.animationsEnabled,
              ariaLabelledBy: 'modal-title',
              ariaDescribedBy: 'modal-body',
              templateUrl: 'myModalContent.html',
              controller: 'ModalInstanceCtrl',
              controllerAs: '$ctrl',
              size: size,
              appendTo: parentElem,
              resolve: {
                items: function () {
                  return $ctrl.items;
                }
              }
            });

            modalInstance.result.then(function (selectedItem) {
              $ctrl.selected = selectedItem;
            }, function () {
              $log.info('Modal dismissed at: ' + new Date());
            });
          };

          $ctrl.openComponentModal = function () {
            var modalInstance = $uibModal.open({
              animation: $ctrl.animationsEnabled,
              component: 'modalComponent',
              resolve: {
                items: function () {
                  return $ctrl.items;
                }
              }
            });

            modalInstance.result.then(function (selectedItem) {
              $ctrl.selected = selectedItem;
            }, function () {
              $log.info('modal-component dismissed at: ' + new Date());
            });
          };

          $ctrl.openMultipleModals = function () {
            $uibModal.open({
              animation: $ctrl.animationsEnabled,
              ariaLabelledBy: 'modal-title-bottom',
              ariaDescribedBy: 'modal-body-bottom',
              templateUrl: 'stackedModal.html',
              size: 'sm',
              controller: function($scope) {
                $scope.name = 'bottom';  
              }
            });

            $uibModal.open({
              animation: $ctrl.animationsEnabled,
              ariaLabelledBy: 'modal-title-top',
              ariaDescribedBy: 'modal-body-top',
              templateUrl: 'stackedModal.html',
              size: 'sm',
              controller: function($scope) {
                $scope.name = 'top';  
              }
            });
          };

          $ctrl.toggleAnimation = function () {
            $ctrl.animationsEnabled = !$ctrl.animationsEnabled;
          };
        });

        // Please note that $uibModalInstance represents a modal window (instance) dependency.
        // It is not the same as the $uibModal service used above.

        app.controller('ModalInstanceCtrl', function ($uibModalInstance, items) {
          var $ctrl = this;
          $ctrl.items = items;
          $ctrl.selected = {
            item: $ctrl.items[0]
          };

          $ctrl.ok = function () {
            $uibModalInstance.close($ctrl.selected.item);
          };

          $ctrl.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          };
        });

        // Please note that the close and dismiss bindings are from $uibModalInstance.

        app.component('modalComponent', {
          templateUrl: 'myModalContent.html',
          bindings: {
            resolve: '<',
            close: '&',
            dismiss: '&'
          },
          controller: function () {
            var $ctrl = this;

            $ctrl.$onInit = function () {
              $ctrl.items = $ctrl.resolve.items;
              $ctrl.selected = {
                item: $ctrl.items[0]
              };
            };

            $ctrl.ok = function () {
              $ctrl.close({$value: $ctrl.selected.item});
            };

            $ctrl.cancel = function () {
              $ctrl.dismiss({$value: 'cancel'});
            };
          }
        });
 
  
</script>