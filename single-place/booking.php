<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('google-map');

$place_id = $place_title = '';

$classes = array();

$place_id = get_the_ID();
$place_title = get_the_title( $place_id );
$place_author_id = get_post_field( 'post_author', $place_id );

$author_link = get_author_posts_url($place_author_id);
$user_email  = get_the_author_meta('user_email', $place_author_id);
$avatar_url  = get_avatar_url($place_author_id);
$author_name = get_the_author_meta('display_name', $place_author_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $place_author_id);
$author_avatar_image_id  = get_the_author_meta('author_avatar_image_id', $place_author_id);
if( !empty($author_avatar_image_url) ){
    $avatar_url = $author_avatar_image_url;
}
$avatar_url = golo_image_resize_url($avatar_url, 50, 50, true);

$place_meta_data = get_post_custom( $place_id );

$place_booking            = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking'][0] : '';
$place_booking_site       = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_site']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_site'][0] : '';
$place_booking_2            = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_2']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_2'][0] : '';
$place_booking_site_2       = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_site_2']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_site_2'][0] : '';
$place_booking_banner_url = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_banner_url']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_banner_url'][0] : '';
$place_booking_banner     = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_banner']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_banner'][0] : '';
$place_booking_type       = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type'][0] : '';
$place_booking_form       = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_form']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_form'][0] : '';

$enable_sticky_booking_type = golo_get_option('enable_sticky_booking_type', 1);
if( $enable_sticky_booking_type ) {
	$classes[] = 'has-sticky';
}

$opening_monday         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_monday', true);
$opening_monday_time    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_monday_time', true);
$opening_tuesday        = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_tuesday', true);
$opening_tuesday_time   = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_tuesday_time', true);
$opening_wednesday      = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_wednesday', true);
$opening_wednesday_time = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_wednesday_time', true);
$opening_thursday       = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_thursday', true);
$opening_thursday_time  = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_thursday_time', true);
$opening_friday         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_friday', true);
$opening_friday_time    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_friday_time', true);
$opening_saturday       = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_saturday', true);
$opening_saturday_time  = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_saturday_time', true);
$opening_sunday         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_sunday', true);
$opening_sunday_time    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_sunday_time', true);

$arr_monday_time    = explode('-', $opening_monday_time);
$arr_tuesday_time   = explode('-', $opening_tuesday_time);
$arr_wednesday_time = explode('-', $opening_wednesday_time);
$arr_thursday_time  = explode('-', $opening_thursday_time);
$arr_friday_time    = explode('-', $opening_friday_time);
$arr_saturday_time  = explode('-', $opening_saturday_time);
$arr_sunday_time    = explode('-', $opening_sunday_time);

$storeSchedule = [
    'Mon' => $opening_monday_time,
    'Tue' => $opening_tuesday_time,
    'Wed' => $opening_wednesday_time,
    'Thu' => $opening_thursday_time,
    'Fri' => $opening_friday_time,
    'Sat' => $opening_saturday_time,
    'Sun' => $opening_sunday_time
];

$day = [
    'Mon' => __('Monday', 'golo-framework'),
    'Tue' => __('Tuesday', 'golo-framework'),
    'Wed' => __('Wednesday', 'golo-framework'),
    'Thu' => __('Thursday', 'golo-framework'),
    'Fri' => __('Friday', 'golo-framework'),
    'Sat' => __('Saturday', 'golo-framework'),
    'Sun' => __('Sunday', 'golo-framework'),
];

$place_timezone = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_timezone', true);        
$tzstring = convert_place_timezone($place_timezone);
$dt = new DateTime("now", new DateTimeZone($tzstring) );
$timestamp = strtotime($dt->format("Y-m-d H:i:s"));
$current_day = date('D', $timestamp);

$status = golo_status_time_place($storeSchedule);

$title = $icon_url = '';
$map_type               = golo_get_option('map_type', 'google_map');
$map_zoom_level         = golo_get_option('map_zoom_level', '15');
$googlemap_type = 'roadmap';
$openstreetmap_style = $mapbox_style = 'streets-v11';
if( $map_type == 'google_map' ){
    $google_map_style       = golo_get_option('googlemap_style', '');
    $googlemap_type       = golo_get_option('googlemap_type', 'roadmap');
} else if( $map_type == 'openstreetmap' ) {
    $openstreetmap_style           = golo_get_option('openstreetmap_style', 'streets-v11');
    $openstreetmap_api_key      = Golo_Helper::golo_get_option('openstreetmap_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
} else {
    $mapbox_style       = golo_get_option('mapbox_style', 'streets-v11');
    $googlemap_api_key      = Golo_Helper::golo_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
}



$place_address          = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
$place_location         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);
if (!empty($place_location['location'])) {
    list($lat, $lng) = explode(',', $place_location['location']);
}


$primary_term               = get_primary_taxonomy_id($place_id, 'place-categories');

