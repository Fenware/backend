<?php

include_once 'core/conexion.php';
class Model{

    private $conn;

    function __construct(){
        $db = new Conexion();
        $this->conn = $db->connect();
    }

    // function load($model){
    //     require_once 'models/' . $model . 'Model.php'; 
    // }


    function query($stm, $datos = []){
        $data = $this->conn->prepare($stm);
        $data->execute($datos);
        $data = $data->fetchAll();
        return $this->toUTF8($data);
    }

    public function nonQuery($stm, $datos = []){
        $data = $this->conn->prepare($stm);
        $data->execute($datos);
        $rows = $this->conn->affected_rows;
        if($rows >= 1){
            return $rows;
        }else{
            return 0;
        }
    }

    // function query($stm, $datos = []){
    //     $data = $this->conn->prepare($stm);
    //     $data->execute($datos);
    //     return $data->fetchAll();
    // }

    

    private function toUTF8($array){
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

}

