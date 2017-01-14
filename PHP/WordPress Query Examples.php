//Basic
<?php query_posts( array('showposts' => 4, 'paged' => get_query_var('paged')) ); ?>
        <?php if ( have_posts() ) while ( have_posts() ): the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="news-title entry-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
 
                <div class="entry-meta">
                    <?php nebula_meta('on', 0); ?> <?php nebula_meta('cat'); ?> <?php nebula_meta('by'); ?> <?php nebula_meta('tags'); ?>
                </div>
 
                <div class="entry-content">
                    <?php echo nebula_the_excerpt('Read More &raquo;', 35, 1); ?>
                </div><!-- .entry-content -->
            </article><!-- #post-## -->
        <?php endwhile; ?>
 
 
//Cached using Transients
<?php
            $cached_query = get_transient('example_cached_query');
            if ( empty($cached_query) || is_debug() ){
                $cached_query = new WP_Query(array(
                    'post_type' => 'event',
                    'category_name' => 'concert',
                    'showposts' => 2,
                    'paged' => get_query_var('paged')
                ));
                set_transient('example_cached_query', $cached_query, 60*60); //1 hour cache
            }
            while ( $cached_query->have_posts() ): $cached_query->the_post();
        ?>
 
            <div class="home-feed-item event-feed-item">
                <h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>
                <?php echo nebula_the_excerpt('Read More &raquo;', 35, 1); ?>
            </div>
 
        <?php endwhile; ?>
 
 
//Multicolumn
<?php $count = 0; ?>
<?php query_posts( array( 'category_name' => 'Documentation', 'showposts' => 4, 'paged' => get_query_var('paged') ) ); ?>
    <?php if ( have_posts() ) while ( have_posts() ): the_post(); ?>
        <?php if ( $count%2 == 0 && $count != 0 ): ?>
            </div><!--/row-->
            <div class="row multi-column-query">
        <?php endif; ?>
 
        <div class="eight columns">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="news-title entry-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
 
                <div class="entry-meta">
                    <?php nebula_meta('on', 0); ?> <?php nebula_meta('cat'); ?> <?php nebula_meta('by'); ?> <?php nebula_meta('tags'); ?>
                </div>
 
                <div class="entry-content">
                    <?php echo nebula_the_excerpt('Read More &raquo;', 35, 1); ?>
                </div><!-- .entry-content -->
            </article><!-- #post-## -->
        </div><!--/columns-->
 
        <?php $count++; ?>
    <?php endwhile; ?>
 
 
 
 
 
<?php
            //Example "Event" post type query sorted by event time
            //query_posts(array('post_type' => array('event'), 'meta_key' => 'event_date', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'showposts' => 6, 'paged' => get_query_var('paged')));
        ?>
 
 
        <?php
            /*
                Example using loop.php (the "right" way to loop)
                This allows all post listings to be consistent.
            */
 
            //query_posts(array('showposts' => 4, 'paged' => get_query_var('paged')));
            //get_template_part('loop');
        ?>
 
 
        <?php //Example using a custom loop ?>
        <?php query_posts(array('showposts' => 4, 'paged' => get_query_var('paged'))); ?>
        <?php if ( have_posts() ) while ( have_posts() ): the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="news-title entry-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
 
                <div class="entry-meta">
                    <?php nebula_meta('on', 0); ?> <?php nebula_meta('cat'); ?> <?php nebula_meta('by'); ?> <?php nebula_meta('tags'); ?>
                </div>
 
                <div class="entry-content">
                    <?php echo nebula_the_excerpt('Read More &raquo;', 35, 1); ?>
                </div>
            </article>
        <?php endwhile; ?>
 
 
        <?php
            //If paginating, Pagenavi is recommended:
            wp_pagenavi();
        ?>
 
 
        <?php
            //Example using Nebula Infinite Load (see also infinite_load.php)
            //nebula_infinite_load_query(array('showposts' => 4, 'paged' => 1));
        ?>
 
 
        <?php
            //Always reset queries!
            wp_reset_query();
        ?>
