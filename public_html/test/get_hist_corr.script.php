<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();


/*  Overlap the table w/ other data sources
                      fid1.id as 1_id, fid1.lookup_code as 1_lookup_code,fid1.class as 1_class,fid1.title as 1_title,fid1.subtitle as 1_subtitle,fid1.freq as 1_freq,fid1.units as 1_units,fid1.obs_start as 1_obs_start,fid1.obs_end as 1_obs_end,
                      
                      fred2.id as 2_id, fred2.lookup_code as 2_lookup_code,fred2.class as 2_class,fred2.title as 2_title,fred2.subtitle as 2_subtitle,fred2.freq as 2_freq,fred2.units as 2_units,fred2.obs_start as 2_obs_start,fred2.obs_end as 2_obs_end,
                      
                      LEFT JOIN tags_fid fid1
                      ON CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,''))= CONCAT('fid',fid1.id)
                      
                      LEFT JOIN tags_fred fid2
                      ON CONCAT(t0.sc2,IFNULL(t0.fk_fred_id2,''),IFNULL(t0.fk_fid_id2,''))= CONCAT('fid',fid2.id)
*/

$tagsCorrRaw = $sql->selectToAssoc("
SELECT
t0.id,t0.naturalid,t0.added_on,t0.lastupdated,t0.obs_start,t0.obs_end,t0.obs_end_val,t0.obs_end_input_min,t0.freq,CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,'')) AS concatid1, CONCAT(sc2,IFNULL(fk_fred_id2,''),IFNULL(fk_fid_id2,'')) AS concatid2,

fred1.id as 1_id, fred1.lookup_code as 1_lookup_code,fred1.class as 1_class,fred1.title as 1_title,fred1.subtitle as 1_subtitle,fred1.freq as 1_freq,fred1.units as 1_units,fred1.obs_start as 1_obs_start,fred1.obs_end as 1_obs_end,

fred2.id as 2_id, fred2.lookup_code as 2_lookup_code,fred2.class as 2_class,fred2.title as 2_title,fred2.subtitle as 2_subtitle,fred2.freq as 2_freq,fred2.units as 2_units,fred2.obs_start as 2_obs_start,fred2.obs_end as 2_obs_end

FROM `tags_corr` t0

LEFT JOIN tags_fred fred1
ON CONCAT(t0.sc1,IFNULL(t0.fk_fred_id1,''),IFNULL(t0.fk_fid_id1,''))= CONCAT('fred',fred1.id)

LEFT JOIN tags_fred fred2
ON CONCAT(t0.sc2,IFNULL(t0.fk_fred_id2,''),IFNULL(t0.fk_fid_id2,''))= CONCAT('fred',fred2.id)

ORDER BY 1_class,t0.id

",'');

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
SELECT *,CONCAT('fred',id) AS concatid
FROM `tags_fred` ORDER BY `class`,`id` ASC;
",'');


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
  $o = round(sqrt(abs($v)),1);
  if ($v <= 0) $rgba = [0,0,255,$o];
  else $rgba = [255,0,0,$o];
  
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
