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
        $date = date('Y-m-d H:i:s', time());
        $row = parent::nonQuery($stm,[$id_student,$id_teacher,$id_group,$id_subject,$theme,$date]);
        $id = parent::lastInsertId();
        $stm = 'INSERT INTO individual(id) VALUES(?)';
        parent::nonQuery($stm,[$id]);
        //devuelvo la consulta con datos extra
        $stm = 'SELECT * FROM `query` WHERE id = ?';
        $consulta =  parent::query($stm,[$id]);
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
        $subject = parent::query($stm,[$id_subject]);
        //agrego el campo subject_name
        $consulta[0]['subject_name'] = $subject[0]['name'];

        //busco el grupo
        $stm = 'SELECT * FROM `group` WHERE id = ?';
        $group = parent::query($stm,[$id_group]);
        //agrego el campo subject_name
        $consulta[0]['group_name'] = $group[0]['name'];
        return $consulta;
    }

  
    public function getConsultasFromUser($id){
        $stm = 'SELECT * FROM `query` WHERE id_student = ? OR id_teacher = ? AND `state` != 0';
        $consultas = parent::query($stm,[$id,$id]);
        foreach($consultas as &$consulta){
            //busco al estudiante que la  creo
            $stm_autor = 'SELECT * FROM `user` WHERE id = ?';
            $autor = parent::query($stm_autor,[$consulta['id_student']]);
            //agrego el campo student_name
            $consulta['student_name'] = $autor[0]['name'].' '.$autor[0]['surname'];
             //busco al docente al que va dirigido  
            $stm_teacher = 'SELECT * FROM `user` WHERE id = ?';
            $teacher = parent::query($stm_teacher,[$consulta['id_teacher']]);
            //agrego el campo teacher_name
            $consulta['teacher_name'] = $teacher[0]['name'].' '.$teacher[0]['surname'];
            //busco el nombre de la materia
            $stm = 'SELECT * FROM `subject` WHERE id = ?';
            $id_subject = $consulta['id_subject'];
            $subject = parent::query($stm,[$id_subject]);
            //agrego el campo subject_name
            $consulta['subject_name'] = $subject[0]['name'];

            //busco el grupo
            $stm = 'SELECT * FROM `group` WHERE id = ?';
            $id_group = $consulta['id_group'];
            $group = parent::query($stm,[$id_group]);
            //agrego el campo subject_name
            $consulta['group_name'] = $group[0]['name'];
        }
        return $consultas;
    }   

    

    public function postMessagge($user,$consulta,$content){
        $stm = 'INSERT INTO `message`(id_user,id_query,content,`date`) VALUES(?,?,?,?)';
        $date = date('Y-m-d H:i:s', time());
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