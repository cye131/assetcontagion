<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();


/*  Overlap the table w/ other data sources
  SELECT
  t0.id,t0.naturalid,t0.added_on,t0.lastupdated,t0.obs_start,t0.obs_end,t0.obs_end_val,t0.obs_end_input_min,t0.freq,CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,'')) AS concatid1, CONCAT(sc2,IFNULL(fk_fred_id2,''),IFNULL(fk_fid_id2,'')) AS concatid2,
  
  CONCAT(IFNULL(fred1.id,''),IFNULL(fid1.id,'')) AS 1_id,
  CONCAT(IFNULL(fred2.id,''),IFNULL(fid2.id,'')) AS 2_id,
  
  CONCAT(IFNULL(fred1.lookup_code,''),IFNULL(fid1.lookup_code,'')) AS 1_lookup_code,
  CONCAT(IFNULL(fred2.lookup_code,''),IFNULL(fid2.lookup_code,'')) AS 2_lookup_code,
  
  CONCAT(IFNULL(fred1.class,''),IFNULL(fid1.class,'')) AS 1_class,
  CONCAT(IFNULL(fred2.class,''),IFNULL(fid2.class,'')) AS 2_class,
  
  CONCAT(IFNULL(fred1.title,''),IFNULL(fid1.title,'')) AS 1_title,
  CONCAT(IFNULL(fred2.title,''),IFNULL(fid2.title,'')) AS 2_title,
  
  CONCAT(IFNULL(fred1.subtitle,''),IFNULL(fid1.subtitle,'')) AS 1_subtitle,
  CONCAT(IFNULL(fred2.subtitle,''),IFNULL(fid2.subtitle,'')) AS 2_subtitle,
  
  CONCAT(IFNULL(fred1.freq,''),IFNULL(fid1.freq,'')) AS 1_freq,
  CONCAT(IFNULL(fred2.freq,''),IFNULL(fid2.freq,'')) AS 2_freq,
  
  CONCAT(IFNULL(fred1.units,''),IFNULL(fid1.units,'')) AS 1_units,
  CONCAT(IFNULL(fred2.units,''),IFNULL(fid2.units,'')) AS 2_units,
  
  CONCAT(IFNULL(fred1.units,''),IFNULL(fid1.units,'')) AS 1_units,
  CONCAT(IFNULL(fred2.units,''),IFNULL(fid2.units,'')) AS 2_units,
  
  CONCAT(IFNULL(fred1.units,''),IFNULL(fid1.units,'')) AS 1_units,
  CONCAT(IFNULL(fred2.units,''),IFNULL(fid2.units,'')) AS 2_units
  
  FROM `tags_corr` t0
  
  LEFT JOIN tags_fred fred1
  ON CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,''))= CONCAT('fred',fred1.id)
  
  LEFT JOIN tags_fred fred2
  ON CONCAT(t0.sc2,IFNULL(t0.fk_fred_id2,''),IFNULL(t0.fk_fid_id2,''))= CONCAT('fred',fred2.id)
  
  LEFT JOIN tags_fid fid1
  ON CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,''))= CONCAT('fid',fid1.id)
  
  LEFT JOIN tags_fid fid2
  ON CONCAT(t0.sc2,IFNULL(t0.fk_fred_id2,''),IFNULL(t0.fk_fid_id2,''))= CONCAT('fid',fid2.id)

  ORDER BY 1_class,t0.id
*/

$freq = $_GET['freq'] ?? 'd';

$colSel = ['id','lookup_code','class','title','subtitle','freq','units','obs_start','obs_end'];
$sources = ['fred','fid'];
$q = MyPDO::makeQueryTagsCorr($colSel,$sources);

$query = "
SELECT
t0.id,t0.naturalid,t0.added_on,t0.lastupdated,t0.obs_start,t0.obs_end,t0.obs_end_val,t0.obs_end_input_min,t0.freq,{$q['selSel']}
{$q['querySel']}
FROM `tags_corr` t0
{$q['joinSel']}
WHERE t0.freq = :freq
ORDER BY 1_class,t0.id
";

$tagsCorrRaw = $sql->selectToAssoc($query,array('freq'=>$freq),'');

$tagsCorr = array();

