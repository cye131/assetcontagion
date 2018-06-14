<?PHP
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();


/* Get existing correlation-pairs
 *
 */
$existingdoubles = $sql -> selectToAssoc("
SELECT id,CONCAT(sc1,IFNULL(fk_fred_id1,''),IFNULL(fk_fid_id1,'')) AS concatid1,CONCAT(sc2,IFNULL(fk_fred_id2,''),IFNULL(fk_fid_id2,'')) AS concatid2
FROM tags_corr
",'','');

(new TestOutput($existingdoubles))->print();


/* Get raw series with which to find correlation-pairs with
 *
 */
$series = $sql -> selectToAssoc("
SELECT t1.*,'fred' AS source,CONCAT('fred',id) AS concatid
FROM tags_fred AS t1

UNION

SELECT t2.*,'fid' AS source,CONCAT('fid',id) AS concatid
FROM tags_fid AS t2
",'','naturalid');
                                    
ksort($series); //Need to be alphabetically-ordered
(new TestOutput($series))->print();


/* Calculates correlation-pairs and places it into $sqldata
 *
 */
$serieskeys = array_column($series,'naturalid');
$doubles = (array) CorrelationData::getDupletCombinations($serieskeys); //Preserves alphabetical ordering

$sqldata = array();
$i = (int) 0;
foreach ($doubles as $double) {
    if ($series[$double[0]]['freq'] !== $series[$double[1]]['freq']) continue; //Skip if the two series have disequal frequencies
    
    $repeat = (bool) false;
    foreach ($existingdoubles as $old) {
      if ( ($series[$double[0]]['concatid'] === $old['concatid1'] && $series[$double[1]]['concatid'] === $old['concatid2']) ||
           ($series[$double[1]]['concatid'] === $old['concatid1'] && $series[$double[0]]['concatid'] === $old['concatid2'])
         ) {
        $repeat = (bool) true;break;
        }
    }
    if ($repeat === true) continue;
    
    
    $sqldata[$i] = array(
        'naturalid' => $series[$double[0]]['naturalid'] .'.'. $series[$double[1]]['naturalid'],
        'freq' => $series[$double[0]]['freq'],
        'sc1' => $series[$double[0]]['source'],
        'sc2' => $series[$double[1]]['source'],        
    );
    
    if ($series[$double[0]]['source'] === 'fred') {
        $sqldata[$i]['fk_fred_id1'] = $series[$double[0]]['id'];
        $sqldata[$i]['fk_fid_id1'] = NULL;   
    } elseif ($series[$double[0]]['source'] === 'fid') {
        $sqldata[$i]['fk_fred_id1'] = NULL; 
        $sqldata[$i]['fk_fid_id1'] = $series[$double[0]]['id'];  
    }
    
    if ($series[$double[1]]['source'] === 'fred') {
        $sqldata[$i]['fk_fred_id2'] = $series[$double[1]]['id'];
        $sqldata[$i]['fk_fid_id2'] = NULL;   
    } elseif ($series[$double[1]]['source'] === 'fid') {
        $sqldata[$i]['fk_fred_id2'] = NULL; 
        $sqldata[$i]['fk_fid_id2'] = $series[$double[1]]['id'];  
    }

    $sqldata[$i]['class1'] = $series[$double[0]]['class'];
    $sqldata[$i]['class2'] = $series[$double[1]]['class'];
    
    $sqldata[$i]['added_on'] = date('Y-m-d');

    $i++;
}


/* Insert into SQL
 *
 */


if ( count($sqldata) > 0) {

  (new TestOutput($sqldata))->print();

  $colnames = array('naturalid','freq','sc1','sc2','fk_fred_id1','fk_fid_id1','fk_fred_id2','fk_fid_id2','class1','class2','added_on');

  $sql -> multipleInsert('tags_corr',$colnames,$sqldata);
  
  
}
