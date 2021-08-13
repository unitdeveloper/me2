<div id="findItems" class="modal fade" role="dialog" tabindex="-1" data-keyboard="true">
    <div class="modal-dialog modal-lg ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-green-ew">
                <button type="button" class="close" ng-click="closeModal()">&times;</button>
                <h4 class="modal-title">{{modalHeader}}</h4>
            </div>
            <div class="modal-body">
                <div class=" ">
                    <div class="col-xs-offset-8">
                        <div class="col-xs-12">
                            <div class="form-group has-warning has-feedback">
                            <input type="text" class="form-control" ng-model="itemSearch" placeholder="<?=Yii::t('common','Search')?>">
                            <span class="form-control-feedback">
                                <i class="glyphicon glyphicon-search"></i>
                            </span>
                            </div>
                        </div>
                    </div>


                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Name</th>
                            <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="pointer" ng:repeat="item in findItem | limitTo:10" ng-click="defineCode($event)" data-key="{{item.no}}">
                            <td ng-bind="$index+1"></td>
                            <td>
                                <div ng-bind="item.barcode"> 
                                <button type="button" data-key="{{item.no}}"
                                class="btn btn-success btn-xs"
                                ng-class="{hidden : item.active === false}"
                                ng-click="updateItem($event)">
                                <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Comfirm')?>
                                </button>
                                </div>
                            </td>
                            <td ng-bind="item.desc_th"></td>
                            <td class="text-right" ng-bind="item.price"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div align="left" ng-bind-html="bodyModal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" ng-click="closeModal()"><i class="fa fa-power-off" aria-hidden="true"></i> Close</button>
            </div>
        </div>
        <!-- /Modal content-->
    </div>
</div>



<div id="financeModal" class="modal fade" >
    <div class="modal-dialog  ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" ng-click="closeModalFn()">&times;</button>
                <h4 class="modal-title" ng-bind-html="fnHeader"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6 col-xs-5"><h3>ยอดเงิน</h3></div>
                    <div class="col-sm-6 col-xs-7"><div class="subtotal" style="color:#fff;" ng-bind="subtotal|number:2"></div></div>
                </div>

                <div class="row">
                    <div class="col-sm-6 col-xs-5"><h3>รับเงิน</h3></div>
                    <div class="col-sm-6 col-xs-7">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-secondary btn-default-ew" type="button" ng-click="rcMoney=0"><?=Yii::t('common','Clear')?></button>
                            </span>
                            <input type="number" name="rcMoney" step=any ng-pattern="/^-?[0-9][^\.]*$/" class="form-control subtotal" ng-model="rcMoney" ng-keyup="receiveMoney($event)" >
                        </div>
                         
                        <div class="money-selected">
                            <div href="javascript:void(0);" class="banknotes" id="bank-1000" ng-click="banknote(1000)">1,000</div>
                            <div href="javascript:void(0);" class="banknotes" id="bank-500" ng-click="banknote(500)">500</div>
                            <div href="javascript:void(0);" class="banknotes" id="bank-100" ng-click="banknote(100)">100</div>
                            
                            
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <div class="row ">
                    <div class="col-xs-6" ng:if="rcMoney > 0"><h3>เงินทอน</h3></div>
                    <div class="col-xs-6" ng:if="rcMoney > 0">
                        <div class="subtotal" style="background-color:#ccc;color:#000; border:1px solid green;" ><span ng-bind="rcMoney - subtotal|number:2"></span></div>
                    </div>  

                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="button" ng-click="confirmPayment($event)"  class="btn btn-success btn-lg "><i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Confirm Payment')?></button>
                    </div>
                </div>
            </div>

        </div>
        
        <!-- /Modal content-->
        
    </div>
</div>
