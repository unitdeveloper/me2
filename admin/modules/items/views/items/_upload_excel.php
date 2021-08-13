<div class="modal fade modal-full" id="modal-item-upload">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Upload</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 mt-10"><input type="file" id="fileUpload" /> </div>
                    <div class="col-xs-12 mt-10" id="dvExcel"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-primary confirm-upload"><i class="fas fa-upload"></i> <?= Yii::t('common','Upload')?></button>
            </div>
        </div>
    </div>
</div>


 
<script type="text/javascript">

    $("body").on("change", "#fileUpload", function () {
        //Reference the FileUpload element.
        var fileUpload = $("#fileUpload")[0];
 
        //Validate whether File is valid Excel file.
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
        if (regex.test(fileUpload.value.toLowerCase())) {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
 
                //For Browsers other than IE.
                if (reader.readAsBinaryString) {
                    reader.onload = function (e) {
                        ProcessExcel(e.target.result);
                    };
                    reader.readAsBinaryString(fileUpload.files[0]);
                } else {
                    //For IE Browser.
                    reader.onload = function (e) {
                        var data = "";
                        var bytes = new Uint8Array(e.target.result);
                        for (var i = 0; i < bytes.byteLength; i++) {
                            data += String.fromCharCode(bytes[i]);
                        }
                        ProcessExcel(data);
                    };
                    reader.readAsArrayBuffer(fileUpload.files[0]);
                }
            } else {
                alert("This browser does not support HTML5.");
            }
        } else {
            alert("Please upload a valid Excel file.");
        }
    });

    
    
</script>


<?php 
$js=<<<JS

let excelJson = [];

const ProcessExcel = (data) => {
    //Read the Excel File data.
    var workbook = XLSX.read(data, {
        type: 'binary'
    });

    //Fetch the name of First Sheet.
    var firstSheet = workbook.SheetNames[0];

    //Read all rows from First Sheet into an JSON array.
    var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);

    //Create a HTML Table element.
    var table = $("<table class='table table-bordered table-prepare-upload' />");
    //table[0].border = "0";

    //Add the header row.
    var row = $(table[0].insertRow(-1));

    //Add the header cells.
    var headerCell = $("<th />");
    headerCell.html("Id");
    row.append(headerCell);

    var headerCell = $("<th />");
    headerCell.html("Code");
    row.append(headerCell);

    var headerCell = $("<th />");
    headerCell.html("Name");
    row.append(headerCell);

    var headerCell = $("<th />");
    headerCell.html("Detail");
    row.append(headerCell);

    var headerCell = $("<th />");
    headerCell.html("Size");
    row.append(headerCell);

    var headerCell = $("<th />");
    headerCell.html("Quantity");
    row.append(headerCell);

    var headerCell = $("<th />");
    headerCell.html("Unit");
    row.append(headerCell);

    excelJson = [];
    //Add the data rows from Excel file.
    for (var i = 0; i < excelRows.length; i++) {

        
        excelJson.push({
            id: excelRows[i].id,
            code: excelRows[i].code ? excelRows[i].code : '',
            name: excelRows[i].name ? excelRows[i].name : '',
            detail: excelRows[i].detail ? excelRows[i].detail : '',
            size: excelRows[i].size  ? excelRows[i].size : '',
            qty: excelRows[i].qty,
            unit: excelRows[i].unit ? excelRows[i].unit : ''
        });
        //Add the data row.
        var row = $(table[0].insertRow(-1));

        //Add the data cells.
        var cell = $("<td />");
        cell.html(excelRows[i].id);
        row.append(cell);

        cell = $("<td />");
        cell.html(excelRows[i].code);
        row.append(cell);

        cell = $("<td />");
        cell.html(excelRows[i].name);
        row.append(cell);

        cell = $("<td />");
        cell.html(excelRows[i].detail);
        row.append(cell);

        cell = $("<td />");
        cell.html(excelRows[i].size);
        row.append(cell);

        cell = $("<td />");
        cell.html(excelRows[i].qty);
        row.append(cell);

        cell = $("<td />");
        cell.html(excelRows[i].unit);
        row.append(cell);
    }
 
    var dvExcel = $("#dvExcel");
    dvExcel.html("");
    dvExcel.append(table);
};

JS;
$this->registerJS($js,\yii\web\View::POS_HEAD); 
?>


<?php 
$jsx=<<<JS
    
    $('body').on('click', '.confirm-upload', function(){    


        fetch("?r=items/items/create-ajax-multiple", {
            method: "POST",
            body: JSON.stringify(excelJson),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {
             
            $('#modal-item-upload').modal('hide');

            res.raw.map(model => {
                // Insert to cache
                if(!model.already){                    
                    state.data.raw.push({
                        code: model.code,
                        detail: model.detail,
                        id: model.id,
                        img: model.img,
                        name: model.name,
                        stock: model.stock,
                        size: model.size,
                        unit: model.unit,
                    });
                }

            })
            
            // Render
            renderTable(state.data);

            // Clear excel render   
            excelJson = [];   
            $('#dvExcel').html('');  
            $("#fileUpload").val('');
            
            
        })
        .catch(error => {
            console.log(error);        
        });

  
        
    })

JS;
$this->registerJS($jsx,\yii\web\View::POS_END); 
?>

<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/xlsx.full.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/jszip.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>