<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined( 'ABSPATH' ) || exit;

$archive_place_items_amount = golo_get_option('archive_place_items_amount', '16');
$archive_place_columns      = golo_get_option('archive_place_columns', '3');
$custom_place_image_size    = golo_get_option('custom_place_image_size', '540x480' );
$enable_archive_filter      = golo_get_option('enable_archive_filter', '1');
$enable_archive_map         = golo_get_option('enable_archive_map', '1');
$default_map                = golo_get_option('default_map', '1');
$archive_city_columns_lg    = golo_get_option('archive_city_columns_lg', '2');
$archive_place_columns_md   = golo_get_option('archive_place_columns_md', '1');
$archive_place_columns_sm   = golo_get_option('archive_place_columns_sm', '2');
$archive_place_columns_xs   = golo_get_option('archive_place_columns_xs', '2');

$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';
$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$key          = isset( $_GET['s'] ) ? golo_clean(wp_unslash($_GET['s'])) : '';

$archive_class   = array();
$archive_class[] = 'area-places';
$archive_class[] = 'grid';
$archive_class[] = 'columns-'. $archive_place_columns;
$archive_class[] = 'columns-lg-'. $archive_city_columns_lg;
$archive_class[] = 'columns-md-'. $archive_place_columns_md;
$archive_class[] = 'columns-sm-'. $archive_place_columns_sm;
$archive_class[] = 'columns-xs-'. $archive_place_columns_xs;


$location_check_id = get_term_by('name', $key, 'place-city');


$tax_query = array();

// Get Default Order
$search_order = golo_get_option('default_search_order', 'place_featured');
$meta_key_search_list = array('place_featured', 'average_rating');

if ( in_array( $search_order, $meta_key_search_list ) ) {
    $meta_key = 'golo-' . $search_order;
} 

if (isset($meta_key)) {
    $order_by = 'meta_value';
    $order_by .= ' ' . 'date';
} else {
    $order_by = $search_order;
}

$args = array(
    'posts_per_page'      => $archive_place_items_amount,
    'post_type'           => 'place',
    'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'tax_query'           => $tax_query,
    's'                   => $key,
    'meta_key'              => isset($meta_key)? $meta_key : '',
    'orderby'               => $order_by
);


if ($location_check_id) {
    $args['tax_query'][] = array(
        'taxonomy' => 'place-city',
        'field'    => 'term_id',
        'terms'    => $location_check_id->term_id
    );
    $args['s'] = '';
}

$category = isset($_GET['category']) ? golo_clean(wp_unslash($_GET['category'])) : '';
$category_id = isset($_GET['caid']) ? golo_clean(wp_unslash($_GET['caid'])) : '';
if ( !empty($category_id) ) {
    $tax_query[] = array(
        'taxonomy' => 'place-categories',
        'field'    => 'term_id',
        'terms'    => $category_id
    );
}

$location = isset($_GET['place_location']) ? golo_clean(wp_unslash($_GET['place_location'])) : '';
$location_id = isset($_GET['ciid']) ? golo_clean(wp_unslash($_GET['ciid'])) : '';
if (empty($location)) {
    if ( !empty($current_city) ) {
        $location_id = get_term_by('slug', $current_city, 'place-city');

        $tax_query[] = array(
            'taxonomy' => 'place-city',
            'field'    => 'term_id',
            'terms'    => $location_id->term_id
        );
    }    
} else {
    if (!empty($location_id)) {
        $tax_query[] = array(
            'taxonomy' => 'place-city',
            'field'    => 'term_id',
            'terms'    => $location_id
        );
    }
}

$place_type = isset($_GET['place_type']) ? golo_clean(wp_unslash($_GET['place_type'])) : '';
$place_type_id = isset($_GET['tyid']) ? golo_clean(wp_unslash($_GET['tyid'])) : '';
if( !empty($place_type_id) ) {
    
    $tax_query[] = array(
        'taxonomy' => 'place-type',
        'field'    => 'term_id',
        'terms'    => $place_type_id
    );
}

$amenities_id = isset($_GET['amenities']) ? golo_clean(wp_unslash($_GET['amenities'])) : '';
if( !empty($amenities_id) ) {
    $tax_query[] = array(
        'taxonomy' => 'place-amenities',
        'field'    => 'term_id',
        'terms'    => $amenities_id
    );
}

if ( is_tax() ) {
    $current_term   = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    $taxonomy_title = $current_term->name;
    $taxonomy_name  = get_query_var('taxonomy');
    if (!empty($taxonomy_name)) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy_name,
            'field'    => 'slug',
            'terms'    => $current_term->slug
        );
    }
}

