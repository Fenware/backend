<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
$res = new Response();
echo json_encode($res->error('There is nothing in here'));