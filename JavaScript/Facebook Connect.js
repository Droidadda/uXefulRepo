//Load the SDK asynchronously
function facebookSDK(){
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
}
 
//Facebook Connect functions
function facebookConnect(){
    window.fbConnectFlag = false;
    if ( nebula.site.options.facebook_app_id ){
        window.fbAsyncInit = function(){
            FB.init({
                appId: nebula.site.options.facebook_app_id,
                channelUrl: nebula.site.template_directory + '/includes/channel.php',
                status: true,
                xfbml: true
            });
 
            checkFacebookStatus();
            FB.Event.subscribe('edge.create', function(href, widget){ //Facebook Likes
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('FB Liked'));
                ga('send', {'hitType': 'social', 'socialNetwork': 'Facebook', 'socialAction': 'Like', 'socialTarget': href, 'page': nebula.dom.document.attr('title')});
                ga('send', 'event', 'Social', 'Facebook Like');
                nebulaConversion('facebook', 'like');
            });
 
            FB.Event.subscribe('edge.remove', function(href, widget){ //Facebook Unlikes
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('FB Unliked'));
                ga('send', {'hitType': 'social', 'socialNetwork': 'Facebook', 'socialAction': 'Unlike', 'socialTarget': href, 'page': nebula.dom.document.attr('title')});
                ga('send', 'event', 'Social', 'Facebook Unlike');
                nebulaConversion('facebook', 'like', 'remove');
            });
 
            FB.Event.subscribe('message.send', function(href, widget){ //Facebook Send/Share
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('FB Share'));
                ga('send', {'hitType': 'social', 'socialNetwork': 'Facebook', 'socialAction': 'Send', 'socialTarget': href, 'page': nebula.dom.document.attr('title')});
                ga('send', 'event', 'Social', 'Facebook Share');
                nebulaConversion('facebook', 'share');
            });
 
            FB.Event.subscribe('comment.create', function(href, widget){ //Facebook Comments
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('FB Comment'));
                ga('send', {'hitType': 'social', 'socialNetwork': 'Facebook', 'socialAction': 'Comment', 'socialTarget': href, 'page': nebula.dom.document.attr('title')});
                ga('send', 'event', 'Social', 'Facebook Comment');
                nebulaConversion('facebook', 'comment');
            });
        };
 
        nebula.dom.document.on('click touch tap', '.facebook-connect', function(){
            facebookLoginLogout();
            return false;
        });
    } else {
        jQuery('.facebook-connect').remove();
    }
}
 
//Connect to Facebook without using Facebook Login button
function facebookLoginLogout(){
    if ( !nebula.user.facebook.status ){
        FB.login(function(response){
            checkFacebookStatus();
        }, {scope:'public_profile,email'});
    } else {
        FB.logout(function(response){
            checkFacebookStatus();
        });
    }
    return false;
}
 
//Fetch Facebook user information
function checkFacebookStatus(){
    FB.getLoginStatus(function(response){
        nebula.user.facebook = {'status': response.status}
        if ( nebula.user.facebook.status === 'connected' ){ //User is logged into Facebook and is connected to this app.
            FB.api('/me', function(response){
                //Update the Nebula User Facebook Object
                nebula.user.facebook = {
                    id: response.id,
                    name: {
                        first: response.first_name,
                        last: response.last_name,
                        full: response.name,
                    },
                    gender: response.gender,
                    email: response.email,
                    image: {
                        base: 'https://graph.facebook.com/' + response.id + '/picture',
                        thumbnail: 'https://graph.facebook.com/' + response.id + '/picture?width=100&height=100',
                        large: 'https://graph.facebook.com/' + response.id + '/picture?width=1000&height=1000',
                    },
                    url: response.link,
                    location: {
                        locale: response.locale,
                        timezone: response.timezone,
                    },
                    verified: response.verified,
                }
                nebulaConversion('facebook', 'connect');
 
                //Update Nebula User Object
                nebula.user.name = {
                    first: response.first_name,
                    last: response.last_name,
                    full: response.name,
                };
                nebula.user.gender = response.gender;
                nebula.user.email = response.email;
                nebula.user.location = {
                    locale: response.locale,
                    timezone: response.timezone,
                }
 
                ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('FB Connect'));
                ga('send', 'event', 'Social', 'Facebook Connect', nebula.user.facebook.id);
                nebula.dom.body.removeClass('fb-disconnected').addClass('fb-connected fb-' + nebula.user.facebook.id);
                nebula.dom.document.trigger('fbConnected');
            });
        } else if ( nebula.user.facebook.status === 'not_authorized' ){ //User is logged into Facebook, but has not connected to this app.
            nebulaConversion('facebook', 'connect', 'remove');
            nebula.dom.body.removeClass('fb-connected').addClass('fb-not_authorized');
            nebula.dom.document.trigger('fbNotAuthorized');
        } else { //User is not logged into Facebook.
            nebulaConversion('facebook', 'connect', 'remove');
            nebula.dom.body.removeClass('fb-connected').addClass('fb-disconnected');
            nebula.dom.document.trigger('fbDisconnected');
        }
    });
}
