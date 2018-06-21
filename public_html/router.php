<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/classes/" . $classname . '.class.php';
}


require_once '/var/www/correlation/vendor/autoload.php';
$loader = new Twig_Loader_Filesystem(__DIR__.'/templates');
$twig = new Twig_Environment($loader,array(
                                           'debug' => true,
                                           'cache' => '/var/www/correlation/cache'
                                           ));



//Set default URL
if (isset($_GET['path']) && strlen($_GET['path'])) $request = $_GET['path'];
else $request = 'regions';

$requestvars = array(
                     'title' => '',
                     'modeldata' => array()
                     );



/* Model returns $modeldata -> $modeldata['script'] goes into <script></script> tag on bottom of body
 *
 *
 *
 */

 $fromRouter = [];
 $model = [];
 $logic = [];
 $toScript = [];
  
//Routes - Main Sites
if ($request == 'stocksectorcorrelation') {
  $title = 'Stock Sector Industry Correlation Lookup';
  $model = 'correlations/getstocks.script.php';
}

elseif ($request == 'stock') {
  $title = 'Stock-to-Stock Correlation Lookup';
}

elseif ($request == 'regions') {
  $title = 'Global Stock Market Correlations';
  $fromRouter = ['category' => 'reg','corr_type' => 'rho','freq' => 'd', 'trail' => 30];
  $model[] = 'get_tags_series';
  $model[] = 'get_tags_correl';
  $model[] = 'get_tags_gfi';
  $logic[] ='heatmap';
  $toScript = ['tagsSeries','tagsCorrel','heatMapData','tagsGFI'];
}




//Routes - Update Sites

elseif ($request == 'updatehistseries') {
  $title = 'Historical Series Updater';
  $model[] = 'get_tags_series';
  $model[] = 'get_specs_categories';
  $toScript = ['tagsSeries','specsCategories'];
}

//Currently does not use categories correctly!
elseif ($request == 'updatetagscorrel') {
  $title = 'Correlation Tags Updater';
  $model[] = 'get_tags_series';
  $model[] = 'get_tags_correl';
  $model[] = 'get_specs_categories';
  $toScript = ['tagsSeries','tagsCorrel','specsCategories'];
}

elseif ($request == 'updatehistcorrel') {
  $title = 'Correlation History Updater';
  $fromRouter = ['corr_type' => 'rho'];
  $model[] = 'get_tags_correl';
  $toScript = ['tagsCorrel'];
}









//Send request
if (count($model) > 0) {
  $sql = new MyPDO();
  foreach ($model as $m) {
    require_once("/var/www/correlation/models/$m.model.php");
    }
}

if (count($logic) > 0) {
  foreach ($logic as $l) {
    require_once("/var/www/correlation/models/$l.logic.php");
  }
}

if (count($toScript) > 0) {
  $scriptStr = '';
  foreach ($toScript as $varName) {
    $scriptStr .= "$varName = ".json_encode(${$varName}).';'; 
  }
  $script = new StaticFile('js',$scriptStr);
  $requestvars['script'] = $script->minify();
}
else $requestvars['script'] = '';


if (isset($title)) $requestvars['title'] = $title; else $title = 'No Title';

echo $twig->render($request.'.html', $requestvars);  
