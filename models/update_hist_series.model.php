<?php

$series = $fromAjax['series'] ?? NULL;
$histSeries = array('data' => array(),'info' => array());



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
$histSeries['info']['url'] = $results['url'];
if (!isset($data) || count($data) === 0) {
    $histSeries['info']['errorMsg'] = 'No data was able to be retrieved via cURL';
    $histSeries['info']['insertedHistData'] = (bool) FALSE;
}
elseif (count($data) === 1 && $data[0]['date'] === $scraper->min_date) { // If the only entry is the min-date, don't update
    $histSeries['info']['errorMsg'] = 'No new data to add';
    $histSeries['info']['insertedHistData'] = (bool) FALSE;
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
if ( isset($histSeries['info']['insertedHistData']) && $histSeries['info']['insertedHistData'] === FALSE) {
}
elseif ( !isset($sql -> successRowsChanged) || $sql -> successRowsChanged === 0 ) {
    $histSeries['info'] = array('rowsChg' => 0,
                                    'errorMsg' => 'Could not update historical data',
                                    'insertedHistData' => (bool) FALSE
                                    );
}
else {
    $histSeries['info'] = array('rowsChg' => $sql -> successRowsChanged,
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
if ($histSeries['info']['insertedHistData'] === TRUE) {
    
    $queryVals = array('obs_end' => $histSeries['info']['lastDate'],'s_id' => $series['s_id']);
    
    if (is_null($series['obs_start']) || strlen($series['obs_start']) <= 0 ) {
        $includeObsStart = 'obs_start=:obs_start,';
        $queryVals['obs_start'] = $histSeries['info']['firstDate'];
    } else {
        $includeObsStart = '';
    }
    
    //print_r($series);
    //print_r($histSeries['info']);
    //print_r($queryVals);
    $query = "UPDATE tags_series SET $includeObsStart obs_end=:obs_end,last_updated=now() WHERE s_id=:s_id";
    $stmt = $sql->prepare($query);
    $stmt->execute($queryVals);
    
    $histSeries['info']['updatedTags'] = (bool) TRUE;
    
    
    $histSeries['data'] = $data;
}


//echo json_encode($histSeries);
