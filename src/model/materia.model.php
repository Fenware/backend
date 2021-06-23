<?php

require_once 'core/model.php';
require_once 'core/response.php';

class MateriaModel extends Model{

    private $id;
    private $nombre;

    
    public function postMateria($nombre){
        $stm = 'INSERT INTO materia (nombre) VALUES(?)';
        $rows = parent::nonQuery($stm,[$nombre]);
        return $rows;
    }

    public function deleteMateria($id){
        $stm = 'UPDATE materia SET estado = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        return $rows;
    }

    public function getMaterias(){
        $stm = 'SELECT * FROM materia WHERE estado = 1';
        $data = parent::query($stm);
        return $data;
    }
    public function getMateriaId($id){
        $stm = 'SELECT * FROM materia WHERE id = ? AND estado = 1';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function getMateriasNombre($nombre){
        $stm = "SELECT * FROM materia WHERE nombre LIKE ? AND estado = 1";
        $data = parent::query($stm,['%'.$nombre.'%']);
        return $data;
    }
    public function putMateria($id,$nombre){
        $stm = 'UPDATE materia SET nombre = ? WHERE id = ?';
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