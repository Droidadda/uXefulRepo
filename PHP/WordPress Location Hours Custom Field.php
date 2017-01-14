//Location Hours Metabox and Custom Fields
add_action('load-post.php', 'schc_post_meta_boxes_setup');
add_action('load-post-new.php', 'schc_post_meta_boxes_setup');
function schc_add_post_meta_boxes(){
    add_meta_box('schc-location-hours', 'Location Hours', 'schc_location_hours_meta_box', 'location', 'side', 'default');
}
function schc_location_hours_meta_box($object, $box){
    wp_nonce_field(basename(__FILE__), 'schc_location_hours_nonce');
    ?>
    <div>
        <p style="font-size: 12px; color: #444;">Times should be in the format <strong>"5:30 pm"</strong> or <strong>"17:30"</strong>.</p>
 
        <?php
            $weekdays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
            foreach ( $weekdays as $weekday ):
        ?>
            <div class="businessday">
                <input type="checkbox" name="schc_location_hours_<?php echo $weekday; ?>_enabled" value="1" <?php checked('1', get_post_meta($object->ID, 'schc_location_hours_' . $weekday . '_enabled', true)); ?> /> <span style="display: inline-block; width: 75px; font-size: 12px;"><?php echo ucfirst($weekday); ?>:</span> <input class="business-hour" type="text" name="schc_location_hours_<?php echo $weekday; ?>_open" value="<?php echo get_post_meta($object->ID, 'schc_location_hours_' . $weekday . '_open', true); ?>" style="width: 65px;" /> &ndash; <input class="business-hour" type="text" name="schc_location_hours_<?php echo $weekday; ?>_close" value="<?php echo get_post_meta($object->ID, 'schc_location_hours_' . $weekday . '_close', true); ?>" style="width: 65px;"  />
            </div>
        <?php endforeach; ?>
 
        <p style="font-size: 12px; color: #444;">Each location uses the site-wide <a href="themes.php?page=nebula_options#daysoff" target="_blank">Days Off setting</a>. To override this, enter days off for this specific location below.</p>
        <p style="font-size: 12px; color: #444;">Comma-separated list of special days this office is closed.</p>
        <p style="font-size: 12px; color: #444;">Use date formatting (<strong>"7/4"</strong>), or day of the month (<strong>"Fourth Thursday of November"</strong>). <a href="http://mistupid.com/holidays/" target="_blank">Holiday Occurrences Reference &raquo;</a></p>
 
        <small style="display: block;"><strong>Days Off (Override):</strong></small>
        <textarea name="schc_location_hours_daysoff" style="display: block; width: 100%; margin-bottom: 10px;" placeholder="<?php echo get_option('nebula_business_hours_closed'); ?>"><?php echo get_post_meta($object->ID, 'schc_location_hours_daysoff', true); ?></textarea>
 
        <small><strong>Note:</strong> This assumes days off that fall on weekends are observed the Friday before or the Monday after.</small>
    </div>
<?php }
function schc_post_meta_boxes_setup(){
    add_action('add_meta_boxes', 'schc_add_post_meta_boxes');
    add_action('save_post', 'schc_save_post_class_meta', 10, 2);
}
function schc_save_post_class_meta($post_id, $post){
    if ( !isset($_POST['schc_location_hours_nonce']) || !wp_verify_nonce($_POST['schc_location_hours_nonce'], basename(__FILE__)) ){
        return $post_id;
    }
 
    $post_type = get_post_type_object($post->post_type); //Get the post type object.
    if ( !current_user_can($post_type->cap->edit_post, $post_id) ){ //Check if the current user has permission to edit the post.
        return $post_id;
    }
 
    $weekdays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
    $options = array('enabled', 'open', 'close');
 
    foreach ( $weekdays as $weekday ){
        foreach ( $options as $option ){
            $new_meta_value = sanitize_text_field($_POST['schc_location_hours_' . $weekday . '_' . $option]); //Get the posted data and sanitize it if needed.
            $meta_value = get_post_meta($post_id, 'schc_location_hours_' . $weekday . '_' . $option, true); //Get the meta value of the custom field key.
 
            if ( $new_meta_value && $meta_value == '' ){ //If a new meta value was added and there was no previous value, add it.
                add_post_meta($post_id, 'schc_location_hours_' . $weekday . '_' . $option, $new_meta_value, true);
            } elseif ( $new_meta_value && $meta_value != $new_meta_value ){ //If the new meta value does not match the old value, update it.
                update_post_meta($post_id, 'schc_location_hours_' . $weekday . '_' . $option, $new_meta_value);
            } elseif ( $new_meta_value == '' && $meta_value ){ //If there is no new meta value but an old value exists, delete it.
                delete_post_meta($post_id, 'schc_location_hours_' . $weekday . '_' . $option, $meta_value);
            }
        }
    }
 
    $new_meta_value = sanitize_text_field($_POST['schc_location_hours_daysoff']); //Get the posted data and sanitize it if needed.
    $meta_value = get_post_meta($post_id, 'schc_location_hours_daysoff', true); //Get the meta value of the custom field key.
 
    if ( $new_meta_value && $meta_value == '' ){ //If a new meta value was added and there was no previous value, add it.
        add_post_meta($post_id, 'schc_location_hours_daysoff', $new_meta_value, true);
    } elseif ( $new_meta_value && $meta_value != $new_meta_value ){ //If the new meta value does not match the old value, update it.
        update_post_meta($post_id, 'schc_location_hours_daysoff', $new_meta_value);
    } elseif ( $new_meta_value == '' && $meta_value ){ //If there is no new meta value but an old value exists, delete it.
        delete_post_meta($post_id, 'schc_location_hours_daysoff', $meta_value);
    }
}
 
 
/****** Use the following to detect currently open/closed for a location: *********/
 
