<?php

include_once 'model/auth.model.php';
include_once 'core/response.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
$res = new Response();
$auth = new AuthModel();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $postBody = file_get_contents('php://input');
    $datosArray = $auth->login($postBody);

    if(isset($datosArray['result']['error_id'])){
        $response_code = $datosArray['result']['error_id'];
        http_response_code($response_code);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);
}else{
    $datosArray = $res->error_405();
    echo json_encode($datosArray);
}
