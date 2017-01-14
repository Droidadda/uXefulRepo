//Nebula certainly has a more updated version of this.

//Detect weather for Zip Code (using Yahoo! Weather)
function nebula_weather($zipcode=null, $data=''){
    $override = apply_filters('pre_nebula_weather', false, $zipcode, $data);
    if ( $override !== false ){return $override;}
 
    if ( !empty($zipcode) && is_string($zipcode) && !ctype_digit($zipcode) ){ //ctype_alpha($zipcode)
        $data = $zipcode;
        $zipcode = get_option('nebula_postal_code', '13204');
    } elseif ( empty($zipcode) ){
        $zipcode = get_option('nebula_postal_code', '13204');
    }
 
    $weather_json = get_transient('nebula_weather_' . $zipcode);
    if ( empty($weather_json) ){ //No ?debug option here (because multiple calls are made to this function). Clear with a force true when needed.
        $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text=' . $zipcode . ')';
 
        WP_Filesystem();
        global $wp_filesystem;
        $weather_json = $wp_filesystem->get_contents('http://query.yahooapis.com/v1/public/yql?q=' . urlencode($yql_query) . '&format=json');
 
        set_transient('nebula_weather_' . $zipcode, $weather_json, 60*5); //5 minute expiration
    }
    $weather_json = json_decode($weather_json);
 
    if ( !$weather_json || empty($weather_json) ){
        trigger_error('A weather error occurred (Forecast for ' . $zipcode . ' may not exist).', E_USER_WARNING);
        return false;
    } elseif ( $data == '' ){
        return true;
    }
 
    switch ( $data ){
        case 'json':
            return $weather_json;
            break;
        case 'reported':
        case 'build':
        case 'lastBuildDate':
            return $weather_json->query->results->channel->lastBuildDate;
            break;
        case 'city':
            return $weather_json->query->results->channel->location->city;
            break;
        case 'state':
        case 'region':
            return $weather_json->query->results->channel->location->region;
            break;
        case 'country':
            return $weather_json->query->results->channel->location->country;
            break;
        case 'location':
            return $weather_json->query->results->channel->location->city . ', ' . $weather_json->query->results->channel->location->region;
            break;
        case 'latitude':
        case 'lat':
            return $weather_json->query->results->channel->item->lat;
            break;
        case 'longitude':
        case 'long':
        case 'lng':
            return $weather_json->query->results->channel->item->long;
            break;
        case 'geo':
        case 'geolocation':
        case 'coordinates':
            return $weather_json->query->results->channel->item->lat . ',' . $weather_json->query->results->channel->item->lat;
            break;
        case 'windchill':
        case 'wind chill':
        case 'chill':
            return $weather_json->query->results->channel->wind->chill;
            break;
        case 'windspeed':
        case 'wind speed':
            return $weather_json->query->results->channel->wind->speed;
            break;
        case 'sunrise':
            return $weather_json->query->results->channel->astronomy->sunrise;
            break;
        case 'sunset':
            return $weather_json->query->results->channel->astronomy->sunset;
            break;
        case 'temp':
        case 'temperature':
            return $weather_json->query->results->channel->item->condition->temp;
            break;
        case 'condition':
        case 'conditions':
        case 'current':
        case 'currently':
            return $weather_json->query->results->channel->item->condition->text;
            break;
        case 'forecast':
            return $weather_json->query->results->channel->item->forecast;
            break;
        default:
            break;
    }
    return false;
}
