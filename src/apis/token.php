<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class TokenAPI extends API{

    private $res;
    function __construct()
    {
        $this->res = new Response();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        echo json_encode('OK');
    }

    public function GET($token,$data){
        exit;
    }

    public function PUT($token,$data){
        exit;
    }

    public function DELETE($token,$data){
        exit;
    }

}