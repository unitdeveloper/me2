<a class=" " data-toggle="collapse" href="#collapseOptions" role="button" aria-expanded="false" aria-controls="collapseOptions">
    Options
</a>
<div class="collapse" id="collapseOptions"  style="margin-bottom:150px;">
    <div class="row">
        <div class="col-xs-4">
        
            <div class="panel panel-success">
                <div class="panel-heading">
                        <h3 class="panel-title">Barcode Position</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Code</label>
                            <input type="text" class="form-control" name="fix-code" id="fix-code" />            
                        </div>
                        <div class="col-xs-6">
                            <label>Color</label>
                            <input type="text" class="form-control" name="fix-code-color" id="fix-code-color" />
                        </div>
                    </div>
                </div>
            </div>
        
            
        </div>
        <div class="col-xs-4">
        
            <div class="panel panel-danger">
                <div class="panel-heading">
                        <h3 class="panel-title">Quantity Position</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Quantity</label>
                            <input type="text" class="form-control" name="fix-qty" id="fix-qty"/>
                        </div>
                        <div class="col-xs-6">
                            <label>Color</label>
                            <input type="text" class="form-control" name="fix-qty-color" id="fix-qty-color" />            
                        </div>
                    </div>     
                </div>
            </div>

        </div>

        <div class="col-xs-4">        
            
            <div class="panel panel-warning">
                <div class="panel-heading">
                        <h3 class="panel-title">Total Line</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Total</label>
                            <input type="text" class="form-control" name="fix-total" id="fix-total"/>
                        </div>
                        <div class="col-xs-6">
                            <label>Color</label>
                            <input type="text" class="form-control" name="fix-total-color" id="fix-total-color"/>
                        </div>
                    </div>       
                </div>
            </div>       
            
            
        </div>
    </div>
</div>


<?php
$Yii    = 'Yii'; 

$js=<<<JS



                
findCompany(res => {
    console.log(res);
    store = res.store;   

    switch (store) {
        case 2: // GOBAL HOUSE
            let globalHouse =  localStorage.getItem("global-house")
                                ? JSON.parse(localStorage.getItem("global-house"))
                                : [];
            break;

        case 4: // Home Hub
            let homehub     =  localStorage.getItem("home-hub")
                                ? JSON.parse(localStorage.getItem("home-hub"))
                                : [];
            break;
    
        default:
            break;
    }

    

})

JS;


$this->registerJs($js,Yii\web\View::POS_END);

?>

                
                