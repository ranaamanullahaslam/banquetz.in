<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;
$current_author = $wp_query->get_queried_object();
$user_id = $current_author->ID;
$description = get_the_author_meta('description', $user_id);
$user_email = get_the_author_meta('user_email', $user_id);

$author_mobile_number = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_mobile_number', $user_id);
$author_fax_number    = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_fax_number', $user_id);
$author_facebook_url  = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_facebook_url', $user_id);
$author_twitter_url   = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_twitter_url', $user_id);
$author_linkedin_url  = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_linkedin_url', $user_id);
$author_pinterest_url = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_pinterest_url', $user_id);
$author_instagram_url = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_instagram_url', $user_id);
$author_youtube_url   = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_youtube_url', $user_id);
$author_skype         = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_skype', $user_id);
?>

<aside id="secondary">
    <?php if( ( is_user_logged_in() && ( $author_mobile_number || $user_email || $author_facebook_url || $author_twitter_url || $author_linkedin_url || $author_pinterest_url || $author_instagram_url || $author_youtube_url || $author_skype ) ) || ( !is_user_logged_in() && $description ) ) : ?>
	<div class="inner-sidebar">

		<h3 class="entry-title"><?php esc_html_e('About', 'golo-framework'); ?></h3>

		<?php if( $description ) : ?>
        <div class="entry-desc">
            <p><?php echo esc_html($description); ?></p>
        </div>
        <?php endif; ?>

		<?php if( is_user_logged_in() && ( $author_mobile_number || $user_email ) ) : ?>
        <div class="contact-info">
            <ul class="list-info">
                <?php if( $author_mobile_number ) : ?>
                <li>
                    <i class="la la-phone large"></i>
                    <a href="tel:<?php echo esc_attr($author_mobile_number); ?>"><?php echo esc_html($author_mobile_number); ?></a>
                </li>
                <?php endif; ?>
                
                <?php if( $user_email ) : ?>
                <li class="email">
                    <i class="la la-envelope large"></i>
                    <a href="mailto: <?php echo esc_attr($user_email); ?>"><?php echo esc_html($user_email); ?></a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if( is_user_logged_in() && ( $author_facebook_url || $author_twitter_url || $author_linkedin_url || $author_pinterest_url || $author_instagram_url || $author_youtube_url || $author_skype ) ) : ?>
        <div class="author-social">
            <ul class="list-info">
                <?php if( !empty($author_facebook_url) ) : ?>
                <li>
                    <a class="facebook hint--top" href="<?php echo esc_attr($author_facebook_url); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Facebook', 'golo-framework' ); ?>"><i class="la la-facebook-f icon-medium"></i></a>
                </li>
                <?php endif; ?>
                
                <?php if( !empty($author_twitter_url) ) : ?>
                <li>
                    <a class="twitter hint--top" href="<?php echo esc_attr($author_twitter_url); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Twitter', 'golo-framework' ); ?>"><i class="lab la-twitter icon-medium"></i></a>
                </li>
                <?php endif; ?>
                
                <?php if( !empty($author_linkedin_url) ) : ?>
                <li>
                    <a class="linkedin hint--top" href="<?php echo esc_attr($author_linkedin_url); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Linkedin', 'golo-framework' ); ?>"><i class="lab la-linkedin-in icon-medium"></i></a>
                </li>
                <?php endif; ?>

                <?php if( !empty($author_pinterest_url) ) : ?>
                <li>
                    <a class="pinterest hint--top" href="<?php echo esc_attr($author_pinterest_url); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Pinterest', 'golo-framework' ); ?>"><i class="lab la-pinterest-p icon-medium"></i></a>
                </li>
                <?php endif; ?>

                <?php if( !empty($author_instagram_url) ) : ?>
                <li>
                    <a class="instagram hint--top" href="<?php echo esc_attr($author_instagram_url); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Instagram', 'golo-framework' ); ?>"><i class="lab la-instagram icon-medium"></i></a>
                </li>
                <?php endif; ?>

                <?php if( !empty($author_youtube_url) ) : ?>
                <li>
                    <a class="youtube hint--top" href="<?php echo esc_attr($author_youtube_url); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Youtube', 'golo-framework' ); ?>"><i class="lab la-youtube icon-medium"></i></a>
                </li>
                <?php endif; ?>

                <?php if( !empty($author_skype) ) : ?>
                <li>
                    <a class="skype hint--top" href="skype:<?php echo esc_attr($author_skype); ?>?chat" target="_blank" aria-label="<?php esc_attr_e( 'Skype', 'golo-framework' ); ?>"><i class="lab la-skype icon-medium"></i></a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
	</div>
    <?php endif; ?>
</aside>