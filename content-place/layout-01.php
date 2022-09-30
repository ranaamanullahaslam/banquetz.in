<?php 
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();

if( !empty($place_id) ){
    $id = $place_id;
}

$attach_id = get_post_thumbnail_id($id);
$excerpt   = get_the_excerpt($id);

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
    'enable_sp_product'                     => 'enable_sp_product',
);

$place_details_order    = golo_get_option( 'place_details_order', $place_details_order_default );

$no_image_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
$default_image = golo_get_option('default_place_image', '');

if (preg_match('/\d+x\d+/', $custom_place_image_size)) {
    $image_sizes = explode('x', $custom_place_image_size);
    $width		 = $image_sizes[0];
    $height		 = $image_sizes[1];
    $image_src 	 = golo_image_resize_id($attach_id, $width, $height, true);
    if( $default_image != '' )
    {
        if( is_array($default_image) && $default_image['url'] != '' )
        {
            $resize = golo_image_resize_url($default_image['url'], $width, $height, true);
            if ($resize != null && is_array($resize)) {
                $no_image_src = $resize['url'];
            }
        }
    }
} else {
    if (!in_array($custom_place_image_size, array('full', 'thumbnail'))) {
        $custom_place_image_size = 'full';
    }
    $image_src = wp_get_attachment_image_src($attach_id, $custom_place_image_size);
    if ($image_src && !empty($image_src[0])) {
        $image_src = $image_src[0];
    }
    if (!empty($image_src)) {
        list($width, $height) = getimagesize($image_src);
    }
    if($default_image != '')
    {
        if(is_array($default_image) && $default_image['url'] != '')
        {
            $no_image_src = $default_image['url'];
        }
    }
}

if ($image_src == '') {
    $image_src = $no_image_src;
}

$place_meta_data = get_post_custom($id);

$price_short    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short'][0] : '';
$price_unit    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_unit']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_unit'][0] : '';
$price_range    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range'][0] : '';
$place_featured = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_featured']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_featured'][0] : '0';
$place_logged   = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_logged']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_logged'][0] : '0';

if( $place_logged && !is_user_logged_in() ) {
    return;
}

// Rating
$rating = $total_reviews = $total_stars = 0;
$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 )";
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
$get_comments   = $wpdb->get_results($comments_query);
if (!is_null($get_comments)) {
    foreach ($get_comments as $comment) {
        if ($comment->comment_approved == 1) {
            $total_reviews++;
            if( $comment->meta_value > 0 ){
                $total_stars += $comment->meta_value;
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }
}

$price = '';

$currency_sign = golo_get_option('currency_sign', '$');
$low_price     = golo_get_option('low_price', '$');
$medium_price  = golo_get_option('medium_price', '$$');
$high_price    = golo_get_option('high_price', '$$$');

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

    case 'd':
        $price_unit = esc_html__( 'day', 'golo-framework' );
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

$place_type = get_the_terms( $id, 'place-type');

$effect_class = isset($effect_class) ? $effect_class : '';

$place_item_class[] = 'place-item';
$place_item_class[] = $effect_class;

if( !empty($layout) ) 
{
    $place_item_class[] = $layout;
}

if( $place_featured )
{
    $place_item_class[] = 'golo-place-featured';
}

$place_item_class[] = 'place-' . $id;

?>

<div class="<?php echo join(' ', $place_item_class); ?>">
    <div class="place-inner">
        <?php if( !empty($image_src) ) { ?>
        <div class="place-thumb">
            <a class="entry-thumb" href="<?php echo get_the_permalink($id); ?>"><img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($image_src) ?>" alt="<?php echo get_the_title($id); ?>" title="<?php echo get_the_title($id); ?>"></a>
        </div>
        <?php } ?>
        
        <?php 
            golo_get_template('place/wishlist.php', array(
                'place_id' => $id
            ));
        ?>

        <?php if( $place_featured === '1' ) : ?>
        <span class="place-featured">
            <i class="las la-star icon-large"></i>
        </span>
        <?php endif; ?>
        
        <div class="entry-detail">
            <div class="entry-head">
                <?php if ($place_type): ?>
                <div class="place-type list-item">
                    <div class="place-term">
                        <?php foreach ($place_type as $type) {
                            $type_link = get_term_link($type, 'place-type');
                            
                            ?>
                                <span><?php echo esc_html($type->name); ?></span>
                            <?php
                        } ?>
                    </div>
                </div>
                <?php endif; ?>

                <h3 class="place-title"><a href="<?php echo get_the_permalink($id); ?>"><?php echo get_the_title($id); ?></a></h3>
            </div>
            
            
            <div class="entry-bottom">
                <?php if (in_array('enable_sp_review', $place_details_order )) : ?>
                    <div class="place-preview">
                    <?php if( !empty($my_review) ) { ?>
                        <div class="place-rating">
                            <span><?php echo esc_html($rating); ?></span>
                            <i class="la la-star"></i>
                        </div>
                        <span class="count-reviews">
                            <?php printf(_n('(%s review)', '(%s Reviews)', $total_reviews, 'golo-framework'), $total_reviews); ?>
                        </span>
                    <?php }else{ ?>
                        <span class="no-reviews">
                            <?php esc_html_e('(no reviews)', 'golo-framework'); ?>
                        </span>
                    <?php } ?>
                    </div>
                <?php endif; ?>

                <?php if( !empty($price) ) : ?>
                <div class="place-price">
                    <span>                     
                        <?php echo esc_html($price); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>