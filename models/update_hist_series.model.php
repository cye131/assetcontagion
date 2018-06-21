<?php

$series = $fromAjax['series'] ?? $fromRouter['series'] ?? NULL;
$uHistSeries = array('data' => array(),'info' => array());



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
$uHistSeries['info']['url'] = $results['url'];

if (isset($results['errorMsg']) && strlen($results['errorMsg']) > 0) {
    $uHistSeries['info']['errorMsg'] = $results['errorMsg'];
    $uHistSeries['info']['insertedHistData'] = (bool) FALSE;
}
else if (!isset($data) || count($data) === 0) {
    $uHistSeries['info']['errorMsg'] = 'No data was able to be retrieved via cURL';
    $uHistSeries['info']['insertedHistData'] = (bool) FALSE;
}
elseif (count($data) === 1 && $data[0]['date'] === $scraper->min_date) { // If the only entry is the min-date, don't update
    $uHistSeries['info']['errorMsg'] = 'No new data to add';
    $uHistSeries['info']['insertedHistData'] = (bool) FALSE;
}
else {
    $colNames = array('date','pretty_date','value','chg','fk_id');
    $sql -> multipleInsert ('hist_series',$colNames,$data);
}




/* Check if historical data insert was successful
 *
 *
 *
 */
if ( isset($uHistSeries['info']['insertedHistData']) && $uHistSeries['info']['insertedHistData'] === FALSE) {
}
elseif ( !isset($sql -> successRowsChanged) || $sql -> successRowsChanged === 0 ) {
    $uHistSeries['info'] = array('rowsChg' => 0,
                                    'errorMsg' => 'Could not update historical data',
                                    'insertedHistData' => (bool) FALSE
                                    );
}
else {
    $uHistSeries['info'] = array('rowsChg' => $sql -> successRowsChanged,
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
if ($uHistSeries['info']['insertedHistData'] === TRUE) {
    
    $queryVals = array('obs_end' => $uHistSeries['info']['lastDate'],'s_id' => $series['s_id']);
    
    if (is_null($series['obs_start']) || strlen($series['obs_start']) <= 0 ) {
        $includeObsStart = 'obs_start=:obs_start,';
        $queryVals['obs_start'] = $uHistSeries['info']['firstDate'];
    } else {
        $includeObsStart = '';
    }
    
    //print_r($series);
    //print_r($uHistSeries['info']);
    //print_r($queryVals);
    $query = "UPDATE tags_series SET $includeObsStart obs_end=:obs_end,last_updated=now() WHERE s_id=:s_id";
    $stmt = $sql->prepare($query);
    $stmt->execute($queryVals);
    
    $uHistSeries['info']['updatedTags'] = (bool) TRUE;
    
    
    $uHistSeries['data'] = $data;
}


//echo json_encode($uHistSeries);
