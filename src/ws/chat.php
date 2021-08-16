<?php
//namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Firebase\JWT\JWT;
use React\Dns\Query\FallbackExecutor;

require_once '/var/www/html/core/response.php';

require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/config/config.php';
require_once '/var/www/html/core/response.php';
require_once '/var/www/html/model/user.model.php';


class Chat implements WampServerInterface {
    /**
     * A lookup of all the topics clients have subscribed to
     */

    //private $token_manager;
    private $user_model;
    private $res;
    protected $clients;
    protected $subscribedTopics = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    //$this->token_manager = new TokenManager();
        $this->res = new Response();
        $this->user_model = new UserModel();
    }

    public function onSubscribe(ConnectionInterface $conn, $chat) {
        //obtengo el contenido del url ?token=asdasdasdasdasasd
        $querystring = $conn->httpRequest->getUri()->getQuery();
        //Separo el contenido  me queda token en  la posicion 0 y el token mismo en la posicion 1
        $temp_token = explode('=',$querystring);
        //valido el token
        $token = $this->hasValidToken($temp_token[1]);
        if($token != false){
            //El supuesto error en token es solo VScode priando colores
            //Chequeo que el usuario pertenesca al grupo al que busca suscribirse
            $access = $this->user_model->UserHasAccesToChat($token->user_id,$chat);
            if($access){
                //Lo suscribo al grupo
                $this->subscribedTopics[$chat->getId()] = $chat;
                echo "Connection {$conn->resourceId} has suscribed to {$chat}\n";
            }else{
                //Lo desconecto por seguridad
                $this->clients->detach($conn);
                $conn->close();
            }
        }else{
            $this->clients->detach($conn);
            $conn->close();
        }
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {

    }
    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        //Comento callError por que me lo marca como error . 0 idea de porque 
        //$conn->callError($id, $topic, 'You are not allowed to make calls')->close();
        $conn->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    //Se ejecuta cuando se crea una sala
    public function onMessageEntry($entry) {
        $entryData = json_decode($entry, true);
        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$entryData['category']];
        
        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function checkToken($jwt){
        if ($jwt) {
            $secret_key  = SECRET_KEY;
            //Des encripto el token
            try {
                $token =  JWT::decode(
                    $jwt,
                    $secret_key,
                    ['HS512']
                );
            } catch (\Throwable $th) {
                echo json_encode($this->res->error('Tu token no se pudo des-encriptar'));
                $token = false;
            }
            return $token;
        }else{
            echo json_encode($this->res->error('Te falta el token'));
        }
    }
    
    private function validToken($token){
        $now = new DateTimeImmutable();
        $serverName = URL;
        if($token != false){
            if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
        
    }

    public function hasValidToken($jwt){
        try {
            $token = $this->checkToken($jwt);
            $foo  = $this->validToken($token);
            if($foo == true){
                return $token;
            }else{
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }


}