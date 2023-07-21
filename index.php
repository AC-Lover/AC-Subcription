<?php
//error_log(json_encode($_SERVER,256|64|128));

if ((isset($_SERVER['HTTP_USER_AGENT']) and empty($_SERVER['HTTP_USER_AGENT'])) or !isset($_SERVER['HTTP_USER_AGENT'])){
    http_response_code(403);
//    exit("<h2>Access Denied</h2></br>You don't have permission to view this site.</br>Error code:403 forbidden");
    exit('<!DOCTYPE html>
<html lang="en">
<head>
<title>Access Denied</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta charset="UTF-8">
<style>
body{
background-color: black;
color: white;
}

h1 {
color: red;
}

h6{
color: red;
text-decoration: underline;
}

</style>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class="w3-display-middle">
<h1 class="w3-jumbo w3-animate-top w3-center"><code>Access Denied</code></h1>
<hr class="w3-border-white w3-animate-left" style="margin:auto;width:50%">
<h3 class="w3-center w3-animate-right">You don\'t have permission to view this site.</h3>
<h3 class="w3-center w3-animate-zoom">🚫🚫🚫🚫</h3>
<h6 class="w3-center w3-animate-zoom">error code:403 forbidden</h6>
</div>
</body>
</html>');
}

$isTextHTML=str_contains(($_SERVER['HTTP_ACCEPT']??''),'text/html');

const BASE_URL="https://23.88.34.98:2083";

$URL=BASE_URL.$_SERVER['SCRIPT_URL']??'';

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,$URL);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 17);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST , 'GET');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER,$isTextHTML?[
    'Accept: text/html'
]:[]);

$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (curl_error($ch)) {
    http_response_code($code);
    die('Error !'.__LINE__);
}
curl_close($ch);
$headers=get_headers_from_curl_response($data);

if (!$isTextHTML and (empty($headers) or $code!==200) ){
http_response_code($code);
die('Error !'.__LINE__);
}



foreach ($headers as $key=>$header){
    header("$key: $header");
}

function get_headers_from_curl_response(&$response): array
{
    $headers = [];

    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

    foreach (explode("\r\n", $header_text) as $i=>$line) {
        if ( $i===0 ) continue;
        list ($key, $value) = explode(': ', $line);

        if (in_array($key,['content-disposition','content-type','subscription-userinfo','profile-update-interval'])) $headers[$key] = $value;
    }
    $response=trim(str_replace($header_text,'',$response));
    return $headers;
}


echo $data;
