<?php
spl_autoload_register('myAutoloader');
function myAutoloader($classname) {
  require_once __DIR__."/../classes/$classname.class.php";
}
require_once __DIR__.'/../vendor/autoload.php';

$sql = new MyPDO();

/* Make date array
 *
 *
 *
 */
$dates = [];

$date = new DateTime('2010-01-01');
$today = (new DateTime())->setTime(0,0,0);

while ($date < $today) {
    $date -> modify('+1 day');
    
    if ($date -> format('D') !== 'Sat' && $date -> format('D') !== 'Sun') {
        $dates[] = $date -> format('Y-m-d');
    }
}

print_r($dates);



/* Get category types
 * 
 *
 *
 */
require_once(__DIR__.'/../models/get_specs_categories.model.php');
print_r($specsCategories);

/* Calculate averages
 *
 *
 *
 */
$corrIndex = [];
$sql = new MyPDO();
$q = '
        SELECT
        t0.*,
        
        t1.s_id AS s_id_1,t1.freq AS freq_1,
        t2.s_id AS s_id_2,t2.freq AS freq_2,
        
        t1b.b_id AS b_id_1, t1b.name AS name_1,t1b.grouping AS grouping_1,t1b.lookup_code AS lookup_code_1,t1b.proxy AS proxy_1,
        t2b.b_id AS b_id_2, t2b.name AS name_2,t2b.grouping AS grouping_2,t2b.lookup_code AS lookup_code_2,t2b.proxy AS proxy_2,
        
        th.value as h_value, th.pretty_date as h_pretty_date
        
        FROM tags_correl t0
        
        LEFT JOIN tags_series AS t1
        ON t0.fk_id_1 = t1.s_id
        LEFT JOIN tags_series_base AS t1b
        ON t1.fk_id = t1b.b_id
        
        LEFT JOIN tags_series AS t2
        ON t0.fk_id_2 = t2.s_id
        LEFT JOIN tags_series_base AS t2b
        ON t2.fk_id = t2b.b_id
        
        LEFT JOIN hist_correl AS th
        ON (t0.s_corr_id = th.fk_id)
        
        WHERE (th.pretty_date = :pretty_date
        AND t1b.category LIKE CONCAT(:cat_nid,"%") AND t2b.category LIKE CONCAT(:cat_nid,"%")
        AND t0.corr_type = :corr_type
        AND t0.freq = :freq
        AND t0.trail = :trail
        )
        ';

$i = (int) 0;
foreach ($specsCategories as $cat) {
if ( strlen($cat['cat_freqtrails']) === 0 || strlen($cat['cat_corrtypes']) === 0 ) continue;
$freqtrails = explode(',',$cat['cat_freqtrails']);
$corrtypes = explode(',',$cat['cat_corrtypes']);
foreach ($freqtrails as $ft) {
foreach ($corrtypes as $ct) {
foreach ($dates as $d) {

    
    $varsToBind = [
        'pretty_date' => $d,
        'freq' => explode('.',$ft)[0],
        'trail' => explode('.',$ft)[1],
        'corr_type' => $ct,
        'cat_nid' => $cat['cat_nid']
    ];
    

    
    $tagsCorrel = $sql->selectToAssoc($q,$varsToBind,'');
    
    if (count($tagsCorrel) === 0) continue;
    
    $vals = [];
    foreach ($tagsCorrel as $row) {
        $vals[] = $row['h_value'];
    }
    
    $corrIndex[$i] = $varsToBind;
    $corrIndex[$i]['value'] = array_sum($vals)/count($vals);
    

    $i++;

}
}
}
}

$sql->multipleInsert('hist_corrindex',['pretty_date','freq','trail','corr_type','cat_nid','value'],$corrIndex);


print_r($corrIndex);