//Check if a specific location is open/closed
//$id is the location ID, $datetime is either a UTC integer or a string, $general is for if the location is open at any time that day.
function is_location_closed($id=false, $datetime=false, $general=false){ return !is_location_open($id, $datetime, $general); }
function is_location_open($id=false, $datetime=false, $general=false){
    if ( !$id ){
        $id = get_the_id();
    }
 
    if ( !$datetime ){
        $datetime = time();
    } else {
        if ( strtotime($datetime) ){
            $datetime = strtotime($datetime);
        }
    }
 
    $today = strtolower(date('l', $datetime));
 
    $locationHours = array();
    foreach ( array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') as $weekday ){
        $locationHours[$weekday] = array(
            'enabled' => get_post_meta($id, 'schc_location_hours_' . $weekday . '_enabled', true),
            'open' => get_post_meta($id, 'schc_location_hours_' . $weekday . '_open', true),
            'close' => get_post_meta($id, 'schc_location_hours_' . $weekday . '_close', true)
        );
    }
 
    if ( get_post_meta($id, 'schc_location_hours_daysoff', true) ){
        $days_off = explode(', ', get_post_meta($id, 'schc_location_hours_daysoff', true));
    } else {
        $days_off = explode(', ', get_option('nebula_business_hours_closed'));
    }
    if ( !empty(array_filter($days_off)) ){
        foreach ( $days_off as $key => $day_off ){
            $days_off[$key] = strtotime($day_off . ' ' . date('Y', $datetime));
 
            if ( date('N', $days_off[$key]) == 6 ){ //If the date is a Saturday
                $days_off[$key] = strtotime(date('F j, Y', $days_off[$key]) . ' -1 day');
            } elseif ( date('N', $days_off[$key]) == 7 ){ //If the date is a Sunday
                $days_off[$key] = strtotime(date('F j, Y', $days_off[$key]) . ' +1 day');
            }
 
            if ( date('Ymd', $days_off[$key]) == date('Ymd', $datetime) ){
                return false;
            }
        }
    }
 
    if ( $locationHours[$today]['enabled'] == '1' ){ //If the Nebula Options checkmark is checked for this day of the week.
        if ( $general ){
            return true;
        }
 
        $openToday = date('Gi', strtotime($locationHours[$today]['open']));
        $closeToday = date('Gi', strtotime($locationHours[$today]['close']));
 
        if ( date('Gi', $datetime) >= $openToday && date('Gi', $datetime) <= $closeToday ){
            return true;
        } else {
            return false;
        }
    }
 
    return false;
}
 
 
 
function location_open_until($id=false){
    if ( !$id ){
        $id = get_the_id();
    }
 
    if ( is_location_open($id) ){
        return get_post_meta($id, 'schc_location_hours_' . strtolower(date('l')) . '_close', true);
    }
 
    return false;
}
