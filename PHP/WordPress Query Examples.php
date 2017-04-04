<?php
	//Example "Event" post type query sorted by event time
	$args = array('post_type' => array('event'), 'meta_key' => 'event_date', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'showposts' => 6, 'paged' => get_query_var('paged'));
?>

<?php
	/*
		Example using loop.php
		This allows all post listings to be consistent.
	*/

	//query_posts($args);
	//get_template_part('loop');
?>




<?php //Example using a custom loop with query_posts (avoid if possible in favor of WP_Query) ?>
<?php query_posts($args); ?>
<?php while ( have_posts() ): the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php if ( has_post_thumbnail() ): ?>
			<a href="<?php echo get_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
		<?php endif; ?>

        <h2 class="news-title entry-title"><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>

        <div class="entry-meta">
        	<?php nebula()->meta('on', 0); ?> <?php nebula()->meta('cat'); ?> <?php nebula()->meta('by'); ?> <?php nebula()->meta('tags'); ?>
        </div>

        <div class="entry-content">
            <?php echo nebula()->excerpt(array('more' => 'Read More &raquo;', 'length' => 35, 'ellipsis' => true)); ?>
        </div>
    </article>
<?php endwhile; ?>

<?php //Example using a custom loop with WP_Query ?>
<?php $example_query = new WP_Query($args); ?>
<?php //Example to get just the first post ID: $example_query->posts[0]->ID; ?>
<?php while ( $example_query->have_posts() ): $example_query->the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php if ( has_post_thumbnail() ): ?>
			<a href="<?php echo get_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
		<?php endif; ?>

        <h2 class="news-title entry-title"><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>

        <div class="entry-meta">
        	<?php nebula()->meta('on', 0); ?> <?php nebula()->meta('cat'); ?> <?php nebula()->meta('by'); ?> <?php nebula()->meta('tags'); ?>
        </div>

        <div class="entry-content">
            <?php echo nebula()->excerpt(array('more' => 'Read More &raquo;', 'length' => 35, 'ellipsis' => true)); ?>
        </div>
    </article>
<?php endwhile; ?>




<?php
	//Cached Query using transients
	//Note: You must set a new transient for each page of the query.
	$cached_query = get_transient('example_cached_query' . get_query_var('paged'));
	if ( empty($cached_query) || is_debug() ){
	    $cached_query = new WP_Query(array(
	        'post_type' => 'event',
	        'category_name' => 'concert',
	        'showposts' => 2,
	        'paged' => get_query_var('paged')
	    ));
	    set_transient('example_cached_query' . get_query_var('paged'), $cached_query, 60*60); //1 hour cache
	}
	while ( $cached_query->have_posts() ): $cached_query->the_post();
?>
    <div class="home-feed-item event-feed-item">
        <h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>
        <?php echo nebula()->excerpt(array('more' => 'Read More &raquo;', 'length' => 35, 'ellipsis' => true)); ?>
    </div>
<?php endwhile; ?>

<?php
	if ( is_plugin_active('wp-pagenavi/wp-pagenavi.php') ){
		wp_pagenavi(array('query' => $cached_query));
	}
?>




<?php
	/*
		Multi-column query
		Note: Consider using WP_Query as shown in the basic query example.
		Also note: Using Bootstrap, creating a new row may not be necessary. You could continue to repeat columns with no adverse affects.
	*/
?>
<?php query_posts(array('category_name' => 'Documentation', 'showposts' => 4, 'paged' => get_query_var('paged'))); ?>
<?php if ( have_posts() ): ?>
	<?php $count = 0; ?>
	<div class="row multi-column-query">
		<?php while ( have_posts() ): the_post(); ?>
	        <?php if ( $count%2 == 0 && $count != 0 ): //Not exactly necessary with Bootstrap, but doesn't hurt. ?>
	            </div><!--/row-->
	            <div class="row multi-column-query">
	        <?php endif; ?>

	        <div class="col-md-6">
			    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			        <h2 class="news-title entry-title"><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>

			        <div class="entry-meta">
			        	<?php nebula()->meta('on', 0); ?> <?php nebula()->meta('cat'); ?> <?php nebula()->meta('by'); ?> <?php nebula()->meta('tags'); ?>
			        </div>

			        <div class="entry-content">
			            <?php echo nebula()->excerpt(array('more' => 'Read More &raquo;', 'length' => 35, 'ellipsis' => true)); ?>
			        </div>
			    </article>
			</div><!--/col-->

	        <?php $count++; ?>
	    <?php endwhile; ?>
	</div><!--/row-->

	<?php
		//If paginating, Pagenavi is recommended:
		if ( is_plugin_active('wp-pagenavi/wp-pagenavi.php') ){
			wp_pagenavi(); //query_posts
			//wp_pagenavi(array('query' => $cached_query)); //WP_Query
		} else {
			global $wp_query;
			$big = 999999999; //An unlikely integer
			echo '<div class="wp-pagination">';
				echo paginate_links(array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'format' => '?paged=%#%',
					'current' => max(1, get_query_var('paged')),
					'total' => $wp_query->max_num_pages
				));
			echo '</div>';
		}
	?>


<?php
	//Example using Nebula Infinite Load (see also infinite_load.php)
	//nebula()->infinite_load_query($args);
?>

<?php
	wp_reset_query(); //Reset for the main loop (query_posts)
	wp_reset_postdata(); //Reset for WP_Query
?>
