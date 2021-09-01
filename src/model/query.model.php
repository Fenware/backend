<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';
/*
Modelo para las consultas
*/
class QueryModel extends Model{

    private $id;
    private $id_student;
    private $id_teacher;
    private $id_group;
    private $id_subject;
    private $theme;
    private $creation_date;
    private $finish_date;
    private $resume;

    private $res;
    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    /*
    Crea una consulta
    */
    public function createQuery(){
        $stm = 'INSERT INTO `query`(id_student,id_teacher,id_group,id_subject,theme,creation_date) VALUES(?,?,?,?,?,?)';
        $this->creation_date = date('Y-m-d H:i:s', time());
        $row = parent::nonQuery($stm,[$this->id_student,$this->id_teacher,$this->id_group,$this->id_subject,$this->theme,$this->creation_date]);
        $this->id = parent::lastInsertId();
        //devuelvo la consulta con datos extra
        $stm = 'SELECT * FROM `query` WHERE id = ?';
        $consulta =  parent::query($stm,[$this->id]);
        if($consulta){

            //busco al estudiante que la creó
            $stm_autor = 'SELECT * FROM `user` WHERE id = ?';
            $autor = parent::query($stm_autor,[$consulta[0]['id_student']]);
            //agrego el campo student_name
            $consulta[0]['student_name'] = $autor[0]['name'].' '.$autor[0]['surname'];
            //busco al docente al que va dirigido  
            $stm_teacher = 'SELECT * FROM `user` WHERE id = ?';
            $teacher = parent::query($stm_teacher,[$consulta[0]['id_teacher']]);
            //agrego el campo teacher_name
            $consulta[0]['teacher_name'] = $teacher[0]['name'].' '.$teacher[0]['surname'];
    
            //busco el nombre de la materia
            $stm = 'SELECT * FROM `subject` WHERE id = ?';
            $subject = parent::query($stm,[$this->id_subject]);
            //agrego el campo subject_name
            $consulta[0]['subject_name'] = $subject[0]['name'];
    
            //busco el grupo
            $stm = 'SELECT * FROM `group` WHERE id = ?';
            $group = parent::query($stm,[$this->id_group]);
            //agrego el campo subject_name
            $consulta[0]['group_name'] = $group[0]['name'];
            return $consulta;
        }else{
            return 0;
        }
    }

    
    /*
    Envia un mensaje a una consulta
    */
    public function postMessagge($user,$consulta,$content){
        $stm = 'INSERT INTO `message`(id_user,id_query,content,`date`) VALUES(?,?,?,?)';
        $date = date('Y-m-d H:i:s', time());
        $rows = parent::nonQuery($stm,[$user,$consulta,$content,$date]);
        $id = parent::lastInsertId();
        $stm = 'SELECT m.id,m.id_query,m.id_user,u.name,u.surname,m.content,m.`date` FROM `message` m ,`user` u WHERE m.id = ? AND m.id_user = u.id';
        $mensaje =  parent::query($stm, [ $id ]);
        return $mensaje[0];
    }

    /*
    Devuelve los mensajes de una consulta
    */
    public function getMessageFromQuery($consulta){
        $stm = 'SELECT m.id,m.id_query,m.id_user,u.name,u.surname,m.content,m.`date` FROM `message` m ,`user` u WHERE m.id_query = ? AND m.id_user = u.id';
        $messages = parent::query($stm,[$consulta]);
        return $messages;
    }

    /*
    Cierra una consulta
    */
    public function closeQuery($consulta){
        $this->finish_date = date('Y-m-d H:i:s', time());
        $messages = $this->getMessageFromQuery($consulta);
        $resume = 'Resumen \n';
        foreach($messages as $message){
            $resume .= 'Autor : ' . $message['name'].' '.$message['surname']. ' msg : '. $message['content'].' \n';
        }
        $stm = 'UPDATE `query` 
        SET `state` = 0 ,finish_date = ? ,`resume` = ?
        WHERE id = ?';
        $rows = parent::nonQuery($stm,[$this->finish_date,$resume,$consulta]);
        //Genero el resumen de la consulta
        return $rows;
    }

    /*
    Cambio el estado de una consulta a contestada
    */
    public function setQueryToAnswered($consulta){
        $stm = 'UPDATE `query` SET `state` = 2 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$consulta]);
        return $rows;
    }


    public function getQueryById($consulta){
        $stm = 'SELECT * FROM `query` WHERE id = ?';
        $consulta =  parent::query($stm,[$consulta]);
        //busco al estudiante que la creó
        $stm_autor = 'SELECT * FROM `user` WHERE id = ?';
        $autor = parent::query($stm_autor,[$consulta[0]['id_student']]);
        //agrego el campo student_name
        $consulta[0]['student_name'] = $autor[0]['name'].' '.$autor[0]['surname'];
        //busco al docente al que va dirigido  
        $stm_teacher = 'SELECT * FROM `user` WHERE id = ?';
        $teacher = parent::query($stm_teacher,[$consulta[0]['id_teacher']]);
        //agrego el campo teacher_name
        $consulta[0]['teacher_name'] = $teacher[0]['name'].' '.$teacher[0]['surname'];

        //busco el nombre de la materia
        $stm = 'SELECT * FROM `subject` WHERE id = ?';
        $id_subject = $consulta[0]['id_subject'];
        $subject = parent::query($stm,[$id_subject]);
        //agrego el campo subject_name
        $consulta[0]['subject_name'] = $subject[0]['name'];

        //busco el grupo
        $stm = 'SELECT * FROM `group` WHERE id = ?';
        $id_group = $consulta[0]['id_group'];
        $group = parent::query($stm,[$id_group]);
        //agrego el campo subject_name
        $consulta[0]['group_name'] = $group[0]['name'];
        return $consulta[0];
    }


    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function setStudent($student){
        $this->id_student = $student;
    }

    public function getStudent(){
        return $this->id_student;
    }

    public function setTeacher($teacher){
        $this->id_teacher = $teacher;
    }

    public function getTeacher(){
        return $this->id_teacher;
    }

    public function setGroup($group){
        $this->id_group = $group;
    }

    public function getIdTeacher(){
        return $this->id_group;
    }

    public function setSubject($subject){
        $this->id_subject = $subject;
    }

    public function getSubject(){
        return $this->id_subject;
    }

    public function setTheme($theme){
        $this->theme = $theme;
    }

    public function getTheme(){
        return $this->theme;
    }

    public function setCreationDate($date){
        $this->creation_date = $date;
    }

    public function getCreationDate(){
        return $this->creation_date;
    }

    public function setFinishDate($date){
        $this->finish_date = $date;
    }

    public function getFinishDats(){
        return $this->finish_date;
    }

    public function setResume($resume){
        $this->resume = $resume;
    }

    public function getResume(){
        return $this->resume;
    }

}