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
        return $rows;
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
        return $rows;
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