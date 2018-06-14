<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();


$tagsFid = $sql->selectToAssoc("
SELECT * FROM tags_fid
",'','lookup_code');

(new TestOutput($tagsFid)) -> print();




//Seperate by every 10 rows
$i = (int) 0; $j = (int) 0;
$groupsOfFive = array();

foreach ($tagsFid as $lookupCode => $tag) {
    if ( ($i%5) === 0 && $i!== 0) {//every 10th row, create a new $urlstr subarray
        $j++;
    }
    $groupsOfFive[$j][] = $tag;
    $i++;
}


(new TestOutput($groupsOfFive)) -> print();



//Pulls data via curl
foreach ($groupsOfFive as $fiveSecurities) {
    $fidData = new FidData($fiveSecurities,0);
    $fidData->fetchData('1980/01/01',null);
    $fidData->cleanAndDecodeData();
    $fidData->createTSArrayFromJsonArray();
    
    (new TestOutput($fidData->tsarray)) -> print();

    $colnames = array('id','date','value','chg','fk_tags_id');  
    $sql -> multipleInsert ('hist_fid',$colnames,$fidData->tsarray);
}