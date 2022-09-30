<?php 

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$place_meta_data = get_post_custom( $place_id );
$place_address  = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
$place_location = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);

if( $place_location && $place_location['address'] )
{
    $location = $place_location['address'];
}
else
{
    $location = $place_address;
}

$yelp_review = isset($place_meta_data[GOLO_METABOX_PREFIX . 'yelp_review']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'yelp_review'][0] : '';
$yelp_review_title = $yelp_review_type = null;
if ($yelp_review > 0) {
    $yelp_review_title = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'yelp_review_title', true);
    $yelp_review_type = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'yelp_review_type', true);
}

if ($yelp_review > 0) :
?>

<div class="place-review-yelp place-area">

    <div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('What is Nearby?', 'golo-framework'); ?></h3>
    </div>

	<?php if( !empty($yelp_review_title[0]) && !empty($yelp_review_type[0]) ): ?>
        <?php for ($i = 0; $i < $yelp_review; $i++) { ?>
            <?php 
            	if (!empty($yelp_review_title[$i]) && !empty($yelp_review_type[$i])) : 
            		$yelp['title'] = $yelp_review_title[$i];
					$yelp['term'] = $yelp_review_type[$i];
					$yelp['location'] = $location;

					echo YELP_Review::render($yelp);
            ?>
            <?php endif; ?>
        <?php } ?>
	<?php endif; ?>
</div>

<?php endif; ?>