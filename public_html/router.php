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
else $request = 'stocksectorcorrelation';

$requestvars = array(
                     'title' => '',
                     'modeldata' => array()
                     );



/* Model returns $modeldata -> $modeldata['script'] goes into <script></script> tag on bottom of body
 *
 *
 *
 */
  
//Routes - Main Sites
if ($request == 'stocksectorcorrelation') {
  $title = 'Stock Sector Industry Correlation Lookup';
  $model = 'correlations/getstocks.script.php';
}

elseif ($request == 'stock') {
  $title = 'Stock-to-Stock Correlation Lookup';
}

elseif ($request == 'financialcontagion') {
  $title = 'Financial Contagion Index';
  $model[] = 'get_tags_series';
  $model[] = 'get_tags_correl';
  $logic[] ='heatmap';
  $toScript = ['tagsSeries','tagsCorrel','heatMapData'];
}




//Routes - Update Sites

elseif ($request == 'updatehistseries') {
  $title = 'Historical Series Updater';
  $model[] = 'get_tags_series';
  $toScript = ['tagsSeries'];
}
//->fix
elseif ($request == 'updatetagscorrel') {
  $title = 'Correlation Tags Updater';
  $model[] = 'get_tags_series';
  $model[] = 'get_tags_correl';
  $model[] = 'update_tags_correl';
  $toScript = ['tagsSeries','tagsCorrel'];
}

elseif ($request == 'updatehistcorrel') {
  $title = 'Correlation History Updater';
  $model[0] = 'get_tags_correl';
  $toScript = ['tagsCorrel'];
}




//Send request
if (isset($model)) {
  $sql = new MyPDO();
  foreach ($model as $m) {
    require_once("/var/www/correlation/models/$m.model.php");
    }
}


if (isset($logic)) {
  foreach ($logic as $l) {
    require_once("/var/www/correlation/models/$l.logic.php");
  }
}

if (isset($toScript)) {
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
