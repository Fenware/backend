<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class GroupModel extends Model{
    private $id;
    private $name;
    private $year;
    private $subjects;
    private $state;

    public function __construct()
    {
        parent::__construct();
    }