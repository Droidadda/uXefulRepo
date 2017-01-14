//Fuzzy meta sub key finder (Used to query ACF nested repeater fields).
//Example: 'key' => 'dates_%_start_date',
add_filter('posts_where' , 'nebula_fuzzy_posts_where');
function nebula_fuzzy_posts_where($where){
    if ( strpos($where, '_%_') > -1 ){
        $where = preg_replace("/meta_key = ([\'\"])(.+)_%_/", "meta_key LIKE $1$2_%_", $where);
    }
    return $where;
}
