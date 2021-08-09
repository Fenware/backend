<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/conexion.php';

/*
Clase Model :
Todaos los model heredan de esta clase
*/

class Model{

    private $conn;

    function __construct(){
        $db = new Conexion();
        $this->conn = $db->connect();
    }


    /*
    Funcion para hacer consultas en las que espero datos a la base de datos segura
    Ej:
    $id;Esta variable corresponde a un input del usuario
    $stm = 'SELECT * FROM `user` WHERE id = ?';
    query($stm, [$id] );
    */
    function query($stm, $datos = []){
        $data = $this->conn->prepare($stm);
        $data->execute($datos);
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
        return $this->toUTF8($data);
    }

    /*
    Funcion para hacer consultas en las que no datos a la base de datos segura
    Ej:
    $id;Esta variable corresponde a un input del usuario
    $stm = 'UPDATE `user` SET state = 1 WHERE id = ?';
    query($stm, [$id] );
    */

    public function nonQuery($stm, $datos = []){
        $data = $this->conn->prepare($stm);
        $data->execute($datos);
        $rows = $data->rowCount();
        return $rows;
    }

    /*
    Esta funcion esta para asegurarnos de que la informacion de la base de datos esta en utf8
    */

    private function toUTF8($array){
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    /*
    Esta funcion devuelve el ultimo id modificado
    Ej:
    $stm = 'INSERT INTO user(name) VALUES('Felipe')';
    query($stm);
    $id = lastInsertId();
    $id es el id de user en la base de datos
    */
    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }
}

