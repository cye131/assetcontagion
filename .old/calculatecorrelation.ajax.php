<?php
require_once('functions.php');

/** CREATES FINAL ARRAY FOR BOTH CSV AND JSON FEED
 * $correlation -> comparison='stock-industry','historicaldata'->...
 * 
 *
 */
 //print_r($_POST);
 $data = json_decode($_POST['ajax'],true);
 $historicaldata = ($data['historicaldata']);
 //print_r($historicaldata);
 $namesandtickers = $data['namesandtickers'];

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