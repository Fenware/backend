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

    function __construct() 
    {
        parent::__construct();
    }


    public function getUser($user){
        $stm = 'SELECT * FROM user WHERE email = ? OR nickname = ?';
        $data = parent::query($stm,[$user,$user]);
        return $data;
    }

    public function getUserType($id,$type){
        $stm = 'SELECT * FROM '.$type.' WHERE id = ?';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    public function getAllUsers(){
        $stm = 'SELECT * FROM user';
        $data = parent::query($stm);
        return $data;
    }




    public function hash($password){
        $hashed_pwd  = password_hash($password,PASSWORD_DEFAULT);
        return $hashed_pwd;
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