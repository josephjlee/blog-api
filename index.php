<?php
require_once 'models/database.php';
require_once 'api.php';
require_once 'core/router.php';
require_once 'helpers/log_helper.php';
//Api start point
$api= new Api($db_config);
$router= new Router($routes);
$method= $router->get_method();
if($method){
    echo $api->{$router->get_method()}();
}
else{
    header('Content-Type: application/json');
    $response['result']="fail";
    $response['errors']="Requested url is not found";
    echo json_encode($response);
}
