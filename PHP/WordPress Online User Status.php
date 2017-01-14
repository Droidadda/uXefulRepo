//Check Nebula for updated version of this

//Update user online status
add_action('init', 'nebula_users_status_init');
add_action('admin_init', 'nebula_users_status_init');
function nebula_users_status_init(){
    $logged_in_users = get_transient('users_status');
    $unique_id = $_SERVER['REMOTE_ADDR'] . '.' . preg_replace("/[^a-zA-Z0-9]+/", "", $_SERVER['HTTP_USER_AGENT']);
    $current_user = wp_get_current_user();
 
    //@TODO "Nebula" 0: Technically, this should be sorted by user ID -then- unique id -then- the rest of the info. Currently, concurrent logins won't reset until they have ALL expired. This could be good enough, though.
 
    if ( !isset($logged_in_users[$current_user->ID]['last']) || $logged_in_users[$current_user->ID]['last'] < time()-900 ){ //If a last login time does not exist for this user -or- if the time exists but is greater than 15 minutes, update.
        $logged_in_users[$current_user->ID] = array(
            'id' => $current_user->ID,
            'username' => $current_user->user_login,
            'last' => time(),
            'unique' => array($unique_id),
        );
        set_transient('users_status', $logged_in_users, 1800); //30 minutes
    } else {
        if ( !in_array($unique_id, $logged_in_users[$current_user->ID]['unique']) ){
            array_push($logged_in_users[$current_user->ID]['unique'], $unique_id);
            set_transient('users_status', $logged_in_users, 1800); //30 minutes
        }
    }
}
 
 
 
//Check if a user has been online in the last 15 minutes
function nebula_is_user_online($id){
    $override = apply_filters('pre_nebula_is_user_online', false, $id);
    if ( $override !== false ){return $override;}
 
    $logged_in_users = get_transient('users_status');
    return isset($logged_in_users[$id]['last']) && $logged_in_users[$id]['last'] > time()-900; //15 Minutes
}
 
//Check when a user was last online.
function nebula_user_last_online($id){
    $override = apply_filters('pre_nebula_user_last_online', false, $id);
    if ( $override !== false ){return $override;}
 
    $logged_in_users = get_transient('users_status');
    if ( isset($logged_in_users[$id]['last']) ){
        return $logged_in_users[$id]['last'];
    } else {
        return false;
    }
}
 
//Get a count of online users, or an array of online user IDs.
function nebula_online_users($return='count'){
    $override = apply_filters('pre_nebula_online_users', false, $return);
    if ( $override !== false ){return $override;}
 
    $logged_in_users = get_transient('users_status');
    if ( empty($logged_in_users) ){
        return ( $return == 'count' )? 0 : false;
    }
    $user_online_count = 0;
    $online_users = array();
 
    foreach ( $logged_in_users as $user ){
        if ( !empty($user['username']) && isset($user['last']) && $user['last'] > time()-900 ){
            $online_users[] = $user;
            $user_online_count++;
        }
    }
 
    return ( $return == 'count' )? $user_online_count : $online_users;
}
 
function nebula_user_single_concurrent($id){
    $override = apply_filters('pre_nebula_user_single_concurrent', false, $id);
    if ( $override !== false ){return $override;}
 
    $logged_in_users = get_transient('users_status');
    if ( isset($logged_in_users[$id]['unique']) ){
        return count($logged_in_users[$id]['unique']);
    } else {
        return 0;
    }
}
