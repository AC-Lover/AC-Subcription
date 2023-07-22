<?php

if ((isset($_SERVER['HTTP_USER_AGENT']) and empty($_SERVER['HTTP_USER_AGENT'])) or !isset($_SERVER['HTTP_USER_AGENT'])){
    header('Location: /');
    exit();
}
$isTextHTML=str_contains(($_SERVER['HTTP_ACCEPT']??''),'text/html');

const BASE_URL="https://YOUR_IP:PORT"; // Replace IP address and port

$URL=BASE_URL.$_SERVER['SCRIPT_URL']??'';
$URL .= $isTextHTML?'/info':'';

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,$URL);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 17);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST , 'GET');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


$response = curl_exec($ch);
if (curl_error($ch)) die('Error !'.__LINE__);
curl_close($ch);


$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
$response=trim(str_replace($header_text,'',$response));

if ($isTextHTML){
    ?>
    <form id="myForm" action="../view-service/" method="post">
    <?php
    echo '<input type="hidden" name="userData" value="'.htmlentities(base64_encode($response)).'">';

    ?>
</form>
<script type="text/javascript">
    document.getElementById('myForm').submit();
</script>
<?php
    return;
}

$isOK=false;
foreach (explode("\r\n", $header_text) as $i=>$line){
    if ($i === 0) continue;
    list ($key,$value) = explode(': ', $line);
    if (in_array($key,['content-disposition','content-type','subscription-userinfo','profile-update-interval'])) {
        header("$key: $value");
        $isOK=true;
    }
}

if (!$isTextHTML and !$isOK ) die('Error !'.__LINE__);

echo $response;
