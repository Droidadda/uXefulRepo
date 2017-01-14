//Nebula definitely has a more updated version of this.

//Breadcrumbs
function the_breadcrumb(){
    $override = apply_filters('pre_the_breadcrumb', false);
    if ( $override !== false ){echo $override; return;}
 
    global $post;
    $delimiter = '<span class="arrow">&rsaquo;</span>'; //Delimiter between crumbs
    $home = '<i class="fa fa-home"></i>'; //Text for the 'Home' link
    $showCurrent = 1; //1: Show current post/page title in breadcrumbs, 0: Don't show
    $before = '<span class="current">'; //Tag before the current crumb
    $after = '</span>'; //Tag after the current crumb
    $dontCapThese = array('the', 'and', 'but', 'of', 'a', 'and', 'or', 'for', 'nor', 'on', 'at', 'to', 'from', 'by', 'in');
    $homeLink = home_url('/');
 
    if ( $GLOBALS['http'] && is_int($GLOBALS['http']) ){
        echo '<div class="breadcrumbcon"><nav class="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ' . $before . 'Error ' . $GLOBALS['http'] . $after;
    } elseif ( is_home() || is_front_page() ){
        echo '<div class="breadcrumbcon"><nav class="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a></nav></div>';
        return false;
    } else {
        echo '<div class="breadcrumbcon"><nav class="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
        if ( is_category() ){
            $thisCat = get_category(get_query_var('cat'), false);
            if ( $thisCat->parent != 0 ){
                echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
            }
            echo $before . 'Category: ' . single_cat_title('', false) . $after;
        } elseif ( is_search() ){
            echo $before . 'Search results' . $after;
        } elseif ( is_day() ){
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;
        } elseif ( is_month() ){
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;
        } elseif ( is_year() ){
            echo $before . get_the_time('Y') . $after;
        } elseif ( is_single() && !is_attachment() ){
            if ( get_post_type() != 'post' ){
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
                if ( $showCurrent == 1 ){
                    echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                }
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ( $showCurrent == 0 ){
                    $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                }
                echo $cats;
                if ( $showCurrent == 1 ){
                    echo $before . get_the_title() . $after;
                }
            }
        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ){
            if ( is_archive() ){ //@TODO "Nebula" 0: Might not be perfect... This may never else out.
                $userdata = get_user_by('slug', get_query_var('author_name'));
                echo $before . $userdata->first_name . ' ' . $userdata->last_name . $after;
            } else { //What does this one do?
                $post_type = get_post_type_object(get_post_type());
                echo $before . $post_type->labels->singular_name . $after;
            }
        } elseif ( is_attachment() ){ //@TODO "Nebula" 0: Check for gallery pages? If so, it should be Home > Parent(s) > Gallery > Attachment
            if ( !empty($post->post_parent) ){ //@TODO "Nebula" 0: What happens if the page parent is a child of another page?
                echo '<a href="' . get_permalink($post->post_parent) . '">' . get_the_title($post->post_parent) . '</a>' . ' ' . $delimiter . ' ' . get_the_title();
            } else {
                echo get_the_title();
            }
        } elseif ( is_page() && !$post->post_parent ){
            if ( $showCurrent == 1 ){
                echo $before . get_the_title() . $after;
            }
        } elseif ( is_page() && $post->post_parent ){
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ( $parent_id ){
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ( $i = 0; $i < count($breadcrumbs); $i++ ){
                echo $breadcrumbs[$i];
                if ( $i != count($breadcrumbs)-1 ){
                    echo ' ' . $delimiter . ' ';
                }
            }
            if ( $showCurrent == 1 ){
                echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
            }
        } elseif ( is_tag() ){
            echo $before . 'Tag: ' . single_tag_title('', false) . $after;
        } elseif ( is_author() ){
            global $author;
            $userdata = get_userdata($author);
            echo $before . $userdata->display_name . $after;
        } elseif ( is_404() ){
            echo $before . 'Error 404' . $after;
        }
 
        if ( get_query_var('paged') ){
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ){
                echo ' (';
            }
            echo 'Page ' . get_query_var('paged');
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ){
                echo ')';
            }
        }
        echo '</nav></div><!--/breadcrumbcon-->';
    }
}
