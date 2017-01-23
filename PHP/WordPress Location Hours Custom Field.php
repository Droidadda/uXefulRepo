<?php

//Fixed Prices Metabox and Custom Field
add_action('load-post.php', 'bre_post_meta_boxes_setup');
add_action('load-post-new.php', 'bre_post_meta_boxes_setup');
function bre_add_post_meta_boxes(){
	add_meta_box('bre-fixed-prices-electric', 'Electric Fixed Prices', 'bre_fixed_prices_electric_meta_box', 'page', 'side', 'default');
}
function bre_fixed_prices_electric_meta_box($object, $box){
	wp_nonce_field(basename(__FILE__), 'bre_fixed_prices_electric_nonce');
	?>

	<p>Explanation here.</p>

	<?php $bre_fixed_prices_electric_data = get_post_meta($object->ID, 'bre_fixed_prices_electric', true); ?>
	<div><label>National Grid</label> $<input name="bre-fixed-prices-electric[national_grid]" placeholder="0" value="<?php echo $bre_fixed_prices_electric_data['national_grid']; ?>" style="width: 100px;" /></div>
	<div><label>ConEd</label> $<input name="bre-fixed-prices-electric[coned]" placeholder="0" value="<?php echo $bre_fixed_prices_electric_data['coned']; ?>" style="width: 100px;" /></div>

	<?php
}
function bre_post_meta_boxes_setup(){
	add_action('add_meta_boxes', 'bre_add_post_meta_boxes');
	add_action('save_post', 'bre_save_post_class_meta', 10, 2);
}
function bre_save_post_class_meta($post_id, $post){
	if ( !isset($_POST['bre_fixed_prices_electric_nonce']) || !wp_verify_nonce($_POST['bre_fixed_prices_electric_nonce'], basename(__FILE__)) ){
		return $post_id;
	}

	$post_type = get_post_type_object($post->post_type); //Get the post type object.
	if ( !current_user_can($post_type->cap->edit_post, $post_id) ){ //Check if the current user has permission to edit the post.
		return $post_id;
	}

	$old_data = get_post_meta($post_id, 'bre_fixed_prices_electric', true);
	$new_data = $_POST['bre-fixed-prices-electric']; //Get the raw data

	$prepped_data = array();
	foreach ( $new_data as $key => $value ){
		$prepped_data[$key] = sanitize_text_field($value); //Sanitize the data
	}

	if ( !empty( $prepped_data ) && $prepped_data != $old_data ){
	    update_post_meta($post_id, 'bre_fixed_prices_electric', $prepped_data);
	} elseif ( empty($prepped_data) && $old_data ){
	    delete_post_meta($post_id, 'bre_fixed_prices_electric', $old_data);
	}
}
