<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

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

    public function postOrientation($name,$year,$subjects){
        $stm = 'SELECT * FROM orientation WHERE `name` = ? AND `year` = ? AND `state` = 1';
        $orientation = parent::query($stm,[$name,$year]);
        if($orientation){
            return 'La orientacion ya existe';
        }else{
            $stm = 'SELECT * FROM orientation WHERE `name` = ? AND `year` = ? AND `state` = 0';
            $orientation = parent::query($stm,[$name,$year]);
            if($orientation){
                $id = $orientation[0]['id'];
                $stm = 'UPDATE orientation SET `state` = 1 WHERE id = ?';
                parent::nonQuery($stm,[$id]);
                $rows = $this->postSubjectsInOrientation($id,$subjects);
                return (int)$id;
            }else{
                $stm = 'INSERT INTO orientation(`name`,`year`) VALUES(?,?)';
                $rows = parent::nonQuery($stm,[$name,$year]);
                if($rows > 0){
                    $id = parent::lastInsertId();
                    $rows = $this->postSubjectsInOrientation($id,$subjects);
                    return (int)$id;
                }else{
                    return 'Algo salio mal al crear la orientacion';
                }
            }
        }
    }

    public function postSubjectsInOrientation($id,$subjects){
        $count = count($subjects);
        for($i = 0 ;$i < $count ;$i++){
            $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ? AND `state` = 0';
            $subject_orientation = parent::query($stm,[$subjects[$i],$id]);
            if($subject_orientation){
                $stm = 'UPDATE subject_orientation SET `state` = 1 WHERE id_subject = ? AND id_orientation = ?';
                $rows = parent::nonQuery($stm,[$subjects[$i],$id]);
            }else{
                $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ? AND `state` = 1';
                $subject_orientation = parent::query($stm,[$subjects[$i],$id]);
                if($subject_orientation){
                    //La orientacion ya existia , paso 
                }else{
                    $stm = 'INSERT INTO subject_orientation(id_subject,id_orientation) VALUES(?,?)';
                    $rows = parent::nonQuery($stm,[$subjects[$i],$id]);
                }
            }
        }
        if($rows > 0){
            return 1;
        }else{
            return 0;
        }
        
    }

    public function deleteSubjectsInOrientation($id,$subjects){
        $error = false;
        $count = count($subjects);
        for($i = 0 ;$i < $count ;$i++){
            $subject_id  = $subjects[$i];
            $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ? AND `state` = 1';
            $subject_orientation = parent::query($stm,[$subject_id,$id]);
            if($subject_orientation){
                $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_subject = ? AND id_orientation = ?';
                $rows = parent::nonQuery($stm,[$subject_id,$id]);
                if($rows == 0){
                    $error = true;
                }
            }
        }
        if($error){
            return 0;
        }else{
            return 1;
        }
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
        $stm = 'SELECT so.id_orientation,so.id_subject FROM orientation o,`subject` s,subject_orientation so WHERE so.id_orientation = ? AND s.id = so.id_subject AND o.id = so.id_orientation AND s.state = 1 AND  o.state = 1 AND  so.state = 1';
        return parent::query($stm,[$id]);
    }
    public function putOrientation($id,$name,$year){
        $stm = 'UPDATE orientation SET `name` = ? , `year` = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$name,$year,$id]);
        return $rows;
    }

    public function putOrientationSubjects($id,$s_add,$s_remove){
        //First we add  the new subjects
        $error = false;
        $count = count($s_add);
        if($count > 0){
            $stm = 'INSERT INTO subject_orientation(id_subject,id_orientation) VALUES(?,?)';
            for($i = 0;$i < $count;$i++){
                $rows = parent::nonQuery($stm,[$s_add[$i],$id]);
                if($rows == 0){
                    $error = true;
                }
            }
        }
        
        $count = count($s_remove);
        if($count > 0){
            $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_subject = ? AND id_orientation = ?';
            for($i = 0;$i < $count;$i++){
                $rows = parent::nonQuery($stm,[$s_remove[$i],$id]);
                if($rows == 0){
                    $error = true;
                }
            }
        }
        if($error){
            return 0;
        }else{
            return 1;
        }
        
    }

    public function deleteOrientation($id){
        $error = false;
        $stm = 'UPDATE orientation SET `state` = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        if($rows > 0 ){
            $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_orientation  = ?';
            $rows = parent::nonQuery($stm,[$id]);
            if($rows == 0){
                $error = true;
            }
        }else{
            $error = true;
        }
        if($error){
            return 0;
        }else{
            return 1;
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
