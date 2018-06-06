<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
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
  
//Routes
if ($request == 'stocksectorcorrelation') {
  $title = 'Stock Sector Industry Correlation Lookup';
  $model = 'correlations/getstocks.script.php';
}

elseif ($request == 'stock') {
  $title = 'Stock-to-Stock Correlation Lookup';
}

elseif ($request == 'financialcontagion') {
  $title = 'Financial Contagion Index';
  $model = 'test/get_correlation_hist.script.php';
}




//Send request
if (isset($model)) require_once($model);

if (isset($modeldata['script'])) {
  $script = new StaticFile('js',$modeldata['script']);
  $requestvars['script'] = $script->minify();
}
else $requestvars['script'] = '';


if (isset($title)) $requestvars['title'] = $title; else $title = 'No Title';

echo $twig->render($request.'.html', $requestvars);  

?>
