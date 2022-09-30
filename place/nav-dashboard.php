<?php 
$show_dashboard   = golo_get_option('show_dashboard','1');
$show_profile     = golo_get_option('show_profile','1');
$show_my_places   = golo_get_option('show_my_places','1');
$show_my_booking  = golo_get_option('show_my_booking','1');
$show_booking     = golo_get_option('show_booking','1');
$show_my_wishlist = golo_get_option('show_my_wishlist','1');
$current_user = wp_get_current_user();
?>

<?php if( $show_dashboard || $show_profile || $show_my_places || $show_my_booking || $show_booking || $show_my_wishlist ) : ?>
<div class="agent-nav">
	<div class="container">
		<ul>
			<?php if( $show_dashboard && !in_array( 'customer', (array) $current_user->roles ) ) : ?><li class="<?php if(is_page('dashboard')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('dashboard'); ?>"><?php esc_html_e('Dashboard', 'golo-framework'); ?></a></li><?php endif; ?>
			<?php if( $show_profile ) : ?><li class="<?php if(is_page('my-profile')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_profile'); ?>"><?php esc_html_e('Profile', 'golo-framework'); ?></a></li><?php endif; ?>
			<?php if( $show_my_places && !in_array( 'customer', (array) $current_user->roles ) ) : ?><li class="<?php if(is_page('my-places')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_places'); ?>"><?php esc_html_e('My Places', 'golo-framework'); ?></a></li><?php endif; ?>
			<?php if( $show_my_booking && !in_array( 'customer', (array) $current_user->roles ) ) : ?><li class="<?php if(is_page('bookings')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('bookings'); ?>"><?php esc_html_e('Bookings', 'golo-framework'); ?></a></li><?php endif; ?>
			<?php if( $show_my_wishlist ) : ?><li class="<?php if(is_page('my-wishlist')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_wishlist'); ?>"><?php esc_html_e('Wishlist', 'golo-framework'); ?></a></li><?php endif; ?>
			<?php if( $show_booking ) : ?><li class="<?php if(is_page('my-booking')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_booking'); ?>"><?php esc_html_e('My Bookings', 'golo-framework'); ?></a></li><?php endif; ?>
		</ul>
	</div>
</div>
<?php endif; ?>