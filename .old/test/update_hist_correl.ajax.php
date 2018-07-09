<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();

$corrTag = $_POST['ajax'] ?? NULL;
$ajax = array('data' => array(),'info' => array());


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
    'h_id' => str_replace('-','',$prettyDate).'.'.$corrTag['s_corr_id'],
    'pretty_date' => $prettyDate,
    'value' => $row['correlation'],
    'fk_id' => $corrTag['s_corr_id']
    );
  
}




if (!isset($data) || count($data) === 0) {
    $ajax['info']['errorMsg'] = 'No data was able to be calculated';
    $ajax['info']['insertedHistData'] = (bool) FALSE;
}
elseif (count($dataVals) === 0) {
    $ajax['info']['errorMsg'] = 'Not enough data for correlation calculations';
    $ajax['info']['insertedHistData'] = (bool) FALSE;
}
else {
    $colNames = array('h_id','pretty_date','value','fk_id');
    $sql -> multipleInsert ('hist_correl',$colNames,$dataVals);
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
                                    'errorMsg' => 'No new data to add',
                                    'insertedHistData' => (bool) FALSE
                                    );
}
else {
    $ajax['info'] = array('rowsChg' => $sql -> successRowsChanged,
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
if ($ajax['info']['insertedHistData'] === TRUE) {
    
    $queryVals = array('obs_end' => $ajax['info']['lastDate'],
                                    'obs_end_val' => $ajax['info']['lastVal'],
                                    'obs_end_input_min' => $ajax['info']['lastFirstInput'],
                                    'obs_count' => $ajax['info']['obsCount'],
                                    's_corr_id' => $corrTag['s_corr_id']
                                    );
    
    if (is_null($corrTag['obs_start']) || strlen($corrTag['obs_start']) <= 0 ) {
        $includeObsStart = 'obs_start=:obs_start,';
        $queryVals['obs_start'] = $ajax['info']['firstDate'];
    } else {
        $includeObsStart = '';
    }
    
    //print_r($series);
    //print_r($ajax['info']);
    //print_r($queryVals);
    $query = "UPDATE tags_correl SET $includeObsStart obs_end=:obs_end, obs_end_val=:obs_end_val, obs_end_input_min=:obs_end_input_min, obs_count=obs_count + :obs_count, last_updated=now() WHERE s_corr_id=:s_corr_id";
    $stmt = $sql->prepare($query);
    $stmt->execute($queryVals);
    
    $ajax['info']['updatedTags'] = (bool) TRUE;
    
    
    $ajax['data'] = $data;
}


echo json_encode($ajax);




