<?php

//Generate a random integer between two numbers with an exclusion array
//Call it like: random_number_between_but_not(1, 10, array(5, 6, 7, 8));
function random_number_between_but_not($min=null, $max=null, $butNot=null){
    if ( $min > $max ){ //If min is greater than max, swap variables
        $tmp = $min;
        $min = $max;
        $max = $tmp;
    }
    if ( gettype($butNot) == 'array' ){
        foreach( $butNot as $key => $skip ){
            if( $skip > $max || $skip < $min ){
                unset($butNot[$key]);
            }
        }
        if ( count($butNot) == $max-$min+1 ){
            trigger_error('Error: no number exists between ' . $min .' and ' . $max .'. Check exclusion parameter.', E_USER_ERROR);
            return false;
        }
        while ( in_array(($randnum = rand($min, $max)), $butNot));
    } else {
        while (($randnum = rand($min, $max)) == $butNot );
    }
    return $randnum;
}
