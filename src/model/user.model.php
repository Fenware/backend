<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class UserModel extends Model{

    private $id;
    private $ci;
    private $nombre;
    private $segundo_nombre;
    private $apellido;
    private $segundo_apellido;
    private $email;
    private $avatar;
    private $nickname;
    private $password;
    private $res;

    function __construct() 
    {
        $this->res = new Response();
        parent::__construct();
    }

    public function postUser($data){
        $stm = 'INSERT INTO `user`(ci,`name`,surname,email,nickname,`password`)
        VALUES(:ci,:name,:surname,:email,:nickname,:password)';
        $foo = [
            'ci' => $data['ci'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'nickname' => $data['nickname'],
            'password' => password_hash($data['password'],PASSWORD_DEFAULT)//Hash password
        ];
        $rows = parent::nonQuery($stm,$foo);
        if($rows > 0){
            return parent::lastInsertId();
        }else{
            return 'error';
        } 
    }

    public function patchUser($id,$column,$value){
        $stm = 'UPDATE `user` SET '.$column.' = :value WHERE id = :id';
        $foo = [
            'value' => $value,
            'id' => $id
        ];
        return parent::nonQuery($stm,$foo);
    }

    public function setUserType($id,$type){
        $stm = 'INSERT INTO '.$type.'(id) VALUES(:id)';
        $foo = ['id' => $id];
        return parent::nonQuery($stm,$foo);
    }
    public function getUser($user){
        $stm = 'SELECT * FROM user WHERE email = ? OR nickname = ?';
        $data = parent::query($stm,[$user,$user]);
        return $data;
    }



    public function getPendentUsers(){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account
        FROM user 
        WHERE state_account = 2';
        $data = parent::query($stm);
        return $data;
    }

    public function getUserByCiSafe($ci){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account
        FROM user WHERE ci = ?';
        $data = parent::query($stm,[$ci]);
        return $data;
    }

    public function getUserByIdSafe($id){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account 
        FROM user WHERE id = ?';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function checkUserType($id,$type){
        $stm = 'SELECT * FROM '.$type.' WHERE id = ?';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function getUserType($id){
        if($this->checkUserType($id,'teacher')){
            return 'teacher';
        }elseif($this->checkUserType($id,'student')){
            return 'student';
        }elseif($this->checkUserType($id,'administrator')){
            return 'administrator';
        }else{
            return 'No type';
        }
    }
    
    //No muestra administradores por seguridad
    public function getAllUsers(){
        $stm = 'SELECT u.id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account 
        FROM user u,administrator a
        WHERE a.id != u.id';
        $data = parent::query($stm);
        return $data;
    }


    public function hash($password){
        $hashed_pwd  = password_hash($password,PASSWORD_DEFAULT);
        return $hashed_pwd;
    }

    public function giveUserGroup($id,$code,$type){
        $stm = 'SELECT * FROM `group` WHERE `code` = ?';
        $group = parent::query($stm,[$code]);
        if($group){
            $stm = 'INSERT INTO '.$type.'_group(id_'.$type.',id_group) VALUES(?,?)';
            $rows = parent::nonQuery($stm,[$id,$group[0]['id']]);
            return $rows;
        }else{
            return $this->res->error('El grupo no existe');
        }
    }

    public function deleteUserGroup($id,$group = 0,$type){
        switch($type){
            case 'teacher':
                $stm =  'UPDATE teacher_group SET `state` = 0 WHERE id_teacher = ? AND id_group = ?';
                $rows = parent::nonQuery($stm,[$id,$group]);
                break;
            case 'student' : 
                $stm =  'UPDATE student_group SET `state` = 0 WHERE id_student = ?';
                $rows = parent::nonQuery($stm,[$id]);
                break;
        }
        return $rows;
    }

    public function userHasGroup($id){
        $stm = 'SELECT * FROM student_group WHERE id_student = ?';
        $rows = parent::nonQuery($stm,[$id]);
        if($rows > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the value of ci
     */ 
    public function getCi()
    {
        return $this->ci;
    }

    /**
     * Set the value of ci
     *
     * @return  self
     */ 
    public function setCi($ci)
    {
        $this->ci = $ci;
    }

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get the value of segundo_nombre
     */ 
    public function getSegundo_nombre()
    {
        return $this->segundo_nombre;
    }

    /**
     * Set the value of segundo_nombre
     *
     * @return  self
     */ 
    public function setSegundo_nombre($segundo_nombre)
    {
        $this->segundo_nombre = $segundo_nombre;
    }

    /**
     * Get the value of apellido
     */ 
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set the value of apellido
     *
     * @return  self
     */ 
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * Get the value of segundo_apellido
     */ 
    public function getSegundo_apellido()
    {
        return $this->segundo_apellido;
    }

    /**
     * Set the value of segundo_apellido
     *
     * @return  self
     */ 
    public function setSegundo_apellido($segundo_apellido)
    {
        $this->segundo_apellido = $segundo_apellido;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get the value of avatar
     */ 
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the value of avatar
     *
     * @return  self
     */ 
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Get the value of nickname
     */ 
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set the value of nickname
     *
     * @return  self
     */ 
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the value of estado_cuenta
     */ 
    public function getEstado_cuenta()
    {
        return $this->estado_cuenta;
    }

    /**
     * Set the value of estado_cuenta
     *
     * @return  self
     */ 
    public function setEstado_cuenta($estado_cuenta)
    {
        $this->estado_cuenta = $estado_cuenta;
    }

}