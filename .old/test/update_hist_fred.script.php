<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
}
$sql = new MyPDO();


//Get FRED data tags

$fredtags = $sql -> selectAssoc('tags_fred',['*'],'lookup_code','');

echo '<pre>';
print_r($fredtags);
echo '</pre>';


//Get FRED historical data
foreach ($fredtags as $seriesname => $series) {
    $fred = new FRED($series['lookup_code'],$series['freq'],$series['id']);
    
    echo '<pre>';
    $data = ($fred->get_fred_data());
    print_r( $data );
    
    echo '</pre>';
    
    $colnames = array('id','date','value','chg','fk_tags_id');

    
    $sql -> multipleInsert ('hist_fred',$colnames,$data);
}


?>