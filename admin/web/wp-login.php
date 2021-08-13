<?PHP
 
function fetch($obj){
    $obj = (Object)$obj;
    $ewin_api = $obj->host;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$ewin_api);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($ch, CURLOPT_POST, 1);
    if (isset($obj->method)){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $obj->method);
    }else{
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    }

    if (isset($obj->body))
    curl_setopt($ch, CURLOPT_POSTFIELDS,$obj->body);
    // follow redirects
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    if (isset($obj->header))
    curl_setopt($ch, CURLOPT_HTTPHEADER, $obj->header);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // execute!
    $response = curl_exec($ch);

    // close the connection, release resources used
    curl_close($ch);

    // do anything you want with your response
    return $response;
}

$ip = $_SERVER['REMOTE_ADDR'];


$response = fetch([
    'host' => 'http://127.0.0.1:3000/shell',
    'body' => json_encode([
        'ip' => $ip
    ]),
    'method' => 'POST',
    'header' => [
        'Content-type: application/json',
    ]
]);


$response = json_decode($response);
$location = [];

foreach ($response->iptable->deny as $key => $value) {
    if ($value->ip == $ip ){
        $location["find-worpress"] = $value->location;
    }
}

$line = fetch([
    'host' => 'http://www.ewinl.com/index.php?r=line-bot/robot&token=4573&ip='.$ip.'&message='.json_encode($location).'&url=wp-login.php',
    'method' => 'POST',
    'header' => [
        'Content-type: application/json',
    ]
]);

echo json_encode($location);


 
?>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>

$.getJSON("https://api.ipify.org/?format=json", function(e) {

      $.ajax({
        url: 'https://admin.ewinl.com/index.php?r=line-bot/robot&token=4573&ip=' + e.ip + '&message=wp&url=wp-login.php',
        type: "POST",
        async: true,
        dataType: "JSON",
        success: function(response) {
            
        }
    });
});

</script>
