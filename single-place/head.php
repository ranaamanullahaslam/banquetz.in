<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$country = $city_id = $city_name = $city_slug = $country_name = '';

$place_id    = get_the_ID();
$place_title = get_the_title();

$place_city 	  = get_the_terms( $place_id, 'place-city');
$place_amenities  = get_the_terms( $place_id, 'place-amenities');
$place_categories = get_the_terms( $place_id, 'place-categories');

if( $place_city ) {
	$city_id      = $place_city[0]->term_id;
	$city_name    = $place_city[0]->name;
	$city_slug    = $place_city[0]->slug;
	$country      = get_term_meta( $city_id, 'place_city_country', true );
	$country_name = golo_get_country_by_code($country);
}
$verified_listing 			= get_post_meta($place_id, GOLO_METABOX_PREFIX . 'verified_listing', true);
$cd_status 					= get_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_status', true);
$enable_claim_listing    	= golo_get_option('enable_claim_listing', '1' );
$primary_term = get_primary_taxonomy_id($place_id, 'place-categories');
$primary_terms = get_term_by('id', $primary_term, 'place-categories');
?>

<!-- Title/ Price -->
<div class="place-heading place-area">
	<?php if( $country || $place_city || $place_categories ) : ?>
	<div class="entry-categories">
		<?php if( $place_city ) : ?>
		<div class="place-city">
			<a href="<?php echo get_term_link( $city_slug, 'place-city'); ?>"><?php echo esc_html($city_name); ?></a>
		</div>
		<?php endif; ?>

		<?php 
			if( $place_categories ) :
				$cate_link = get_term_link($primary_term, 'place-categories');
		?>
        <div class="place-cate">
        	<?php if ($primary_terms) { ?>
        	<a href="<?php echo esc_url($cate_link); ?>?city=<?php echo esc_attr($city_slug); ?>"><?php echo esc_html($primary_terms->name); ?></a>
        	<?php } ?>
			<?php 

            foreach ($place_categories as $cate) {
            	if ($cate->term_id != $primary_term) {
                $cate_link = get_term_link($cate, 'place-categories');
                ?>
                    <a href="<?php echo esc_url($cate_link); ?>?city=<?php echo esc_attr($city_slug); ?>"><?php echo esc_html($cate->name); ?></a>
                <?php
            	}
            }
            ?>
		</div>
        <?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if( !empty($place_title) ) : ?>
	<div class="place-title">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php if (!empty($enable_claim_listing)) : ?>
		<div class="claim-badge with-tip" data-tip-content="Click to claim this listing.">			
			<?php 
				if ($verified_listing == 1) :
			?>	

			<div class="verified-badge">
				<i class="las la-check-circle"></i>
				<div class="tip-content"><?php esc_html_e('Listing has verified and belong the business owner or manager.', 'golo-framework'); ?></div>
			</div>

			<?php
				elseif( $cd_status == 'pending' ) :
			?>

			<div class="wait-badge">
				<i class="las la-exclamation-circle"></i>
				<div class="tip-content"><?php esc_html_e('You have submitted a verification request for this place, please wait for the administrator to confirm.', 'golo-framework'); ?></div>
			</div>

			<?php
				else :
			?>
			<div class="verified-badge not-verified">
				<i class="las la-exclamation-circle"></i>
				<div class="tip-content"><?php esc_html_e('Not verified.', 'golo-framework'); ?>
				<?php 
					global $wp_query;
					$place_author_id = $wp_query->get_queried_object()->post_author;
					$login_user_id = wp_get_current_user()->ID;

					if ($place_author_id == $login_user_id) {
						?> 												
						<a href="#" class="btn-open-claim"><?php esc_html_e('Claim this listing', 'golo-framework'); ?></a>
						<?php
					}
				?>
				</div>
			</div>

			<div class="contact-agent popup">
				<div class="bg-overlay"></div>
				<div class="inner-popup inner-booking claim-popup">
					<a href="#" class="btn-close"><i class="la la-times icon-large"></i></a>
					<h3><?php esc_html_e('Fill out this form to claim your business listing', 'golo-framework'); ?></h3>

					<form action="#" method="POST" class="golo-form formClaim row">
						<div class="form-group col-sm-6">
							<input class="form-control" name="your_name" type="text" placeholder="<?php esc_attr_e( 'Your Name', 'golo-framework' ); ?>" autocomplete="off">
						</div>
						<div class="form-group col-sm-6">
							<input class="form-control" name="your_email" type="email" placeholder="<?php esc_attr_e( 'Email Address', 'golo-framework' ); ?>" autocomplete="off">
						</div>
						<div class="form-group col-sm-6">
							<input class="form-control" name="your_username" type="text" placeholder="<?php esc_attr_e( 'Username', 'golo-framework' ); ?>" autocomplete="off">
						</div>
						<div class="form-group col-sm-6">
							<input class="form-control" name="your_listing" type="text" placeholder="<?php esc_attr_e( 'Listing Url', 'golo-framework' ); ?>" autocomplete="off">
						</div>
						<div class="form-group col-sm-12">
							<textarea class="form-control" name="messager" id="" cols="30" placeholder="<?php esc_attr_e( 'Please provide additional details for your claim here', 'golo-framework' ); ?>"></textarea>
						</div>
						<div class="bottom-form col-sm-12">
							<input type="hidden" name="place_id" value="<?php echo get_the_ID(); ?>">
							<button type="submit" class="btn-submit btn gl-button"><?php esc_html_e( 'Send', 'golo-framework' ); ?></button>
							<div class="form-messages">
								<i class="la la-thumbs-up icon-success"></i>
								<i class="la la-exclamation-circle icon-warning"></i>
								<span></span>
							</div>
						</div>
						<div class="golo-loading-effect"><span class="golo-dual-ring small"></span></div>
					</form>
				</div>
			</div>

			<?php
				endif;
			?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>