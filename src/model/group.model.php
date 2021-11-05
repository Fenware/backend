<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';

/*
    Modelo para los grupos
*/
class GroupModel extends Model{
    private $id;
    private $name;
    private $code;
    private $res;
    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }



    /*
    Se usa para determianr si ya  existe un grupo con x nombre en un año
    */
    public function getGroupInYear($name,$year){
        $stm = 
            "SELECT g.id,g.id_orientation,g.`name`,g.`code` ,g.state
            FROM `group` g , orientation o 
            WHERE g.id_orientation = o.id AND g.`name` = ? AND o.`year` = ?";
            $grupo = parent::query($stm , [$name, $year]);
        return !empty($grupo) ? $grupo[0] : $grupo;
    }

    /*
    Crea un grupo
    */
    public function postGroup($name,$orientation){
        //genero el codigo del grupo
        $code = $this->generateCode();
        $stm = 'INSERT INTO `group`(id_orientation,`name`,code) VALUES(?,?,?)';
        parent::nonQuery($stm,[$orientation,$name,$code]);
        return $this->lastInsertId();
    }

    /*
    Cambia el estado de un grupo a activos
    */
    public function setGroupActive($id){
        $stm = 'UPDATE `group` SET `state` = 1 WHERE id = ?';
        return parent::nonQuery($stm,[$id]);
    }

    /*
    Devuelve todos los grupos
    */
    public function getGroups(){
        $stm = 'SELECT g.id ,g.id_orientation, o.name AS orientation_name , o.year, g.`name`,g.`code`,g.`state` FROM `group` g,orientation o WHERE g.`state` = 1 AND g.id_orientation = o.id';
        $data = parent::query($stm);
        return $data;
    }
    /*
    Devuelve un grupo por id
    */
    public function getGroupById($id){
        $stm = 'SELECT g.id ,g.id_orientation, o.name AS orientation_name , o.year, g.`name`,g.`code`,g.`state` FROM `group` g,orientation o WHERE g.`state` = 1 AND g.id_orientation = o.id AND g.id = ?';
        $group_data = parent::query($stm,[$id]);
        $grupo = !empty($group_data) ? $group_data[0] : $group_data;
        return $grupo;
    }

    /*
    Devuelve un grupo por nombre
    */
    public function getGroupByName($name){
        $stm = 'SELECT * FROM `group` WHERE `name` LIKE ? AND `state` = 1 ';
        $data = parent::query($stm,['%'.$name.'%']);
        return $data;
    }

    /*
    Modifica un grupo
    */
    public function putGroup($id,$name){
        $stm = 'UPDATE `group` SET `name` = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$name,$id]);
        return $rows;
    }

    /*
    'Borra' un grupo
    */
    public function deleteGroup($id){
        $stm = 'UPDATE `group` SET `state` = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        return $rows;
    }

    /*
    Saca a todos los docentes del grupo y los saca de las materias del grupo
    */
    public function removeAllTeachersFromGroup($id){
        $stm = 'UPDATE `teacher_group` SET `state` = 0 WHERE id_group = ?';
        parent::nonQuery($stm , [$id]);
        $stm = 'UPDATE `teacher_group_subject` SET `state` = 0 WHERE id_group = ?';
        parent::nonQuery($stm , [$id]);
    }

    /*
    Saco a todos los alumnos del grupo
    */
    public function removeAllStudentsFromGroup($id){
        $stm = 'UPDATE `student_group` SET `state` = 0 WHERE id_group = ?';
        return parent::nonQuery($stm , [$id]);
    }

    /*
    Cierra todas las consultas y chats que pertenescan al grupo
    */
    public function closeAllQuerysInGroup($id){
        $stm = 'UPDATE `query` SET `state` = 0 WHERE id_group = ?';
        return parent::nonQuery($stm , [$id]);
    }

    /*
    Devuelve un grupo en base a su codigo
    */
    public function getGroupByCode($code){
        $stm = 'SELECT * FROM `group` WHERE `code` = ? AND `state` = 1';
        $group = parent::query($stm,[$code]);
        return !empty($group) ? $group[0] : $group;
    }
    /*
    Genera el codigo para un grupo
    */
    private function generateCode(){
        $used_code = true;
        do{
            $code = $this->randomString(8);
            $stm = 'SELECT * FROM `group` WHERE `code` = ? ';
            $dataDB = parent::query($stm,[$code]);
            if($dataDB > 0){
                $used_code = false;
            }
        }while($used_code == true);
        return $code;
    }


    
    /*
    Devuelve el id de la orientacion de un grupo
    */
    public function getGroupOrientation($group){
        $stm = 'SELECT * FROM `group` WHERE id = ?';
        $data = parent::query($stm,[$group]);
        $orientation = !empty($data) ? $data[0]['id_orientation'] : $data;
        return $orientation;
    }

    /*
    Chequea si una materia esta en un grupo
    */
    public function IsSubjectInGroup($group,$subject){
        $orientation = $this->getGroupOrientation($group);
        $stm = 'SELECT * FROM subject_orientation WHERE id_orientation = ? AND id_subject =?';
        $subject = parent::query($stm,[$orientation,$subject]);
        if($subject){
            return true;
        }else{
            return false;
        }

    }

    /*
    Genera un string aleatorio  (es usado el generar un codigo)
    */
    private function randomString($length){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /*
    Devuelve los estudiantes en un grupo
    */
    public function getStudentsInGroup($group){
        $stm = 'SELECT u.id,u.ci,u.`name`,u.middle_name,u.surname,u.second_surname,u.email,u.avatar,u.nickname,u.state_account,u.connection_time
        FROM `user` u,student_group sg
        WHERE u.id = sg.id_student AND sg.id_group = ? AND sg.`state` = 1';
        $users = parent::query($stm , [$group]);
        return $users;
    }

    /*
    Devuelve los docentes en un grupo
    */
    public function getTeachersInGroup($group){
        $stm = 'SELECT u.id,u.ci,u.`name`,u.middle_name,u.surname,u.second_surname,u.email,u.avatar,u.nickname,u.state_account,u.connection_time
        FROM `user` u,teacher_group tg
        WHERE u.id = tg.id_teacher AND tg.id_group = ? AND tg.`state` = 1';
        $teachers = parent::query($stm , [$group]);
        return $teachers;
    }


}