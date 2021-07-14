<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class SubjectModel extends Model{

    private $id;
    private $nombre;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function postSubject($nombre){
        $stm = 'INSERT INTO `subject` (`name`) VALUES(?)';
        $rows = parent::nonQuery($stm,[$nombre]);
        if($rows > 0){
            $id = parent::lastInsertId();
            $stm = 'UPDATE `subject` SET state = 1 WHERE id = ?';
            $rows = parent::nonQuery($stm,[$id]);
            return $id;
        }else{
            return 'error';
        }
    }

    public function deleteSubject($id){
        $stm = 'UPDATE `subject` SET state = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        return $rows;
    }

    public function getSubjects(){
        $stm = 'SELECT * FROM `subject` WHERE `state` = 1';
        $data = parent::query($stm);
        return $data;
    }
    public function getSubjectById($id){
        $stm = 'SELECT * FROM `subject` WHERE id = ? AND `state` = 1';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function getSubjectByName($name){
        $stm = "SELECT * FROM `subject` WHERE `name` LIKE ? AND `state` = 1";
        $data = parent::query($stm,['%'.$name.'%']);
        return $data;
    }
    public function putSubject($id,$nombre){
        $stm = 'UPDATE `subject` SET `name` = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$nombre,$id]);
    }

    public function GiveSubjectInGroupToTeacher($teacher,$group,$subject){
        $stm = 'INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) VALUES(?,?,?)';
        $rows = parent::nonQuery($stm,[$teacher,$group,$subject]);
        return $rows;
    }

    public function removeTeacherFromSubjectInGroup($teacher,$group,$subject){
        $stm = 'UPDATE teacher_group_subject SET `state` = 0 WHERE id_teacher = ? AND id_group = ? AND id_subject = ?';
        $rows = parent::nonQuery($stm,[$teacher,$group,$subject]);
        return $rows;
    }
    //To check if a  subject in a  group is already talken
    public function IsSubjectInGroupTaken($group,$subject){
        $stm = 'SELECT * FROM teacher_group_subject t,`user` u 
        WHERE t.id_teacher = i.id AND t.id_group = ? AND t.id_subject = ? AND u.state_account = 1';
        $data = parent::query($stm,[$group,$subject]);
        if($data){
            return true;
        }else{
            return false;
        }
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
}