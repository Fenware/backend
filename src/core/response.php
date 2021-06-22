<?php

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

    public function error_404(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 404,
            'error_msg' => 'Pagina no encontrada'
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

    

    public function OOPSIE(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => 'OwO',
            'error_msg' => 'OOPSIE WOOPSIE!! Uwu We made a fucky wucky!! A wittle fucko boingo! The code monkeys at our headquarters are working VEWY HAWD to fix this!'
        );
        return $this->response;
    }


    public function error($error_msg,$error_num = 200){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            'error_id' => $error_num,
            'error_msg' => $error_msg
        );
        return $this->response;
    }

}