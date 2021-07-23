<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class ConsultaModel extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function createConsulta($id_student,$id_teacher,$id_group,$id_subject,$theme){
        $stm = 'INSERT INTO `query`(id_student,id_teacher,id_group,id_subject,theme,creation_date) VALUES(?,?,?,?,?,?)';
        $date = date('m/d/Y h:i:s a', time());
        $rows = parent::nonQuery($stm,[$id_student,$id_teacher,$id_group,$id_subject,$theme,$date]);
        $id = parent::lastInsertId();
        $stm = 'INSERT INTO individual(id) VALUES(?)';
        $rows = parent::nonQuery($stm,[$id]);
        return $id;
    }

    public function getConsultasFromUser($id){
        $stm = 'SELECT * FROM `query` WHERE id_student = ? OR id_teacher = ? AND `state` != 0';
        $querys = parent::query($stm,[$id,$id]);
        return $querys;
    }

    public function postMessagge($user,$consulta,$content){
        $stm = 'INSERT INTO `message`(id_user,id_query,content,`date`) VALUES(?,?,?,?)';
        $date = date('m/d/Y h:i:s a', time());
        $rows = parent::nonQuery($stm,[$user,$consulta,$content,$date]);
        return $rows;
    }

    public function getMessageFromConsulta($consulta){
        $stm = 'SELECT * FROM `message` WHERE id_query = ?';
        $messages = parent::query($stm,[$consulta]);
        return $messages;
    }

    public function closeConsulta($consulta){
        $stm = 'UPDATE `query` SET `state` = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$consulta]);
        return $rows;
    }
}