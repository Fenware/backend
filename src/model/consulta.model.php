<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/query.model.php';
/*
Modelo para las consultas
*/
class ConsultaModel extends QueryModel{

    public function __construct()
    {
        parent::__construct();
    }

    /*
    Crea una consulta
    */

    
    public function createConsulta(){
        $stm = 'INSERT INTO individual(id) VALUES(?)';
        parent::nonQuery($stm,[parent::getId()]);
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
    

}