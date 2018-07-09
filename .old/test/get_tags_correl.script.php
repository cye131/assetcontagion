<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();



$tagsCorrel = $sql -> selectToAssoc("
SELECT *
FROM tags_correl
",'','');

$json = json_encode($tagsCorrel);


$modeldata['script']  = "tagsCorrel=$json";