//meta query place sort_by

if (!empty($sort_by)) {
    if( $sort_by == 'featured' ) {
        $meta_query[] = array(
            'key'     => GOLO_METABOX_PREFIX. 'place_featured',
            'value'   => 1,
            'type'    => 'NUMERIC',
            'compare' => '=',
        );
    }
    
    if( $sort_by == 'rating' ) {
        $args['meta_key'] = GOLO_METABOX_PREFIX . 'average_rating';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'DESC';
    }

    if( $sort_by == 'newest' ) {
        $args['orderby'] = array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
        );
    }
}

$tax_count = count($tax_query);
if ($tax_count > 0) {
    $args['tax_query'] = array(
        'relation' => 'AND',
        $tax_query
    );
}

$data       = new WP_Query($args);
$total_post = $data->found_posts;



if( $default_map ){
    $class_inner[] = 'no-map';
} else {
    if( !empty($enable_archive_map) ) {
        $class_inner[] = 'has-map';
    }else{
        $class_inner[] = 'no-map';
    }
}

?>

<div class="inner-content <?php echo join(' ', $class_inner); ?>">

    <div class="container">

        <div class="col-left">

            <?php do_action( 'golo_archive_heading_filter', $current_city, $current_term, $total_post); ?>

            <?php
                /**
                 * @Hook: golo_output_content_wrapper_start
                 * 
                 * @hooked output_content_wrapper_start
                 */
                do_action( 'golo_output_content_wrapper_start' ); 
            ?> 
                
                <div class="top-area">

                    <div class="entry-left">
                        <span class="result-count">
                            <?php if( !empty($key) ) { ?>
                                <?php printf( esc_html__( '%1$s results for: "%2$s"', 'golo-framework' ), '<span>'. $total_post .'</span>', $key ); ?>
                            <?php }else{ ?>
                                <?php printf( esc_html__( '%1$s results', 'golo-framework' ), '<span>'. $total_post .'</span>' ); ?>
                            <?php } ?>
                        </span>
                    </div>

                    <div class="entry-right">
                        
                        <?php
                            $search_fields = golo_get_option( 'search_fields' );
                            if( in_array('sort_by', $search_fields ) ) {
                        ?>
                        
                        <select name="sort_by" class="sort-by filter-control nice-select right">
                            <option value=""><?php esc_html_e('Sort by', 'golo-framework'); ?></option>
                            <option value="newest"><?php esc_html_e('Newest', 'golo-framework'); ?></option>
                            <option value="rating"><?php esc_html_e('Average rating', 'golo-framework'); ?></option>
                            <option value="featured"><?php esc_html_e('Featured', 'golo-framework'); ?></option>
                        </select>
                        
                        <?php } ?>
                        
                        <?php if( !empty($enable_archive_filter) ) { ?>
                        <div class="btn-canvas-filter hidden-lg-up">
                            <a href="#"><?php esc_html_e('Filter', 'golo-framework'); ?></a>
                            <i class="las la-filter"></i>
                        </div>
                        <?php } ?>
                        
                        <?php if( !empty($enable_archive_map) ) { ?>
                        <div class="btn-maps-filter golo-button">
                            <a href="#">
                                <i class="las la-map-marked-alt"></i>
                                <?php esc_html_e('Maps view', 'golo-framework'); ?>
                            </a>
                        </div>
                        <?php } ?>

                    </div>
                </div>

                <div class="<?php echo join(' ', $archive_class); ?>">

                    <?php if ( $data->have_posts() ) { ?>

                        <?php while ( $data->have_posts() ) : $data->the_post(); ?>

                            <?php golo_get_template('content-place.php', array(
                                'custom_place_image_size' => $custom_place_image_size
                            )); ?>

                        <?php endwhile; ?>

                    <?php } else { ?>

                        <div class="item-not-found"><?php esc_html_e('No item found', 'golo-framework'); ?></div>

                    <?php } ?>

                </div>

                <?php
                    $max_num_pages = $data->max_num_pages;
                    golo_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call'));
                    wp_reset_postdata();
                ?>

            <?php
                /**
                 * @Hook: golo_output_content_wrapper_end
                 * 
                 * @hooked output_content_wrapper_end
                 */
                do_action( 'golo_output_content_wrapper_end' );
            ?>
        </div>
        
        <?php if( !empty($enable_archive_map) ) { ?>
        <div class="col-right">
            <?php
                /**
                 * @Hook: golo_archive_map_filter
                 * 
                 * @hooked archive_map_filter
                 */
                do_action( 'golo_archive_map_filter');
            ?>
        </div>
        <?php } ?>

    </div>

</div>

