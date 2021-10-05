<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';
require_once '/var/www/html/model/query.model.php';
/*
Modelo para las consultas
*/
class ConsultaModel extends QueryModel{

    private $res;
    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    /*
    Crea una consulta
    */

    
    public function createConsulta(){
        $stm = 'INSERT INTO individual(id) VALUES(?)';
        parent::nonQuery($stm,[parent::getId()]);
    }

    public function getConsultaById($id){
        $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`individual` i 
                WHERE q.`state` != 0 AND q.id = ? AND q.id = i.id';
        $c = parent::query($stm,[$id]);
        $consulta = $c[0];
        $consulta = parent::getExtraData($consulta);
        return $consulta ;
    }

    /*
    Devuelve todas las consultas de un usuario que no esten cerradas
    */
    public function getConsultasFromUser($id,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`individual` i 
                WHERE q.`state` != 0 AND q.id_teacher = ? AND q.id = i.id';
                break;
            case 'student':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`individual` i 
                WHERE q.`state` != 0 AND q.id_student = ? AND q.id = i.id';
                break;
        }
        $consultas = parent::query($stm,[$id]);
        foreach($consultas as &$consulta){
            $consulta = parent::getExtraData($consulta);
        }
        return $consultas;
    }   

    /*
    Devuelve una consulta en base a su id
    */
    

    /*
    Devuelve todas las consultas de un usuario sin importar su estado
    */
    public function getAllConsultasFromUser($id,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`individual` i 
                WHERE q.id_teacher = ? AND q.id = i.id';
                break;
            case 'student':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`individual` i 
                WHERE q.id_student = ? AND q.id = i.id';
                break;
        }
        $consultas = parent::query($stm,[$id]);
        foreach($consultas as &$consulta){
            $consulta = parent::getExtraData($consulta);
        }
        return $consultas;
    } 
    

    private function addData($consulta){
        
    }

}