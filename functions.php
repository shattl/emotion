<?php
session_start();
if (!isset($_SESSION['key'])) $_SESSION['key'] = md5(mt_rand(0,20000).mt_rand(0,10000).mt_rand(0,90000));
function curl($method, $data = "") {
	global $_SESSION;
    $ch = curl_init();
    $settings = array(
        CURLOPT_URL => "https://emotion.megalabs.ru/api/v15/{$method}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
          "Content-Type: application/json",
        ),
        CURLOPT_COOKIEFILE => "/tmp/{$_SESSION['key']}.cookie",
        CURLOPT_COOKIEJAR => "/tmp/{$_SESSION['key']}.cookie",
    );
    if (!empty($data)) {
        $settings[CURLOPT_POSTFIELDS] = $data;
    }    
    curl_setopt_array($ch, $settings);
    $e = curl_exec($ch);
    return $e;
}

function getCode($msisdn) {
    $resp = curl("ident/{$msisdn}");
    $c = json_decode($resp,true);
    if ($c['code']=='0') return true;
    else {echo "Ошибка №{$c['code']} при ident"; die();}
}

function checkCode($msisdn, $code) {
    $resp = curl("verify",json_encode(["code"=>$code, "msisdn"=>$msisdn]));
    $c = json_decode($resp,true);
    if ($c['code']=='0') return true;
    else {echo "Ошибка №{$c['code']} при verify"; die(PHP_EOL.print_r($scope,true));}
}

function getPasswordAfterThat($msisdn) {
    $resp = curl("login",json_encode(["msisdn"=>$msisdn]));
    $c = json_decode($resp,true);
    if ($c['code']=='0') return $c['password'];
    elseif ($c['code']=="100008") {echo "Услуги eMotion еще подключаются на Вашем номере, ожидайте поступления SMS о подключении и попробуйте еще раз..."; die();}
    else {echo "Ошибка №{$c['code']} при login"; die();}
}
function getBalance($msisdn, $password) {
    $resp = file_get_contents("https://emotion.megalabs.ru/sm/client/balance?login={$msisdn}@multifon.ru&password={$password}");
    $xml = new SimpleXMLElement($resp);
    $x2a = xml2array($xml);
    return $x2a['balance'];
}
?>
