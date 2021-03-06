var messageData = [{
            'message': jQuery(".ajax-example-form input.message").val()
        }];
        jQuery.ajax({
            type: "POST",
            url: nebula.site.ajax.url,
            data: {
                nonce: nebula.site.ajax.nonce,
                action: 'nebula_example_ajax',
                data: messageData,
            },
            success: function(response){
                jQuery('.example-response').css('border', '1px solid green').text(response);
                nebulaConversion('contact', 'Example AJAX Form');
            },
            error: function(MLHttpRequest, textStatus, errorThrown){
                jQuery('.example-response').css('border', '1px solid red').text('Error: ' + MLHttpRequest + ', ' + textStatus + ', ' + errorThrown);
                ga('send', 'event', 'Error', 'AJAX Error', 'Example AJAX');
            },
            timeout: 60000
        });
 
//PHP for WordPress
add_action('wp_ajax_nebula_example_ajax', 'nebula_example_ajax_function');
add_action('wp_ajax_nopriv_nebula_example_ajax', 'nebula_example_ajax_function');
function nebula_example_ajax_function() {
    if ( !wp_verify_nonce($_POST['nonce'], 'nebula_ajax_nonce')){ die('Permission Denied.'); }
    echo 'Success! Your message was: "' . $_POST['data'][0]['message'] . '"';
    exit();
}
