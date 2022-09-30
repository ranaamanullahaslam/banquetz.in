<?php 
$min_suffix    = golo_get_option('enable_min_css', 0) == 1 ? '.min' : '';
$min_suffix_js = golo_get_option('enable_min_js', 0) == 1 ? '.min' : '';

$map_zoom_level         = golo_get_option('map_zoom_level', '15');
$map_type               = golo_get_option('map_type', 'google_map');
if( $map_type == 'google_map' ) {
    $google_map_style       = golo_get_option('googlemap_style', '');
} else {
    $mapbox_api_key         = Golo_Helper::golo_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
	$google_map_style       = golo_get_option('mapbox_style', 'streets-v11');
}
$google_map_needed      = 'true';
$map_cluster_icon_url   = GOLO_PLUGIN_URL . 'assets/images/cluster-icon.png';
$default_cluster        = golo_get_option('cluster_icon', '');
$map_pin_cluster        = golo_get_option('map_pin_cluster', 1);
$enable_filter_location = golo_get_option('enable_filter_location', 0);
$googlemap_type         = golo_get_option('googlemap_type', 'roadmap');

wp_enqueue_script('google-map');
wp_enqueue_script('markerclusterer');
wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'search_map', GOLO_PLUGIN_URL . 'templates/place/place-search-map/js/place-search-map' . $min_suffix_js . '.js', array('jquery'), GOLO_PLUGIN_VER, true);
wp_localize_script(GOLO_PLUGIN_PREFIX . 'search_map', 'golo_search_map_vars',
    array(
        'ajax_url'               => GOLO_AJAX_URL,
        'not_found'              => esc_html__("We didn't find any results, you can retry with other keyword.", 'golo-framework'),
        'item_amount'            => '12',
        'marker_image_size'      => '100x100',
        'googlemap_default_zoom' => $map_zoom_level,
        'clusterIcon'            => $map_cluster_icon_url,
        'map_pin_cluster'        => $map_pin_cluster,
        'google_map_needed'      => $google_map_needed,
        'google_map_style'       => $google_map_style,
        'googlemap_type'         => $googlemap_type ,
        'enable_filter_location' => $enable_filter_location,
        'not_place'              => esc_html__('No place found', 'golo-framework'),
    )
);

$price_is_slider = golo_get_option('price_is_slider', true );
$area_is_slider  = golo_get_option('area_is_slider', true );

$min_price = isset( $_GET['min_price'] ) ? golo_clean(wp_unslash($_GET['min_price'])) : '0';
$max_price = isset( $_GET['max_price'] ) ? golo_clean(wp_unslash($_GET['max_price'])) : '1000000';

$min_area = isset( $_GET['min_area'] ) ? golo_clean(wp_unslash($_GET['min_area'])) : '0';
$max_area = isset( $_GET['max_area'] ) ? golo_clean(wp_unslash($_GET['max_price'])) : '10000';

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

$place_details_order    = golo_get_option( 'place_details_order', $place_details_order_default );

if (in_array('enable_sp_review', $place_details_order )) {
    $review = '';
} else {
    $review = 'hidden';
}

?>

<div class="place-search" data-review="<?php echo esc_attr($review); ?>">
    <div class="entry-map">
        <input id="pac-input" class="controls" type="text" placeholder="<?php esc_html_e( 'Search Box', 'golo-framework' ); ?>">
        <div class="nav-place-map">
            <a href="#" class="btn-close">
                <i class="la la-times medium"></i>
                <span><?php esc_html_e('Back to list', 'golo-framework'); ?></span>
            </a>
            
            <?php if( get_query_var('taxonomy') == 'place-city' ) { ?>
            <select name="category" class="search-control form-control golo-nice-select">
                <option value=""><?php esc_html_e('Show all','golo-framework'); ?></option>
                <?php 
                $place_cates = get_categories(array(
                    'taxonomy'   => 'place-categories',
                    'hide_empty' => 1,
                    'orderby'    => 'term_id',
                    'order'      => 'ASC'
                ));
                if($place_cates) :
                    foreach ($place_cates as $place_cate) {
                    ?>
                        <option value="<?php echo esc_attr($place_cate->slug); ?>"><?php echo esc_html($place_cate->name); ?></option>
                    <?php } ?>
                <?php endif; ?>
            </select>
            <?php } ?>
        </div>
        <?php if( $map_type == 'google_map' ) { ?>
            <div id="place-search-map" class="golo-map-place maptype" style="height: 100vh;width: 100%;" data-maptype="<?php echo $map_type; ?>"></div>
        <?php } else { ?>
            <div id="map" class="maptype" style="width: 100%; height: 100vh;" data-maptype="<?php echo $map_type; ?>" data-key="<?php if( $mapbox_api_key ) { echo $mapbox_api_key; } ?>" data-level="<?php if( $map_zoom_level ) { echo $map_zoom_level; } ?>" data-type="<?php if( $google_map_style ) { echo $google_map_style; } ?>"></div>
        <?php } ?>
        <div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>
        <div class="no-result"><span><?php esc_html_e("We didn't find any results",'golo-framework'); ?></span></div>
    </div>
    
    <?php if( is_tax() ){ ?>
    <div class="hidden-field hidden">
        <?php 
            $current_city   = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';
            $current_term   = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $taxonomy_title = $current_term->name;
            $taxonomy_name  = get_query_var('taxonomy');
        ?>
        <div class="field-control">
            <input type="hidden" class="form-control" name="city" value="<?php echo esc_attr($current_city); ?>">
            <input type="hidden" class="form-control" name="taxonomy_name" value="<?php echo esc_attr($taxonomy_name); ?>">
            <input type="hidden" class="form-control" name="current_term" value="<?php echo esc_attr($current_term->slug); ?>">
        </div>
    </div>
    <?php } ?>
</div>
