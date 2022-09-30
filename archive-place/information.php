<?php 

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$archive_city_banner_layout = golo_get_option('archive_city_banner_layout', 'layout-01' );

if( $archive_city_banner_layout == 'layout-01' ) {
	return;
}

$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';

if( $current_city ){
	$current_term = get_term_by('slug', $current_city, 'place-city');
}else{
	$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
}

$term_id      = $current_term->term_id;
$taxonomy_des = term_description($term_id, $current_term->slug);
$currency     = !empty( get_term_meta($term_id, 'place_city_currency', true) ) ? get_term_meta($term_id, 'place_city_currency', true) : '';
$language     = !empty( get_term_meta($term_id, 'place_city_language', true) ) ? get_term_meta($term_id, 'place_city_language', true) : '';
$visit_time   = !empty( get_term_meta($term_id, 'place_city_visit_time', true) ) ? get_term_meta($term_id, 'place_city_visit_time', true) : '';

$info_class = array('information');
if( empty($taxonomy_des) ){
	$info_class[] = 'no-des';
}
?>

<?php if( $current_term->taxonomy == 'place-city' ) { ?>
<div class="<?php echo join(' ', $info_class); ?>">
	<div class="container">
		<div class="inner-information">
			<div class="row">
				<div class="col-xl-6">
					<div class="entry-title">
						<h3><?php esc_html_e('Introducing', 'golo-framework'); ?></h3>
					</div>

					<?php if( !empty($taxonomy_des) ) : ?>
						<div class="entry-description">
							<?php echo wp_kses_post($taxonomy_des); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-xl-6">
					<ul class="entry-detail">
						<?php if( !empty($currency) ) : ?>
						<li>
							<strong><?php esc_html_e('CURRENCY','golo-framework'); ?></strong>
							<span>
								<i class="la la-money-bill-wave large"></i>
								<i><?php echo esc_html($currency); ?></i>
							</span>
						</li>
						<?php endif; ?>

						<?php if( !empty($language) ) : ?>
						<li>
							<strong><?php esc_html_e('LANGUAGE','golo-framework'); ?></strong>
							<span>
								<i class="la la-language large"></i>
								<i><?php echo esc_html($language); ?></i>
							</span>
						</li>
						<?php endif; ?>

						<?php if( !empty($visit_time) ) : ?>
						<li>
							<strong><?php esc_html_e('BEST TIME TO VISIT','golo-framework'); ?></strong>
							<span>
								<i class="la la-calendar large"></i>
								<i><?php echo esc_html($visit_time); ?></i>
							</span>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>