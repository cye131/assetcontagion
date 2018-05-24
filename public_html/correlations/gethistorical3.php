<?php
function __autoload($class_name) {
    require_once "classes/" . $class_name . '.class.php';
}
require_once('functions.php');

require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');



/** STORES POST VARIABLES INTO $NAMEANDTICKERS
 *
 *
 *
 */
$namesandtickers = array();
$sqllookuplist = array();
$questionmarks = array();
$classification_type = (string) 'gics';

foreach($_POST as $key => $row){
    $namesandtickers[$key] = $row;
    if ($key !== 'stock') {
        if ($key !== 'market') $sqllookuplist[] = $classification_type.'_'.$row['lookup_code'];
        else $sqllookuplist[] = $row['lookup_code'];
        $questionmarks[] = '?';
    }
}
//print_r($namesandtickers);





/** PULLS STOCK DATA FROM FIDELITY WEBSITE
 *
 *
 *
 */
$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 0);

$headers = array(
    'Host: fastquote.fidelity.com',
    'Referer: https://eresearch.fidelity.com/eresearch/markets_sectors/sectors/sectors_in_market.jhtml',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36'
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$url = 'https://fastquote.fidelity.com/service/historical/chart/lite/json?productid=research&symbols='.$namesandtickers['stock']['lookup_code'].'&dateMin=2010/01/01:00:00:00&dateMax='.date('Y-m-d').':00:00:00&intraday=n&granularity=1&incextendedhours=n&dd=y';
//echo $url;

curl_setopt($ch, CURLOPT_URL, $url);
$html = curl_exec($ch);
curl_close($ch);

//echo $html;

$clean = substr($html,0,-2); //remove opening and closing ( in data
$clean = substr($clean,2);
$data = json_decode($clean,true);
$data = $data['SYMBOL'][0];


if (!isset($data)) exit();
//print_r($data);

$firststockdate = fid_date($data['BARS']['CB'][0]['lt']);





/** PULLS HISTORICAL DATA FOR INDICES OF RELEVANT SECTORS FROM DATABASE
 *
 *
 *
 */

$sql = new MyPDO('mysql:dbname='.DB_DATABASE,DB_USER,DB_PASSWORD);
//include the extra question mark for the $firststockdate
$stmt = $sql->prepare("
                   SELECT * FROM `sector_indexhistoricaldata` WHERE `classification_id` IN 
                   (".implode(',',$questionmarks).") 
                   AND `date` >= ?
                   ORDER BY `classification_id`,`date`
                  ");

$i = (int) 0;
$sqllookuplist[] = $firststockdate; //Add firststockdate to list of binded variables
foreach($sqllookuplist as $bind){
   $stmt->bindValue(++$i, $bind);
}


$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (strpos($row['classification_id'], '_') === false) $classification_id = (string) $row['classification_id']; //leaves .SPX alone
    else $classification_id = (string) substr($row['classification_id'],strpos($row['classification_id'],'_')+1); //strips gics from gics_.GSPCT
    
    $historicaldata[$classification_id][$row['date']] = array (
        'close' => $row['close'],
        'roi' => $row['roi'],
    );
}

if (empty($historicaldata)) (New SendJsonErrorAndExit('Error 0403: SQL Historical Data Lookup is Empty'))->sendJsonErrorAndExit();





/** MOVES CURL DATA INTO SAME FORMAT AND ARRAY AS SQL DATA AND CALCULATES ROI
 *
 *
 *
 */
foreach ($data['BARS']['CB'] as $key => $row) {
    $date = (string) fid_date($row['lt']);
    $historicaldata[$data['IDENTIFIER']][$date]['close'] = $row['cl'];
    
    if ($key > 0) $historicaldata[$data['IDENTIFIER']][$date]['roi'] = (float) round(($row['cl']-$lastprice)/$lastprice,4);
    else $historicaldata[$data['IDENTIFIER']][$date]['roi'] = (float) 0;
    $lastprice = (float) $row['cl']; 
}
 
unset($data);
//print_r($historicaldata);



/** CREATES FINAL ARRAY FOR BOTH CSV AND JSON FEED
 * $correlation -> comparison='stock-industry','historicaldata'->...
 * 
 *
 */

 $correlation = array('index' => array());
 
 function createCorrelArray($type1 = 'stock',$type2 = 'market',$index = 0) {
    
    global $correlation;
    global $historicaldata;
    global $namesandtickers;
    
    $lookup_codes = array($namesandtickers[$type1]['lookup_code'],$namesandtickers[$type2]['lookup_code']);

    
    //creates the index sub-array
    $correlation['index'][$index] = array(
        'types' => array($type1,$type2),
        'names' => array($namesandtickers[$type1]['name'],$namesandtickers[$type2]['name']),
        'lookup_codes' => $lookup_codes
    );
    
    //calculates first shared date shared date between the 2 data sets
    $firstdates = array();
    $thirtiethdates = array();
    $types = array($type1,$type2);
    for ($i = 0; $i <= 1; $i++) {
        $firstdates[] = strtotime(array_keys($historicaldata[$lookup_codes[$i]])[0]);
    }
    $correlation['index'][$index]['firstshareddate'] = date('Y-m-d',max($firstdates));


    //creates the historical data sub-arrays
    $correlation[$index]['json_data1'] = array();
    $correlation[$index]['json_data2'] = array();
    $correlation[$index]['json_correlation'] = array();

    $correlation[$index]['csv_data'] = array(); //this will index both data1 and data2 under the same date to allow us to check if both data points exist on the same days

    foreach ($historicaldata as $id => $tsdata) {
        if (!in_array($id,$lookup_codes)) continue;
        
        if ($id === $lookup_codes[0]) $datalevel = 'data1';
        elseif ($id === $lookup_codes[1]) $datalevel = 'data2';
        
        //$datalevel_l30 = (string) $datalevel.'_l30';


        $i = (int) 0;
        
        //puts the data into the sub-arrays
        foreach ($tsdata as $tsdate => $tsrow) {
            $correlation[$index]['json_'.$datalevel][$i][0] = (integer) strtotime($tsdate) * 1000; //Needs to be *1000 for Javascript to read
            $correlation[$index]['json_'.$datalevel][$i][1] = (float) $tsrow['close'];

            $correlation[$index]['csv_data'][$tsdate]['date'] = (string) $tsdate;
            $correlation[$index]['csv_data'][$tsdate][$datalevel.'_price'] = (float) $tsrow['close'];

            $correlation[$index]['csv_data'][$tsdate][$datalevel.'_roi'] = (float) $tsrow['roi'];
                        
            $i++;
        }
        
    }
    
    
    //calculates correlation checking that both data points exist using the csv_data subarray
    $i = (int) 0;
    $data1_l30 = array();
    $data2_l30 = array();

    foreach ($correlation[$index]['csv_data'] as $date => $csvrow) {
        if (!isset($csvrow['data1_roi']) || !isset($csvrow['data2_roi'])) continue;
                
        $data1_l30[] = $csvrow['data1_roi'];
        $data2_l30[] = $csvrow['data2_roi'];

        if  (count($data1_l30) > 30) {
            array_shift($data1_l30);
            array_shift($data2_l30);
        }
        
        if  (count($data1_l30) === 30) { //adds correlation to buy csv and json data
            $correlation[$index]['csv_data'][$date]['correlation'] = (float) round(correlation($data1_l30,$data2_l30),4);
            $correlation[$index]['json_correlation'][$i][0] = (integer) strtotime($date) * 1000; 
            $correlation[$index]['json_correlation'][$i][1] = $correlation[$index]['csv_data'][$date]['correlation'];
            
            $mostrecentcorrelation = (float) $correlation[$index]['csv_data'][$date]['correlation'];
            
            $i++;
        }
        
        /* For debugging
        $correlation[$index]['csv_data'][$date]['data1_l30'] = $data1_l30;
        $correlation[$index]['csv_data'][$date]['data2_l30'] = $data2_l30;
        */
    }
    
    //Add most recent correlation to the index
    $correlation['index'][$index]['lastcorrelation'] = $mostrecentcorrelation;
    

 }
 
createCorrelArray('stock','industry',0);
createCorrelArray('stock','sector',1);
createCorrelArray('stock','market',2);
createCorrelArray('industry','sector',3);
createCorrelArray('industry','market',4);
createCorrelArray('market','sector',5);

//print_r($correlation);

echo json_encode($correlation);    

 
//file_put_contents('debug.json',json_encode($correlation));    



 
?>