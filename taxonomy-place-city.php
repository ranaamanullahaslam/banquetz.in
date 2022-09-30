<?php
/**
 * The Template for displaying taxonomy place city
 */

defined( 'ABSPATH' ) || exit;

get_header( 'golo' );

$archive_city_layout_style = golo_get_option('archive_city_layout_style', 'layout-default');
$enable_map_event = golo_get_option('enable_map_event', '1');
$archive_city_layout_style = !empty($_GET['layout']) ? Golo_Helper::golo_clean(wp_unslash($_GET['layout'])) : $archive_city_layout_style;

$map_event = '';

if( $enable_map_event == '1' && ($archive_city_layout_style == 'layout-column' || $archive_city_layout_style == 'layout-top-filter') ) {
	$map_event = 'map-event';
} else {
	$map_event = 'map-event-zoom';
}

$archive_classes = array('archive-layout', 'archive-city', $archive_city_layout_style, $map_event);

?>

<div class="<?php echo join(' ', $archive_classes); ?>">
    
    <?php golo_get_template( 'archive-city/' . $archive_city_layout_style . '.php' ); ?>

</div>

<?php
get_footer( 'golo' );