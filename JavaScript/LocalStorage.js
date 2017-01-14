jQuery(document).ready(function() {
        if ( jQuery('.cform7-message').length ){
            jQuery('.cform7-message').on('keyup', function(){
                localStorage.setItem('global_message', jQuery('.cform7-message').val());
                jQuery('.cform7-message').val(localStorage.getItem('global_message'));
            });
 
            jQuery(window).bind('storage',function(e){
                jQuery('.cform7-message').val(localStorage.getItem('global_message'));
            });
 
            jQuery('#localstorage-form').submit(function(){
                jQuery('.cform7-message').val('');
                localStorage.removeItem('global_message');
                return false;
            });
        }
    });
 
    jQuery(window).on('load', function(){
        if ( jQuery('.cform7-message').val() != '' ) {
            localStorage.setItem('global_message', jQuery('.cform7-message').val());
            jQuery('.cform7-message').val(localStorage.getItem('global_message'));
        } else {
            jQuery('.cform7-message').val(localStorage.getItem('global_message'));
        }
    });
