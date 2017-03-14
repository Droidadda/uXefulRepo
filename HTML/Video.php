<video id="video" autobuffer autoplay muted loop>
  <source src="video/placeholder.webm" type="video/webm" />
  <source src="video/placeholder.mp4"  type="video/mp4" />
  <source src="video/placeholder.ogv"  type="video/ogg" />
</video>

<!-- Using Nebula w/ Device Detection: -->
<div id="videocon">
  <?php if ( nebula_is_browser('ie', '8', '<=') || nebula_is_browser('safari', '8', '<=') || nebula_get_device('formfactor') == 'mobile' ): ?>
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hero.png" />
  <?php else: ?>
    <video id="video" autobuffer autoplay muted loop>
      <source src="<?php echo get_stylesheet_directory_uri(); ?>/video/placeholder.webm" type="video/webm" />
      <source src="<?php echo get_stylesheet_directory_uri(); ?>/video/placeholder.mp4"  type="video/mp4" />
      <source src="<?php echo get_stylesheet_directory_uri(); ?>/video/placeholder.ogv"  type="video/ogg" />
    </video>
    <?php endif; ?>
</div>