foreach ($tagsCorrRaw as $tagName => $tagRow) {
    foreach ($tagRow as $tagKey => $tagValue) {
        if (substr($tagKey,0,2) == '1_' ||  substr($tagKey,0,2) == '2_') {
            $tagsCorr[$tagName]['series'][substr($tagKey,2)][(int) substr($tagKey,0,2)] = $tagValue;
        } else {
            $tagsCorr[$tagName][$tagKey] = $tagValue;
        }
    }
}

(new TestOutput($tagsCorr))->print();




$tagsDataRaw= $sql->selectToAssoc("
SELECT * FROM (

SELECT t1.*,'fred' AS source,CONCAT('fred',id) AS concatid
FROM tags_fred AS t1

UNION

SELECT t2.*,'fid' AS source,CONCAT('fid',id) AS concatid
FROM tags_fid AS t2

) AS a

WHERE a.freq = :freq
ORDER BY a.class ASC
",array('freq'=>$freq),'');


(new TestOutput($tagsDataRaw))->print();

//Creates array for heatmap
$hmData = array();
$hmInfo = array();

$i = (int) 0;
for ($x=0;$x<count($tagsDataRaw);$x++) {
for ($y=0;$y<count($tagsDataRaw);$y++) {
    
    $hmData[$i] = array(
        'x-name' => $tagsDataRaw[$x]['title'],
        'y-name' => $tagsDataRaw[$y]['title'],
        'x' => $x,
        'y' => $y
    );
    $concatids = $tagsDataRaw[$x]['concatid'].$tagsDataRaw[$y]['concatid'];
    foreach ($tagsCorr as $tagRow) {
        if ( ($tagRow['concatid1'] === $tagsDataRaw[$x]['concatid'] && $tagRow['concatid2'] === $tagsDataRaw[$y]['concatid']) ||
             ($tagRow['concatid1'] === $tagsDataRaw[$y]['concatid'] && $tagRow['concatid2'] === $tagsDataRaw[$x]['concatid'])
           ) {
            $hmData[$i]['value'] = $tagRow['obs_end_val'];
            $hmData[$i]['obs_start'] = $tagRow['obs_start'];
            $hmData[$i]['obs_end'] = $tagRow['obs_end'];
            $hmData[$i]['freq'] = $tagRow['freq'];
            $hmData[$i]['lastupdated'] = $tagRow['lastupdated'];
        }
    }

    $i++;
}
}
(new TestOutput($hmData))->print();


//creates colors + tooltip info for heatmap points
function colorFormat($v) {
  //$o = round(sqrt(abs($v)),1);
  if ($v <= 0) $maxColor = [0,0,255,1];
  else $maxColor = [255,0,0,1];
  $minColor = [251,250,182,0];

  for($i=0;$i<=3;$i++) {
    $rgba[$i] = round(($maxColor[$i] - $minColor[$i]) * abs($v),2) + $minColor[$i];
  }

  
  return 'rgba('.implode(',',$rgba).')';
}

foreach ($hmData as $i => $row) {
  if (!isset($row['value']) ) {
    $hmData[$i]['color'] = 'grey';
    $hmData[$i]['tooltip'] = (bool) FALSE;
  } else {
    $hmData[$i]['color'] = colorFormat($row['value']);
    $hmData[$i]['tooltip'] = (bool) TRUE;
  }
}

$values = array_column($hmData,'value');
$hmInfo['min'] = (float) min($values);
$hmInfo['max'] = (float) max($values);

//creates column names for heatmap
$hmInfo['titles'] = array_column($tagsDataRaw,'title');


//creates array indexing colors to classes
$hmInfo['colorarray'] = array();
$colors = ["#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1"];

$i = (int) 0;
foreach ($tagsDataRaw as $tag) {
  if ( in_array($tag['class'],array_keys($hmInfo['colorarray'])) ) continue;
  $hmInfo['colorarray'][$tag['class']] = $colors[$i];
  $i++;
}

$modeldata['script'] = ''
                        .'var tags =' .json_encode($tagsDataRaw) .';'
                        .'var heatMapData = new Array();'
                        .'heatMapData.data=' .json_encode($hmData).';'
                        .'heatMapData.info=' .json_encode($hmInfo).';';



//(new TestOutput($tagsDataRaw))->print();