$icon_marker = get_term_meta( $primary_term, 'place_categories_icon_marker', true );
if( !empty($icon_marker['url']) ) {
	$icon_url = $icon_marker['url'];
} else {
	$icon_url    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
}

if (empty($primary_term)) {
	$place_categories = get_the_terms( $place_id, 'place-categories');
	if( $place_categories ) {
	    foreach ($place_categories as $cate) {
	        $cate_id     = $cate->term_id;
	        $icon_marker = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
	        if( !empty($icon_marker['url']) ) {
	            $icon_url = $icon_marker['url'];
	            break;
	        } else {
	        	$icon_url    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
	        }
	    }
	}
}

$place_phone     = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_phone', true);
$place_phone2    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_phone2', true);
$place_website   = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_website', true);
$place_email     = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_email', true);
$place_facebook  = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_facebook', true);
$place_instagram = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_instagram', true);
$place_twitter = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_twitter', true);



$additional_detail = isset($place_meta_data[GOLO_METABOX_PREFIX . 'additional_detail']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'additional_detail'][0] : '';
$additional_detail_icon = $additional_detail_name = $additional_detail_url = null;
if ($additional_detail > 0) {
    $additional_detail_icon = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'additional_detail_icon', true);
    $additional_detail_name = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'additional_detail_name', true);
    $additional_detail_url = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'additional_detail_url', true);
}

$place_address  = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
$place_location = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);

if (!empty($place_location['location'])) {
    list($lat, $lng) = explode(',', $place_location['location']);
}
if( $map_type == 'google_map' || $map_type == 'openstreetmap' ){
	if( $place_location && $place_location['address'] )
	{
	    $google_map_address_url = "http://maps.google.com/?q=" . $place_location['address'];
	    $map_address = $place_location['address'];
	}
	else
	{
	    $google_map_address_url = "http://maps.google.com/?q=" . $place_address;
	    $map_address = $place_address;
	}
} else {
	$map_address = $place_address;
}

$place_details_order_default = array(
    'sort_order'                            => 'enable_sp_amenities|enable_sp_description|enable_sp_menu|enable_sp_location|enable_sp_contact|enable_sp_additional_fields|enable_sp_time_opening|enable_sp_video|enable_sp_faqs|enable_sp_nearby_yelp_review|enable_sp_google_review|enable_sp_author_info|enable_sp_review|enable_sp_product',
    'enable_sp_amenities'                   => 'enable_sp_amenities',
    'enable_sp_description'                 => 'enable_sp_description',
    'enable_sp_menu'                        => 'enable_sp_menu',
    'enable_sp_location'                    => 'enable_sp_location',
    'enable_sp_contact'                     => 'enable_sp_contact',
    'enable_sp_additional_fields'           => 'enable_sp_additional_fields',
    'enable_sp_time_opening'                => 'enable_sp_time_opening',
    'enable_sp_video'                       => 'enable_sp_video',
    'enable_sp_faqs'                        => 'enable_sp_faqs',
    'enable_sp_nearby_yelp_review'          => 'enable_sp_nearby_yelp_review',
    'enable_sp_google_review'          		=> 'enable_sp_google_review',
    'enable_sp_author_info'                 => 'enable_sp_author_info',
    'enable_sp_review'                      => 'enable_sp_review',
    'enable_sp_product'                      => 'enable_sp_product',
);

$place_details_order 				= golo_get_option( 'place_details_order', $place_details_order_default );
$datetimepicker_language       	  	= golo_get_option('datetimepicker_language', '' );
$enable_readmore_mobile 			= golo_get_option('enable_readmore_mobile', '1' );

if($enable_readmore_mobile != '1'){
	$classes[] = 'show-on-mobile';
}

$dtp_language = '';
if ($datetimepicker_language && $datetimepicker_language != '') {
	$dtp_language = $datetimepicker_language;
}

$place_coupon_title 		= golo_get_option( 'place_coupon_title', '' );
$place_coupon_description 	= golo_get_option( 'place_coupon_description', '' );
$place_coupon_image 		= golo_get_option( 'place_coupon_image', '' );
$place_coupon_code 			= golo_get_option( 'place_coupon_code', '' );

?>

<?php if( $place_booking_type && $enable_readmore_mobile ) : ?>
<div class="booking-bar">
	<h3><?php esc_html_e('Booking', 'golo-framework'); ?></h3>
	<div class="golo-button">
		<a href="#"><?php esc_html_e('View', 'golo-framework'); ?></a>
	</div>
</div>
<?php endif; ?>

