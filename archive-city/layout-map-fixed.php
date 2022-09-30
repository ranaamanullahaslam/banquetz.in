<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined( 'ABSPATH' ) || exit;

$archive_place_items_amount = golo_get_option('archive_city_items_amount', '16');
$archive_place_image_size   = golo_get_option('archive_place_image_size', '540x480' );
$enable_city_filter         = golo_get_option('enable_city_filter', '1');
$enable_city_map            = golo_get_option('enable_city_map', '1');
$default_map                = golo_get_option('default_map', '1');
$archive_city_columns       = golo_get_option('archive_city_columns', '3');
$archive_city_columns_lg    = golo_get_option('archive_city_columns_lg', '3');
$archive_city_columns_md    = golo_get_option('archive_city_columns_md', '2');
$archive_city_columns_sm    = golo_get_option('archive_city_columns_sm', '2');
$archive_city_columns_xs    = golo_get_option('archive_city_columns_xs', '1');

$class_inner     = array();
$archive_class   = array();
$archive_class[] = 'area-places';
$archive_class[] = 'grid';
$archive_class[] = 'columns-'. $archive_city_columns;
$archive_class[] = 'columns-lg-'. $archive_city_columns_lg;
$archive_class[] = 'columns-md-'. $archive_city_columns_md;
$archive_class[] = 'columns-sm-'. $archive_city_columns_sm;
$archive_class[] = 'columns-xs-'. $archive_city_columns_xs;

if (is_tax()) {
    $current_term   = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    $taxonomy_title = $current_term->name;
    $taxonomy_name  = get_query_var('taxonomy');
}

$tax_query = array();
$args = array(
    'posts_per_page'      => $archive_place_items_amount,
    'post_type'           => 'place',
    'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'tax_query'           => array(
        array(
            'taxonomy' => $taxonomy_name,
            'field'    => 'slug',
            'terms'    => $current_term->slug
        )
    ),
    'meta_key'            => 'golo-place_featured',
    'orderby'             => 'meta_value',
);

if (!empty($current_term)) {
    $tax_query[] = array(
        'taxonomy' => $taxonomy_name,
        'field'    => 'slug',
        'terms'    => $current_term->slug
    );
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
    if( !empty($enable_city_map) ) {
        $class_inner[] = 'has-map';
    }else{
        $class_inner[] = 'no-map';
    }
}

/**
* @Hook: golo_archive_place_before
*
*/
do_action( 'golo_archive_place_before' ); 

?>

<div class="main-content">

    <div class="container">

        <div class="inner-content <?php echo join(' ', $class_inner); ?>">

            <?php do_action( 'golo_archive_heading_filter', $taxonomy_name, $current_term, $total_post); ?>

            <div class="row">

                <div class="col-left col-md-6">

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
                                    <?php printf( _n( '%s Result', '%s Results', $total_post, 'golo-framework' ), '<span class="count">' . esc_html( $total_post ) . '</span>' ); ?>
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
                                
                                <?php if( !empty($enable_city_filter) ) { ?>
                                <div class="btn-canvas-filter hidden-lg-up">
                                    <a href="#"><?php esc_html_e('Filter', 'golo-framework'); ?></a>
                                    <i class="las la-filter"></i>
                                </div>
                                <?php } ?>
                                
                                <?php if( !empty($enable_city_map) ) { ?>
                                <div class="btn-maps-filter golo-button">
                                    <a href="#">
                                        <i class="las la-map-marked-alt"></i>
                                        <?php esc_html_e('Maps view', 'golo-framework'); ?>
                                    </a>
                                </div>

                                <div class="btn-control btn-switch btn-hide-map">
                                    <span><?php esc_html_e('Maps', 'golo-framework'); ?></span>
                                    <label class="switch">
                                        <input type="checkbox" value="hide_map" <?php if( !$default_map ) { echo "checked"; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <?php } ?>

                            </div>
                            
                        </div>

                        <div class="<?php echo join(' ', $archive_class); ?>">

                            <?php if ( $data->have_posts() ) { ?>

                                <?php while ( $data->have_posts() ) : $data->the_post(); ?>

                                    <?php golo_get_template('content-place.php', array(
                                        'custom_place_image_size' => $archive_place_image_size
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
                
                <?php if( !empty($enable_city_map) ) { ?>
                <div class="col-right col-md-6">
                    
                    <div class="btn-control btn-switch btn-hide-map">
                        <span><?php esc_html_e('Show map', 'golo-framework'); ?></span>
                        <label class="switch">
                            <input type="checkbox" value="hide_map" <?php if( !$default_map ) { echo "checked"; } ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>

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

    </div>

</div>

<?php

/**
* @Hook: golo_archive_place_after
*
* @hooked archive_related_city
*/
do_action( 'golo_archive_place_after' );