<?php 

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';

if( $current_city ){
	$current_term  = get_term_by('slug', $current_city, 'place-city');
	$taxonomy_name = 'place-city';
}else{
	$current_term  = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
	$taxonomy_name = get_query_var('taxonomy');
}

$categories = array();
$args = array(
	'posts_per_page' => -1,
	'post_type'      => 'place',
	'tax_query' 	 => array(
	    array(
	      	'taxonomy' => $taxonomy_name,
	        'field'    => 'slug',
	        'terms'    => $current_term->slug
	    )
	 )
);
$places = get_posts( $args );
foreach ($places as $place) {
	$cates = wp_get_post_terms( $place->ID, 'place-categories' );
	foreach ($cates as $cate) {
		$categories[] = $cate->term_id;
	}
}
$categories = array_unique($categories);

?>

<div class="nav-categories">
	<div class="container">
		<div class="entry-nav">

			<?php if( $current_term->taxonomy == 'place-city' ) { ?>
				<div class="entry-categories">
					<ul>
						<?php if( $taxonomy_name == 'place-city' ) : ?>
						<li class="<?php if( get_query_var('term') == $current_term->slug ) { echo esc_attr('active'); } ?>">
							<a href="<?php echo get_term_link($current_term); ?>"><i class="las la-city"></i><?php echo esc_html($current_term->name); ?></a>
						</li>
						<?php endif; ?>

						<?php 
						foreach ($categories as $cate_id) {
							$term = get_term_by('id', $cate_id, 'place-categories');
							if( $current_term->slug ) {
								$url = get_term_link($term) . '?city=' . $current_term->slug;
							}
						?>
							<li class="<?php if( get_query_var('term') == $term->slug ) { echo esc_attr('active'); } ?>"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($term->name); ?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php }else{ ?>
				<div class="entry-tax">
					<div class="tax-name">
						<h2><?php echo esc_html($current_term->name); ?></h2>
					</div>
				</div>
			<?php } ?>

			<div class="maps-view golo-button">
				<a href="#">
					<i class="las la-map-marked-alt"></i>
					<?php esc_html_e('Maps view', 'golo-framework'); ?>
				</a>
			</div>
		</div>
	</div>
</div>