<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();

$tagsCorr = $sql->selectToAssoc("
SELECT
t0.*,

t1.s_id AS s_id_1,t1.freq AS freq_1,
t2.s_id AS s_id_2,t2.freq AS freq_2,

t1b.b_id AS b_id_1, t1b.name AS name_1,t1b.grouping AS grouping_1,t1b.lookup_code AS lookup_code_1,t1b.proxy AS proxy_1,
t2b.b_id AS b_id_2, t2b.name AS name_2,t2b.grouping AS grouping_2,t2b.lookup_code AS lookup_code_2,t2b.proxy AS proxy_2

FROM tags_correl t0

LEFT JOIN tags_series AS t1
ON t0.fk_id_1 = t1.s_id
LEFT JOIN tags_series_base AS t1b
ON t1.fk_id = t1b.b_id

LEFT JOIN tags_series AS t2
ON t0.fk_id_2 = t2.s_id
LEFT JOIN tags_series_base AS t2b
ON t2.fk_id = t2b.b_id

WHERE t0.freq = 'd' AND t0.trail='30'
",'','');


$tagsSeries = $sql->selectToAssoc("
SELECT *
FROM `tags_series` AS series

LEFT JOIN `tags_series_base` AS base

ON series.fk_id = base.b_id

WHERE series.freq = 'd'

ORDER BY base.category,base.b_id,series.freq
",'','');




//Creates array for heatmap
$hmData = array();
$hmInfo = array();

$i = (int) 0;
for ($x=0;$x<count($tagsSeries);$x++) {
for ($y=0;$y<count($tagsSeries);$y++) {
    
    $hmData[$i] = array(
        'x-name' => $tagsSeries[$x]['name'],
        'y-name' => $tagsSeries[$y]['name'],
        'x' => $x,
        'y' => $y
    );
    foreach ($tagsCorr as $tagRow) {
        if (
            ($tagRow['s_id_1'] === $tagsSeries[$x]['b_id'] && $tagRow['s_id_2'] === $tagsSeries[$y]['b_id']) ||
            ($tagRow['s_id_1'] === $tagsSeries[$y]['b_id'] && $tagRow['s_id_2'] === $tagsSeries[$x]['b_id'])
           ) {
            
            $hmData[$i]['value'] = $tagRow['obs_end_val'];
            $hmData[$i]['obs_start'] = $tagRow['obs_start'];
            $hmData[$i]['obs_end'] = $tagRow['obs_end'];
            $hmData[$i]['freq'] = $tagRow['freq'];
            $hmData[$i]['last_updated'] = $tagRow['last_updated'];
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
$hmInfo['titles'] = array_column($tagsSeries,'name');


//creates array indexing colors to classes
$hmInfo['colorarray'] = array();
$colors = ["#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1"];

$i = (int) 0;
foreach ($tagsSeries as $tag) {
  if ( in_array($tag['category'],array_keys($hmInfo['colorarray'])) ) continue;
  $hmInfo['colorarray'][$tag['category']] = $colors[$i];
  $i++;
}

$modeldata['script'] = ''
                        .'var tags =' .json_encode($tagsSeries) .';'
                        .'var tagsCorr =' .json_encode($tagsCorr) .';'

                        .'var heatMapData = new Array();'
                        .'heatMapData.data=' .json_encode($hmData).';'
                        .'heatMapData.info=' .json_encode($hmInfo).';';

