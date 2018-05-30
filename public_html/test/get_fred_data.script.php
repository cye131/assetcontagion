<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
}
$sql = new MyPDO();


$fredseries = ['DGS2','DGS10'];


foreach ($fredseries as $series) {
    $fred = new FRED($series);
    
    echo '<pre>';
    $data = ($fred->get_fred_data());
    print_r( $data );
    
    echo '</pre>';
    
    $colnames = array('id','code','date','value','chg');

    
    
    $sql -> multipleInsert ('fred_hist',$colnames,$data);
}


?>