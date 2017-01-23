<?php

//Recursive Glob
function glob_r($pattern, $flags = 0){
    $override = apply_filters('pre_glob_r', false, $pattern, $flags);
    if ( $override !== false ){return $override;}
 
    $files = glob($pattern, $flags);
    foreach ( glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir ){
        $files = array_merge($files, glob_r($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}