<?php if( $place_booking_type == 'info' ) : ?>
<div class="place-booking booking-info <?php echo implode(" ", $classes); ?>">

	<div class="bg-overlay"></div>

	<div class="inner-booking">
		<div class="top-detail">


			<div class="time-status">
				<?php if( ! empty($status) ) : ?>
					<?php echo wp_kses_post($status); ?>
				<?php endif; ?>

				<div class="toggle-select">
					<div class="toggle-show">
						<span><?php esc_html_e($day[$current_day]); ?></span>
						<?php esc_html_e($storeSchedule[$current_day]); ?>
						<i class="las la-angle-down"></i>
					</div>
					<div class="toggle-list">
						<ul>
							<li class="<?php if( $current_day == 'Mon' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_monday); ?></span>
								<?php echo esc_html($opening_monday_time) ?>
							</li>
							<li class="<?php if( $current_day == 'Tue' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_tuesday); ?></span>
								<?php echo esc_html($opening_tuesday_time) ?>
							</li>
							<li class="<?php if( $current_day == 'Wed' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_wednesday); ?></span>
								<?php echo esc_html($opening_wednesday_time) ?>
							</li>
							<li class="<?php if( $current_day == 'Thu' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_thursday); ?></span>
								<?php echo esc_html($opening_thursday_time) ?>
							</li>
							<li class="<?php if( $current_day == 'Fri' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_friday); ?></span>
								<?php echo esc_html($opening_friday_time) ?>
							</li>
							<li class="<?php if( $current_day == 'Sat' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_saturday); ?></span>
								<?php echo esc_html($opening_saturday_time) ?>
							</li>
							<li class="<?php if( $current_day == 'Sun' ) : echo 'active'; endif; ?>">
								<span><?php echo esc_html($opening_sunday); ?></span>
								<?php echo esc_html($opening_sunday_time) ?>
							</li>
						</ul>
					</div>
				</div>
			</div>


			<?php if( !empty($avatar_url['url']) ) : ?>
				<div class="author-avatar">
					<a class="hint--top" href="<?php echo esc_url($author_link); ?>" aria-label="<?php echo esc_attr($author_name); ?>">
		                <img src="<?php echo esc_url($avatar_url['url']); ?>" title="<?php echo esc_attr($author_name); ?>" alt="<?php echo esc_attr($author_name); ?>" >
		            </a>
				</div>
            <?php endif; ?>
		</div>

		<div class="map-detail">
			<div class="place-map place-area">
			    <?php if( $map_type == 'google_map' ){ ?>
			        <div id="golo-place-map" class="golo-place-map maptype" data-maptype="<?php echo $map_type; ?>" style="height: 170px;width: 100%; z-index: 1;"></div>
			    <?php } else if( $map_type == 'openstreetmap' ) { ?>
			        <div id="openstreetmap_booking" class="maptype" data-maptype="<?php echo $map_type; ?>" style="height: 170px;width: 100%; z-index: 1;" data-key="<?php if( $openstreetmap_api_key ) { echo $openstreetmap_api_key; } ?>"></div>
			    <?php } else { ?>
			        <div id="mapbox_booking" class="maptype" data-maptype="<?php echo $map_type; ?>" style="height: 170px;width: 100%; z-index: 1;" data-key="<?php if( $googlemap_api_key ) { echo $googlemap_api_key; } ?>"></div>
			    <?php } ?>
			</div>
		</div>

		<div class="contact-detail">
			<?php if( $map_address || $place_phone || $place_phone2 || $place_website || $place_email || $place_facebook || $place_instagram || $place_twitter ) : ?>
			<div class="place-contact place-area">
				<div class="entry-heading">
			        <h3 class="entry-title"><?php esc_html_e('Business Info', 'golo-framework'); ?></h3>
			    </div>
				
				<div class="entry-detail">
					<ul>
						<?php if( !empty($map_address) ) : ?>
						<li>
							<i class="las la-map-marked-alt large"></i>
							<a href="<?php echo esc_url($google_map_address_url); ?>" target="_blank"><?php echo esc_html($map_address); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($place_phone) ) : ?>
						<li>
							<i class="la la-phone large"></i>
							<a href="tel:<?php echo esc_attr($place_phone); ?>"><?php echo esc_html($place_phone); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($place_phone2) ) : ?>
						<li>
							<i class="la la-phone large"></i>
							<a href="tel:<?php echo esc_attr($place_phone2); ?>"><?php echo esc_html($place_phone2); ?></a>
						</li>
						<?php endif; ?>
						
						<?php if( !empty($place_email) ) : ?>
						<li>
							<i class="la la-envelope large"></i>
							<a href="mailto: <?php echo esc_attr($place_email); ?>"><?php echo esc_html($place_email); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($place_website) ) : ?>
						<li>
							<i class="la la-globe large"></i>
							<a href="<?php echo esc_url($place_website); ?>" target="_blank"><?php echo esc_html($place_website); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($place_facebook) ) : ?>
						<li>
							<i class="la la-facebook-f large"></i>
							<a href="<?php echo esc_url($place_facebook); ?>" target="_blank"><?php echo esc_html($place_facebook); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($place_instagram) ) : ?>
						<li>
							<i class="la la-instagram icon-large"></i>
							<a href="<?php echo esc_url($place_instagram); ?>" target="_blank"><?php echo esc_html($place_instagram); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($place_twitter) ) : ?>
						<li>
							<i class="lab la-twitter icon-large"></i>
							<a href="<?php echo esc_url($place_twitter); ?>" target="_blank"><?php echo esc_html($place_twitter); ?></a>
						</li>
						<?php endif; ?>

						<?php if( !empty($additional_detail_icon[0]) && !empty($additional_detail_url[0]) ): ?>
				            <?php for ($i = 0; $i < $additional_detail; $i++) { ?>
				                <?php if (!empty($additional_detail_icon[$i]) && !empty($additional_detail_url[$i])): ?>
				                    <li>
				                       	<i class="<?php echo esc_html($additional_detail_icon[$i]); ?> large"></i>
				                        <a href="<?php echo esc_attr($additional_detail_url[$i]); ?>" target="_blank">
				                        	<?php 
				                        		if (!empty($additional_detail_name[$i])) :
				                        			echo esc_html($additional_detail_name[$i]);
				                        		else :  
				                        			echo esc_html($additional_detail_url[$i]);
				                        		endif;
				                        	?>
				                        </a>
				                    </li>
				                <?php endif; ?>
				            <?php } ?>
						<?php endif; ?>
					</ul>
				</div>
				<?php if ($place_phone || $place_phone2 || !empty( $user_email)) : ?>
				<div class="button-contact">
					<?php if ($place_phone || $place_phone2) : ?>
					<div class="golo-button">
						<?php if ($place_phone) : ?>
							<a class="btn-call-us" href="tel:<?php echo esc_attr($place_phone); ?>"><?php esc_html_e('Call Us', 'golo-framework'); ?></a>
						<?php elseif ($place_phone2) : ?>
							<a class="btn-call-us" href="tel:<?php echo esc_attr($place_phone2); ?>"><?php esc_html_e('Call Us', 'golo-framework'); ?></a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if ( !empty( $user_email ) ): ?>
					<div class="btn-send-message">
	                    <a class="btn-open-popup gl-button" href="#"><?php esc_html_e('Send Message', 'golo-framework'); ?></a>

	                    
	                    <div class="contact-agent popup">
	                        <div class="bg-overlay"></div>

	                        <div class="inner-popup text-center custom-scrollbar">

	                            <a href="#" class="btn-close"><i class="la la-times icon-large"></i></a>
	                            
	                            <div class="entry-heading">
	                                <h3><?php esc_html_e('Send me a message', 'golo-framework'); ?></h3>
	                            </div>

	                            <form action="#" method="POST" id="contact-agent-form" class="row">
	                                <input type="hidden" name="target_email" value="<?php echo esc_attr( $user_email ); ?>">
	                                <input type="hidden" name="place_url" value="<?php echo get_permalink(); ?>">
	 
	                                <div class="form-group golo-field col-sm-4">
	                                    <input class="form-control" name="sender_name" type="text" placeholder="<?php esc_attr_e( 'Full Name', 'golo-framework' ); ?> *">
	                                    <div class="hidden name-error form-error"><?php esc_html_e( 'Please enter your Name!', 'golo-framework' ); ?></div>
	                                </div>

	                                <div class="form-group golo-field col-sm-4">
	                                    <input class="form-control" name="sender_phone" type="text" placeholder="<?php esc_attr_e( 'Phone Number', 'golo-framework' ); ?> *">
	                                    <div class="hidden phone-error form-error"><?php esc_html_e( 'Please enter your Phone!', 'golo-framework' ); ?></div>
	                                </div>

	                                <div class="form-group golo-field col-sm-4">
	                                    <input class="form-control" name="sender_email" type="email" placeholder="<?php esc_attr_e( 'Email Address', 'golo-framework' ); ?> *">
	                                    <div class="hidden email-error form-error" data-not-valid="<?php esc_attr_e( 'Your Email address is not Valid!', 'golo-framework' ) ?>" data-error="<?php esc_attr_e( 'Please enter your Email!', 'golo-framework' ) ?>"><?php esc_html_e( 'Please enter your Email!', 'golo-framework' ); ?></div>
	                                </div>

	                                <div class="form-group area-field golo-field col-sm-12">
	                                    <textarea class="form-control" name="sender_msg" rows="5" placeholder="<?php esc_attr_e( 'Message', 'golo-framework' ); ?> *"><?php $title=get_the_title(); echo sprintf(esc_html__( 'Hello, I am interested in [%s]', 'golo-framework' ), esc_html($title)) ?></textarea>
	                                    <div class="hidden message-error form-error"><?php esc_html_e( 'Please enter your Message!', 'golo-framework' ); ?></div>
	                                </div>

	                                <div class="bottom-form col-sm-12">
	                                    <?php wp_nonce_field('golo_contact_agent_ajax_nonce', 'golo_security_contact_agent'); ?>
	                                    <input type="hidden" name="action" id="contact_agent_with_place_url_action" value="golo_contact_agent_ajax">
	                                    <button type="submit" class="agent-contact-btn btn gl-button"><?php esc_html_e( 'Submit Request', 'golo-framework' ); ?></button>
	                                    <div class="form-messages"></div>
	                                </div>
	                            </form>
	                        </div>
	                    </div>
	                    
	                </div>
	                <?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if( ($place_booking_type == 'link' && !empty($place_booking)) || ($place_booking_type == 'link' && !empty($place_booking_2)) ) : ?>
