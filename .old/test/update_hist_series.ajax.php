<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();

$series = $_POST['ajax'] ?? NULL;
$ajax = array('data' => array(),'info' => array());



/* Passes an array indexed by source type with sub-elements for lookup_code and last_updated
 *
 *
 *
 */
$className = 'DataScraper'.ucfirst($series['source']);
$scraper = new $className($series);
$results = $scraper -> return_results();
$data = $results['data'];



/* Update historical data in SQL
 *
 *
 *
 */
//print_r($data);
//exit();
$ajax['info']['url'] = $results['url'];
if (!isset($data) || count($data) === 0) {
    $ajax['info']['errorMsg'] = 'No data was able to be retrieved via cURL';
    $ajax['info']['insertedHistData'] = (bool) FALSE;
}
elseif (count($data) === 1 && $data[0]['date'] === $scraper->min_date) { // If the only entry is the min-date, don't update
    $ajax['info']['errorMsg'] = 'No new data to add';
    $ajax['info']['insertedHistData'] = (bool) FALSE;
}
else {
    $colNames = array('h_id','date','pretty_date','value','chg','fk_id');
    $sql -> multipleInsert ('hist_series',$colNames,$data);
}




/* Check if historical data insert was successful
 *
 *
 *
 */
if ( isset($ajax['info']['insertedHistData']) && $ajax['info']['insertedHistData'] === FALSE) {
}
elseif ( !isset($sql -> successRowsChanged) || $sql -> successRowsChanged === 0 ) {
    $ajax['info'] = array('rowsChg' => 0,
                                    'errorMsg' => 'Could not update historical data',
                                    'insertedHistData' => (bool) FALSE
                                    );
}
else {
    $ajax['info'] = array('rowsChg' => $sql -> successRowsChanged,
                                    'errorMsg' => '',
                                    'insertedHistData' => (bool) TRUE,
                                    'firstDate' => array_slice($data, 0,1)[0]['date'],
                                    'lastDate' => array_slice($data, -1)[0]['date']
                                    );
} 




/* If so then update the data tags
 *
 *
 *
 */
if ($ajax['info']['insertedHistData'] === TRUE) {
    
    $queryVals = array('obs_end' => $ajax['info']['lastDate'],'s_id' => $series['s_id']);
    
    if (is_null($series['obs_start']) || strlen($series['obs_start']) <= 0 ) {
        $includeObsStart = 'obs_start=:obs_start,';
        $queryVals['obs_start'] = $ajax['info']['firstDate'];
    } else {
        $includeObsStart = '';
    }
    
    //print_r($series);
    //print_r($ajax['info']);
    //print_r($queryVals);
    $query = "UPDATE tags_series SET $includeObsStart obs_end=:obs_end,last_updated=now() WHERE s_id=:s_id";
    $stmt = $sql->prepare($query);
    $stmt->execute($queryVals);
    
    $ajax['info']['updatedTags'] = (bool) TRUE;
    
    
    $ajax['data'] = $data;
}


echo json_encode($ajax);
