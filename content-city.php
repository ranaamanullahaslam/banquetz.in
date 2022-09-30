<?php 
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( empty( $term_id ) ) {
    return;
}

$attach_id      = '';
$term           = get_term_by('id', $term_id, 'place-city');
$term_count     = $term->count;
$term_name      = $term->name;
$term_link      = get_term_link( $term, 'place-city');
$country        = get_term_meta( $term_id, 'place_city_country', true );
$featured_image = get_term_meta( $term_id, 'place_city_featured_image', true );

if ($featured_image && !empty($featured_image['url'])) {
    $attach_id = $featured_image['id'];
}

$no_image_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
$default_image = golo_get_option('default_place_image','');

if (preg_match('/\d+x\d+/', $custom_city_image_size)) {
    
    $image_sizes = explode('x', $custom_city_image_size);
    $width       = $image_sizes[0];
    $height      = $image_sizes[1];
    $image_src   = golo_image_resize_id($attach_id, $width, $height, true);

    if( $default_image != '' )
    {
        if( is_array($default_image) && $default_image['url'] != '' )
        {
            $resize = golo_image_resize_url($default_image['url'], $width, $height, true);
            if ($resize != null && is_array($resize)) {
                $no_image_src = $resize['url'];
            }
        }
    }
} else {
    if (!in_array($custom_city_image_size, array('full', 'thumbnail'))) {
        $custom_city_image_size = 'full';
    }
    $image_src = wp_get_attachment_image_src($attach_id, $custom_city_image_size);
    if ($image_src && !empty($image_src[0])) {
        $image_src = $image_src[0];
    }
    if (!empty($image_src)) {
        list($width, $height) = getimagesize($image_src);
    }
    if($default_image != '')
    {
        if(is_array($default_image) && $default_image['url'] != '')
        {
            $no_image_src = $default_image['url'];
        }
    }
}

if ($image_src == '') {
    $image_src = $no_image_src;
}

$city_item_class   = array('city-item');


?>

<div class="<?php echo join(' ', $city_item_class); ?>">
    <div class="city-inner">
        <div class="entry-thumb">
            <a href="<?php echo esc_url($term_link); ?>"><img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($image_src) ?>" alt="<?php echo esc_attr($term_name); ?>" title="<?php echo esc_attr($term_name); ?>"></a>
        </div>

        <?php  

        if( !empty($country) && isset($layout) && $layout == 'grid' ) : 

            $country_name = golo_get_country_by_code($country);
        ?>
            <div class="entry-country">
                <h4><a href="<?php echo esc_url(home_url('/')); ?>country/?id=<?php echo esc_attr($country); ?>"><?php echo __($country_name); ?></a></h4>
            </div>
        <?php endif; ?>
        
        <div class="entry-detail">
            <h3><a href="<?php echo esc_url($term_link); ?>" title="<?php echo esc_attr($term_name); ?>"><?php echo esc_html($term_name); ?></a></h3>
            <span>
                <?php printf( _n( '%s place', '%s places', $term_count, 'golo-framework' ), esc_html( $term_count ) ); ?>        
            </span>
        </div>
    </div>
</div>