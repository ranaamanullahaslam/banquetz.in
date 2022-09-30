<?php 

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$place_meta_data = get_post_custom( $place_id );

$google_review_placeid = isset($place_meta_data[GOLO_METABOX_PREFIX . 'google_review_placeid']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'google_review_placeid'][0] : '';

if ($google_review_placeid == '') {
    return;
}

echo goloGetReviews($google_review_placeid);