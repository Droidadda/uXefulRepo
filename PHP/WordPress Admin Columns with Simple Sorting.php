<?php

//Add ID column on post/page listings
add_filter('manage_faqs_posts_columns', 'nebula_faq_columns_head');
function nebula_faq_columns_head($defaults){
    $defaults['faq-category'] = 'Category';
    return $defaults;
}

add_action('manage_posts_custom_column', 'nebula_faq_columns_content', 15, 3);
function nebula_faq_columns_content($column_name, $id){
    if ( $column_name == 'faq-category' ){
        $post = get_post();
        echo $post->custom_fields["faq_category"][0];
    }
}

//Sortable columns in Product admin
add_filter('manage_edit-faqs_sortable_columns', 'nebula_faq_sortable_columns');
function nebula_faq_sortable_columns($columns) {
    $columns['faq-category'] = 'Category';
    return $columns;
}
add_action('pre_get_posts', 'nebula_faq_columns_orderby');
function nebula_faq_columns_orderby($query) {
    $orderby = $query->get('orderby');

    if ( $orderby == 'faq-category' ) {
        $query->set('meta_key', 'faq_category');
        $query->set('orderby', 'meta_value');
    }
}
