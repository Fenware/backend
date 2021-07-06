<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class TeacherSubjectAPI extends API{
    
    private $materia;
    private $res;
    private $user;
    function __construct()
    {
        $this->res = new Response();
        $this->materia = new SubjectModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'teacher'){

        }
    }
    public function GET($token,$data){
        if($token->user_type == 'teacher'){
            
        }
    }
    public function PUT($token,$data){
        if($token->user_type == 'teacher'){
            
        }
    }
    public function DELETE($token,$data){
        if($token->user_type == 'teacher'){
            
        }
    }
}