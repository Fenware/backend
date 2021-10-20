<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/model/orientation.model.php';
require_once '/var/www/html/core/response.php';

/*
Modelo de los usuarios
*/
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
    private $grupo;
    function __construct() 
    {
        $this->res = new Response();
        parent::__construct();
    }

    /*
    Crea un usuario
    */
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
    /*
    Modifica un usuario
    !!!ESTA FUNCION ES MUY HDP , HAY QUE CAMBIARLA EN OTRO MOMENTO!!!
    */
    public function patchUser($id,$column,$value){
        $stm = 'UPDATE `user` SET '.$column.' = ? WHERE id = ?';
        return parent::nonQuery($stm,[$value,$id]);
    }

    /*
    Le doy el tipo de usuario a un  usuario (alumno o docente)
    !!! HAY QUE SEPARAR ESTO EN 2 FUNCIONES  DISTINTAS !!!
    */
    public function setUserType($id,$type){
        $stm = 'INSERT INTO '.$type.'(id) VALUES(:id)';
        $foo = ['id' => $id];
        return parent::nonQuery($stm,$foo);
    }

    /*
    Devuelve la informacion de un usuario(no devolver esta info al usuario)
    */
    public function getUser($user){
        $stm = 'SELECT * FROM user WHERE email = ? OR nickname = ?';
        $data = parent::query($stm,[$user,$user]);
        return $data;
    }


    /*
    Devuelve los usuarios pendientes
    */
    public function getPendentUsers(){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account
        FROM user 
        WHERE state_account = 2';
        $data = parent::query($stm);
        return $data;
    }

    /*
    Devuelve la informacion de un usuario por cedula
    */
    public function getUserByCiSafe($ci){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account,connection_time
        FROM user WHERE ci = ?';
        $data = parent::query($stm,[$ci]);
        return $data;
    }

    /*
    Devuelve la informacion de un usuario por id
    */
    public function getUserByIdSafe($id){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account ,connection_time
        FROM user WHERE id = ?';
        $user = parent::query($stm,[$id]);
        return !empty($user) ? $user[0] : $user;
    }

    /*
    Devuelve la informacion de un usuario por nickname
    */
    public function getUserByNicknameSafe($nickname){
        $stm = 'SELECT id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account ,connection_time
        FROM user WHERE nickname = ?';
        $user = parent::query($stm,[$nickname]);
        return !empty($user) ? $user[0] : $user;
    }

    /*
    Chequea si un usuario es de cierto tipo
    */
    public function checkUserType($id,$type){
        $stm = 'SELECT * FROM '.$type.' WHERE id = ?';
        $data = parent::query($stm,[$id]);
        return $data;
    }

    /*
    Devuelve que tipo de usuario es un usuario
    */
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
    
    /*
    Devuelve todos los usuarios
    */
    public function getAllUsers(){
        $stm = 'SELECT u.id,ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account ,connection_time
        FROM user u,administrator a
        WHERE a.id != u.id AND u.state_account = 1';
        $users = parent::query($stm);

        // Loop para agregarle el tipo de usuario
        foreach ($users as &$user) {
            $user_type = $this->getUserType($user['id']);
            $user['type'] = $user_type;
        }

        return $users;
    }


    /*
    Proteje la constraseña de un usuario 
    */
    public function hash($password){
        $hashed_pwd  = password_hash($password,PASSWORD_DEFAULT);
        return $hashed_pwd;
    }


    /*
    Agrego a un docente a un grupo
    */
    public function giveTeacherGroup($teacher,$group){
        $stm = 'INSERT INTO teacher_group(id_teacher,id_group) VALUES(?,?)';
        $rows = parent::nonQuery($stm,[$teacher,$group]);
        if($rows > 0){
            return $rows;
        }else{  
            $stm = 'UPDATE teacher_group SET `state` = 1 WHERE id_teacher = ? AND id_group = ?';
            $rows_state = parent::nonQuery($stm,[$teacher,$group]);
            return $rows_state;
        }
    }

    /*
    Agrego a un alumno a un grupo
    */
    public function giveStudentGroup($student,$group){
        $stm = 'INSERT INTO student_group(id_student,id_group) VALUES(?,?)';
        $rows = parent::nonQuery($stm,[$student,$group]);
        if($rows > 0){
            return $rows;
        }else{  
            $stm = 'UPDATE student_group SET `state` = 1 WHERE id_student = ? AND id_group = ?';
            $rows_state = parent::nonQuery($stm,[$student,$group]);
            return $rows_state;
        }
    }


    /*
    Remueve a un usuario de un grupo
    */
    public function deleteUserGroup($id,$group = 0,$type){
        switch($type){
            case 'teacher':
                $stm =  'UPDATE teacher_group SET `state` = 0 WHERE id_teacher = ? AND id_group = ?';
                $rows = parent::nonQuery($stm,[$id,$group]);
                $this->removeTeacherFromAllSubjectsInGroup($id,$group);
                break;
            case 'student' : 
                $stm =  'UPDATE student_group SET `state` = 0 WHERE id_student = ?';
                $rows = parent::nonQuery($stm,[$id]);
                break;
        }
        return $rows;
    }

    /*
    Chequea si un usuario esta en un grupo
    */
    public function IsUserInGroup($user,$group,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT * FROM teacher_group WHERE id_teacher = ? AND id_group = ? AND `state` = 1';
                break;
            case 'student':
                $stm = 'SELECT * FROM student_group WHERE id_student = ? AND id_group = ? AND `state` = 1';
                break;
            default:
                return false;
                break;
        }
        $data =  parent::query($stm,[$user,$group]);
        if($data){
            return true;
        }else{
            return false;
        }
    }

    /*
    Devuelve los grupos de un usuario
    */
    public function getUserGroups($user,$type){
        switch($type){
            case 'teacher':
                $stm = 'SELECT o.id AS id_orientation ,o.`name` AS orientation_name ,o.`year` AS orientation_year,g.id ,g.`name`, g.code
                FROM teacher_group tg,`group` g,orientation o 
                WHERE tg.id_teacher = ? AND tg.id_group = g.id AND g.id_orientation = o.id AND tg.state = 1 AND g.state = 1 AND o.state = 1';
                $grupos = parent::query($stm,[$user]);
                $orientation_model = new OrientationModel();
                foreach($grupos as &$grupo){
                    $grupo['subjects'] = $orientation_model->getOrientationSubjects($grupo['id_orientation']);
                }
                break;
            case 'student':
                $stm = 'SELECT o.id AS id_orientation ,o.`name` AS orientation_name ,o.`year` AS orientation_year,g.id AS id_group ,g.`name` AS group_name
                FROM student_group sg,`group` g,orientation o 
                WHERE sg.id_student = ? AND sg.id_group = g.id AND g.id_orientation = o.id AND sg.state = 1 AND g.state = 1 AND o.state = 1';
                $grupos = parent::query($stm,[$user]);
                break;
            default:
                $grupos = 'No correct user type';
                break;
        }
        
        return $grupos;
    }

    /*
    Chequea si un estudiante pertenece a un grupo
    */
    public function userHasGroup($id){
        $stm = 'SELECT * FROM student_group WHERE id_student = ? AND `state` = 1';
        $rows = parent::nonQuery($stm,[$id]);
        if($rows > 0){
            return true;
        }else{
            return false;
        }
    }

    /*
    Chequea si un usuario tiene acceso a una consulta
    */
    public function UserHasAccesToConsulta($user,$consulta){
        $stm = 'SELECT q.id FROM `query` q,`individual` i  WHERE (q.id_student = ? OR q.id_teacher = ?) AND q.id = ? AND q.id = i.id';
        $query = parent::query($stm,[$user,$user,$consulta]);
        if(empty($query)){
            return false;
        }else{
            return true;
        }
    }

    /*
    Chequeo si un  usuario tiene acceso a un chat
    !!! HABRIA QUE MOVER ESTO A class Chat !!! 
    */
    public function UserHasAccesToChat($user,$chat){
        $stm = 'SELECT q.id,q.id_group,q.id_subject FROM `query` q,`room` r WHERE q.id = ? AND q.id = r.id';
        $query = parent::query($stm,[$chat]);
        if($query){
            $grupo = $query[0]['id_group'];
            $materia = $query[0]['id_subject'];
            $type = $this->getUserType($user);
            
            switch($type){
                case 'teacher':
                    $stm = 'SELECT * FROM `teacher_group_subject` WHERE id_teacher = ? AND id_group = ? AND id_subject = ? AND `state` = 1';
                    $acces = parent::query($stm, [$user,$grupo,$materia] );
                    if($acces){
                        return true;
                    }else{
                        return false;
                    }
                    break;
                case 'student':
                    
                    $stm = 'SELECT * FROM `student_group` WHERE id_student = ? AND id_group = ? AND `state` = 1';
                    $acces = parent::query($stm, [$user,$grupo] );
                    if($acces){
                        return true;
                    }else{
                        return false;
                    }
                    break; 
                default:
                    return false;
                    break;      
            }
        }else{
            return false;
        }
    }

    /*
    Chequea si un estudiante es el autor de una consutla
    */
    public function StudentIsAutorOfQuery($student,$consulta){
        $stm = 'SELECT * FROM `query` WHERE id_student = ? AND id_query = ?';
        $rows = parent::query($stm,[$student,$consulta]);
        if($rows > 0){
            return true;
        }else{
            return false;
        }
    }

    /*
    Cambia la cantidad de salas maximas abiertas con una misma materia en un grupo de un docente
    */
    public function setMaxRoomsPerGs($teacher,$max){
        $stm = 'UPDATE teacher SET max_rooms_per_gs = ? WHERE id = ?';
        $rows = parent::nonQuery($stm , [$max,$teacher] );
        return $rows;
    }

    /*
    Devuelve la cantidad de salas maximas abiertas con una misma materia en un grupo de un docente
    */
    public function getMaxRoomsPerGs($teacher){
        $stm =  'SELECT max_rooms_per_gs FROM teacher WHERE id = ?';
        $max_rooms_per_gs = parent::query($stm, [$teacher] );
        return $max_rooms_per_gs[0]['max_rooms_per_gs'];
    }
    
    /*
    Esta funcion no se usa nunca y seguramente sea borrada por que ya hay  una mejor solucion para lo que busca hacer
    */
    public function actualizeLastConnectionTime($user){
        $date = date('Y-m-d H:i:s', time());
        $stm = 'UPDATE `user` SET connection_time = ? WHERE id = ?';
        $rows = parent::nonQuery($stm , [$date ,$user]);
        return $rows;
    }

    /*
    Esta funcion no se usa nunca y seguramente sea borrada por que ya hay  una mejor solucion para lo que busca hacer
    */
    public function getLastConnectionTime($user){
        $date = date('Y-m-d H:i:s', time());
        $stm = 'SELECT connection_time FROM `user` WHERE id = ?';
        $time = parent::query($stm , [$user]);
        return $time;
    }

    /*
    Borra a un usuario
    */
    public function removeUser($user,$type){
        $rows = $this->patchUser($user,'state_account',0);
        switch($type){
            case 'teacher':
                $this->removeTeacherFromAllGroups($user);
                $this->removeTeacherFromAllSubjects($user);
                break;
            case 'student':
                $this->deleteUserGroup($user,0,$type);
            default:
                break;
        }
        $this->removeTeacherFromAllGroups($user);
        return $rows;
    }


    public function removeTeacherFromAllGroups($teacher){
        $stm = 'UPDATE teacher_group SET `state` = 0 WHERE id_teacher = ?';
        $rows = parent::nonQuery($stm, [$teacher] );
        return $rows;
    }

    public function removeTeacherFromAllSubjects($teacher){
        $stm = 'UPDATE teacher_group_subject SET `state` = 0 WHERE id_teacher = ?';
        $rows = parent::nonQuery($stm, [$teacher] );
        return $rows;
    }

    public function removeTeacherFromAllSubjectsInGroup($teacher,$group){
        $stm = 'UPDATE teacher_group_subject SET `state` = 0 WHERE id_teacher = ? AND id_group = ?';
        $rows = parent::nonQuery($stm, [$teacher ,$group] );
        return $rows;
    }



    public function validateCI($CedulaDeIdentidad) {
        $regexCI = '/^([0-9]{1}[.]?[0-9]{3}[.]?[0-9]{3}[-]?[0-9]{1}|[0-9]{3}[.]?[0-9]{3}[-]?[0-9]{1})$/';

        if (!preg_match($regexCI, $CedulaDeIdentidad)) {
            return false;
        } else {
            // Limpiamos los puntos y guiones para solo quedarnos con los números.
            $numeroCedulaDeIdentidad = preg_replace("/[^0-9]/","",$CedulaDeIdentidad);

            // Armarmos el array que va a permitir realizar las multiplicaciones necesarias en cada digito.
            $arrayCoeficiente = [2,9,8,7,6,3,4,1];

            // Variable donde se va a guardar el resultado de la suma.
            $suma = 0;

            // Simplemente para que se entienda que esto es el cardinal de digitos que tiene el array de coeficiente.
            $lenghtArrayCoeficiente = 8;

            // Contamos la cantidad de digitos que tiene la cadena de números de la CI que limpiamos.
            $lenghtCedulaDeIdentidad = strlen($numeroCedulaDeIdentidad);

            // Esto nos asegura que si la cédula es menor a un millón, para que el cálculo siga funcionando, simplemente le ponemos un cero antes y funciona perfecto.
            if ($lenghtCedulaDeIdentidad == 7) {
                $numeroCedulaDeIdentidad = 0 . $numeroCedulaDeIdentidad;
                $lenghtCedulaDeIdentidad++;
            }

            for ($i = 0; $i < $lenghtCedulaDeIdentidad; $i++) {
                // Voy obteniendo cada caracter de la CI.
                $digito = substr($numeroCedulaDeIdentidad, $i, 1);

                // Ahora lo forzamos a ser un int.
                $digitoINT = intval($digito);

                // Obtengo el coeficiente correspondiente a esta posición.
                $coeficiente = $arrayCoeficiente[$i];

                // Multiplico el caracter por el coeficiente y lo acumulo a la suma total
                $suma = $suma + $digitoINT * $coeficiente;
            }

            // si la suma es múltiplo de 10 es una ci válida
            if (($suma % 10) == 0) {
                return true;
            } else {
                return false;
            }		
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