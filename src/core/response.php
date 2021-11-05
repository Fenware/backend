<?php

/*
Esta clase es para crear objetos de la misma y tener un estandar de respuestas
*/
class Response{

    public $response = [
        'status' => 'ok',
        'result' => array()
    ];

    public function error_400(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 400,
            'error_msg' => 'Campos incompletos o incorrectos'
        );
        return $this->response;
    }

    public function error_403(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 403,
            'error_msg' => 'No tienes los permisos para acceder a este recurso'
        );
        return $this->response;
    }
    public function error_404(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 404,
            'error_msg' => 'Recurso no encontrado'
        );
        return $this->response;
    }

    public function error_405(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 405,
            'error_msg' => 'Metodo no  permitido'
        );
        return $this->response;
    }

    public function error_NO_DB(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 000,
            'error_msg' => 'El sistema se ha caido'
        );
        return $this->response;
    }

    public function error_500(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 500,
            'error_msg' => 'Algo salio mal en el servidor'
        );
        return $this->response;
    }

    public function OOPSIE(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 010,
            'error_msg' => 'ERROR'
        );
        return $this->response;
    }

    /*
    Metodo para crear errores unicos
    */
    public function error($error_msg,$error_num = 666){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => $error_num,
            'error_msg' => $error_msg
        );
        return $this->response;
    }

    public function auth_error(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 401,
            'error_msg' => 'No pudimos autenticarte'
        );
        return $this->response;
    }
}