<?PHP
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();

ini_set('memory_limit','1024M');



/* Get existing correlation pairs if one of the pairs is equal to requestedSymbol
 *
 */
$requestedSymbol = (string) $_GET['symbol'] ?? null;


$tagsCorr = $sql -> selectToAssoc("
SELECT
  t0.*,
  CONCAT(IFNULL(fred1.lookup_code,''),IFNULL(fid1.lookup_code,'')) AS 1_lookup_code,
  CONCAT(IFNULL(fred2.lookup_code,''),IFNULL(fid2.lookup_code,'')) AS 2_lookup_code,
  CONCAT(IFNULL(fred1.id,''),IFNULL(fid1.id,'')) AS id1,
  CONCAT(IFNULL(fred2.id,''),IFNULL(fid2.id,'')) AS id2
FROM tags_corr AS t0

LEFT JOIN tags_fred fred1
ON CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,''))= CONCAT('fred',fred1.id)

LEFT JOIN tags_fred fred2
ON CONCAT(t0.sc2,IFNULL(t0.fk_fred_id2,''),IFNULL(t0.fk_fid_id2,''))= CONCAT('fred',fred2.id)

LEFT JOIN tags_fid fid1
ON CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,''))= CONCAT('fid',fid1.id)

LEFT JOIN tags_fid fid2
ON CONCAT(t0.sc2,IFNULL(t0.fk_fred_id2,''),IFNULL(t0.fk_fid_id2,''))= CONCAT('fid',fid2.id)

HAVING (1_lookup_code = :requestedSymbol OR 2_lookup_code = :requestedSymbol)

",array('requestedSymbol' => $requestedSymbol),"");

if (count($tagsCorr) === 0) {
  echo 'Non-existent symbol';
  exit();
}
(new TestOutput($tagsCorr)) -> print();


/* Wrangle out other symbols needed to calculate all correlation series related to $requestedSymbol
 *
 */
$lookupSymbols = array();
$lastUpdated = array();
foreach ($tagsCorr as $tag) {
  $lookupSymbols[$tag['sc1']][] = $tag['id1'];
  $lookupSymbols[$tag['sc2']][] = $tag['id2'];
  $lastUpdated[$tag['sc1'].$tag['id1']][] = isset($tag['lastupdated']) ? strtotime($tag['lastupdated']) : null;
  $lastUpdated[$tag['sc2'].$tag['id2']][] = isset($tag['lastupdated']) ? strtotime($tag['lastupdated']) : null;
}
foreach ($lookupSymbols as $sourceName => $sourceRow) {
  $querySymbols[$sourceName] = array_unique($sourceRow);
}
unset($lookupSymbols);


/* Create query for historical data
 *
 */
 (new TestOutput($lastUpdated)) -> print();
 (new TestOutput($querySymbols)) -> print();

 $queryBase = array();
 $dataToInsert = array();
 foreach ($querySymbols as $sourceName => $codes) {
    $qMarks = MyPDO::CreateQMarks($codes);
    $whereQuery = array();
    
    foreach ($codes as $code) {
      $dataToInsert[] = $code;
      echo  isset($lastUpdated[$sourceName.$code]);
      echo $lastUpdated[$sourceName.$code] !== NULL ;
            
      if( isset($lastUpdated[$sourceName.$code]) && !empty($lastUpdated[$sourceName.$code]) ) {
        $whereQuery[$code] = "(fk_tags_id = ? AND date>= ?)";
        $dataToInsert[] = date('Y-m-d',min($lastUpdated[$sourceName.$code]));
      } else {
        $whereQuery[$code] = "(fk_tags_id = ?) ";
      }
    }
    $whereStr = implode(' OR ',$whereQuery);
    
    $queryBase[$sourceName] = "
  (SELECT *, CONCAT('$sourceName',fk_tags_id) AS concatid
  FROM hist_$sourceName
  WHERE $whereStr
  )
    ";
 }
 
$query = implode(' UNION ',$queryBase);
 echo $query;
 (new TestOutput($dataToInsert)) -> print();

 
$seriesData = $sql -> selectToAssoc($query,$dataToInsert,array('concatid','date'));
//(new TestOutput($seriesData)) -> print();

if (count($seriesData) === 0) {
 (new TestOutput('No new data to output')) -> print();
  exit();  
}




/* Run calculations for correlation
 *
 */
//print_r(array_keys($seriesData));

$result = array();$index = array();
foreach ($tagsCorr as $double) {
  $id1 = $double['sc1'] . $double["fk_{$double['sc1']}_id1"]; 
  $id2 = $double['sc2'] . $double["fk_{$double['sc2']}_id2"];
  echo $id1.$id2.'<br>';
  if (isset($seriesData[$id1]) && isset($seriesData[$id2])) {
    $correlationdata = New CorrelationData ($seriesData[$id1],$seriesData[$id2],[$id1,$id2]);
    $correlationdata->calculateCorrelation(['chg','chg'],30);
    $result[] = $correlationdata->correlationdata;
    $index[] = $correlationdata->correlationindex;
  } else {
    $result[] = array();
    $index[] = array('UPDATED' => (bool) FALSE,
                     'ERRINFO' => (string) 'No Series Data'
                     );
  }
  
}
//(new TestOutput($result)) -> print();
(new TestOutput($index)) -> print();


//SQL query for historical data
$datavals = array();
for ($i=0;$i<count($result);$i++) {
  if (!isset($result[$i]['timeseries'])) continue; 
  foreach ($result[$i]['timeseries'] as $date=>$row) {
    if ( !isset($row['correlation']) || is_null($row['correlation']) ) continue;
    
    $datavals[] = array(
      'id' => str_replace('-','',$date).'.'.$tagsCorr[$i]['id'],
      'date' => $date,
      'value' => $row['correlation'],
      'fk_tags_id' => $tagsCorr[$i]['id']
      );
    
  }
}

$colnames = array('id','date','value','fk_tags_id');
if (count($datavals) > 0 ) $sql -> multipleInsert('hist_corr',$colnames,$datavals);

//SQL query for tags
$tagData = array();

foreach ($tagsCorr as $n => $double) {  
  if ($index[$n]['UPDATED'] === TRUE ) {
    foreach ($double as $k=>$v) {
      if ($k === 'id1' || $k === 'id2' || $k === '1_lookup_code' || $k === '2_lookup_code') continue;
      $tagData[$n][$k] = $v;
    }
    $tagData[$n]['lastupdated'] = date('Y-m-d H:i:s');
    $tagData[$n]['obs_end'] = $index[$n]['correl_mostrecent_date'];
    $tagData[$n]['obs_end_val'] = $index[$n]['correl_mostrecent_val'];
    $tagData[$n]['obs_end_input_min'] = $index[$n]['correl_mostrecent_earliestinput_date'];
    
    if ( !isset($index[$n]['obs_start']) || is_null($index[$n]['obs_start']) ) $tagData[$n]['obs_start'] = $index[$n]['correl_freqago_date'];
  }
}

(new TestOutput($tagData)) -> print();



$tagCols = array('id','naturalid','added_on','lastupdated','obs_start','obs_end','obs_end_val','obs_end_input_min','freq','sc1','sc2','fk_fred_id1','fk_fred_id2','fk_fid_id1','fk_fid_id2','class1','class2');
if (count($tagData) > 0) $sql -> multipleInsert('tags_corr',$tagCols,$tagData);

