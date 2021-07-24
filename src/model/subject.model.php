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
        if($rows > 0){
            $id = parent::lastInsertId();
            return $id;
        }else{
            $stm = 'SELECT state FROM `subject` WHERE name = ?';
            $data = parent::query($stm,[$nombre]);
            if($data[0]['state'] == 1){
                return 'error';
            }else{
                $stm = 'UPDATE `subject` SET state = 1 WHERE name = ?';
                $rows = parent::nonQuery($stm,[$nombre]);
                $stm = 'SELECT id FROM `subject` WHERE name = ?';
                $data = parent::query($stm,[$nombre]);
                return $data[0]['id'];
            }
        }
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
    }

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
            return 0;
        }
    }

    public function removeTeacherFromSubjectInGroup($teacher,$group,$subject){
        $stm = 'UPDATE teacher_group_subject SET `state` = 0 WHERE id_teacher = ? AND id_group = ? AND id_subject = ?';
        $rows = parent::nonQuery($stm,[$teacher,$group,$subject]);
        return $rows;
    }
    //To check if a  subject in a  group is already talken
    public function IsSubjectInGroupTaken($group,$subject){
        $stm = 'SELECT * FROM teacher_group_subject t,`user` u 
        WHERE t.id_teacher = i.id AND t.id_group = ? AND t.id_subject = ? AND u.state_account = 1';
        $data = parent::query($stm,[$group,$subject]);
        if($data){
            return true;
        }else{
            return false;
        }
    }
    
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

    public function getTeacherFromSubjectInGroup($subject,$group){
        $stm = 'SELECT *
        FROM teacher_group_subject tgs
        WHERE tgs.id_subject = ? AND tgs.id_group = ? AND `state`= 1 ';
        $data = parent::query($stm,[$subject,$group]);
        try {
            $id_teacher = $data[0]['id_teacher'];
            return (int)$id_teacher;
        } catch (\Throwable $th) {
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