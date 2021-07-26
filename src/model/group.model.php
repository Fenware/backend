<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
    Modelo para los grupos
*/
class GroupModel extends Model{
    private $id;
    private $name;
    private $code;

    public function __construct()
    {
        parent::__construct();
    }

    /*
    Crea el grupo
    */
    public function postGroup($name,$orientation){
        $stm = 'SELECT * FROM orientation WHERE `state` = 1 AND `name` = ? AND id_orientation = ?';
        $query_orientation = parent::query($stm,[$orientation]);
        //Chequeo si la orientacion ya existe
        if($query_orientation){
            //genero el codigo del grupo
            $code = $this->generateCode();

            //Compruevo si el grupo ya existe y esta activo
            $stm = 'SELECT * FROM `group` WHERE `name` = ? AND id_orientation = ? AND `state` = 1';
            $grupo_existe = parent::query($stm, [$name,$orientation] );
            //Compruevo si el grupo ya existe
            if($grupo_existe){
                return 'El grupo ya existe';
            }else{
                //Chequeo si el grupo existe pero esta 'borrado'
                $stm = 'SELECT * FROM `group` WHERE `name` = ? AND id_orientation = ? AND `state` = 0';
                $grupo_existe_borrado = parent::query($stm, [$name,$orientation] );
                if($grupo_existe_borrado){
                    //Cambio su estado de  0 a 1 (1 = activo)
                    $stm = 'UPDATE `group` SET `state` = 1 WHERE `name` = ? AND id_orientation = ?';
                    parent::nonQuery($stm,[$name,$orientation]);
                    $id = $grupo_existe_borrado[0]['id'];
                }else{
                    //Creo el grupo
                    $stm = 'INSERT INTO `group`(id_orientation,`name`,code) VALUES(?,?,?)';
                    parent::nonQuery($stm,[$orientation,$name,$code]);
                    $id = $this->lastInsertId();
                }
                //Me aseguro de devolver un numero
                return (int)$id;
            }
            
        }else{
            return 'La orientacion no existe o fue borrada';
        }
        
    }

    /*
    Devuelve todos los grupos
    */
    public function getGroups(){
        $stm = 'SELECT * FROM `group` WHERE `state` = 1 ';
        $data = parent::query($stm);
        return $data;
    }
    /*
    Devuelve un grupo por id
    */
    public function getGroupById($id){
        $stm = 'SELECT * FROM `group` WHERE id = ? AND `state` = 1 ';
        $data = parent::query($stm,[$id]);
        return $data;
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