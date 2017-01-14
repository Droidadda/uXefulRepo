<?php if ( have_rows('repeater_field_name') ): ?>
    <?php while ( have_rows('repeater_field_name') ): the_row(); ?>
        <?php echo get_sub_field('sub_field_name'); ?>
    <?php endwhile; ?>
<?php endif; ?>
