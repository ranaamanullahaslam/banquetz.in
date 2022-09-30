<?php 
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';

if( $current_city ){
	$current_term = get_term_by('slug', $current_city, 'place-city');
	
}else{
	$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
	
}
if( $current_term->taxonomy == 'place-city' ){
	$term_id        = $current_term->term_id;
	$taxonomy_title = $current_term->name;
	$image   = get_term_meta( $term_id, 'place_city_banner_image', true );
} else {
	$term_id        = $current_term->term_id;
	$taxonomy_title = $current_term->name;
	$image   = get_term_meta( $term_id, 'place_category_banner_image', true );
}

$no_image_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
$default_image = golo_get_option('default_place_image','');

$country = get_term_meta( $term_id, 'place_city_country', true );

$intro   = get_term_meta( $term_id, 'place_city_banner_intro', true );

$image_src = '';
if ($image && !empty($image['url'])) {
	$image_src = $image['url'];
} else {
    if($default_image != '') {
        if(is_array($default_image) && $default_image['url'] != '')
        {
            $image_src = $default_image['url'];
        }
    } else {
        $image_src = $no_image_src;
    }
}

$archive_city_layout_style  = golo_get_option('archive_city_layout_style', 'layout-default' );
$archive_city_banner_layout = golo_get_option('archive_city_banner_layout', 'layout-01' );

$class_align = 'block-center';
$class_city_layout = array();

if( $archive_city_banner_layout == 'layout-01' ) {
	$class_align = 'block-left';
}

if( $archive_city_banner_layout == 'layout-02' ) {
	$class_align = 'block-center';
}
$class_city_layout[] = $archive_city_banner_layout;

$class_city_layout[] = $archive_city_layout_style;
$class_city_layout[] = $class_align;

$term_id    = $current_term->term_id;
$currency   = !empty( get_term_meta($term_id, 'place_city_currency', true) ) ? get_term_meta($term_id, 'place_city_currency', true) : '';
$language   = !empty( get_term_meta($term_id, 'place_city_language', true) ) ? get_term_meta($term_id, 'place_city_language', true) : '';
$visit_time = !empty( get_term_meta($term_id, 'place_city_visit_time', true) ) ? get_term_meta($term_id, 'place_city_visit_time', true) : '';
$place_city_url = !empty( get_term_meta($term_id, 'place_city_youtube_url', true) ) ? get_term_meta($term_id, 'place_city_youtube_url', true) : '';

?>

<?php if( $image_src ) : ?>
<div class="golo-page-title page-title-city <?php echo join(' ', $class_city_layout); ?>">
	<div class="entry-page-title">
		<div class="entry-image">
			<?php if (golo_oembed_get($place_city_url)) : ?>
				<div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                    <?php echo golo_oembed_get($place_city_url, array('wmode' => 'transparent')); ?>
                </div>
			<?php else : ?>
				<img src="<?php echo esc_url( $image_src ) ?>" alt="<?php echo esc_attr( $taxonomy_title ); ?>" title="<?php echo esc_attr( $taxonomy_title ); ?>">
			<?php endif; ?>
			
			<?php if( $archive_city_banner_layout == 'layout-02' ) : ?>
			<div class="intro">
				<?php 
				if( !empty($country) ) : 
					$country_name = golo_get_country_by_code($country);
				?>
					<a class="entry-country" href="<?php echo esc_url(home_url('/')); ?>country/?id=<?php echo esc_attr($country); ?>"><?php echo esc_html($country_name); ?></a>
				<?php endif; ?>
				<h1 class="entry-title"><?php echo esc_html($taxonomy_title); ?></h1>
				<p><?php echo esc_html($intro); ?></p>
			</div>
			<?php endif; ?>
		</div>
		<div class="entry-detail">
			<div class="intro">
				<?php 
				if( !empty($country) ) : 
					$country_name = golo_get_country_by_code($country);
				?>
					<a class="entry-country" href="<?php echo esc_url(home_url('/')); ?>country/?id=<?php echo esc_attr($country); ?>"><?php echo esc_html($country_name); ?></a>
				<?php endif; ?>
				<h1 class="entry-title"><?php echo esc_html($taxonomy_title); ?></h1>
				<p><?php echo esc_html($intro); ?></p>
			</div>

			<?php if( $archive_city_banner_layout == 'layout-01' ) : ?>
				
			<ul class="info">
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

			<div class="after-image">
				<svg width="198px" height="400px" viewBox="0 0 198 400" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				    <defs>
				        <path d="M0,0 L26.2945309,0 C18.4769913,82.8373178 27.6126461,152.561479 53.7014953,210.169989 C79.7903445,267.7785 127.889846,331.05517 198,400 L0,400 L0,0.956 Z" id="path-1"></path>
				    </defs>
				    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				        <g transform="translate(-606.000000, -80.000000)">
				            <g transform="translate(0.000000, 80.000000)">
				                <g transform="translate(606.000000, 0.000000)">
				                    <mask fill="white">
				                        <use xlink:href="#path-1"></use>
				                    </mask>
				                    <use fill="#23D3D3" xlink:href="#path-1"></use>
				                </g>
				            </g>
				        </g>
				    </g>
				</svg>
			</div>

			<?php endif; ?>

		</div>
	</div>
</div>
<?php endif; ?>
