<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$enable_city_post = golo_get_option('enable_city_post', '0');
if( $enable_city_post == '0' ) {
    return;
}

$custom_city_image_size = golo_get_option('archive_city_image_size', '270x370' );

$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';

if( $current_city ){
    $current_term = get_term_by('slug', $current_city, 'place-city');
}else{
    $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
}

$current_id = '';
if( $current_term ) {
    $current_id = $current_term->term_id;
}

$place_cities = get_terms(array(
    'taxonomy'   => 'place-city',
    'number'     => 12,
    'hide_empty' => true,
    'orderby'    => 'term_id',
    'order'      => 'DESC',
    'exclude'    => $current_id
));

$slick_attributes = array(
    '"slidesToShow": 4',
    '"slidesToScroll": 1',
    '"autoplay": true',
    '"infinite": true',
    '"autoplaySpeed": 5000',
    '"arrows": false',
    '"responsive": [{ "breakpoint": 376, "settings": {"slidesToShow": 1,"variableWidth": true,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 479, "settings": {"slidesToShow": 2,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 600, "settings": {"slidesToShow": 2,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 992, "settings": {"slidesToShow": 3} },{ "breakpoint": 1200, "settings": {"slidesToShow": 4} } ]'
);
$wrapper_attributes[] = "data-slick='{". implode(', ', $slick_attributes) ."}'";

?>

<?php if( $place_cities ) : ?>
<div class="related-city">
    <div class="container">   
        <div class="block-heading category-heading">
            <h3 class="entry-title"><?php esc_html_e('Explore Other Cities', 'golo-framework'); ?></h3>
        </div>

        <div class="inner-related golo-slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
            <?php
            foreach ($place_cities as $place_city) {
                $term_id = $place_city->term_id;
            ?>
                <?php golo_get_template('content-city.php', array(
                    'term_id'                => $term_id,
                    'custom_city_image_size' => $custom_city_image_size
                )); ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php endif; ?>
