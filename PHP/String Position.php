<?php

//Syntax:
substr($haystack, $start, $end);
 
//Use that in combination with strpos (which returns a string position). So like:
 
$haystack = "lorem ipsum dolor: sit amet";
$desired_string = substr($haystack, strpos($haystack, "dolor: ")+7); //Returns EVERYTHING after "dolor:"  excluding "dolor: "
 
//If you know how long the desired string is after the first position, then you can hard-code the end like:
$desired_string = substr($haystack, strpos($haystack, "dolor: ")+7, 3); //Returns 3 characters after "dolor: " exlcuding "dolor: "
 
//If you know the string after the desired string, you can find it\'s position as the end, but it must be added to the start (because the position is calculated from the beginning of $haystack, not from the first parameter\'s position).
$haystack = "lorem ipsum dolor: sit amet";
$needle_start = strpos($haystack, "dolor: ")+7;
$needle_end = strpos($haystack, " amet")+5-$needle_start;
$desired_string = strtolower(substr($haystack, $needle_start, $needle_end));
 
//Check if a string exists:
if ( strpos($haystack, $needle) > -1 ) { ... }
