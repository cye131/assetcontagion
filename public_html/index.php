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



if (isset($_GET['path']) && strlen($_GET['path'])) $request = $_GET['path'];
else $request = 'index';

$requestvars = array(
                     'title' => '',
                     'script' => NULL
                     );




if ($request == 'index') {
  $requestvars['title'] = 'Stock Sector Industry Correlation Lookup';
  require_once('correlations/getstocks.script.php');
  $requestvars['script'] = $script;
}

//elseif 




  
  echo $twig->render($request.'.html', $requestvars);  

?>
