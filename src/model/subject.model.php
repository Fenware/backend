<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';
/*
Modelo para las materias
*/
class SubjectModel extends Model{

    private $id;
    private $nombre;
    private $res;
    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }
    
    /*
    Crea una materia
    */
    public function postSubject($nombre){
        $stm = 'INSERT INTO `subject` (`name`) VALUES(?)';
        return parent::nonQuery($stm,[$nombre]);
    }

    /*
    Borra una materia
    */
    public function deleteSubject($id){
        $stm = 'UPDATE `subject` SET state = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        return $rows;
    }

    /*
    Devuelve todas las materias
    */
    public function getSubjects(){
        $stm = 'SELECT * FROM `subject` WHERE `state` = 1';
        $data = parent::query($stm);
        return $data;
    }

    /*
    Devuelve materias en base a un id
    */
    public function getSubjectById($id){
        $stm = 'SELECT * FROM `subject` WHERE id = ? AND `state` = 1';
        $materia_data = parent::query($stm,[$id]);
        $materia = $materia_data[0];
        return $materia;
    }

    /*
    Devuelve materias en base a un nombre
    */
    public function getSubjectByName($name){
        $stm = "SELECT * FROM `subject` WHERE `name` LIKE ? AND `state` = 1";
        $data = parent::query($stm,['%'.$name.'%']);
        return $data[0];
    }

    /*
    Modifica una materia
    */
    public function putSubject($id,$nombre){
        $stm = 'UPDATE `subject` SET `name` = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$nombre,$id]);
        return $rows;
    }

    /*
    Le da la materia de un grupo a un docente
    */
    public function GiveSubjectInGroupToTeacher($teacher,$group,$subject){
        $stm = 'INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) VALUES(?,?,?)';
        $rows = parent::nonQuery($stm,[$teacher,$group,$subject]);
        $stm = 'UPDATE teacher_group_subject SET `state` = 1 WHERE id_teacher = ? AND id_group = ? AND id_subject = ?';
        $rows_state = parent::nonQuery($stm,[$teacher,$group,$subject]);
        
        if($rows > 0){
            //se pudo agregar al 
            return 1;
        }elseif($rows_state > 0){
            //se pudo  devolver la materia al profesor
            return 1;
        }else{
            //no se pudo tomar la materia
            return $this->res->error('Ocurrio un problema al tomar la materia',1013);
        }
    }

    /*
    Remueve a un docente de la materia en un grupo
    */
    public function removeTeacherFromSubjectInGroup($teacher,$group,$subject){
        $stm = 'UPDATE teacher_group_subject SET `state` = 0 WHERE id_teacher = ? AND id_group = ? AND id_subject = ?';
        $rows = parent::nonQuery($stm,[$teacher,$group,$subject]);
        return $rows;
    }
    /*
    Cheque si la materia en un grupo ya esta tomada
    */
    public function IsSubjectInGroupTaken($group,$subject){
        $stm = 'SELECT * FROM teacher_group_subject t,`user` u 
        WHERE t.id_teacher = u.id AND t.id_group = ? AND t.id_subject = ? AND u.state_account = 1 AND t.`state` = 1';
        $data = parent::query($stm,[$group,$subject]);
        if($data){
            return true;
        }else{
            return false;
        }
    }
    
    /*
    Devuele las materias de un docente
    */
    public function getTeacherSubjects($teacher){
        $stm = 'SELECT tgs.id_group,g.id_orientation,tgs.id_subject ,s.name
        FROM `subject` s ,teacher_group_subject tgs ,`group` g
        WHERE tgs.id_teacher = ? AND s.id = tgs.id_subject AND tgs.state = 1 AND s.state = 1 AND tgs.id_group = g.id';
        $data = parent::query($stm,[$teacher]);
        //le agrego selected pa ayudar a los de frontend
        //es remobible pero ellos se tienen que enterar
        foreach($data as $item){
            $item['selected'] = true;
        }
        return $data;
    }

    public function getTeacherSubjectsInGroup($teacher,$group){
        $stm = 'SELECT s.id
        FROM `subject` s ,teacher_group_subject tgs ,`group` g
        WHERE tgs.id_teacher = ? AND g.id = ? AND s.id = tgs.id_subject AND tgs.state = 1 AND s.state = 1 AND tgs.id_group = g.id';
        $data = parent::query($stm,[$teacher,$group]);
        return $data;
    }

    /*
    Devuelve el id del docente que enseña una materia en un grupo
    */
    public function getTeacherFromSubjectInGroup($subject,$group){
        $stm = 'SELECT *
        FROM teacher_group_subject tgs
        WHERE tgs.id_subject = ? AND tgs.id_group = ? AND `state`= 1 ';
        $data = parent::query($stm,[$subject,$group]);
        if($data){
            $id_teacher = $data[0]['id_teacher'];
            return (int)$id_teacher;
        }else{
            return 'Ningun profesor tiene esta materia';
        }
    }

    public function changeSubjectState($id,$state){
        $stm = 'UPDATE `subject` SET state = ? WHERE id = ?';
        return parent::nonQuery($stm,[$state,$id]);
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