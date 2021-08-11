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


class Pusher implements WampServerInterface {
    /**
     * A lookup of all the topics clients have subscribed to
     */

    //private $token_manager;
    private $res;
    protected $clients;
    protected $subscribedTopics = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    //$this->token_manager = new TokenManager();
        $this->res = new Response();
    }

    public function onSubscribe(ConnectionInterface $conn, $topic) {
        $querystring = $conn->httpRequest->getUri()->getQuery();
        print_r($querystring);
        $token = explode('=',$querystring);
        $token = $this->hasValidToken($token[1]);
        if($token != false){
            
            $this->subscribedTopics[$topic->getId()] = $topic;
            echo "Connection {$conn->resourceId} has suscribed to {$topic}\n";
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
        //$conn->callError($id, $topic, 'You are not allowed to make calls')->close();

        echo '???';
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    public function onBlogEntry($entry) {
        echo 'sala nueva';
        $entryData = json_decode($entry, true);
        print_r($entry);
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