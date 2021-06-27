<?php

require_once 'core/model.php';
require_once 'core/response.php';

class MateriaModel extends Model{

    private $id;
    private $nombre;

    
    public function postMateria($nombre){
        $stm = 'INSERT INTO `subject` (`name`) VALUES(?)';
        $rows = parent::nonQuery($stm,[$nombre]);
        return $rows;
    }

    public function deleteMateria($id){
        $stm = 'UPDATE `subject` SET state = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        return $rows;
    }

    public function getMaterias(){
        $stm = 'SELECT * FROM `subject` WHERE `state` = 1';
        $data = parent::query($stm);
        return $data;
    }
    public function getMateriaId($id){
        $stm = 'SELECT * FROM `subject` WHERE id = ? AND `state` = 1';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function getMateriasNombre($nombre){
        $stm = "SELECT * FROM `subject` WHERE `name` LIKE ? AND `state` = 1";
        $data = parent::query($stm,['%'.$nombre.'%']);
        return $data;
    }
    public function putMateria($id,$nombre){
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