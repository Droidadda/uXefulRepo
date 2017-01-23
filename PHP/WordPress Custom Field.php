<?php

<?php
	
//Fixed Prices Metabox and Custom Field
add_action('load-post.php', 'example_post_meta_boxes_setup');
add_action('load-post-new.php', 'example_post_meta_boxes_setup');
function example_add_post_meta_boxes(){
	add_meta_box('example-info', 'Example Information', 'example_meta_box', 'page', 'side', 'default');
}
function example_meta_box($object, $box){
	wp_nonce_field(basename(__FILE__), 'example_info_nonce');
	$example_info = get_post_meta($object->ID, 'example_info', true);
	?>

	<table class="fixed-pricing-table">
		<thead>
			<tr>
				<td>Provider</td>
				<td>Price</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Field 1</td>
				<td><input name="example-info[field_one]" placeholder="0" value="<?php echo $example_info['field_one']; ?>" /></td>
			</tr>
			<tr>
				<td>Field 2</td>
				<td><input name="example-info[field_two]" placeholder="0" value="<?php echo $example_info['field_two']; ?>" /></td>
			</tr>
			<tr>
				<td>Field 3</td>
				<td><input name="example-info[field_three]" placeholder="0" value="<?php echo $example_info['field_three']; ?>" /></td>
			</tr>
		</tbody>
	</table>

	<?php
}
function example_post_meta_boxes_setup(){
	add_action('add_meta_boxes', 'example_add_post_meta_boxes');
	add_action('save_post', 'example_save_post_class_meta', 10, 2);
}
function example_save_post_class_meta($post_id, $post){
	if ( !isset($_POST['example_info_nonce']) || !wp_verify_nonce($_POST['example_info_nonce'], basename(__FILE__)) ){
		return $post_id;
	}

	$post_type = get_post_type_object($post->post_type); //Get the post type object.
	if ( !current_user_can($post_type->cap->edit_post, $post_id) ){ //Check if the current user has permission to edit the post.
		return $post_id;
	}

	$old_data = get_post_meta($post_id, 'example_info', true);
	$new_data = $_POST['example-info']; //Get the raw data

	$prepped_data = array();
	foreach ( $new_data as $provider => $price ){
		$prepped_data[$provider] = sanitize_text_field($price); //Sanitize the data
	}

	//Update the data
	if ( !empty( $prepped_data ) && $prepped_data != $old_data ){
	    update_post_meta($post_id, 'example_info', $prepped_data);
	} elseif ( empty($prepped_data) && $old_data ){
	    delete_post_meta($post_id, 'example_info', $old_data);
	}
}
