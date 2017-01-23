<?php

//Traverse multidimensional arrays
function in_array_r($needle, $haystack, $strict = true){
    $override = apply_filters('pre_in_array_r', false, $needle, $haystack, $strict);
    if ( $override !== false ){return $override;}
 
    foreach ($haystack as $item){
        if ( ($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)) ){
            return true;
        }
    }
    return false;
}
