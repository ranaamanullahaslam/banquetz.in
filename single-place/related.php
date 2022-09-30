<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$city_id = $city_name = $city_slug = '';

$place_id = get_the_ID();

$place_city       = get_the_terms( $place_id, 'place-city');
$place_amenities  = get_the_terms( $place_id, 'place-amenities');
$place_categories = get_the_terms( $place_id, 'place-categories');

$enable_single_place_related             = golo_get_option('enable_single_place_related', '1' );

if( $place_city ) {
    $city_id      = $place_city[0]->term_id;
    $city_name    = $place_city[0]->name;
    $city_slug    = $place_city[0]->slug;
}

$categories = array();
if( $place_categories ) :
    foreach ($place_categories as $cate) {
        $cate_id = $cate->term_id;
        $categories[] = $cate_id;
    }
endif;

$args = array(
    'posts_per_page'      => 4,
    'post_type'           => 'place',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
    'exclude'             => $place_id,
    'orderby' => array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    ),
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'place-city',
            'field'    => 'id',
            'terms'    => $city_id
        ),
        array(
            'taxonomy' => 'place-categories',
            'field'    => 'id',
            'terms'    => $categories
        )
     )
);
$places = get_posts( $args );

?>

<?php if( $places && $enable_single_place_related ) : ?>
<div class="related-place">
    <div class="container">   
        <div class="block-heading">
            <h3 class="entry-title"><?php esc_html_e('Similar places', 'golo-framework'); ?></h3>
        </div>

        <div class="inner-related">
            <?php echo golo_get_place_by_category( 4, 4, $city_id, $categories, '540x480' ); ?>
        </div>
    </div>
</div>
<?php endif; ?>