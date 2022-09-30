<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
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
    'enable_sp_google_review'               => 'enable_sp_google_review',
    'enable_sp_author_info'                 => 'enable_sp_author_info',
    'enable_sp_review'                      => 'enable_sp_review',
    'enable_sp_product'                     => 'enable_sp_product',
);

$place_details_order    = golo_get_option( 'place_details_order', $place_details_order_default );

$args = array(
    'posts_per_page'      => -1,
    'post_type'           => 'product',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
);
$products = get_posts( $args );

?>

<?php if( $products && in_array('enable_sp_product', $place_details_order ) ) : ?>
<div class="related-place related-product"> 
    <div class="block-heading">
        <h3 class="entry-title"><?php esc_html_e('Similar products', 'golo-framework'); ?></h3>
    </div>

    <div class="inner-related">
        <?php echo golo_get_product_related( 6, 3, '540x480' ); ?>
    </div>
</div>
<?php endif; ?>