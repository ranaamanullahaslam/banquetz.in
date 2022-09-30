<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $wpdb;

$place_id = get_the_ID();

$place_meta_data = get_post_custom( $place_id );

$price_short = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short'][0] : '';
$price_unit    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_unit']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_unit'][0] : '';
$price_range = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range'][0] : '';

$price = '';

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
    'enable_sp_google_review'               => 'enable_sp_google_review',
    'enable_sp_author_info'                 => 'enable_sp_author_info',
    'enable_sp_review'                      => 'enable_sp_review',
    'enable_sp_product'                      => 'enable_sp_product',
);

$currency_sign          = golo_get_option('currency_sign', '$');
$low_price              = golo_get_option('low_price', '$');
$medium_price           = golo_get_option('medium_price', '$$');
$high_price             = golo_get_option('high_price', '$$$');
$place_details_order    = golo_get_option( 'place_details_order', $place_details_order_default );

if( $price_range && $price_range != 0 ){

    if( $price_range == 1 ){
        $price = esc_html__('Free', 'golo-framework');
    }
    if( $price_range == 2 ){
        $price = $low_price;
    }
    if( $price_range == 3 ){
        $price = $medium_price;
    }
    if( $price_range == 4 ){
        $price = $high_price;
    }
}

switch ($price_unit) {
    case 'h':
        $price_unit = esc_html__( 'hour', 'golo-framework' );
        break;

    case 'plate':
        $price_unit = esc_html__( 'plate', 'golo-framework' );
        break;

    case 'm':
        $price_unit = esc_html__( 'month', 'golo-framework' );
        break;

    case '':
        $price_unit = esc_html__( '', 'golo-framework' );
        break;
    
    default:
        $price_unit = esc_html__( 'month', 'golo-framework' );
        break;
}

if( $price_short ){
    $end_string = '';

    if ( '' != $price_unit ) {
        $end_string = '/' . $price_unit;
    }

    $price = golo_get_format_money( $price_short ) . $end_string;
}

$place_city = get_the_terms( $place_id, 'place-city');
$place_type = get_the_terms( $place_id, 'place-type');

if( $place_city ) {
	$city_slug    = $place_city[0]->slug;
}

$rating = $total_reviews = $total_stars = 0;

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$place_id     = get_the_ID();

$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
$get_comments   = $wpdb->get_results($comments_query);
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND comment.user_id = $user_id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");

if (!is_null($get_comments)) {
    foreach ($get_comments as $comment) {
        if ($comment->comment_approved == 1) {
            if( !empty($comment->meta_value) ){
                $total_reviews++;
            }
            if( $comment->meta_value > 0 ){
                $total_stars += $comment->meta_value;
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }
}

update_post_meta( $place_id, 'golo-average_rating', $rating );

?>

<?php if( !empty($price) || $place_type ) : ?>
<div class="place-meta place-area">
    <?php if (in_array('enable_sp_review', $place_details_order )) { ?>
    <div class="place-review">
        <span class="rating-count">
            <span><?php echo esc_html($rating); ?></span>
            <i class="la la-star"></i>
        </span>
        <span class="review-count"><?php printf(_n('(%s review)', '(%s reviews)', $total_reviews, 'golo-framework'), $total_reviews); ?></span>
    </div>
    <?php } ?>

	<?php if( !empty($price) ) : ?>
    <div class="place-price">
        <span>                     
            <?php echo esc_html($price); ?>
        </span>
    </div>
    <?php endif; ?>
	
	<?php if( $place_type ) : ?>
    <div class="place-type list-item">
		<?php 
        foreach ($place_type as $type) {
            ?>
                <span><?php echo esc_html($type->name); ?></span>
            <?php
        }
        ?>
	</div>
    <?php endif; ?>
</div>
<?php endif; ?>