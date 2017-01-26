<input id="testcert" type="file" name="testcert" />

<script>
	jQuery(document).on('change', '#testcert', function(e){ //This triggers on change of the file input field, but can be changed.
	    var fd = new FormData(); //Uses the Form API
	    fd.append('file', jQuery(this)[0].files[0]);
	    fd.append('action', 'tax_cert_upload'); //The PHP AJAX function to call

	    jQuery.ajax({
	        type: 'POST',
	        url: nebula.site.ajax.url, //admin-ajax.php
	        nonce: nebula.site.ajax.nonce,
	        data: fd,
	        contentType: false,
	        processData: false,
	        success: function(response){
	            console.log('success!');
	            console.log(response);
	        },
	        error: function(){
		        console.log('ajax error :(');
	        }
	    });
	});
</script>


<?php
//functions.php

//Upload files via AJAX
add_action('wp_ajax_tax_cert_upload', 'tax_cert_upload');
add_action('wp_ajax_nopriv_tax_cert_upload', 'tax_cert_upload');
function tax_cert_upload(){
	echo "Inside PHP function. Raw files:\n";
  print_r($_FILES);

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php'); //might only need this one.
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$upload_tax_cert = wp_handle_upload($_FILES['file'], array('test_form' => FALSE)); //This array is required or else it does not work.
	if ( $upload_tax_cert && !isset($upload_tax_cert['error']) ){
		echo "File is valid, and was successfully uploaded.\n";
		print_r($upload_tax_cert);
	} else {
		echo "There was an error\n";
		print_r($upload_tax_cert['error']);
	}

	wp_die();
}
