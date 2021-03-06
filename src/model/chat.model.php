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
    Devuelve la cantidad de chats abiertos en un grupo y materia
    */
    public function amountOfActiveChatsFromSubjecGroup($subject,$group){
        $stm = 'SELECT * FROM `query` q ,room r WHERE q.id_subject = ? AND q.id_group = ? AND q.`state` != 0 AND q.id = r.id';
        $amount = parent::query($stm , [$subject,$group] );
        return count($amount);
    }

    /*
    le da a un query la clasificacion de room(chat)
    */
    public function createChat(){
        $stm = 'INSERT INTO `room`(id) VALUES(?)';
        return parent::nonQuery($stm,[parent::getId()]);
    }

    /*
    Devuelve todas las consultas de un usuario que no esten cerradas
    esta  funcion esta duplicada, hay que borrar una de las 2 y cambiar donde se use la borrada
    */
    public function getChatFromUser($chat){
        $stm = 'SELECT * FROM `query` WHERE id = ?';
        $consultas = parent::query($stm,[$chat]);
        foreach($consultas as &$consulta){
            //busco al estudiante que la  creo
            $consulta =  parent::getExtraData($consulta);
            $consulta = $this->addData($consulta);
        }
        return $consultas[0];
    }   

    /*
    Devuelve un chat en base a su id
    */
    public function getChatById($id)
    {
        $stm = 'SELECT q.id ,q.id_student,q.id_teacher,q.id_group,q.id_subject,q.theme ,q.creation_date, q.finish_date,q.`resume`,q.`state`
                FROM `query` q ,`room` r 
                WHERE q.id = ? AND q.id = r.id';
        $c = parent::query($stm,[$id]);
        if($c){
            $chat = $c[0];
            $chat = parent::getExtraData($chat);
            $chat = $this->addData($chat);
        }else{
            $chat = $c;
        }
        return $chat;
    }

    /*
    Devuelve todas los chats de un usuario
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
    
    /*
    Devuelve todas los chats de un usuario sin importar su estado
    */
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
    
    /*
    Agrega un participante a un chat
    */
    public function addParticipant($chat,$user){
        $stm = 'INSERT INTO room_participants(id_room,id_user) VALUES(?,?)';
        $rows = parent::nonQuery($stm , [$chat,$user] );
        return $rows;
    }

    /*
    Devuelve los participantes a un chat
    */
    public function getParticipants($chat){
        $stm = 'SELECT r.id_room ,u.name,u.middle_name,u.surname,u.second_surname
        FROM room_participants r,`user` u
        WHERE r.id_room = ? AND r.id_user = u.id';
        $participants = parent::query($stm , [$chat] );
        return $participants;
    }

    /*
    Se usa para que al pedir un chat,le agregemos los participantes
    */
    private function addData($consulta){
        $consulta['participants'] = $this->getParticipants($consulta['id']);
        return $consulta;
    }

    /*
    Pregunta si el  usuario es el autor o el docente del chat, en dicho caso tendra acceso total sobre el chat (cerrar el chat)
    */
    public function userHasHighAccessToChat($user,$chat){
        $stm = 'SELECT q.id FROM `query` q,`room` r  WHERE (q.id_student = ? OR q.id_teacher = ?) AND q.id = ? AND q.id = r.id';
        $query = parent::query($stm,[$user,$user,$chat]);
        if(empty($query)){
            return false;
        }else{
            return true;
        }
    }

    
}