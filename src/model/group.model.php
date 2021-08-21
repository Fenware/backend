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
    Crea el grupo
    */
    public function postGroup($name,$orientation){
        $stm = 'SELECT * FROM orientation WHERE `state` = 1 AND id = ?';
        $query_orientation = parent::query($stm,[$orientation]);
        //Chequeo si la orientacion ya existe
        if($query_orientation){
            $year = (int)$query_orientation[0]['year'];

            //Compruebo si el grupo ya existe y esta activo
            $stm = 
            "SELECT g.id,g.id_orientation,g.`name`,g.`code` ,g.state
            FROM `group` g , orientation o 
            WHERE g.id_orientation = o.id AND g.`name` = ? AND o.`year` = ?";
            $grupo_existe = parent::query($stm , [$name, $year]); 
            //Compruebo si el grupo ya existe
            if($grupo_existe){
                $state = $grupo_existe[0]['state'];
                if($state == 1){
                    return $this->res->error('El grupo ya existe',1030);
                }else{
                    $stm = 'UPDATE `group` SET `state` = 1 WHERE id = ?';
                    $id = $grupo_existe[0]['id'];
                    parent::nonQuery($stm,[$id]);
                    return $this->getGroupById($id);
                }
            }else{
                //genero el codigo del grupo
                $code = $this->generateCode();
                $stm = 'INSERT INTO `group`(id_orientation,`name`,code) VALUES(?,?,?)';
                parent::nonQuery($stm,[$orientation,$name,$code]);
                $id = $this->lastInsertId();
                return $this->getGroupById($id);
            }
        }else{
            return $this->res->error('La orientacion no existe o fue borrada',1031);
        }
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
        $stm = 'SELECT * FROM `group` WHERE id = ?';
        $group_data = parent::query($stm,[$id]);
        $grupo = $group_data[0];
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
    public function putGroup($id,$name,$orientation){
        $stm = 'UPDATE `group` SET `name` = ? , id_orientation = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$name,$orientation,$id]);
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
        $orientation = $data[0]['id_orientation'];
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


}