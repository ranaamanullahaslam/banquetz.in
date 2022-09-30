<?php
/**
 * The Template for displaying taxonomy place city
 */

defined( 'ABSPATH' ) || exit;

$custom_city_image_size = golo_get_option('archive_city_image_size', '540x740' );
$archive_country_items_amount = golo_get_option('archive_country_items_amount', '12');

$country = isset( $_GET['id'] ) ? golo_clean(wp_unslash($_GET['id'])) : 'FR';
$country = strtoupper($country);

$no_image_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
$default_image = golo_get_option('default_place_image', '');

if( empty($country) ){
	return;
}

$archive_class 	  = array();
$archive_class[]  = 'archive-place';
$archive_class[]  = 'area-places';
$archive_class[]  = 'grid';
$archive_class[]  = 'columns-4 columns-md-3 columns-sm-2 columns-xs-2';

if ( get_query_var( 'paged' ) ) {
	$paged = get_query_var('paged');
}elseif( get_query_var('page') ){
	$paged = get_query_var('page');
}else{
	$paged = 1;
}

$per_page = $archive_country_items_amount;
$number_of_series = count( get_terms( 
	'place-city',
	array(
		'hide_empty' => 0,
		'meta_query' => array(
	        array(
				'key'     => 'place_city_country',
				'value'   => $country,
				'compare' => '='
	        )
	    )
	) 
) );
$offset   = $per_page * ( $paged - 1 ); 

$args = array(
	'taxonomy'   => 'place-city',
	'number'     => $per_page,
	'offset'     => $offset,
	'order'      => 'DESC',
	'hide_empty' => 0,
	'meta_query' => array(
        array(
			'key'     => 'place_city_country',
			'value'   => $country,
			'compare' => '='
        )
    )
);

$terms = get_terms($args);

$term_id = $terms[0]->term_id;

$image   = get_term_meta( $term_id, 'place_city_banner_image', true );
$image_src = '';
if ($image && !empty($image['url'])) {
	$image_src = $image['url'];
} else {
	if( $default_image != '' )
    {
        if( is_array($default_image) && $default_image['url'] != '' )
        {
            $resize = golo_image_resize_url($default_image['url'], $width, $height, true);
            if ($resize != null && is_array($resize)) {
                $image_src = $resize['url'];
            }
        }
    }
}

if ($image_src == '') {
    $image_src = $no_image_src;
}

?>

<?php 
if( !empty($country) ) : 
	$country_name = golo_get_country_by_code($country);
?>

<div class="golo-single-country">

	<div class="golo-page-title page-title-city layout-default block-center">
		<div class="entry-page-title">
			<div class="entry-image">
				<img src="<?php echo esc_url( $image_src ) ?>" alt="<?php echo esc_attr( $country_name ); ?>" title="<?php echo esc_attr( $country_name ); ?>">
			</div>
			<div class="entry-detail">
				<div class="intro">
					<h1 class="entry-title"><?php echo esc_html($country_name); ?></h1>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="main-area">
		<div class="container">
			<div class="<?php echo join(' ', $archive_class); ?>">
				<?php if( $terms ) :
			        foreach ($terms as $term) {
			            $term_id = $term->term_id;
			        ?>
			            <?php golo_get_template('content-city.php', array(
			                'term_id'                => $term_id,
			                'custom_city_image_size' => $custom_city_image_size
			            )); ?>
			        <?php } ?>
			    <?php endif; ?>
			</div>

			<?php 
			$pagination = paginate_links( 
				array(
					'base'      => get_pagenum_link(1) . '%_%',
					'format'    => '&paged=%#%',
					'current'   => $paged,
					'total'     => ceil( $number_of_series / $per_page ),
					'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'golo-framework'),
					'next_text' => __('<i class="fal fa-chevron-right"></i>', 'golo-framework'),
				)
			);
			?>
			
			<?php if( $pagination ) : ?>
			<div class="golo-pagination normal">
				<div class="pagination">
					<?php echo wp_kses_post($pagination); ?>
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>

</div>
