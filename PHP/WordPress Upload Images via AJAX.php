<input id="testcert" type="file" name="testcert" />

<script>
	jQuery(document).on('change', '#testcert', function(e){ //This triggers on change of the file input field, but can be changed.
	    var fd = new FormData(); //Uses the Form API
	    fd.append('file', jQuery(this)[0].files[0]);
	    fd.append('action', 'tax_cert_upload'); //The PHP AJAX function to call
	    fd.append('nonce', nebula.site.ajax.nonce);
	    fd.append('custom_filename', 'optional_custom_filename_here'); //Could be pulled from an input val...

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
	if ( !wp_verify_nonce($_POST['nonce'], 'nebula_ajax_nonce') ){ die('Permission Denied.'); }

	//Rename the file
	if ( !empty($_POST['custom_filename']) ){ //Either pass it here or hard-code it.
		$file_name_parts = explode(".", $_FILES["file"]["name"]);
		$_FILES["file"]["name"] = sanitize_text_field($_POST['custom_filename']) . '.' . end($file_name_parts);
	}

	require_once(ABSPATH . 'wp-admin/includes/file.php'); //might only need this one.

	$upload_tax_cert = wp_handle_upload($_FILES['file'], array(
		'action' => $_POST['action'],
		'test_form' => false, //Required or else it fails
		'mimes' => array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png' => 'image/png',
			'pdf' => 'application/pdf',
			'doc' => 'application/msword',
		),
	));

	if ( $upload_tax_cert && !isset($upload_tax_cert['error']) ){
		echo "File is valid, and was successfully uploaded.\n";
		print_r($upload_tax_cert);
	} else {
		echo "There was an error\n";
		print_r($upload_tax_cert['error']);
	}

	wp_die();
}
