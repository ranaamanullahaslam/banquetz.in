<?php
use Elementor\Core\Settings\Manager as SettingsManager;
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$id = get_the_ID();
$type_single_place = golo_get_option('type_single_place', 'type-1' );
$type_single_place = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : $type_single_place;
$place_gallery     = get_post_meta( get_the_ID(), GOLO_METABOX_PREFIX . 'place_images', true);
$place_video_url   = get_post_meta( get_the_ID(), GOLO_METABOX_PREFIX . 'place_video_url', true);
$attach_id         = get_post_thumbnail_id();

$show = 1;
if( $type_single_place == 'type-2' ){
	$show = 3;
}

$default_image = golo_get_option('default_place_image','');

$general_settings_model = SettingsManager::get_settings_managers( 'general' )->get_model();
$is_global_image_lightbox_enabled = 'yes' === $general_settings_model->get_settings( 'elementor_global_image_lightbox' );

?>

<div class="place-thumbnails place-area <?php echo esc_attr($type_single_place); ?>">
    <div class="entry-place-element">
        <div class="single-place-thumbs <?php if( $is_global_image_lightbox_enabled == false ) { echo 'enable'; } ?>">

			<?php 
				$slick_attributes = array(
		            '"slidesToShow": ' . $show,
		            '"slidesToScroll": 1',
		            '"autoplay": false',
		            '"autoplaySpeed": 5000',
		            '"infinite": true',
		        );

				if( $type_single_place == 'type-2' ) {
					$slick_attributes[] = '"variableWidth": true';
					$slick_attributes[] = '"centerMode": true';
				}

		        $wrapper_attributes[] = "data-slick='{". implode(', ', $slick_attributes) ."}'";
			?>

            <div class="golo-slick-carousel slick-nav margin-0" <?php echo implode(' ', $wrapper_attributes); ?>>
            <?php
            $obj_place_gallery = explode('|', $place_gallery);
            $count = count($obj_place_gallery);
            foreach ( $obj_place_gallery as $key => $image ) :
            	if( $image ) {
            		$image_full_src = wp_get_attachment_image_src( $image, 'full');
                    $thumb_src      = $image_full_src[0];
            	}else{
            	    if($default_image != '')
    			    {
    			        if(is_array($default_image) && $default_image['url'] != '')
    			        {
    			            $thumb_src = $default_image['url'];
    			        }
    			    } else {
    			        $thumb_src = GOLO_PLUGIN_URL . 'assets/images/placeholder-attachment.png';
    			    }
            	}
                
                if ( !empty($thumb_src) ) {
                ?>
                    <figure>
                        <a href="<?php echo esc_url($thumb_src); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="lgslider" class="lgbox">
                            <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                        </a>
                    </figure>
                <?php } ?>
            <?php endforeach; ?>

			<?php 
			if( $count <= 3 && $type_single_place == 'type-2' ) { 
				$place_holder_size = '600x600';
			?>
				<?php for( $i = 0;$i <= 3 - $count;$i++ ){ ?>
				<figure>
                    <a href="<?php echo $thumb_src; ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="lgslider" class="lgbox">
                        <img src="<?php echo $thumb_src; ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                    </a>
                </figure>
                <?php } ?>
			<?php } ?>

            </div>
            
            <?php if( ($place_gallery || $place_video_url) && $type_single_place == 'type-1' ) : ?>
            <div class="entry-media">
            	<?php if($place_gallery) : ?>
            	<a class="btn-gallery btn-normal dark" href="#">
                    <i class="la la-images large"></i>
                    <span><?php esc_html_e( 'Gallery', 'golo-framework' ); ?></span>
                </a>
                <?php endif; ?>
				
				<?php if($place_video_url) : ?>
                <a class="btn-video btn-normal dark" href="<?php echo esc_url($place_video_url); ?>" data-lity>
                    <i class="la la-youtube large"></i>
                    <span><?php esc_html_e( 'Video', 'golo-framework' ); ?></span>
                </a>
            	<?php endif; ?>
            </div>
            <?php endif; ?>
            

            <div class="entry-nav">
            	<?php 
		            golo_get_template('place/wishlist.php', array(
		                'place_id' => $id
		            ));
		        ?>
            	
                
                <div class="toggle-social">
                    <a href="#" class="btn-share">
                    	<i class="la la-share-square large"></i>
                    </a>

                    <?php
                        if( golo_get_option('enable_social_share', '1') == '1' ) {
                            golo_get_template('global/social-share.php');
                        }
                    ?>
                </div>
            </div>
			
			<?php if($type_single_place == 'type-1') { ?>
			<div class="entry-single-head">
				<div class="container">
					<?php 
						golo_get_template('single-place/head.php');
						golo_get_template('single-place/meta.php');
					?>
				</div>
			</div>
            <?php } ?>
        </div>
       
    </div>
</div>