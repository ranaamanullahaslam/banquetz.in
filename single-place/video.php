<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$place_meta_data = get_post_custom( $place_id );

$place_video_url   = isset( $place_meta_data[GOLO_METABOX_PREFIX . 'place_video_url']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_video_url'][0] : '';
$place_video_image = isset( $place_meta_data[GOLO_METABOX_PREFIX . 'place_video_image']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_video_image'][0] : '';

?>
<?php if (!empty($place_video_url)) : ?>
    <div class="place-video place-area">
        <div class="entry-heading">
            <h3 class="entry-title"><?php esc_html_e('Video', 'golo-framework'); ?></h3>
        </div>
        <div class="entry-place-element">
            <div class="entry-thumb-wrap">
                <?php if (wp_oembed_get($place_video_url)) : ?>
                    <?php
                    $image_src = golo_image_resize_id( $place_video_image, 870, 420, true);
                    $width = '870';
                    $height = '420';
                    if (!empty($image_src)):?>
                        <div class="entry-thumbnail">
                            <img class="img-responsive" src="<?php echo esc_url($image_src); ?>" width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" alt="<?php the_title_attribute(); ?>"/>
                            <a class="view-video" href="<?php echo esc_url($place_video_url); ?>" data-lity><i class="lar la-play-circle icon-large"></i></a>
                        </div>
                    <?php else: ?>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($place_video_url, array('wmode' => 'transparent')); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                        <?php echo wp_kses_post($place_video_url); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>