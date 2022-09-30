<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined( 'ABSPATH' ) || exit;

$archive_place_items_city  = golo_get_option('archive_place_items_city', '6');
$archive_place_items_show  = golo_get_option('archive_place_items_show', '4');
$custom_place_image_size   = golo_get_option('archive_place_image_size', '540x480' );

$current_term   = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$term_id        = $current_term->term_id;
$taxonomy_title = $current_term->name;
$taxonomy_name  = get_query_var('taxonomy');

$categories = array();
$args = array(
	'posts_per_page' 	  => -1,
	'post_type'      	  => 'place',
	'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
	'tax_query' 	 	  => array(
	    array(
	      	'taxonomy' => $taxonomy_name,
	        'field'    => 'slug',
	        'terms'    => $current_term->slug
	    )
	),
	'meta_key'            => 'golo-place_featured',
    'orderby'             => 'meta_value',
);
$places = get_posts( $args );
foreach ($places as $place) {
	$cates = wp_get_post_terms( $place->ID, 'place-categories' );
	foreach ($cates as $cate) {
		$categories[] = $cate->term_id;
	}
}
$categories = array_unique($categories);

/**
* @Hook: golo_archive_place_before
*
*/
do_action( 'golo_archive_place_before' ); 

?>

<?php
	/**
	 * @Hook: golo_layout_wrapper_start
	 * 
	 * @hooked layout_wrapper_start
	 */
	do_action( 'golo_layout_wrapper_start' );
?>
	
	<?php
		/**
		 * @Hook: golo_output_content_wrapper_start
		 * 
		 * @hooked output_content_wrapper_start
		 */
		do_action( 'golo_output_content_wrapper_start' ); 
	?>

	    <?php foreach ($categories as $category) { ?>

			<div class="slick-category">

	    	<?php 
			$cate 		= get_term_by( 'id', $category, 'place-categories');
			$cate_name  = $cate->name;
			$cate_count = golo_get_category_count($current_term->slug, $cate->slug);

	    	if( !empty($cate) ) :
	    	?>

		    	<div class="block-heading space-between">
		    		<h2 class="entry-title"><?php echo esc_html($cate_name); ?></h2>

		    		<?php if( $cate_count && $cate_count > $archive_place_items_show ) : ?>
			    		<a href="<?php echo get_term_link($category); ?>?city=<?php echo esc_attr($current_term->slug); ?>" class="entry-count">
			    			<?php echo sprintf( esc_html__( 'See all (%s)', 'golo-framework' ), '<span class="count">' . esc_html( $cate_count ) . '</span>' ); ?>		
			    		</a>
			    	<?php endif; ?>
		    	</div>

		    <?php endif; ?>
	    		
			<?php echo golo_get_place_by_category( $archive_place_items_city, $archive_place_items_show, $term_id, $category, $custom_place_image_size ); ?>
			
			</div>

	    <?php } ?>
    
	<?php
		/**
		 * @Hook: golo_output_content_wrapper_end
		 * 
		 * @hooked output_content_wrapper_end
		 */
		do_action( 'golo_output_content_wrapper_end' );
	?>

<?php
	/**
	 * @Hook: golo_layout_wrapper_end
	 * 
	 * @hooked layout_wrapper_end
	 */
	do_action( 'golo_layout_wrapper_end' );
?>

<?php

/**
* @Hook: golo_archive_place_after
*
* @hooked archive_related_city
*/
do_action( 'golo_archive_place_after' );