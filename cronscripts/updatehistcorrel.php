<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once __DIR__."/../classes/$classname.class.php";
}
require_once __DIR__.'/../vendor/autoload.php';

$sql = new MyPDO();


require_once(__DIR__.'/../models/get_tags_correl.model.php');
//print_r($tagsCorrel);


for ($i=0;$i<count($tagsCorrel);$i++) {
    if ($tagsCorrel[$i]['corr_type'] != 'mic') continue;
    $fromRouter['corrTag'] = $tagsCorrel[$i];
    require(__DIR__.'/../models/update_hist_correl.model.php');
    if ($uHistCorrel['info']['insertedHistData'] === true) {
        echo "Successful ($i): updated {$uHistCorrel['info']['rowsChg']} rows from {$uHistCorrel['info']['firstDate']} to {$uHistCorrel['info']['lastDate']}";
    } else {
        echo "Failed ($i): {$uHistCorrel['info']['errorMsg']}";
    }
    echo "\n";
}

