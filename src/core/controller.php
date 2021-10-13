<?php

/*
Clase Controller :
Todos los controllers heredan de esta clase
*/
class Controller{
    
    protected $data;

    protected $token;

    function __construct($token)
    {
        $this->data = $this->getJson();
        $this->token = $token;
    }


    /*
    Convierte los datos enviados por el usuario de json a array
    */
    private function getJson(){
        $postBody = file_get_contents('php://input');
        $data = json_decode($postBody,true);
        return $data;
    }



    /*
    Chequea que la informacion especificada de un array exista ,no este vacia y que sea del tipo correcto
    El primer parametro es el array, el segundo se usa para indicar el nombre del campo y su tipo
    Ej:
    isTheDataCorrect($array_con_datos,['ci'=>'is_string','edad'=>'is_int'])
    */
    public function isTheDataCorrect($data,$vars){
        $correct = true;
        foreach($vars as $key => $value){
            if(    !isset($data[$key])
                || !$value($data[$key])
                || empty($data[$key])){
                $correct = false;
            }
        }
        return $correct;
    }

    /*
    Chequea si el contenido de un array es de cierto tipo
    Ej:
    isArrayDataCorrect(['Uruguay','Argentina','Chile'],is_int)
    Devuelve falso
    isArrayDataCorrect(['Uruguay','Argentina','Chile'],is_string)
    Devuelve verdadero
    */
    public function isArrayDataCorrect($array,$type){
        $correct = true;
        foreach($array as $value){
            if(!$type($value)){
                $correct = false;
            }
        }
        return $correct;
    }
   

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
}