<div class="place-booking booking-url align-center <?php echo implode(" ", $classes); ?>">

	<div class="bg-overlay"></div>

	<div class="inner-booking">
		<h3><?php esc_html_e('Booking online', 'golo-framework'); ?></h3>
		<a class="gl-button" href="<?php echo esc_url($place_booking); ?>" target="_blank"><?php esc_html_e('Book now', 'golo-framework'); ?></a>
		<?php if( $place_booking_type == 'link' && !empty($place_booking_2) ) { ?>
		<a class="gl-button" href="<?php echo esc_url($place_booking_2); ?>" target="_blank"><?php esc_html_e('Book now 2', 'golo-framework'); ?></a>
		<?php } ?>
		<?php if( !empty($place_booking_site) ) { ?>
		<p class="sub-string"><?php echo sprintf( __( 'By %s', 'golo-framework' ), $place_booking_site); ?></p>
		<?php } ?>
	</div>
</div>
<?php endif; ?>

<?php if( $place_booking_type == 'banner' && !empty($place_booking_banner) ) : ?>

<?php 
$place_booking_banner = unserialize($place_booking_banner);
$banner_url = $place_booking_banner['url'];
?>

<div class="place-booking booking-banner none-shadow align-center <?php echo implode(" ", $classes); ?>">

	<div class="bg-overlay"></div>

	<div class="inner-booking">
		<a href="<?php echo esc_url($place_booking_banner_url); ?>" target="_blank">
			<img src="<?php echo esc_url($banner_url); ?>" alt="<?php echo esc_attr($place_title); ?>">
		</a>
	</div>
