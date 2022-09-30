<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();

$key          = false;
$user_id      = $current_user->ID;
$my_whishlist = get_user_meta($user_id, GOLO_METABOX_PREFIX . 'place_whishlist', true);
$id           = get_the_ID();

if( !empty($place_id) ){
    $id = $place_id;
}

if (!empty($my_whishlist)) {
    $key = array_search($id, $my_whishlist);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
?>

<?php if( is_user_logged_in() ) {  ?>
	<a href="#" class="golo-add-to-wishlist btn-add-to-wishlist <?php echo esc_attr($css_class); ?>" data-place-id="<?php echo intval($id) ?>">
		<span class="icon-heart">
			<i class="la la-bookmark large"></i>
		</span>                                    
	</a>
<?php }else{ ?>
	<div class="logged-out">
		<a href="#popup-form" class="btn-login golo-add-to-wishlist btn-add-to-wishlist <?php echo esc_attr($css_class); ?>" data-place-id="<?php echo intval($id) ?>">
			<span class="icon-heart">
				<i class="la la-bookmark large"></i>
			</span>                                    
		</a>
	</div>
<?php } ?>