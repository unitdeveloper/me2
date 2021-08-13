<?PHP
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;


if(isset($_POST['print'])){

    $connector = new NetworkPrintConnector($_POST['printer'], 9100);
    $printer = new Printer($connector);
    try {
        // ... Print stuff
       $printer -> text($_POST['printer-content']."\r\n");
       $printer -> cut();
        echo "Printing..... to {$_POST['printer']}:9100 <i class='fas fa-sync fa-spin text-primary'></i> <br/>\r\n";
    }catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    } finally {
        $printer -> close();
        
    }
}
?>
<form action="" method="POST">
    <div class="row">
        <div class="col-sm-8">
            <input type="hidden" name="print" value="true">
            <label for="printer" >Printer : </label>
            <input type="text" id="printer" name="printer" value="192.168.1.200" class="form-control">
            <textarea   name="printer-content" rows="10" class="form-control">DIRECT PRINT : PHP ref https://github.com/mike42/escpos-php</textarea>
            <br/>
            <button type="submit" class="btn btn-info pull-right"><i class="fas fa-print"></i> Start Printing</button>
        </div>
        <div class="col-sm-6"></div>
    </div>
</form>


