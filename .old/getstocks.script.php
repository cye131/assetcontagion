<?php
/*
function __autoload($class_name) {
    require_once "/var/www/correlation/public_html/correlations/classes/" . $class_name . '.class.php';
}*/

/** PULLS STOCK DATA WITH CORRESPONDING SECTOR INFO FROM DB
 *
 *
 *
 */

$sql = new MyPDO();

//include the extra question mark for the $firststockdate
$stmt = $sql->prepare("
                   SELECT * FROM `fid_tickers_stocks` ORDER BY `ticker`
                  ");


$stmt->execute();

$data = array();
//$data['lookup_array'] = array();
//$data['jqueryui_array'] = array();

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


$modeldata['script'] = 'stockjson = '.json_encode($data[0]).';autofill = '.json_encode($data[1]).'';
?>