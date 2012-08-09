<?php

require 'Slim/Slim.php';
require 'config/config.php';

//With custom settings
$app = new Slim(array(
    'log.enable' => true,
    'log.path' => './logs',
    'log.level' => 4,
    'app.config'=> $CONFIG
));

/**
 * GET some mock messages
 */
$app->get('/rest/messages', function () {
    $messages = array();
    array_push($messages, array("name"=>"Franz", "email"=>"franz.mathauser@nttdata.com", "message"=>"Hello World!"));
    array_push($messages, array("name"=>"Patrick", "email"=>"patrick.holzmann@nttdata.com", "message"=>"Hello Monday Morning!"));
    echo '{"messages": ' . json_encode($messages) . '}';
});

/**
 * POST 
 */
$app->post('/rest/messages', function () {
    $request = Slim::getInstance()->request();
    $message = json_decode($request->getBody());
    
    $message->id = 1;
    
    echo json_encode($message);
});
/**
 * POST contactsform
 */
$app->post('/rest/contacts', function () {
    $request = Slim::getInstance()->request();
    echo "{success: true}";
});


/**
 * get places from google api
 */
$app->get('/rest/places', function () {
    $request = Slim::getInstance()->request();
    $location = $request->params("location");
    
    $config = Slim::getInstance()->config('app.config');
    $google_api_key = $config['GOOGLE_API_KEY'];
    $nttdata_proxy_conf = $config['NTTDATA_PROXY_CONF'];
    $curl_opt_cainfo = $config['CURLOPT_CAINFO'];
    
    $url = "https://maps.googleapis.com/maps/api/place/search/json?sensor=true&key=".$google_api_key;
    $url .= "&location=$location";
    //$url .= "&rankby=distance";
    $url .= "&radius=500";
    $url .= "&types=bank|finance";
    //$url .= "&keyword=";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if( !empty($nttdata_proxy_conf)){
        curl_setopt($ch, CURLOPT_PROXY, $nttdata_proxy_conf);
    }
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
    if( !empty($curl_opt_cainfo)){
        curl_setopt ($ch, CURLOPT_CAINFO, $curl_opt_cainfo);
    }

    // Get the response and close the channel.
    $response = curl_exec($ch);
    
    curl_close($ch);
    
    echo($response);

});


$app->options('/rest/places', function () {});
  


$app->run();
?>
