Materia
<pre>
<?php
//chdir(dirname(__DIR__));
use Firebase\JWT\JWT;
include_once 'vendor/autoload.php';
include_once 'model/user.model.php';
include_once 'core/response.php';

$res = new Response();
$users = new UserModel();
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $postBody = file_get_contents('php://input');
    if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        header('HTTP/1.0 400 Bad Request');
        return $res->error('CAPO TE FALTA EL TOKEN');
        exit;
    }
    $jwt = $matches[1];
    if (! $jwt) {
        // No token was able to be extracted from the authorization header
        header('HTTP/1.0 400 Bad Request');
        return $res->error('No pudimos estraer tu token pa');
        exit;
    }
    $secret_key  = SECRET_KEY;
    //$token = JWT::decode($jwt, $secretKey, ['HS512']);
    try {
        $token =  JWT::decode(
            $jwt,
            $secret_key,
            ['HS512']
        );
    } catch (\Throwable $th) {
        print_r($res->OOPSIE());
        exit;
    }
    $now = new DateTimeImmutable();
    $serverName = URL;
    if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }else{
        $datosArray = $users->getAllUsers();
        echo "<h1>".$token->user_id."</h1>";
        echo "<h1>".$token->user_type."</h1>";
        print_r(json_encode($datosArray));
    }
    
    
}else{
    echo 'no';
}
?>
</pre>