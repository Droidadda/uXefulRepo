//Add custom body classes
add_filter('body_class', 'nebula_body_classes');
function nebula_body_classes($classes){
    $spaces_and_dots = array(' ', '.');
    $underscores_and_hyphens = array('_', '-');
 
    //Device
    $classes[] = strtolower(nebula_get_device('full')); //Device make and model
    $classes[] = strtolower(str_replace($spaces_and_dots, $underscores_and_hyphens, nebula_get_os('full'))); //Operating System name with version
    $classes[] = strtolower(str_replace($spaces_and_dots, $underscores_and_hyphens, nebula_get_os('name'))); //Operating System name
    $classes[] = strtolower(str_replace($spaces_and_dots, $underscores_and_hyphens, nebula_get_browser('full'))); //Browser name and version
    $classes[] = strtolower(str_replace($spaces_and_dots, $underscores_and_hyphens, nebula_get_browser('name'))); //Browser name
    $classes[] = strtolower(str_replace($spaces_and_dots, $underscores_and_hyphens, nebula_get_browser('engine'))); //Rendering engine
 
    //IE versions outside conditional comments
    if ( nebula_is_browser('ie', '10') ){
        $classes[] = 'lte-ie10';
    } elseif ( nebula_is_browser('ie', '11') ){
        $classes[] = 'lte-ie11';
    }
 
    //User Information
    $current_user = wp_get_current_user();
    if ( is_user_logged_in() ){
        $classes[] = 'user-' . $current_user->user_login;
        $user_info = get_userdata(get_current_user_id());
        $classes[] = 'user-role-' . $user_info->roles[0];
    }
 
    //Post Information
    if ( !is_search() && !is_archive() && !is_front_page() ){
        global $post;
        $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        $parents = get_post_ancestors($post->ID);
        foreach ( $parents as $parent ){
            if ( !empty($parent) ){
                $classes[] = 'ancestor-id-' . $parent;
            }
        }
        foreach ( $segments as $segment ){
            if ( !empty($segment) ){
                $classes[] = 'ancestor-of-' . $segment;
            }
        }
        foreach ( get_the_category($post->ID) as $category ){
            $classes[] = 'cat-' . $category->cat_ID . '-id';
        }
    }
    $nebula_theme_info = wp_get_theme();
    $classes[] = 'nebula';
    $classes[] = 'nebula_' . str_replace('.', '-', $nebula_theme_info->get('Version'));
 
    //Time of Day
    if ( has_business_hours() ){
        $classes[] = ( business_open() )? 'business-open' : 'business-closed';
    }
 
    $relative_time = nebula_relative_time('description');
    foreach( $relative_time as $relative_desc ){
        $classes[] = 'time-' . $relative_desc;
    }
    if ( date('H') >= 12 ){
        $classes[] = 'time-pm';
    } else {
        $classes[] = 'time-am';
    }
 
    if ( get_option('nebula_latitude') && get_option('nebula_longitude') ){
        $lat = get_option('nebula_latitude');
        $lng = get_option('nebula_longitude');
        $gmt = intval(get_option('gmt_offset'));
        $zenith = 90+50/60; //Civil twilight = 96°, Nautical twilight = 102°, Astronomical twilight = 108°
        $sunrise = strtotime(date_sunrise(strtotime('today'), SUNFUNCS_RET_STRING, $lat, $lng, $zenith, $gmt));
        $sunset = strtotime(date_sunset(strtotime('today'), SUNFUNCS_RET_STRING, $lat, $lng, $zenith, $gmt));
 
        if ( time() >= $sunrise && time() <= $sunset ){
            $classes[] = 'time-daylight';
            $classes[] = ( strtotime('now') < $sunrise+(($sunset-$sunrise)/2) )? 'time-light-wax' : 'time-light-wane'; //Before or after solar noon
        } else {
            $classes[] = 'time-darkness';
            $previous_sunset_modifier = ( date('H') < 12 )? 86400 : 0;
            $wane_time = (($sunset-$previous_sunset_modifier)+((86400-($sunset-$sunrise))/2)); //if it is after midnight, then we need to get the previous sunset (not the next- else we're always before tomorrow's wane time)
            $classes[] = ( strtotime('now') < $wane_time )? 'time-dark-wax' : 'time-dark-wane'; //Before or after solar midnight
        }
 
        $sunrise_sunset_length = 45; //Length of sunrise/sunset in minutes. Default: 45
        if ( strtotime('now') >= $sunrise-60*$sunrise_sunset_length && strtotime('now') <= $sunrise+60*$sunrise_sunset_length ){ //X minutes before and after true sunrise
            $classes[] = 'time-sunrise';
        }
        if ( strtotime('now') >= $sunset-60*$sunrise_sunset_length && strtotime('now') <= $sunset+60*$sunrise_sunset_length ){ //X minutes before and after true sunset
            $classes[] = 'time-sunset';
        }
    }
 
    $classes[] = 'date-day-' . strtolower(date('l'));
    $classes[] = 'date-ymd-' . strtolower(date('Y-m-d'));
    $classes[] = 'date-month-' . strtolower(date('F'));
 
    if ( $GLOBALS['http'] && is_int($GLOBALS['http']) ){
        $classes[] = 'error' . $GLOBALS['http'];
    }
 
    return $classes;
}
