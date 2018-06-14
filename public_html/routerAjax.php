<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/classes/" . $classname . '.class.php';
}


//Routes - AJAX
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') exit();

$uri = $_POST['req'] ?? NULL;
$fromAjax = $_POST['ajax'] ?? NULL;

$sql = new MyPDO();
require_once("/var/www/correlation/models/$uri");


