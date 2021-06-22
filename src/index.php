<?php

include_once 'core/model.php';

$model = new Model();
echo json_encode($model->query('SELECT * FROM user'));

?>