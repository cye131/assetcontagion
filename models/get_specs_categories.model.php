<?php

$varsToBind = [];

$category = $fromAjax['category'] ?? $fromRouter['category'] ?? NULL;



if (!is_null($category)) {
    $category_str = 'WHERE specs_categories = :category';
    $varsToBind['category'] = $category;
} else {
    $category_str = '';
}




$specsCategories = $sql -> selectToAssoc("
SELECT cat_nid,cat_name,cat_freqtrails,cat_valtypes,cat_corrtypes
FROM `specs_categories`

$category_str

",$varsToBind,'');