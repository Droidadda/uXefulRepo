//Add Open/Closed info to location listing columns
add_filter('manage_edit-location_columns', 'schc_location_hours_columns_head');
function schc_location_hours_columns_head($defaults){
    $defaults['openclosed'] = 'Open/Closed';
    return $defaults;
}
add_action('manage_location_posts_custom_column', 'schc_location_hours_columns_content', 15, 3);
function schc_location_hours_columns_content($column_name, $id){
    if ( $column_name == 'openclosed' ){
        $date = time();
        $today = strtolower(date('l', $date));
 
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
                $days_off[$key] = strtotime($day_off . ' ' . date('Y', $date));
 
                if ( date('N', $days_off[$key]) == 6 ){ //If the date is a Saturday
                    $days_off[$key] = strtotime(date('F j, Y', $days_off[$key]) . ' -1 day');
                } elseif ( date('N', $days_off[$key]) == 7 ){ //If the date is a Sunday
                    $days_off[$key] = strtotime(date('F j, Y', $days_off[$key]) . ' +1 day');
                }
 
                if ( date('Ymd', $days_off[$key]) == date('Ymd', $date) ){
                    echo '<span style="color: #601a1a;">Off Today</span>';
                    return;
                }
            }
        }
 
        if ( $locationHours[$today]['enabled'] == '1' ){ //If the Nebula Options checkmark is checked for this day of the week.
            $openToday = date('Gi', strtotime($locationHours[$today]['open']));
            $closeToday = date('Gi', strtotime($locationHours[$today]['close']));
 
            if ( date('Gi', $date) >= $openToday && date('Gi', $date) <= $closeToday ){
                echo '<span style="font-weight: bold; color: #58c026;">Open Now</span>';
            } else {
                echo '<span style="color: #ca3838;">Closed</span>';
            }
        } else {
            echo '<span style="color: #962828;">Closed Today</span>';
        }
    }
}
