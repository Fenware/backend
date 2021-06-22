Auth
<?php

include_once 'model/auth.model.php';
include_once 'core/response.php';

$res = new Response();
$auth = new AuthModel();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $postBody = file_get_contents('php://input');
    $datosArray = $auth->login($postBody);
    print_r(json_encode($datosArray));
}else{
    echo 'no';
}
