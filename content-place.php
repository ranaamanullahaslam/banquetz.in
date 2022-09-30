<?php
/**
 * The Template for displaying content place
 */

defined( 'ABSPATH' ) || exit;

$content_place = golo_get_option('layout_content_place', 'layout-01');

if( !empty($place_layout) ) {
	$content_place = $place_layout;
}

$id = $image_size = '';

$id = get_the_ID();

if( !empty($place_id) ){
    $id = $place_id;
}

if( !empty($custom_place_image_size) ){
    $image_size = $custom_place_image_size;
}

$effect_class = 'skeleton-loading';

golo_get_template( 'content-place/' . $content_place . '.php', array(
	'place_id'                => $id,
	'custom_place_image_size' => $image_size,
	'layout'                  => $content_place,
	'effect_class'            => $effect_class,
	'status'                  => !empty($status) ? $status : ''
) );