<?php

require_once 'core/model.php';
require_once 'core/response.php';

class OrientationModel extends Model{
    private $id;
    private $name;
    private $year;
    private $subjects;
    private $state;

    public function __construct()
    {
        parent::__construct();
    }

    public function postOrientation($name,$year,$subjects,$count){
        $stm = 'INSERT INTO orientation(`name`,`year`) VALUES(?,?)';
        $rows = parent::nonQuery($stm,[$name,$year]);
        $error = false;
        if($rows > 0){
            $this->id = parent::lastInsertId();
            for($i = 0 ;$i < $count ;$i++){
                $stm = 'INSERT INTO subject_orientation(id_subject,id_orientation) VALUES(?,?)';
                $rows = parent::nonQuery($stm,[$subjects[$i],$this->id]);
                if($rows == 0){
                    $error = true;
                }
            }
        }
        if($error == true){
            $rows = 0;
        }
        return $rows;
    }
    public function getOrientations(){
        $stm = 'SELECT id,`name`,`year` FROM orientation WHERE `state` = 1';
        return parent::query($stm);
    }

    public function getOrientationById($id){
        $stm = 'SELECT id,`name`,`year` FROM orientation WHERE id = ? AND `state` = 1';
        return parent::query($stm,[$id]);
    }

    public function getOrientationByName($name){
        $stm = 'SELECT id,`name`,`year` FROM orientation WHERE `name` LIKE ? AND `state` = 1';
        return parent::query($stm,['%'.$name.'%']);
    }

    public function getOrientationSubjects($id){
        $stm = 'SELECT s.id,s.`name` FROM orientation o,`subject` s,subject_orientation so WHERE so.id_orientation = ? AND s.id = so.id_subject AND o.id = so.id_orientation AND s.state = 1 AND  o.state = 1 AND  so.state = 1';
        return parent::query($stm,[$id]);
    }
    public function putOrientation($id,$name,$year){
        $stm = 'UPDATE orientation SET `name` = ? , `year` = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$name,$year,$id]);
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
