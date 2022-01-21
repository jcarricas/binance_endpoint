<?php

header('Content-Type: application/json; charset=utf-8');

$symbol = $_GET['symbol'];
$cachetime=0;
if(isset($_GET['cachetime']))
    $cachetime = (int)$_GET['cachetime'];

$response_json=false;
if (file_exists ( './'.$symbol.".json" ) && ($cachetime>0)){
    $cache_content = file_get_contents('./'.$symbol.".json", true);
    $cache_content=json_decode($cache_content,true);
    $saved_timestamp=$cache_content['timestamp'];
    $timestamp=microtime(true)*1000;
    if ($timestamp<=($saved_timestamp+$cachetime*1000))
        $response_json=json_encode($cache_content['data']);
}

if ($response_json){
    //Devolvemos valor en caché
    echo $response_json;
} else {
    //Llamamos a Binance para coger el dato, y lo persistimos en caché
    $headers = array(
        'Content-Type: application/json'
    );
    $url="https://api.binance.com/api/v3/ticker/price?symbol=".$symbol;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    //TODO: controlar posibles errores desde Binance

    $binance_response=json_decode($result,true);

    if ($cachetime>0){
        //Si se ha optado por no usar caché no genero tráfico a disco
        $timestamp=microtime(true)*1000;
        $cache_content=array(
            'timestamp' => $timestamp,
            'data' => $binance_response
        );
        file_put_contents('./'.$symbol.".json", json_encode($cache_content));
    }

    echo ($result);
}

die();

