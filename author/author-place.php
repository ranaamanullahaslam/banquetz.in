<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;
$current_author = $wp_query->get_queried_object();
$author_id = $current_author->ID;
$user_display_name = get_the_author_meta('display_name', $author_id);
$total_places = get_total_posts_by_user($author_id, 'place');
?>

<?php if ($total_places > 0): ?>
<div class="author-places">
	<div class="author-places-inner">
		<div class="block-heading">
			<h3><?php echo sprintf( __( 'Places (%1$s)', 'golo-framework' ), $total_places); ?></h3>
		</div>
		
		<div class="area-content">
			<?php echo get_posts_by_user($author_id, 'place', 6, 3, 2, 2, 2, 1); ?>
		</div>
	</div>
</div>
<?php endif; ?>