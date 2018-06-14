<?php

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
    foreach ($tagsCorrel as $tagRow) {
        if (
            ($tagRow['b_id_1'] === $tagsSeries[$x]['b_id'] && $tagRow['b_id_2'] === $tagsSeries[$y]['b_id']) ||
            ($tagRow['b_id_1'] === $tagsSeries[$y]['b_id'] && $tagRow['b_id_2'] === $tagsSeries[$x]['b_id'])
           ) {
            
            $hmData[$i]['value'] = (float) $tagRow['obs_end_val'];
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
                        .'var tagsCorrel =' .json_encode($tagsCorrel) .';'

                        .'var heatMapData = new Array();'
                        .'heatMapData.data=' .json_encode($hmData).';'
                        .'heatMapData.info=' .json_encode($hmInfo).';';

