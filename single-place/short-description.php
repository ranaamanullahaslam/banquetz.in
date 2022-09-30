<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();
$excerpt  = get_the_excerpt($place_id);
?>

<?php if( !empty($excerpt) ) : ?>
<div class="place-excerpt">
	<p><?php echo wp_trim_words($excerpt); ?></p>
</div>
<?php endif; ?>