<?php
$corrTag = $fromAjax['corrTag'] ?? NULL;
$uHistCorrel = array('data' => array(),'info' => array());


//print_r($series);

$hist = $sql -> selectToAssoc("
SELECT *
FROM hist_series
WHERE hist_series.fk_id = ? OR hist_series.fk_id = ?
",[$corrTag['fk_id_1'],$corrTag['fk_id_2']],['fk_id','pretty_date']);

if (count($hist) !== 2) echo 'ERROR';

$correlationdata = New CorrelationData ($hist,$corrTag);
$results = $correlationdata->calculateCorrelation();
$data = $results['data'];
$index = $results['index'];

//print_r( $results );


/* SQL query for historical data
 *
 *
 *
 */
 
$dataVals = array();
foreach ($data['timeseries'] as $prettyDate=>$row) {
  if ( !isset($row['correlation']) || is_null($row['correlation']) ) continue;
  
  $dataVals[] = array(
    //'h_id' => str_replace('-','',$prettyDate).'.'.$corrTag['s_corr_id'],
    'pretty_date' => $prettyDate,
    'value' => $row['correlation'],
    'fk_id' => $corrTag['s_corr_id']
    );
  
}




if (!isset($data) || count($data) === 0) {
    $uHistCorrel['info']['errorMsg'] = 'No data was able to be calculated';
    $uHistCorrel['info']['insertedHistData'] = (bool) FALSE;
}
elseif (count($dataVals) === 0) {
    $uHistCorrel['info']['errorMsg'] = 'Not enough data for correlation calculations';
    $uHistCorrel['info']['insertedHistData'] = (bool) FALSE;
}
else {
    $colNames = array(/*'h_id',*/'pretty_date','value','fk_id');
    $sql -> multipleInsert ('hist_correl',$colNames,$dataVals);
}


/* Check if historical data insert was successful
 *
 *
 *
 */
if ( isset($uHistCorrel['info']['insertedHistData']) && $uHistCorrel['info']['insertedHistData'] === FALSE) {
}
elseif ( !isset($sql -> successRowsChanged) || $sql -> successRowsChanged === 0 ) {
    $uHistCorrel['info'] = array('rowsChg' => 0,
                                    'errorMsg' => 'No new data to add',
                                    'insertedHistData' => (bool) FALSE
                                    );
}
else {
    $uHistCorrel['info'] = array('rowsChg' => $sql -> successRowsChanged,
                                    'errorMsg' => '',
                                    'insertedHistData' => (bool) TRUE,
                                    'firstDate' => $index['correl_first_date'],
                                    'lastDate' => $index['correl_last_date'],
                                    'lastVal' => $index['correl_last_val'],
                                    'lastFirstInput' => $index['correl_last_earliestinput_date'],
                                    'obsCount' => $index['correl_count']
                                    );
}






/* If so then update the data tags
 *
 *
 *
 */
if ($uHistCorrel['info']['insertedHistData'] === TRUE) {
    
    $queryVals = array('obs_end' => $uHistCorrel['info']['lastDate'],
                                    'obs_end_val' => $uHistCorrel['info']['lastVal'],
                                    'obs_end_input_min' => $uHistCorrel['info']['lastFirstInput'],
                                    'obs_count' => $uHistCorrel['info']['obsCount'],
                                    's_corr_id' => $corrTag['s_corr_id']
                                    );
    
    if (is_null($corrTag['obs_start']) || strlen($corrTag['obs_start']) <= 0 ) {
        $includeObsStart = 'obs_start=:obs_start,';
        $queryVals['obs_start'] = $uHistCorrel['info']['firstDate'];
    } else {
        $includeObsStart = '';
    }
    
    //print_r($series);
    //print_r($uHistCorrel['info']);
    //print_r($queryVals);
    $query = "UPDATE tags_correl SET $includeObsStart obs_end=:obs_end, obs_end_val=:obs_end_val, obs_end_input_min=:obs_end_input_min, obs_count=obs_count + :obs_count, last_updated=now() WHERE s_corr_id=:s_corr_id";
    $stmt = $sql->prepare($query);
    $stmt->execute($queryVals);
    
    $uHistCorrel['info']['updatedTags'] = (bool) TRUE;
    
    
    $uHistCorrel['data'] = $data;
}


//echo json_encode($uHistCorrel);




