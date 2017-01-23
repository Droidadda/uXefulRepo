//JAVASCRIPT
 
//Search autocomplete
function autocompleteSearch(){
    nebula.dom.document.on('blur', ".nebula-search-iconable input", function(){
        jQuery('.nebula-search-iconable').removeClass('searching').removeClass('autocompleted');
    });
 
    jQuery("input#s, input.search").on('keypress paste', function(e){
        thisSearchInput = jQuery(this);
        nebulaTimer('autocompleteSearch', 'start');
        nebulaTimer('autocompleteResponse', 'start');
        if ( !thisSearchInput.hasClass('no-autocomplete') && !nebula.dom.html.hasClass('lte-ie8') && thisSearchInput.val().trim().length ){
            if ( thisSearchInput.parents('form').hasClass('nebula-search-iconable') && thisSearchInput.val().trim().length >= 2 && searchTriggerOnlyChars(e) ){
                thisSearchInput.parents('form').addClass('searching');
                setTimeout(function(){
                    thisSearchInput.parents('form').removeClass('searching');
                }, 10000);
            } else {
                thisSearchInput.parents('form').removeClass('searching');
            }
 
            thisSearchInput.autocomplete({
                position: {
                    my: "left top-2px",
                    at: "left bottom",
                    collision: "flip",
                },
                source: function(request, response){
                    jQuery.ajax({
                        dataType: 'json',
                        type: "POST",
                        url: nebula.site.ajax.url,
                        data: {
                            nonce: nebula.site.ajax.nonce,
                            action: 'nebula_autocomplete_search',
                            data: request,
                        },
                        success: function(data){
                            ga('set', gaCustomMetrics['autocompleteSearches'], 1);
                            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                            ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Internal Search'));
                            if ( data ){
                                jQuery.each(data, function(index, value){
                                    value.label = value.label.replace(/&#038;/g, "\&");
                                });
                                noSearchResults = '';
                            } else {
                                noSearchResults = ' (No Results)';
                                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('No Search Results'));
                            }
                            debounce(function(){
                                ga('send', 'event', 'Internal Search', 'Autocomplete Search' + noSearchResults, request.term);
                                if ( typeof fbq === 'function' ){fbq('track', 'Search', {search_string: request.term});}
                                nebulaConversion('keywords', request.term);
                            }, 500, 'autocomplete success buffer');
                            ga('send', 'timing', 'Autocomplete Search', 'Server Response', Math.round(nebulaTimer('autocompleteSearch', 'lap')), 'Each search until server results');
                            response(data);
                            thisSearchInput.parents('form').removeClass('searching').addClass('autocompleted');
                        },
                        error: function(MLHttpRequest, textStatus, errorThrown){
                            ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                            debounce(function(){
                                ga('set', gaCustomDimensions['sessionNotes'], sessionNote('Autocomplete Search Error'));
                                ga('send', 'event', 'Internal Search', 'Autcomplete Error', request.term);
                            }, 500, 'autocomplete error buffer');
                            thisSearchInput.parents('form').removeClass('searching');
                        },
                        timeout: 60000
                    });
                },
                focus: function(event, ui){
                    event.preventDefault(); //Prevent input value from changing.
                },
                select: function(event, ui){
                    ga('set', gaCustomMetrics['autocompleteSearchClicks'], 1);
                    ga('set', gaCustomDimensions['timestamp'], localTimestamp());
                    ga('send', 'event', 'Internal Search', 'Autocomplete Click', ui.item.label);
                    ga('send', 'timing', 'Autocomplete Search', 'Until Navigation', Math.round(nebulaTimer('autocompleteSearch', 'end')), 'From first initial search until navigation');
                    if ( typeof ui.item.external !== 'undefined' ){
                        window.open(ui.item.link, '_blank');
                    } else {
                        window.location.href = ui.item.link;
                    }
                },
                open: function(){
                    thisSearchInput.parents('form').addClass('autocompleted');
                    var heroAutoCompleteDropdown = jQuery('.form-identifier-nebula-hero-search');
                    heroAutoCompleteDropdown.css('max-width', thisSearchInput.outerWidth());
                },
                close: function(){
                    thisSearchInput.parents('form').removeClass('autocompleted');
                },
                minLength: 3,
            }).data("ui-autocomplete")._renderItem = function(ul, item){
                thisSimilarity = ( typeof item.similarity !== 'undefined' )? item.similarity.toFixed(1) + '% Match' : '';
                var listItem = jQuery("<li class='" + item.classes + "' title='" + thisSimilarity + "'></li>").data("item.autocomplete", item).append("<a> " + item.label.replace(/\\/g, '') + "</a>").appendTo(ul);
                return listItem;
            };
            var thisFormIdentifier = thisSearchInput.parents('form').attr('id') || thisSearchInput.parents('form').attr('name') || thisSearchInput.parents('form').attr('class');
            thisSearchInput.autocomplete("widget").addClass("form-identifier-" + thisFormIdentifier);
        }
    });
}
 
