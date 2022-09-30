<?php
/**
 * The Template for displaying all single place
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'golo' );

?>

<?php
    /**
     * @Hook: golo_single_author_head
     *
     * @hooked author_info - 5
     */
    do_action('golo_single_author_head'); 
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

	<?php
        /**
         * @Hook: golo_single_author_summary
         *
         * @hooked author_place - 5
         * @hooked author_review - 10
         */
        do_action('golo_single_author_summary'); 
    ?>

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
         * @Hook: golo_single_author_sidebar
         *
         * @hooked author_about - 5
         */
        do_action('golo_single_author_sidebar'); 
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

get_footer( 'golo' );



