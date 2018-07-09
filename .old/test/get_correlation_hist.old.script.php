<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
}
$sql = new MyPDO();







//Get historical correlation data & tags
$correlationseries = $sql -> selectAssoc('stats_corr',['*'],['code','date'],'ORDER BY code ASC,date DESC');
$fredtags = $sql -> selectAssoc('tags_fred',['*','"fred" as source'],'lookup_code','ORDER BY class ASC');


//Formats $tags with 'fred0001' etc as index
$unfmt_tags = $fredtags;
$tags = array();
foreach ($unfmt_tags as $tag) {
    $tags[$tag['source'].sprintf('%04d',$tag['id'])] = $tag;
}

function sortByClass($a, $b) {
    return strcasecmp($a['class'],$b['class']);
}

uasort($tags,'SortByClass'); //IMPORTANT!! Must be sorted to keep correlation-pairs with starting classes adjacent to one another (e.g. gold next to oil etc)
unset($unfmt_tags);


//Creates array of latest correlation data with 'fred001_fred003' etc as index
$groupdata = array();

$i = (int) 0;
foreach ($correlationseries as $seriesname=>$tsarray) {
    $sources = explode('_',$seriesname);
    sort($sources);
    
    $groupdata[$i] = array(
        'lastupdate' => array_keys($tsarray)[0],
        'lastcorrelation' => $tsarray[array_keys($tsarray)[0]]['value'],
        'classes' => array($tags[$sources[0]]['class'],$tags[$sources[1]]['class']),
        'titles' => array($tags[$sources[0]]['title'],$tags[$sources[1]]['title']),
        'sources' => array($sources[0],$sources[1])

        );
    
    $i++;
}


//Creates array for heatmap
$heatmapdata = array();
$heatmapinfo = array();
$valuearray = array();

$tagnames = array_keys($tags);
$k = count($groupdata);

for ($i=0;$i<$k;$i++) {
  $heatmapdata[$i] = array();
  $heatmapdata[$i]['x'] = array_search($groupdata[$i]['sources'][0],$tagnames);    
  $heatmapdata[$i]['y'] = array_search($groupdata[$i]['sources'][1],$tagnames);
  $heatmapdata[$i]['value'] = (float) $groupdata[$i]['lastcorrelation'];
  $heatmapdata[$i]['name'] = $groupdata[$i]['sources'][0] . '-' .  $groupdata[$i]['sources'][1];
  $heatmapdata[$i]['lastupdated'] = $groupdata[$i]['lastupdate'];

  $valuearray[$i] = $groupdata[$i]['lastcorrelation'];
  
  //makes (2,0), etc if first part already has (0,2)
  $heatmapdata[$i+$k] = array();
  $heatmapdata[$i+$k]['x'] = array_search($groupdata[$i]['sources'][1],$tagnames);    
  $heatmapdata[$i+$k]['y'] = array_search($groupdata[$i]['sources'][0],$tagnames);
  $heatmapdata[$i+$k]['value'] = (float) $groupdata[$i]['lastcorrelation'];
  $heatmapdata[$i+$k]['name'] = $groupdata[$i]['sources'][0] . '-' .  $groupdata[$i]['sources'][1];
  $heatmapdata[$i+$k]['lastupdated'] = $groupdata[$i]['lastupdate'];

  $valuearray[$i+$k] = $groupdata[$i]['lastcorrelation'];
}

//creates (0,0),(1,1),etc points
$j = count($heatmapdata);
for ($i=0;$i<count($tagnames);$i++) {
    $heatmapdata[$i+$j] = array();
    $heatmapdata[$i+$j]['x'] = $i;
    $heatmapdata[$i+$j]['y'] = $i;
    $heatmapdata[$i+$j]['value'] = (float) 1;
    $heatmapdata[$i+$j]['name'] = $tagnames[$i];
    $heatmapdata[$i+$j]['lastupdated'] = 'N/A';

    $valuearray[$i+$j] = 1;
}

//creates colors + tooltip info for heatmap points
function colorFormat($v) {
  $o = round(sqrt(abs($v)),1);
  if ($v <= 0) {
    $rgba = [0,0,255,$o];
  } else {
    $rgba = [255,0,0,$o];
  }
  return 'rgba('.implode(',',$rgba).')';
  
}

foreach ($heatmapdata as $i => $row) {
  if ($row['value'] == 1) {
    $heatmapdata[$i]['color'] = 'grey';
    $heatmapdata[$i]['tooltip'] = (bool) FALSE;
  } else {
    $heatmapdata[$i]['color'] = colorFormat($row['value']);
    $heatmapdata[$i]['tooltip'] = (bool) TRUE;
  }
}

$heatmapinfo['min'] = (float) min($valuearray);
$heatmapinfo['max'] = (float) max($valuearray);

//creates column names for heatmap
$heatmapinfo['titles'] = array_column($tags,'title');


//creates array indexing colors to classes
$heatmapinfo['colorarray'] = array();
$colors = ["#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1"];

$i = (int) 0;
foreach ($tags as $tag) {
  if ( in_array($tag['class'],array_keys($heatmapinfo['colorarray'])) ) continue;
  $heatmapinfo['colorarray'][$tag['class']] = $colors[$i];
  $i++;
}

usort($tags,'SortByClass'); //Sort again (just in case) & strip assoc. indices so JSON can take it as an array instead of object

$modeldata['script'] = 'groupData='. json_encode($groupdata) .';'
                        .'var tags =' .json_encode($tags) .';'
                        .'var heatMapData = new Array();'
                        .'heatMapData.data=' .json_encode($heatmapdata).';'
                        .'heatMapData.info=' .json_encode($heatmapinfo).';';

/*
echo '<pre>';
print_r($tags);
print_r($groupdata);
print_r($heatmapdata);
//print_r($series);
echo '</pre>';
*/


?>