<?php

$date = $fromAjax['date'] ?? NULL;


if (isset($date)) {
    
    $histCorrel = $sql -> selectToAssoc("
    SELECT * FROM `hist_correl` WHERE pretty_date = :date
    ",['date' => $date],['fk_id']);

} else {
    echo 'No date set';
}