//END JAVASCRIPT
 
 
<?php

//Easily create markup for a Hero area search input
function nebula_hero_search($placeholder='What are you looking for?'){
    $override = apply_filters('pre_nebula_hero_search', false, $placeholder);
    if ( $override !== false ){echo $override; return;}
 
    echo '<div id="nebula-hero-formcon">
        <form id="nebula-hero-search" class="nebula-search-iconable search" method="get" action="' . home_url('/') . '">
            <input type="search" class="nebula-search open input search nofade" name="s" placeholder="' . $placeholder . '" autocomplete="off" x-webkit-speech />
        </form>
    </div>';
}
 
//Autocomplete Search AJAX.
add_action('wp_ajax_nebula_autocomplete_search', 'nebula_autocomplete_search');
add_action('wp_ajax_nopriv_nebula_autocomplete_search', 'nebula_autocomplete_search');
function nebula_autocomplete_search(){
    if ( !wp_verify_nonce($_POST['nonce'], 'nebula_ajax_nonce')){ die('Permission Denied.'); }
 
    ini_set('memory_limit', '256M');
    $_POST['data']['term'] = trim($_POST['data']['term']);
    if ( empty($_POST['data']['term']) ){
        return false;
        exit;
    }
 
    //Test for close or exact matches. Use: $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
    function nebula_close_or_exact($rating=0, $close_threshold=80, $exact_threshold=95){
        if ( $rating > $exact_threshold ){
            return ' exact-match';
        } elseif ( $rating > $close_threshold ){
            return ' close-match';
        }
        return '';
    }
 
    //Standard WP search (does not include custom fields)
    $q1 = new WP_Query(array(
        'post_type' => array('any'),
        'post_status' => 'publish',
        'posts_per_page' => 4,
        's' => $_POST['data']['term'],
    ));
 
    //Search custom fields
    $q2 = new WP_Query(array(
        'post_type' => array('any'),
        'post_status' => 'publish',
        'posts_per_page' => 4,
        'meta_query' => array(
            array(
                'value' => $_POST['data']['term'],
                'compare' => 'LIKE'
            )
        )
    ));
 
    //Combine the above queries
    $autocomplete_query = new WP_Query();
    $autocomplete_query->posts = array_unique(array_merge($q1->posts, $q2->posts), SORT_REGULAR);
    $autocomplete_query->post_count = count($autocomplete_query->posts);
 
    //Loop through the posts
    if ( $autocomplete_query->have_posts() ){
        while ( $autocomplete_query->have_posts() ){
            $autocomplete_query->the_post();
            if ( !get_the_title() ){ //Ignore results without titles
                continue;
            }
            $post = get_post();
 
            $suggestion = array();
            similar_text(strtolower($_POST['data']['term']), strtolower(get_the_title()), $suggestion['similarity']); //Determine how similar the query is to this post title
            $suggestion['label'] = get_the_title();
            $suggestion['link'] = get_permalink();
 
            $suggestion['classes'] = 'type-' . get_post_type() . ' id-' . get_the_id() . ' slug-' . $post->post_name . ' similarity-' . str_replace('.', '_', number_format($suggestion['similarity'], 2));
            if ( get_the_id() == get_option('page_on_front') ){
                $suggestion['classes'] .= ' page-home';
            } elseif ( is_sticky() ){ //@TODO "Nebula" 0: If sticky post. is_sticky() does not work here?
                $suggestion['classes'] .= ' sticky-post';
            }
            $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
            $suggestions[] = $suggestion;
        }
    }
 
    //Find media library items
    $attachments = get_posts(array('post_type' => 'attachment', 's' => $_POST['data']['term'], 'numberposts' => 10, 'post_status' => null));
    if ( $attachments ){
        $attachment_count = 0;
        foreach ( $attachments as $attachment ){
            if ( strpos(get_attachment_link($attachment->ID), '?attachment_id=') ){ //Skip if media item is not associated with a post.
                continue;
            }
            $suggestion = array();
            $attachment_meta = wp_get_attachment_metadata($attachment->ID);
            $path_parts = pathinfo($attachment_meta['file']);
            $attachment_search_meta = ( get_the_title($attachment->ID) != '' )? get_the_title($attachment->ID) : $path_parts['filename'];
            similar_text(strtolower($_POST['data']['term']), strtolower($attachment_search_meta), $suggestion['similarity']);
            if ( $suggestion['similarity'] >= 50 ){
                $suggestion['label'] = ( get_the_title($attachment->ID) != '' )? get_the_title($attachment->ID) : $path_parts['basename'];
                $suggestion['classes'] = 'type-attachment file-' . $path_parts['extension'];
                $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
                if ( in_array(strtolower($path_parts['extension']), array('jpg', 'jpeg', 'png', 'gif', 'bmp')) ){
                    $suggestion['link'] = get_attachment_link($attachment->ID);
                } else {
                    $suggestion['link'] = wp_get_attachment_url($attachment->ID);
                    $suggestion['external'] = true;
                    $suggestion['classes'] .= ' external-link';
                }
                $suggestion['similarity'] = $suggestion['similarity']-0.001; //Force lower priority than posts/pages.
                $suggestions[] = $suggestion;
                $attachment_count++;
            }
            if ( $attachment_count >= 2 ){
                break;
            }
        }
    }
 
    //Find menu items
    $menus = get_transient('nebula_autocomplete_menus');
    if ( empty($menus) || is_debug() ){
        $menus = get_terms('nav_menu');
        set_transient('nebula_autocomplete_menus', $menus, 60*60); //1 hour cache
    }
    foreach($menus as $menu){
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        foreach ( $menu_items as $key => $menu_item ){
            $suggestion = array();
            similar_text(strtolower($_POST['data']['term']), strtolower($menu_item->title), $menu_title_similarity);
            similar_text(strtolower($_POST['data']['term']), strtolower($menu_item->attr_title), $menu_attr_similarity);
            if ( $menu_title_similarity >= 65 || $menu_attr_similarity >= 65 ){
                if ( $menu_title_similarity >= $menu_attr_similarity ){
                    $suggestion['similarity'] = $menu_title_similarity;
                    $suggestion['label'] = $menu_item->title;
                } else {
                    $suggestion['similarity'] = $menu_attr_similarity;
                    $suggestion['label'] = $menu_item->attr_title;
                }
                $suggestion['link'] = $menu_item->url;
                $path_parts = pathinfo($menu_item->url);
                $suggestion['classes'] = 'type-menu-item';
                if ( $path_parts['extension'] ){
                    $suggestion['classes'] .= ' file-' . $path_parts['extension'];
                    $suggestion['external'] = true;
                } elseif ( !strpos($suggestion['link'], nebula_url_components('domain')) ){
                    $suggestion['classes'] .= ' external-link';
                    $suggestion['external'] = true;
                }
                $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
                $suggestion['similarity'] = $suggestion['similarity']-0.001; //Force lower priority than posts/pages.
                $suggestions[] = $suggestion;
                break;
            }
        }
    }
 
    //Find categories
    $categories = get_transient('nebula_autocomplete_categories');
    if ( empty($categories) || is_debug() ){
        $categories = get_categories();
        set_transient('nebula_autocomplete_categories', $categories, 60*60); //1 hour cache
    }
    foreach ( $categories as $category ){
        $suggestion = array();
        $cat_count = 0;
        similar_text(strtolower($_POST['data']['term']), strtolower($category->name), $suggestion['similarity']);
        if ( $suggestion['similarity'] >= 65 ){
            $suggestion['label'] = $category->name;
            $suggestion['link'] = get_category_link($category->term_id);
            $suggestion['classes'] = 'type-category';
            $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
            $suggestions[] = $suggestion;
            $cat_count++;
        }
        if ( $cat_count >= 2 ){
            break;
        }
    }
 
    //Find tags
    $tags = get_transient('nebula_autocomplete_tags');
    if ( empty($tags) || is_debug() ){
        $tags = get_tags();
        set_transient('nebula_autocomplete_tags', $tags, 60*60); //1 hour cache
    }
    foreach ( $tags as $tag ){
        $suggestion = array();
        $tag_count = 0;
        similar_text(strtolower($_POST['data']['term']), strtolower($tag->name), $suggestion['similarity']);
        if ( $suggestion['similarity'] >= 65 ){
            $suggestion['label'] = $tag->name;
            $suggestion['link'] = get_tag_link($tag->term_id);
            $suggestion['classes'] = 'type-tag';
            $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
            $suggestions[] = $suggestion;
            $tag_count++;
        }
        if ( $tag_count >= 2 ){
            break;
        }
    }
 
    //Find authors (if author bios are enabled)
    if ( nebula_option('nebula_author_bios', 'enabled') ){
        $authors = get_transient('nebula_autocomplete_authors');
        if ( empty($authors) || is_debug() ){
            $authors = get_users(array('role' => 'author')); //@TODO "Nebula" 0: This should get users who have made at least one post. Maybe get all roles (except subscribers) then if postcount >= 1?
            set_transient('nebula_autocomplete_authors', $authors, 60*60); //1 hour cache
        }
        foreach ( $authors as $author ){
            $author_name = ( $author->first_name != '' )? $author->first_name . ' ' . $author->last_name : $author->display_name; //might need adjusting here
            if ( strtolower($author_name) == strtolower($_POST['data']['term']) ){ //todo: if similarity of author name and query term is higher than X. Return only 1 or 2.
                $suggestion = array();
                $suggestion['label'] = $author_name;
                $suggestion['link'] = 'http://google.com/';
                $suggestion['classes'] = 'type-user';
                $suggestion['classes'] .= nebula_close_or_exact($suggestion['similarity']);
                $suggestion['similarity'] = ''; //todo: save similarity to array too
                $suggestions[] = $suggestion;
                break;
            }
        }
    }
 
    if ( sizeof($suggestions) >= 1 ){
        //Order by match similarity to page title (DESC).
        function autocomplete_similarity_compare($a, $b){
            return $b['similarity'] - $a['similarity'];
        }
        usort($suggestions, "autocomplete_similarity_compare");
 
        //Remove any duplicate links (higher similarity = higher priority)
        $outputArray = array(); //This array is where unique results will be stored
        $keysArray = array(); //This array stores values to check duplicates against.
        foreach ( $suggestions as $suggestion ){
            if ( !in_array($suggestion['link'], $keysArray) ){
                $keysArray[] = $suggestion['link'];
                $outputArray[] = $suggestion;
            }
        }
    }
 
    //Link to search at the end of the list
    //@TODO "Nebula" 0: The empty result is not working for some reason... (Press Enter... is never appearing)
    $suggestion = array();
    $suggestion['label'] = ( sizeof($suggestions) >= 1 )? '...more results for "' . $_POST['data']['term'] . '"' : 'Press enter to search for "' . $_POST['data']['term'] . '"';
    $suggestion['link'] = home_url('/') . '?s=' . str_replace(' ', '%20', $_POST['data']['term']);
    $suggestion['classes'] = ( sizeof($suggestions) >= 1 )? 'more-results search-link' : 'no-results search-link';
    $outputArray[] = $suggestion;
 
    echo json_encode($outputArray, JSON_PRETTY_PRINT);
    exit;
}
