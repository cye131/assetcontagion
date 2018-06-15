<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/classes/" . $classname . '.class.php';
}


//Routes - AJAX
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') exit();

$model = $_POST['model'] ?? NULL;
$logic = $_POST['logic'] ?? NULL;
$fromAjax = $_POST['fromAjax'] ?? NULL;
$toScript = $_POST['toScript'] ?? NULL;



//Send request
if (isset($model)) {
  $sql = new MyPDO();
  foreach ($model as $m) {
    require_once("/var/www/correlation/models/$m.model.php");
    }
}

if (isset($logic)) {
  foreach ($logic as $l) {
    require_once("/var/www/correlation/models/$l.logic.php");
  }
}

if (isset($toScript)) {
  $res = [];
  foreach ($toScript as $varName) {
    $res[$varName] = (${$varName}); 
  }
  
  echo json_encode($res);
}
else echo 'No requested variables from AJAX!';


