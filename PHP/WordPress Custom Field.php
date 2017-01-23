<?php

//Location Hours Metabox and Custom Fields
add_action('load-post.php', 'example_post_meta_boxes_setup');
add_action('load-post-new.php', 'example_post_meta_boxes_setup');
function example_add_post_meta_boxes(){
    add_meta_box('example-location-hours', 'Example Custom Field', 'example_custom_field_meta_box', 'location', 'side', 'default');
}
function example_custom_field_meta_box($object, $box){
    wp_nonce_field(basename(__FILE__), 'example_custom_field_nonce');
    ?>
    <div>
        <p>Description here</p>
 
        <div>
            <label for="example_custom_field_here">Name here</label> <input type="text" name="example_custom_field_here" value="<?php echo get_post_meta($object->ID, 'example_custom_field_here', true); ?>" />
        </div> 
    </div>
<?php }

function example_post_meta_boxes_setup(){
    add_action('add_meta_boxes', 'example_add_post_meta_boxes');
    add_action('save_post', 'example_save_post_class_meta', 10, 2);
}
function example_save_post_class_meta($post_id, $post){
    if ( !isset($_POST['example_custom_field_nonce']) || !wp_verify_nonce($_POST['example_custom_field_nonce'], basename(__FILE__)) ){
        return $post_id;
    }
 
    $post_type = get_post_type_object($post->post_type); //Get the post type object.
    if ( !current_user_can($post_type->cap->edit_post, $post_id) ){ //Check if the current user has permission to edit the post.
        return $post_id;
    }
 
    $new_meta_value = sanitize_text_field($_POST['example_custom_field_here']); //Get the posted data and sanitize it if needed.
    $meta_value = get_post_meta($post_id, 'example_custom_field_here', true); //Get the meta value of the custom field key.

    if ( $new_meta_value && $meta_value == '' ){ //If a new meta value was added and there was no previous value, add it.
        add_post_meta($post_id, 'example_custom_field_here', $new_meta_value, true);
    } elseif ( $new_meta_value && $meta_value != $new_meta_value ){ //If the new meta value does not match the old value, update it.
        update_post_meta($post_id, 'example_custom_field_here', $new_meta_value);
    } elseif ( $new_meta_value == '' && $meta_value ){ //If there is no new meta value but an old value exists, delete it.
        delete_post_meta($post_id, 'example_custom_field_here', $meta_value);
    }
}