</div>
<?php endif; ?>

<?php if( $place_booking_type == 'form' ) : ?>
<div class="place-booking booking-form align-center <?php echo implode(" ", $classes); ?>">

	<div class="bg-overlay"></div>

	<div class="inner-booking">
		<h3><?php esc_html_e('Make a reservation', 'golo-framework'); ?></h3>

		<form action="#" method="POST" class="golo-form formBooking">
			<div class="form-group form-toggle area-booking form-icon">
				<label class="open-toggle form-control">
					<i class="la la-user-friends left"></i>
					<span><?php esc_html_e('Guest', 'golo-framework'); ?></span>
					<span class="show-data">
						<span class="adults"><span>1</span><?php esc_html_e('adult', 'golo-framework'); ?></span>
						-
						<span class="childrens"><span>0</span><?php esc_html_e('children', 'golo-framework'); ?></span>
					</span>
					<i class="la la-angle-down right"></i>
				</label>
				<div class="inner-toggle">
					<div class="adult">
						<span><?php esc_html_e('Adults', 'golo-framework'); ?></span>
						<div class="product-quantity">
							<div class="minus btn-quantity"><i class="la la-minus"></i></div>
					        <input class="input-text qty text" type="number" inputmode="numeric" value="1" name="adults" min="1" max="99" step="1" required>
					        <div class="plus btn-quantity"><i class="la la-plus"></i></div>
						</div>
					</div>
					<div class="children">
						<span><?php esc_html_e('Childrens', 'golo-framework'); ?></span>
						<div class="product-quantity">
							<div class="minus btn-quantity"><i class="la la-minus"></i></div>
					        <input class="input-text qty text" type="number" inputmode="numeric" value="0" name="childrens" min="0" max="99" step="1">
					        <div class="plus btn-quantity"><i class="la la-plus"></i></div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group form-icon">
				<i class="la la-calendar left"></i>
				<input class="form-control datepicker" data-language="<?php echo $dtp_language; ?>" name="booking_date" type="text" placeholder="<?php esc_attr_e( 'Date', 'golo-framework' ); ?>" autocomplete="off" required>
				<i class="la la-angle-down right"></i>
			</div>
			<div class="form-group form-icon">
				<i class="la la-clock left"></i>
				<input class="form-control timepicker" name="booking_time" type="text" placeholder="<?php esc_attr_e( 'Time', 'golo-framework' ); ?>" autocomplete="off" required>
				<i class="la la-angle-down right"></i>
	        </div>
	        <?php
				if ($place_coupon_code) :
			?>
	        <div class="form-group form-icon form-coupon">
				<i class="las la-ticket-alt left"></i>
				<input class="form-control" name="place_coupon" type="text" placeholder="<?php esc_attr_e( 'Coupon', 'golo-framework' ); ?>" autocomplete="off">
	        </div>
	        <p class="error"><?php esc_html_e("Coupon does not exist!", 'golo-framework'); ?></p>
	        <?php endif; ?>
			<div class="bottom-form">
				<?php if(is_user_logged_in()) { ?>
					<button type="submit" class="btn-submit btn gl-button"><?php esc_html_e( 'Request a book', 'golo-framework' ); ?></button>
				<?php }else{ ?>
					<div class="account logged-out golo-button">
						<a href="#popup-form" class="btn-login"><?php esc_html_e( 'Request a book', 'golo-framework' ); ?></a>
					</div>
				<?php } ?>

				<input type="hidden" name="place_title" value="<?php echo esc_attr($place_title); ?>">
				<input type="hidden" name="place_id" value="<?php echo esc_attr($place_id); ?>">
				<input type="hidden" name="place_coupon_code" value="<?php echo esc_attr($place_coupon_code); ?>">
				<input type="hidden" name="place_author_id" value="<?php echo esc_attr($place_author_id); ?>">
				<p class="sub-string"><?php esc_html_e("You won't be charged yet", 'golo-framework'); ?></p>
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
<?php endif; ?>

