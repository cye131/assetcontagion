<<<<<<< HEAD
<?php

$rFile = basename(__FILE__, '.logic.php').'.r'; 
$rDir = dirname(__FILE__,2).'/rscripts';

$arr = [1,2,3,4,5,6,7,8,9,10];
$arr2 = [1,3,5,1,6,4,7,1,2,3];

$toR = [
    'y1' => $arr,
    'y2' => $arr2
    ];

$shellArgs = escapeshellarg(json_encode($toR));

$sh = exec("Rscript $rDir/$rFile 2>&1 $rDir $shellArgs");


$res = json_decode($sh,TRUE);
=======
<?php

$rFile = basename(__FILE__, '.logic.php').'.r'; 
$rDir = dirname(__FILE__,2).'/rscripts';

$arr = [1,2,3,4,5,6,7,8,9,10];
$arr2 = [1,3,5,1,6,4,7,1,2,3];

$toR = [
    'y1' => $arr,
    'y2' => $arr2
    ];

$shellArgs = escapeshellarg(json_encode($toR));

$sh = exec("Rscript $rDir/$rFile 2>&1 $rDir $shellArgs");


$res = json_decode($sh,TRUE);
>>>>>>> 975553768a294c5739879bca2697957b736e6203
echo $res['MIC'];