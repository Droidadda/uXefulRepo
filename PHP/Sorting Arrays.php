//Alphabetical sort
usort($output, function($a, $b) {
    return strcmp($a['type'], $b['type']);
});
 
 
//Alphabetical sort by primary then secondary
//You can add more dimensions to this by duplicating the first group of $c and it\'s if conditional!
usort($output, function($a, $b) {
    $c = strcmp($a['type'], $b['type']);
    if ( $c != 0 ){
        return $c;
    }
    return strcmp($a['title'], $b['title']);
});
 
 
//Numerical sort
usort($output, function($a, $b) {
    return $a['time'] - $b['time'];
});
