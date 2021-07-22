<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class GroupModel extends Model{
    private $id;
    private $name;
    private $code;

    public function __construct()
    {
        parent::__construct();
    }

    public function postGroup($name,$orientation){
        $stm = 'SELECT * FROM orientation WHERE `state` = 1';
        $query_orientation = parent::query($stm,[$orientation]);
        if($query_orientation){
            $code = $this->generateCode();
            $stm = 'INSERT INTO `group`(id_orientation,`name`,code) VALUES(?,?,?)';
            $rows = parent::nonQuery($stm,[$orientation,$name,$code]);
            $id = parent::lastInsertId();
            $stm = 'UPDATE `group` SET `state` = 1 WHERE code = ?';
            $rows = parent::nonQuery($stm,[$code]);
            return $id;
        }else{
            return ['error'=>'la orientacion no existe o fue borrada'];
        }
        
    }

    public function getGroups(){
        $stm = 'SELECT * FROM `group` WHERE `state` = 1 ';
        $data = parent::query($stm);
        return $data;
    }
    
    public function getGroupById($id){
        $stm = 'SELECT * FROM `group` WHERE id = ? AND `state` = 1 ';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function getGroupByName($name){
        $stm = 'SELECT * FROM `group` WHERE `name` LIKE ? AND `state` = 1 ';
        $data = parent::query($stm,['%'.$name.'%']);
        return $data;
    }


    public function putGroup($id,$name,$orientation){
        $stm = 'UPDATE `group` SET `name` = ? , id_orientation = ? WHERE id = ?';
        $rows = parent::nonQuery($stm,[$name,$orientation,$id]);
        return $rows;
    }

    public function deleteGroup($id){
        $stm = 'UPDATE `group` SET `state` = 0 WHERE id = ?';
        $rows = parent::nonQuery($stm,[$id]);
        return $rows;
    }
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


    

    public function getGroupOrientation($group){
        $stm = 'SELECT * FROM `group` WHERE id = ?';
        $data = parent::query($stm,[$group]);
        $orientation = $data[0]['id_orientation'];
        return $orientation;
    }

    
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