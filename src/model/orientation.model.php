<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';

/*
Modelo de las orietnaciones
*/
class OrientationModel extends Model{
    private $id;
    private $name;
    private $year;
    private $subjects;
    private $state;

    private $res;
    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    /*
    Crea una orientacion
    */
    public function postOrientation($name,$year){
        $stm = 'INSERT INTO orientation(`name`,`year`) VALUES(?,?)';
        parent::nonQuery($stm,[$name,$year]);
        return parent::lastInsertId();
    }

    /*
    Agrega materias a una orientacion
    */
    public function postSubjectInOrientation($orientation,$subject){
        $stm = 'INSERT INTO subject_orientation(id_subject,id_orientation) VALUES(?,?)';
        return parent::nonQuery($stm,[$subject,$orientation]);
        
    }

    public function getSubjectInOrientation($orientation,$subject){
        $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ?';
        $data = parent::query($stm,[$subject,$orientation]);
        return $data[0];
    }
    /*
    Elimina materias de una orientacion
    */
    public function deleteSubjectsInOrientation($id,$subjects){
        $error = false;
        foreach($subjects as $subject){
            //'Borro' la materia dentro de la orientacion
            $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_subject = ? AND id_orientation = ?';
            $rows = parent::nonQuery($stm,[$subject,$id]);
            if($rows < 1){
                $error = true;
            }
        }
        if($error == true){
            return 0;
        }else{
            return 1;
        }
    }

    /*
    Devuelve todas las orientaciones
    */
    public function getOrientations(){
        $stm = 'SELECT id,`name`,`year` FROM orientation WHERE `state` = 1';
        return parent::query($stm);
    }
    /*
    Devuelve una orientaciones en base a un id
    */
    public function getOrientationById($id){
        $stm = 'SELECT id,`name`,`year` FROM orientation WHERE id = ? AND `state` = 1';
        $orientation = parent::query($stm,[$id]);
        $orientation[0]['subjects'] = $this->getOrientationSubjects($id);
        return $orientation[0];
    }

    public function getOrienation($name,$year){
        $stm = 'SELECT * FROM orientation WHERE `name` = ? AND `year` = ?';
        $orientation = parent::query($stm,[$name,$year]);
        return $orientation[0];
    }
    /*
    Devuelve una orientaciones en base a un nombre
    */
    public function getOrientationByName($name){
        $stm = 'SELECT id,`name`,`year` FROM orientation WHERE `name` LIKE ? AND `state` = 1';
        return parent::query($stm,['%'.$name.'%']);
    }
    /*
    Devuelve las materias de una orientacion
    */
    public function getOrientationSubjects($id){
        $stm = 'SELECT s.id, s.name, s.state FROM orientation o,subject s,subject_orientation so WHERE so.id_orientation = ? AND s.id = so.id_subject AND o.id = so.id_orientation AND s.state = 1 AND  o.state = 1 AND  so.state = 1';
        $materias = parent::query($stm,[$id]);
        return $materias;
    }

    /*
    Modifica una orientacion
    */
    public function putOrientation($id,$name,$year){
        $stm = 'UPDATE orientation SET `name` = ? , `year` = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$name,$year,$id]);
        return $rows;
    }


    /*
    'Borra' una orientacion
    */
    public function deleteOrientation($id){
        $stm = 'UPDATE orientation SET `state` = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        if($rows > 0 ){
            $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_orientation  = ?';
            //Borro las materias dentro de la orientacion
            parent::nonQuery($stm,[$id]);
            return 1;
        }else{
            return 0;
        }
    }

    public function changeOrientationState($id,$state){
        $stm = 'UPDATE orientation SET `state` = ? WHERE id = ?';
        return parent::nonQuery($stm,[$state,$id]);
    }

    public function reAddSubject($orientation,$subject){
        $stm = 'UPDATE subject_orientation SET `state` = 1 WHERE id_orientation  = ? AND id_subject = ?';
        return parent::nonQuery($stm,[$orientation,$subject]);
    }
    public function getOrientationGroups($orientation){
        $stm = 'SELECT * FROM `group` WHERE id_orientation = ?';
        $groups = parent::query($stm, [$orientation]);
        return $groups;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }


    public function getYear()
    {
        return $this->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }


    public function getSubjects()
    {
        return $this->subjects;
    }


    public function setSubjects($subjects)
    {
        $this->subjects = $subjects;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
}
