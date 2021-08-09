<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
Modelo de las orietnaciones
*/
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

    /*
    Crea una orientacion
    */
    public function postOrientation($name,$year,$subjects){
        //Chequeo si la orientacion ya existe
        $stm = 'SELECT * FROM orientation WHERE `name` = ? AND `year` = ?';
        $orientation = parent::query($stm,[$name,$year]);
        
        //Chequeo si la orientacion ya existe
        if($orientation){
            $state = $orientation[0]['state'];
            if($state == 1){
                return 'La orientacion ya existe';
            }else{
                $id = $orientation[0]['id'];
                $stm = 'UPDATE orientation SET `state` = 1 WHERE id = ?';
                parent::nonQuery($stm,[$id]);
                //Le agrego sus materias
                $rows = $this->postSubjectsInOrientation($id,$subjects);
                return $this->getOrientationById($id);
            }
        }else{
            $stm = 'INSERT INTO orientation(`name`,`year`) VALUES(?,?)';
            $rows = parent::nonQuery($stm,[$name,$year]);
            if($rows > 0){
                $id = parent::lastInsertId();
                //Le agrego sus materias
                $rows = $this->postSubjectsInOrientation($id,$subjects);
                return $this->getOrientationById($id);
            }else{
                return 'Algo salio mal al crear la orientacion';
            }
        }
    }

    /*
    Agrega materias a una orientacion
    */
    public function postSubjectsInOrientation($id,$subjects){
        $error = false;
        foreach($subjects as $subject){
            //Chequeo q la materia exista y no este borrada
            $stm = 'SELECT * FROM `subject` WHERE id = ? AND `state` = 1';
            $materia_existe = parent::query($stm, [$subject] );
            //Chequeo q la materia exista y no este borrada
            if($materia_existe){
                //Chequeo si la materia ya esta en la orientacion pero 'borrada'
                $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ? AND `state` = 0';
                $subject_orientation = parent::query($stm,[$subject,$id]);
                //Chequeo si la materia ya esta en la orientacion pero 'borrada'
                if($subject_orientation){
                    //Cambio su estado de 0 a 1 activandola
                    $stm = 'UPDATE subject_orientation SET `state` = 1 WHERE id_subject = ? AND id_orientation = ?';
                    $rows = parent::nonQuery($stm,[$subject,$id]);
                    if($rows == 0){
                        $error = true;
                    }
                }else{
                    //Chequeo si la materia ya esta en la orientacion de forma activa
                    $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ? AND `state` = 1';
                    $subject_orientation = parent::query($stm,[$subject,$id]);
                    //Chequeo si la materia ya esta en la orientacion de forma activa
                    if($subject_orientation){
                        //La orientacion ya existia , paso 
                    }else{
                        //Relaciono la materia con la orientacion
                        $stm = 'INSERT INTO subject_orientation(id_subject,id_orientation) VALUES(?,?)';
                        $rows = parent::nonQuery($stm,[$subject,$id]);
                        if($rows == 0){
                            $error = true;
                        }
                    }
                }
            }
        }
        //Si hubo un error devuelvo 0  
        if($error){
            return 0;
        }else{
            return 1;
        }
        
    }
    /*
    Elimina materias de una orientacion
    */
    public function deleteSubjectsInOrientation($id,$subjects){
        $error = false;
        foreach($subjects as $subject){
            //Chequeo si existe la  materia dentro de la orientacion
            $stm = 'SELECT * FROM subject_orientation WHERE id_subject = ? AND id_orientation = ? AND `state` = 1';
            $subject_orientation = parent::query($stm,[$subject,$id]);
            //Chequeo si existe la  materia dentro de la orientacion
            if($subject_orientation){
                //'Borro' la materia dentro de la orientacion
                $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_subject = ? AND id_orientation = ?';
                $rows = parent::nonQuery($stm,[$subject,$id]);
                if($rows == 0){
                    $error = true;
                }
            }
        }
        //Si hubo un error devuelvo 0 
        if($error){
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
        $orientation_data = $orientation[0];
        return $orientation_data;
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
        $stm = 'SELECT so.id_orientation,so.id_subject ,s.name FROM orientation o,subject s,subject_orientation so WHERE so.id_orientation = ? AND s.id = so.id_subject AND o.id = so.id_orientation AND s.state = 1 AND  o.state = 1 AND  so.state = 1';
        $materias = parent::query($stm,[$id]);
        foreach($materias as &$materia){
            $materia['selected'] = true;
        }
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
        $error = false;
        $stm = 'UPDATE orientation SET `state` = 0 WHERE id = ?';
        //'Borro la orientacion'
        $rows = parent::nonQuery($stm,[$id]);
        if($rows > 0 ){
            $stm = 'UPDATE subject_orientation SET `state` = 0 WHERE id_orientation  = ?';
            //Borro las materias dentro de la orientacion
            $rows = parent::nonQuery($stm,[$id]);
            if($rows == 0){
                $error = true;
            }
        }else{
            $error = true;
        }
        if($error){
            return 'No se pudo borrar la orientacion';
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
