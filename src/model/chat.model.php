<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';
require_once '/var/www/html/model/query.model.php';
/*
Modelo para las consultas
*/
class ChatModel extends QueryModel{

    private $res;
    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    /*
    Crea una consulta
    */

    public function amountOfActiveChatsFromSubjecGroup($subject,$group){
        $stm = 'SELECT * FROM `query` q ,room r WHERE q.id_subject = ? AND q.id_group = ? AND q.`state` != 0 AND q.id = r.id';
        $amount = parent::query($stm , [$subject,$group] );
        return count($amount);
    }

    public function createChat(){
        $stm = 'INSERT INTO `room`(id) VALUES(?)';
        return parent::nonQuery($stm,[parent::getId()]);
    }

    /*
    Devuelve todas las consultas de un usuario que no esten cerradas
    */
    public function getChatFromUser($id,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT * FROM `query` WHERE `state` != 0 AND id_teacher = ?';
                break;
            case 'student':
                $stm = 'SELECT * FROM `query` WHERE `state` != 0 AND id_student = ?';
                break;
        }
        $consultas = parent::query($stm,[$id]);
        foreach($consultas as &$consulta){
            //busco al estudiante que la  creo
            $consulta =  parent::getExtraData($consulta);
            $consulta = $this->addData($consulta);
        }
        return $consultas;
    }   

    /*
    Devuelve una consulta en base a su id
    */
    public function getChatById($id)
    {
        $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`room` r 
                WHERE q.`state` != 0 AND q.id = ? AND q.id = r.id';
        $c = parent::query($stm,[$id]);
        $chat = $c[0];
        $chat = parent::getExtraData($chat);
        $chat = $this->addData($chat);
        return $chat;
    }

    /*
    Devuelve todas las consultas de un usuario sin importar su estado
    */
    public function getChatsFromUser($id,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q,`room` r
                WHERE q.`state` != 0 AND q.id = r.id AND q.id_teacher = ? ORDER BY q.creation_date DESC';
                break;
            case 'student':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q,`room` r,student_group sg
                WHERE q.`state` != 0 AND q.id = r.id AND sg.id_group = q.id_group AND sg.id_student = ? ORDER BY q.creation_date DESC';
                break;
        }
        $consultas = parent::query($stm,[$id]);
        foreach($consultas as &$consulta){
            //busco al estudiante que la  creo
            $consulta = parent::getExtraData($consulta);
            $consulta = $this->addData($consulta);
        }
        return $consultas;
    } 
    
    public function getAllChatsFromUser($id,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q,`room` r
                WHERE q.`state` != 0 AND q.id = r.id AND q.id_teacher = ? ORDER BY q.creation_date DESC';
                break;
            case 'student':
                $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q,`room` r,student_group sg
                WHERE q.`state` != 0 AND q.id = r.id AND sg.id_group = q.id_group AND sg.id_student = ? ORDER BY q.creation_date DESC';
                break;
        }
        $consultas = parent::query($stm,[$id]);
        foreach($consultas as &$consulta){
            //busco al estudiante que la  creo
            $consulta = parent::getExtraData($consulta);
            $consulta = $this->addData($consulta);
        }
        return $consultas;
    } 
    
    public function addParticipant($chat,$user){
        $stm = 'INSERT INTO room_participants(id_room,id_user) VALUES(?,?)';
        $rows = parent::nonQuery($stm , [$chat,$user] );
        return $rows;
    }

    public function getParticipants($chat){
        $stm = 'SELECT r.id ,u.name,u.middle_name,u.surname,u.second_surname
        room_participants r,`user` u
        WHERE r.id_room = ? AND r.id_user = u.id';
        $participants = parent::query($stm , [$chat] );
        return $participants;
    }

    private function addData($consulta){
        $consulta['participants'] = $this->getParticipants($consulta['id']);
        return $consulta;
    }

    //Si es el autor o el docente del chatw
    public function userHasHighAccessToChat($user,$chat){
        $stm = 'SELECT q.id FROM `query` q,`room` r  WHERE q.id_student = ? OR q.id_teacher = ? AND q.id = ? AND q.id = r.id';
        $query = parent::query($stm,[$user,$user,$chat]);
        if(empty($query)){
            return false;
        }else{
            return true;
        }
    }

    
}