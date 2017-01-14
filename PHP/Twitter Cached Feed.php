//JAVASCRIPT
 
//Example 1
        //Fill pre-existing HTML with tweet data. This is good for displaying a single, latest tweet.
        jQuery.ajax({
            type: "POST",
            url: nebula.site.ajax.url,
            data: {
                nonce: nebula.site.ajax.nonce,
                action: 'nebula_twitter_cache',
                data: {
                    'username': 'Great_Blakes',
                    'listname': 'nebula',
                    'numbertweets': 5,
                    'includeretweets': 1,
                },
            },
            success: function(response){
                response = JSON.parse(response);
                if ( response.errors ){
                    jQuery('#tweet_user1').parent().html('Error ' + response.errors[0].code);
                    jQuery('#tweet_body1').html(response.errors[0].message);
                } else {
                    jQuery('#tweet_user_photo1').attr('href', 'https://twitter.com/' + response[0].user.screen_name).append('<img src="' + response[0].user.profile_image_url_https + '" title="' + response[0].user.description + '" />');
                    jQuery('#tweet_user1').attr('href', 'https://twitter.com/' + response[0].user.screen_name).text('@' + response[0].user.screen_name);
 
                    if ( nebula.user.client.browser.name == 'Safari' ){
                        var tweetTime = new Date(response[0].created_at);
                    } else {
                        var tweetTime = new Date(Date.parse(response[0].created_at.replace(/( \+)/, ' UTC$1'))); //UTC for IE8
                    }
 
                    jQuery('#tweet_body1').html(tweetLinks(response[0].text)).append(" <span class='twitter-posted-on'><i class='fa fa-clock-o'></i> " + timeAgo(tweetTime) + "</span>");
                }
            },
            error: function(MLHttpRequest, textStatus, errorThrown){
                ga('send', 'event', 'Error', 'AJAX Error', 'Twitter Feed');
            },
            timeout: 60000
        });
 
        //Example 2
        //Generate the markup within a UL to display tweets. This method is good for showing multiple tweets.
        jQuery.ajax({
            type: "POST",
            url: nebula.site.ajax.url,
            data: {
                nonce: nebula.site.ajax.nonce,
                action: 'nebula_twitter_cache',
                data: {
                    'username': 'Great_Blakes',
                    'listname': 'nebula',
                    'numbertweets': 5,
                    'includeRetweets': 1,
                },
            },
            success: function(response){
                response = JSON.parse(response);
                if ( response.errors ){
                    jQuery('.example2').append('<li><strong>Error ' + response.errors[0].code + '</strong><br /><span>' + response.errors[0].message + '</span></span></li>');
                } else {
                    jQuery.each(response, function(i){
                        //console.debug(response[i]); //Just to show all the data that is available.
 
                        if ( nebula.site.browser.name == 'Safari' ){
                            var tweetTime = new Date(response[i].created_at);
                        } else {
                            var tweetTime = new Date(Date.parse(response[i].created_at.replace(/( \+)/, ' UTC$1'))); //UTC for IE8
                        }
 
                        jQuery('.example2').append('<li><a class="twitter-user-photo" href="https://twitter.com/' + response[i].user.screen_name + '" target="_blank"><img src="' + response[i].user.profile_image_url_https + '" title="' + response[i].user.description + '" /></a><strong><a href="https://twitter.com/' + response[i].user.screen_name + '" target="_blank">@' + response[i].user.screen_name + '</a></strong><br /><span>' + tweetLinks(response[i].text) + ' <span class="twitter-posted-on"><i class="fa fa-clock-o"></i> ' + timeAgo(tweetTime) + '</span></span></li>');
                    });
                }
            },
            error: function(MLHttpRequest, textStatus, errorThrown){
                ga('send', 'event', 'Error', 'AJAX Error', 'Twitter Feed');
            },
            timeout: 60000
        });
 
//END JAVASCRIPT
 
 
 
 
//Twitter cached feed
//This function can be called with AJAX or as a standard function.
add_action('wp_ajax_nebula_twitter_cache', 'nebula_twitter_cache');
add_action('wp_ajax_nopriv_nebula_twitter_cache', 'nebula_twitter_cache');
function nebula_twitter_cache($username='Great_Blakes', $listname=null, $number_tweets=5, $include_retweets=1){
    if ( $_POST['data'] ){
        if ( !wp_verify_nonce($_POST['nonce'], 'nebula_ajax_nonce')){ die('Permission Denied.'); }
        $username = ( $_POST['data']['username'] )? $_POST['data']['username'] : 'Great_Blakes';
        $listname = ( $_POST['data']['listname'] )? $_POST['data']['listname'] : null; //Only used for list feeds
        $number_tweets = ( $_POST['data']['numbertweets'] )? $_POST['data']['numbertweets'] : 5;
        $include_retweets = ( $_POST['data']['includeretweets'] )? $_POST['data']['includeretweets'] : 1; //1: Yes, 0: No
    }
 
    error_reporting(0); //Prevent PHP errors from being cached.
 
    if ( $listname ){
        $feed = 'https://api.twitter.com/1.1/lists/statuses.json?slug=' . $listname . '&owner_screen_name=' . $username . '&count=' . $number_tweets . '&include_rts=' . $include_retweets;
    } else {
        $feed = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $username . '&count=' . $number_tweets . '&include_rts=' . $include_retweets;
    }
 
    $bearer = get_option('nebula_twitter_bearer_token', '');
 
    $tweets = get_transient('nebula_twitter_' . $username);
    if ( empty($tweets) || is_debug() ){
        $args = array('headers' => array('Authorization' => 'Bearer ' . $bearer));
        $response = wp_remote_get($feed, $args);
        $tweets = $response['body'];
 
        if ( !$tweets ){
            echo false;
            exit;
        }
 
        set_transient('nebula_twitter_' . $username, $tweets, 60*5); //5 minute expiration
    }
 
    error_reporting(1); //Re-enable PHP error reporting
 
    if ( $_POST['data'] ){
        echo $tweets;
        exit;
    } else {
        return $tweets;
    }
}
