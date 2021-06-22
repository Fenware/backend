index
<?php

include_once 'core/model.php';

$model = new Model();
echo '<pre>';
print_r($model->query('SELECT * FROM user'));
echo '</pre>';
?>