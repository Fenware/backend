<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
/*
Modelo para las materias
*/
class SubjectModel extends Model{

    private $id;
    private $nombre;

    public function __construct()
    {
        parent::__construct();
    }
    
    /*
    Crea una materia
    */
    public function postSubject($nombre){
        $stm = 'SELECT * FROM `subject` WHERE `name` = ? AND `state` = 1';
        $materia_existe = parent::query($stm, [$nombre] );
        if($materia_existe){
            return 'La materia ya existe';
        }else{
            $stm = 'SELECT * FROM `subject` WHERE `name` = ? AND `state` = 0';
            $materia_borrada = parent::query($stm, [$nombre] );
            if($materia_borrada){
                $id = $materia_borrada[0]['id'];
                $stm = 'UPDATE `subject` SET state = 1 WHERE id = ?';
                parent::nonQuery($stm,[$id]);
                return (int)$id;
            }else{
                $stm = 'INSERT INTO `subject` (`name`) VALUES(?)';
                $rows = parent::nonQuery($stm,[$nombre]);
                if($rows > 0){
                    $id = parent::lastInsertId();
                    return (int)$id;
                }else{
                    return 'Surgio un problema al crear la materia';
                }
            }
        
        }
        
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
        $data = parent::query($stm,[$id]);
        return $data;
    }

    /*
    Devuelve materias en base a un nombre
    */
    public function getSubjectByName($name){
        $stm = "SELECT * FROM `subject` WHERE `name` LIKE ? AND `state` = 1";
        $data = parent::query($stm,['%'.$name.'%']);
        return $data;
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
            return 'Ocurrio un problema al intentar tomar la materia';
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