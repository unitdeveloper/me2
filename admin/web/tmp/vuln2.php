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







    $response = fetch([
        'host' => 'http://www.ewinl.com/index.php?r=line-bot/robot&token=4573&message=Exploits&ip='.$_SERVER['REMOTE_ADDR'].'&url=admin/web/tmp',
        'method' => 'POST',
        'header' => [
            'Content-type: application/json',
        ]
    ]);


    echo $response;

    
?>
