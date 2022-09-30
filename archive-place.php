<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined( 'ABSPATH' ) || exit;

get_header( 'golo' );

$archive_place_layout_style = golo_get_option('archive_place_layout_style', 'layout-default');
$enable_map_event = golo_get_option('enable_map_event', 1);
$archive_place_layout_style = !empty($_GET['layout']) ? Golo_Helper::golo_clean(wp_unslash($_GET['layout'])) : $archive_place_layout_style;

$map_event = '';

if( $enable_map_event == '1' && ($archive_place_layout_style == 'layout-column' || $archive_place_layout_style == 'layout-top-filter') ) {
	$map_event = 'map-event';
} else {
	$map_event = 'map-event-zoom';
}

$archive_classes = array('archive-layout', 'archive-place', $archive_place_layout_style, $map_event);

?>

<div class="<?php echo join(' ', $archive_classes); ?>">
    
    <?php golo_get_template( 'archive-place/layout/' . $archive_place_layout_style . '.php' ); ?>

</div>

<?php
get_footer( 'golo' );