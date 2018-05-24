<?php
function __autoload($class_name) {
    require_once "classes/" . $class_name . '.class.php';
}
require_once('functions.php');

require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');

/** PULLS STOCK DATA WITH CORRESPONDING SECTOR INFO FROM DB
 *
 *
 *
 */

$sql = new MyPDO('mysql:dbname='.DB_DATABASE,DB_USER,DB_PASSWORD);
//include the extra question mark for the $firststockdate
$stmt = $sql->prepare("
                   SELECT * FROM `sector_stocksectormatch` ORDER BY `ticker`
                  ");


$stmt->execute();

$data = array();
$data['lookup_array'] = array();
$data['jqueryui_array'] = array();

$i = (int) 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[0][$row['ticker']] = array (
        'ticker' => $row['ticker'],
        'sector' => $row['sector'],
        'industry' => $row['industry'],
        'subindustry' => $row['subindustry'],
        'name' => $row['name']
    );

    $data[1][$i] = array (
        'label' => $row['name'] .' ('. $row['ticker']. ')',
        'value' => $row['ticker']
    );
    
    $i++;
    
}



echo json_encode($data);    


?>