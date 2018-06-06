<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
}
$sql = new MyPDO();
ini_set('memory_limit','1024M');

//Get historical data + tag info
$series = $sql -> selectAssoc('hist_fred',['*','CONCAT("fred",fk_tags_id) as concatid'],['concatid','date'],
                              'ORDER BY date ASC'
                              );

$doubles = $sql -> selectAssoc('tags_corr',['*'],'','');
$doubles = $sql -> selectAssoc('tags_corr',['*'],'','');


//(new TestOutput($series)) -> print();
//(new TestOutput($doubles)) -> print();

$result = array();$index = array();
foreach ($doubles as $double) {
  $id1 = $double['sc1'] . $double["fk_{$double['sc1']}_id1"]; 
  $id2 = $double['sc2'] . $double["fk_{$double['sc2']}_id2"];
  
  if (isset($series[$id1]) && isset($series[$id2])) {
    $correlationdata = New CorrelationData ($series[$id1],$series[$id2],[$id1,$id2]);
    $correlationdata->calculateCorrelation(['chg','chg'],30);
    $result[] = $correlationdata->correlationdata;
    $index[] = $correlationdata->correlationindex;
  } else {
    $result[] = array();
    $index[] = array('UPDATED' => (bool) FALSE);
  }
  
}
(new TestOutput($result)) -> print();
(new TestOutput($index)) -> print();


//SQL query for historical data
$datavals = array();
for ($i=0;$i<count($result);$i++) {
  foreach ($result[$i]['timeseries'] as $date=>$row) {
    if ( !isset($row['correlation']) || is_null($row['correlation']) ) continue;
    
    $datavals[] = array(
      'id' => str_replace('-','',$date).'.'.$doubles[$i]['id'],
      'date' => $date,
      'value' => $row['correlation'],
      'fk_tags_id' => $doubles[$i]['id']
      );
    
  }
  
}

$colnames = array('id','date','value','fk_tags_id');
$sql -> multipleInsert('hist_corr',$colnames,$datavals);



//SQL query for tags

$tagData = array();

foreach ($doubles as $n => $double) {  
  if ($index[$n]['UPDATED'] === TRUE ) {
    foreach ($double as $k=>$v) {
      $tagData[$n][$k] = $v;
    }
    $tagData[$n]['lastupdated'] = date('Y-m-d');
    $tagData[$n]['obs_end'] = $index[$n]['correl_mostrecent_date'];
    $tagData[$n]['obs_end_val'] = $index[$n]['correl_mostrecent_val'];
    $tagData[$n]['obs_end_input_min'] = $index[$n]['correl_mostrecent_earliestinput_date'];
    
    if ( !isset($index[$n]['obs_start']) || is_null($index[$n]['obs_start']) ) $tagData[$n]['obs_start'] = $index[$n]['correl_freqago_date'];
  }
}

(new TestOutput($tagData)) -> print();



$tagCols = array('id','naturalid','added_on','lastupdated','obs_start','obs_end','obs_end_val','obs_end_input_min','freq','sc1','sc2','fk_fred_id1','fk_fred_id2','fk_fid_id1','fk_fid_id2','class1','class2');
$sql -> multipleInsert('tags_corr',$tagCols,$tagData);

