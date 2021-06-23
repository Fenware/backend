<?php

require_once 'core/model.php';
require_once 'core/response.php';

class MateriaModel extends Model{

    private $id;
    private $nombre;

    
    public function postMateria($nombre){
        $stm = 'INSERT INTO materia(nombre) VALUES(?)';
        return parent::nonQuery($stm,[$nombre]);
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