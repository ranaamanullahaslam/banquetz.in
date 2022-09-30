<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$content = get_the_content();
$enable_single_place_toggle_desc = golo_get_option('enable_single_place_toggle_desc', '1' );

if (isset($content) && !empty($content)) : 
?>
	<div class="place-content place-area content-area <?php if( !$enable_single_place_toggle_desc ) : echo 'off-toggle';endif; ?>">
		<div class="inner-content">
			<div class="entry-visibility">
				<?php the_content(); ?>
			</div>
		</div>
	    
	    <div class="toggle-desc">
	    	<a class="show-more" href="#"><?php esc_html_e('Show more', 'golo-framework'); ?></a>
	    	<a class="hide-all" href="#"><?php esc_html_e('Hide all', 'golo-framework'); ?></a>
	    </div>
	</div>
	
<?php endif; ?>