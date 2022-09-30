<?php
/**
 * The Template for displaying all single place
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'golo' );

/**
* @Hook: golo_single_place_before
*
* @hooked gallery_place
*/
do_action( 'golo_single_place_before' );

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

		<?php while ( have_posts() ) : the_post(); ?>

			<?php golo_get_template_part( 'content', 'single-place' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * @Hook: golo_output_content_wrapper_end
		 * 
		 * @hooked output_content_wrapper_end
		 */
		do_action( 'golo_output_content_wrapper_end' );
	?>

	<?php
		$place_id = '';
		$place_id = get_the_ID();
		$place_meta_data = get_post_custom( $place_id );
		$place_booking_type = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type'][0] : '';
		
		if( is_active_sidebar('place_sidebar') || !empty($place_booking_type) ) :

		/**
		 * @hooked golo_sidebar_place
		 */
		do_action( 'golo_sidebar_place' );

		endif;
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
* @Hook: golo_single_place_after
*
* @hooked related_place
*/
do_action( 'golo_single_place_after' );

get_footer( 'golo' );