<?php 
if( $place_booking_type == 'contact' && $place_booking_form != '' ) :
	$form_title    = get_the_title($place_booking_form);
	$cf7_shortcode = '[contact-form-7 id="'. $place_booking_form . '" title="'. $form_title .'"]';
?>
<div class="place-booking booking-contact align-center <?php echo implode(" ", $classes); ?>">
	
	<div class="bg-overlay"></div>

	<div class="inner-booking">
		<h3><?php esc_html_e('Send me a message', 'golo-framework'); ?></h3>

		<?php echo do_shortcode($cf7_shortcode); ?>
	</div>
</div>
<?php endif; ?>

<?php

	if ($place_booking_type == 'form' && $place_coupon_code) :
?>
<div class="coupon-widget" style="background-image: url(<?php echo esc_url($place_coupon_image['url']); ?>);">
	<a class="coupon-top">
		<h3><?php echo $place_coupon_title; ?></h3>
		<div class="coupon-how-to-use"><?php echo $place_coupon_description; ?></div>
	</a>
	<div class="coupon-bottom">
		<div class="coupon-scissors-icon"></div>
		<div class="coupon-code"><?php echo $place_coupon_code; ?></div>
	</div>
</div>
<?php endif; ?>

<script>
    jQuery(document).ready(function () {
    var maptype = jQuery( '.maptype' ).data( 'maptype' );

    if( maptype === 'google_map' ){
        var element = document.getElementById('golo-place-map');
    	if ( element != null ) {

    	        var styles, google_map_style;
    	        var bounds = new google.maps.LatLngBounds();
    	        var silver = [
    	            {
    	                "featureType": "landscape",
    	                "elementType": "labels",
    	                "stylers": [
    	                    {
    	                        "visibility": "off"
    	                    }
    	                ]
    	            },
    	            {
    	                "featureType": "transit",
    	                "elementType": "labels",
    	                "stylers": [
    	                    {
    	                        "visibility": "off"
    	                    }
    	                ]
    	            },
    	            {
    	                "featureType": "poi",
    	                "elementType": "labels",
    	                "stylers": [
    	                    {
    	                        "visibility": "off"
    	                    }
    	                ]
    	            },
    	            {
    	                "featureType": "water",
    	                "elementType": "labels",
    	                "stylers": [
    	                    {
    	                        "visibility": "off"
    	                    }
    	                ]
    	            },
    	            {
    	                "featureType": "road",
    	                "elementType": "labels.icon",
    	                "stylers": [
    	                    {
    	                        "visibility": "off"
    	                    }
    	                ]
    	            },
    	            {
    	                "stylers": [
    	                    {
    	                        "hue": "#00aaff"
    	                    },
    	                    {
    	                        "saturation": -100
    	                    },
    	                    {
    	                        "gamma": 2.15
    	                    },
    	                    {
    	                        "lightness": 12
    	                    }
    	                ]
    	            },
    	            {
    	                "featureType": "road",
    	                "elementType": "labels.text.fill",
    	                "stylers": [
    	                    {
    	                        "visibility": "on"
    	                    },
    	                    {
    	                        "lightness": 24
    	                    }
    	                ]
    	            },
    	            {
    	                "featureType": "road",
    	                "elementType": "geometry",
    	                "stylers": [
    	                    {
    	                        "lightness": 57
    	                    }
    	                ]
    	            }
    	        ];
    
    	        styles = silver;
    
    	        <?php if(!empty($google_map_style)): ?>

    	        	google_map_style = <?php echo json_encode($google_map_style); ?>;

    	        <?php else : ?>
    
    	        	google_map_style = '';

    	    	<?php endif; ?>
    
    	        if ( google_map_style ) {
    	            styles = JSON.parse(google_map_style);
    	        }
    
    	        <?php if(!empty($lat) && !empty($lng)): ?>
    	        var lat = '<?php echo esc_attr($lat) ?>', lng = '<?php echo esc_attr($lng) ?>';
    	        var marker;
    	        var position = new google.maps.LatLng(lat, lng);
    	        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    	        var isDraggable = w > 1024;
    	        var mapOptions = {
    	            mapTypeId: <?php echo "'" . $googlemap_type . "'"; ?>,
    	            center: position,
    	            draggable: isDraggable,
    	            scrollwheel: false,
    	            styles: styles,
    	            mapTypeControl: false,
    	            streetViewControl : false,
    	            rotateControl: false,
    	            zoomControl: true,
    	            fullscreenControl: false,
    	        };
    	        var map = new google.maps.Map(document.getElementById("golo-place-map"), mapOptions);
    	        bounds.extend(position);
    
    	        marker_size = new google.maps.Size(40, 40);
    	        var marker_icon = {
    	            url: '<?php echo esc_url($icon_url) ?>',
    	            size: marker_size,
    	            scaledSize: new google.maps.Size(40, 40),
    	        };
    
    	        marker = new google.maps.Marker({
    	            position: position,
    	            map: map,
    	            icon: marker_icon,
    	            title: '<?php echo esc_html($title) ?>',
    	        });
    
    	        map.fitBounds(bounds);
    	        var boundsListener = google.maps.event.addListener((map), 'idle', function (event) {
    	            this.setZoom(<?php echo esc_js($map_zoom_level); ?>);
    	            google.maps.event.removeListener(boundsListener);
    	        });
    	        <?php else: ?>
    	        document.getElementById('golo-place-map').style.height = 'auto';
    	        <?php endif; ?>
    	}
        
    
    } else if( maptype === 'openstreetmap' ) {
        
        var element = document.getElementById('openstreetmap_booking');
    	if ( element != null ) {
    	    var osm_api = jQuery( '#openstreetmap_booking' ).data( 'key' );
    	    
    	    var stores = {
                "type": "FeatureCollection",
                "features": [
                    {
                        "type": "Feature",
                        "geometry": {
                          "type": "Point",
                          "coordinates": [
                            <?php echo $lat; ?>,
                            <?php echo $lng; ?>
                          ]
                        },
                        "properties": {
                            "iconSize": [40, 40],
                            "icon": <?php echo '"' . esc_url($icon_url) . '"'; ?>,
                        }
                    }    
                ]
            };
            
            stores.features.forEach(function(store, i){
                store.properties.id = i;
            });
            
            var container = L.DomUtil.get('openstreetmap_booking'); if(container != null){ container._leaflet_id = null; }
    				
			var osm_map = new L.map('openstreetmap_booking');
			
			osm_map.on('load', onMapLoad);

			osm_map.setView([<?php echo $lat; ?>, <?php echo $lng; ?>], <?php echo $map_zoom_level; ?>);

            
            function onMapLoad(){
                
                var titleLayer_id = 'mapbox/<?php echo $openstreetmap_style; ?>';
                
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + osm_api, {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    id: titleLayer_id,
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: osm_api
                }).addTo(osm_map);
        
                /**
                 * Add all the things to the page:
                 * - The location listings on the side of the page
                 * - The markers onto the map
                */
                addMarkers();
            
                
            
            };
            
            function flyToStore(currentFeature) {
                osm_map.flyTo(currentFeature.geometry.coordinates, osm_level);
            }
            
            /* This will let you use the .remove() function later on */
            if (!('remove' in Element.prototype)) {
              Element.prototype.remove = function() {
                if (this.parentNode) {
                  this.parentNode.removeChild(this);
                }
              };
            }
            
            function addMarkers() {

                /* For each feature in the GeoJSON object above: */
                stores.features.forEach(function(marker) {
                    /* Create a div element for the marker. */
                    var el = document.createElement('div');
                    /* Assign a unique `id` to the marker. */
                    el.id = "marker-" + marker.properties.id;
                    /* Assign the `marker` class to each marker for styling. */
                    el.className = 'marker';
                    el.style.backgroundImage = 'url(<?php echo '"' . esc_url($icon_url) . '"'; ?>)';
                    el.style.width = marker.properties.iconSize[0] + 'px';
                    el.style.height = marker.properties.iconSize[1] + 'px';
                    /**
                     * Create a marker using the div element
                     * defined above and add it to the map.
                    **/
                    var PlaceIcon = L.Icon.extend({
                        options: {
                            className:      'marker-' + marker.properties.id,
                            iconSize:       [40, 40],
                            shadowSize:     [50, 64],
                            iconAnchor:     [20, 20],
                            shadowAnchor:   [4, 62],
                            popupAnchor:    [-3, -76]
                        }
                    });
				    var icon = new PlaceIcon({iconUrl: <?php echo '"' . esc_url($icon_url) . '"'; ?>});
				    var rating_html = '';
                    if( marker.properties.rating ) {
                        rating_html = 
                        '<div class="place-rating">' +
                            '<span>' + marker.properties.rating + '</span>' +
                            '<i class="la la-star"></i>' +
                        '</div>';
                    }
				    
				    new L.marker([marker.geometry.coordinates[0], marker.geometry.coordinates[1]], {icon: icon}).addTo(osm_map);
                      
                    el.addEventListener('click', function(e){
                        /* Fly to the point */
                        flyToStore(marker);
                        /* Highlight listing in sidebar */
                        var activeItem = document.getElementsByClassName('active');
                        e.stopPropagation();
                        if (activeItem[0]) {
                            activeItem[0].classList.remove('active');
                        }
                    });
                });
            }
    	}
        
    } else {

    	var element = document.getElementById('mapbox_booking');
    	if ( element != null ) {
    	        
	        var mapbox_api = jQuery( '#mapbox_booking' ).data( 'key' );
            mapboxgl.accessToken = mapbox_api;

	        <?php if(!empty($lat) && !empty($lng)): ?>
	            var map = new mapboxgl.Map({
                    container: 'mapbox_booking',
                    style: 'mapbox://styles/mapbox/<?php echo $mapbox_style; ?>',
                    zoom: <?php echo $map_zoom_level; ?>,
                    center: [<?php echo $lng; ?>, <?php echo $lat; ?>],
                });

                map.addControl(new mapboxgl.NavigationControl());
                
                var stores = {
                    "type": "FeatureCollection",
                    "features": [
                        {
                            "type": "Feature",
                            "geometry": {
                              "type": "Point",
                              "coordinates": [
                                <?php echo $lng; ?>,
                                <?php echo $lat; ?>
                              ]
                            },
                            "properties": {
                                "iconSize": [40, 40],
                                "icon": <?php echo '"' . esc_url($icon_url) . '"'; ?>,
                            }
                        }    
                    ]
                };
                
                stores.features.forEach(function(store, i){
                    store.properties.id = i;
                });
            
                /**
                * Wait until the map loads to make changes to the map.
                */
                map.on('load', function (e) {
                    /**
                     * This is where your '.addLayer()' used to be, instead
                     * add only the source without styling a layer
                    */
                    map.addLayer({
                        "id": "locations",
                        "type": "symbol",
                        /* Add a GeoJSON source containing place coordinates and information. */
                        "source": {
                          "type": "geojson",
                          "data": stores
                        },
                        "layout": {
                          "icon-image": "",
                          "icon-allow-overlap": true,
                        }
                      });
            
                    /**
                     * Add all the things to the page:
                     * - The location listings on the side of the page
                     * - The markers onto the map
                    */
                    addMarkers();
                });
                
                function flyToStore(currentFeature) {
                  map.flyTo({
                    center: currentFeature.geometry.coordinates,
                    zoom: <?php echo $map_zoom_level; ?>
                  });
                }
                
                /* This will let you use the .remove() function later on */
                if (!('remove' in Element.prototype)) {
                  Element.prototype.remove = function() {
                    if (this.parentNode) {
                      this.parentNode.removeChild(this);
                    }
                  };
                }
                
                function addMarkers() {
                  /* For each feature in the GeoJSON object above: */
                  stores.features.forEach(function(marker) {
                    /* Create a div element for the marker. */
                    var el = document.createElement('div');
                    /* Assign a unique `id` to the marker. */
                    el.id = "marker-" + marker.properties.id;
                    /* Assign the `marker` class to each marker for styling. */
                    el.className = 'marker';
                    el.style.backgroundImage = 'url(' + marker.properties.icon + ')';
                    el.style.width = marker.properties.iconSize[0] + 'px';
                    el.style.height = marker.properties.iconSize[1] + 'px';
                    /**
                     * Create a marker using the div element
                     * defined above and add it to the map.
                    **/
                    new mapboxgl.Marker(el, { offset: [0, -50/2] })
                      .setLngLat(marker.geometry.coordinates)
                      .addTo(map);
                      
                      el.addEventListener('click', function(e){
                      /* Fly to the point */
                      flyToStore(marker);
                      /* Highlight listing in sidebar */
                      var activeItem = document.getElementsByClassName('active');
                      e.stopPropagation();
                      if (activeItem[0]) {
                        activeItem[0].classList.remove('active');
                      }
                    });
                  });
                }
	        <?php endif; ?>
    	    
    	}
    	
    }
    });
</script>