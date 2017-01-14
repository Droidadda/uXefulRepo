//Nebula probably has an updated version of this.

//Separate a URL into it's components.
function nebula_url_components($segment="all", $url=null){
    $override = apply_filters('pre_nebula_url_components', false, $segment, $url);
    if ( $override !== false ){return $override;}
 
    if ( !$url ){
        $url = nebula_requested_url();
    }
 
    $url_compontents = parse_url($url);
    if ( empty($url_compontents['host']) ){
        return;
    }
    $host = explode('.', $url_compontents['host']);
 
    //Best way to get the domain so far. Probably a better way by checking against all known TLDs.
    preg_match("/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/", parse_url($url, PHP_URL_HOST), $domain);
    $sld = substr($domain[0], 0, strpos($domain[0], '.'));
    $tld = substr($domain[0], strpos($domain[0], '.'));
 
    switch ($segment){
        case ('all'):
        case ('href'):
            return $url;
            break;
 
        case ('protocol'): //Protocol and Scheme are aliases and return the same value.
        case ('scheme'): //Protocol and Scheme are aliases and return the same value.
        case ('schema'):
            if ( $url_compontents['scheme'] != '' ){
                return $url_compontents['scheme'];
            } else {
                return false;
            }
            break;
 
        case ('port'):
            if ( $url_compontents['port'] ){
                return $url_compontents['port'];
            } else {
                switch( $url_compontents['scheme'] ){
                    case ('http'):
                        return 80; //Default for http
                        break;
                    case ('https'):
                        return 443; //Default for https
                        break;
                    case ('ftp'):
                        return 21; //Default for ftp
                        break;
                    case ('ftps'):
                        return 990; //Default for ftps
                        break;
                    default:
                        return false;
                        break;
                }
            }
            break;
 
        case ('user'): //Returns the username from this type of syntax: https://username:password@gearside.com/
        case ('username'):
            if ( $url_compontents['user'] ){
                return $url_compontents['user'];
            } else {
                return false;
            }
            break;
 
        case ('pass'): //Returns the password from this type of syntax: https://username:password@gearside.com/
        case ('password'):
            if ( $url_compontents['pass'] ){
                return $url_compontents['pass'];
            } else {
                return false;
            }
            break;
 
        case ('authority'):
            if ( $url_compontents['user'] && $url_compontents['pass'] ){
                return $url_compontents['user'] . ':' . $url_compontents['pass'] . '@' . $url_compontents['host'] . ':' . nebula_url_components('port', $url);
            } else {
                return false;
            }
            break;
 
        case ('host'): //In http://something.example.com the host is "something.example.com"
        case ('hostname'):
            return $url_compontents['host'];
            break;
 
        case ('www') :
            if ( $host[0] == 'www' ){
                return 'www';
            } else {
                return false;
            }
            break;
 
        case ('subdomain'):
        case ('sub_domain'):
            if ( $host[0] != 'www' && $host[0] != $sld ){
                return $host[0];
            } else {
                return false;
            }
            break;
 
        case ('domain') : //In http://example.com the domain is "example.com"
            return $domain[0];
            break;
 
        case ('basedomain'): //In http://example.com/something the basedomain is "http://example.com"
        case ('base_domain'):
        case ('origin') :
            return $url_compontents['scheme'] . '://' . $domain[0];
            break;
 
        case ('sld') : //In example.com the sld is "example"
        case ('second_level_domain'):
        case ('second-level_domain'):
            return $sld;
            break;
 
        case ('tld') : //In example.com the tld is ".com"
        case ('top_level_domain'):
        case ('top-level_domain'):
            return $tld;
            break;
 
        case ('filepath'): //Filepath will be both path and file/extension
        case ('pathname'):
            return $url_compontents['path'];
            break;
 
        case ('file'): //Filename will be just the filename/extension.
        case ('filename'):
            if ( contains(basename($url_compontents['path']), array('.')) ){
                return basename($url_compontents['path']);
            } else {
                return false;
            }
            break;
 
        case ('extension'): //The extension only (without ".")
            if ( contains(basename($url_compontents['path']), array('.')) ){
                $file_parts = explode('.', $url_compontents['path']);
                return $file_parts[1];
            } else {
                return false;
            }
            break;
 
        case ('path'): //Path should be just the path without the filename/extension.
            if ( contains(basename($url_compontents['path']), array('.')) ){ //@TODO "Nebula" 0: This will possibly give bad data if the directory name has a "." in it
                return str_replace(basename($url_compontents['path']), '', $url_compontents['path']);
            } else {
                return $url_compontents['path'];
            }
            break;
 
        case ('query'):
        case ('queries'):
        case ('search'):
            return $url_compontents['query'];
            break;
 
        case ('fragment'):
        case ('fragments'):
        case ('anchor'):
        case ('hash') :
        case ('hashtag'):
        case ('id'):
            return $url_compontents['fragment'];
            break;
 
        default :
            return $url;
            break;
    }
}
