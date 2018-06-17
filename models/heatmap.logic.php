<?php

/* requires $tagsSeries, $tagsCorrel;
 * tagsCorrel can include historical data (pretty_date and value) in which case the date and obs will be set according to that, instead of the most recent obs value
 *
 *
 */

//Creates array for heatmap
$hmData = array();
$hmInfo = array();

$i = (int) 0;
for ($x=0;$x<count($tagsSeries);$x++) {
for ($y=0;$y<count($tagsSeries);$y++) {
    
    $hmData[$i] = array(
        's_id_x' => $tagsSeries[$x]['s_id'],
        's_id_y' => $tagsSeries[$y]['s_id'],
        /*'x-name' => $tagsSeries[$x]['name'],
        'y-name' => $tagsSeries[$y]['name'],*/
        'x' => $x,
        'y' => $y
    );
    foreach ($tagsCorrel as $tagRow) {
        if (
            ($tagRow['b_id_1'] === $tagsSeries[$x]['b_id'] && $tagRow['b_id_2'] === $tagsSeries[$y]['b_id']) ||
            ($tagRow['b_id_1'] === $tagsSeries[$y]['b_id'] && $tagRow['b_id_2'] === $tagsSeries[$x]['b_id'])
           ) {

            $hmData[$i]['pretty_date'] = $tagRow['h_pretty_date'] ?? $tagRow['obs_end'];
            $hmData[$i]['value'] = $tagRow['h_value'] ?? (float) $tagRow['obs_end_val'];
            //$hmData[$i]['date_used'] = $tagRow['h_pretty_date'] ?? $tagRow['obs_end'];

            //$hmData[$i]['value'] = (float) $tagRow['obs_end_val'];
            $hmData[$i]['obs_start'] = $tagRow['obs_start'];
            $hmData[$i]['obs_end'] = $tagRow['obs_end'];
            $hmData[$i]['freq'] = $tagRow['freq'];
            $hmData[$i]['trail'] = $tagRow['trail'];
            $hmData[$i]['last_updated'] = $tagRow['last_updated'];
          
          }
      }

    $i++;
}
}
//(new TestOutput($hmData))->print();


//creates colors + tooltip info for heatmap points
function colorFormat($v) {
  //$o = round(sqrt(abs($v)),1);
  if ($v <= 0) $maxColor = [0,0,255,1];
  else $maxColor = [255,0,0,1];
  $minColor = [251,250,182,0];

  for($i=0;$i<=2;$i++) {
    $rgba[$i] = round(($maxColor[$i] - $minColor[$i]) * abs($v),0) + $minColor[$i];
  }
  $rgba[3] = round(($maxColor[3] - $minColor[3]) * abs($v),2) + $minColor[3];

  
  return 'rgba('.implode(',',$rgba).')';
}

foreach ($hmData as $i => $row) {
  if (!isset($row['value']) ) {
    $hmData[$i]['color'] = ' #cccccc';
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
$colors = ['#4572A7', '#AA4643', '#0ba828', '#80699B', '#3D96AE','#DB843D', '#92A8CD', '#A47D7C', '#B5CA92'];

$i = (int) 0;
foreach ($tagsSeries as $k => $tag) {
  $groupingFirstPart = explode('.',$tag['grouping'])[0];
  $tagsSeries[$k]['grouping_first_part'] = $groupingFirstPart;
  
  if ( in_array($groupingFirstPart,array_keys($hmInfo['colorarray'])) ) continue;
  $hmInfo['colorarray'][$groupingFirstPart] = $colors[$i];
  $i++;
}

$heatMapData = ['data' => $hmData, 'info' => $hmInfo];
/*
$modeldata['script'] .= ''
                        .'var tagsSeries =' .json_encode($tagsSeries) .';'
                        .'var tagsCorrel =' .json_encode($tagsCorrel) .';'

                        .'var heatMapData = new Array();'
                        .'heatMapData.data=' .json_encode($hmData).';'
                        .'heatMapData.info=' .json_encode($hmInfo).';';
*/
