<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;
$current_author = $wp_query->get_queried_object();
$user_id = $current_author->ID;

$user_display_name = get_the_author_meta('display_name', $user_id);
$description = get_the_author_meta('description', $user_id);
$user_email = get_the_author_meta('user_email', $user_id);
$author_link = get_author_posts_url($user_id);
$avatar_url = get_avatar_url($user_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $user_id);
$author_avatar_image_id  = get_the_author_meta('author_avatar_image_id', $user_id);
if( !empty($author_avatar_image_url) ){
    $avatar_url = $author_avatar_image_url;
}
$avatar_url = golo_image_resize_url($avatar_url, 100, 100, true);

$author_mobile_number = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_mobile_number', $user_id);
$author_fax_number    = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_fax_number', $user_id);
$author_facebook_url  = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_facebook_url', $user_id);
$author_twitter_url   = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_twitter_url', $user_id);
$author_linkedin_url  = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_linkedin_url', $user_id);
$author_pinterest_url = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_pinterest_url', $user_id);
$author_instagram_url = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_instagram_url', $user_id);
$author_youtube_url   = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_youtube_url', $user_id);
$author_skype         = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_skype', $user_id);

$total_places = get_total_posts_by_user($user_id, 'place');
$total_reviews = get_total_reviews_by_user($user_id);
?>

<div class="author-info">

	<div class="container">

		<div class="info-area">
	
			<div class="entry-left">
				<div class="entry-detail">
		            <?php if( $avatar_url['url'] ) : ?>
		            <div class="entry-avatar">
		                <a class="avatar" href="<?php echo esc_url($author_link); ?>">
		                    <img src="<?php echo esc_url($avatar_url['url']); ?>" title="<?php echo esc_attr($user_display_name); ?>" alt="<?php echo esc_attr($user_display_name); ?>" >
		                </a>
		            </div>
		            <?php endif; ?>

		            <div class="entry-info">
		                <h3 class="author-name"><a href="<?php echo esc_url($author_link); ?>"><?php echo esc_html($user_display_name); ?></a></h3>
		                
		                <div class="author-count">
		                	<?php echo sprintf( __( '%1$s places, %2$s reviews', 'golo-framework' ), $total_places, $total_reviews); ?>
		                </div>
		            </div>
		        </div>
			</div>

			<div class="entry-right">
				<div class="btn-send-message">
                    <a class="btn-open-popup gl-button" href="#"><i class="la la-envelope icon-large"></i><?php esc_html_e('Send a message', 'golo-framework'); ?></a>

                    <?php if ( ! empty( $user_email ) ): ?>
                    <div class="contact-agent popup">
                        <div class="bg-overlay"></div>

                        <div class="inner-popup text-center custom-scrollbar">

                            <a href="#" class="btn-close"><i class="la la-times icon-large"></i></a>
                            
                            <div class="entry-heading">
                                <h3><?php esc_html_e('Send me a message', 'golo-framework'); ?></h3>
                            </div>

                            <form action="#" method="POST" id="contact-agent-form" class="row">
                                <input type="hidden" name="target_email" value="<?php echo esc_attr( $user_email ); ?>">
                                <input type="hidden" name="place_url" value="<?php echo get_permalink(); ?>">
 
                                <div class="form-group golo-field col-sm-4">
                                    <input class="form-control" name="sender_name" type="text" placeholder="<?php esc_attr_e( 'Full Name', 'golo-framework' ); ?> *">
                                    <div class="hidden name-error form-error"><?php esc_html_e( 'Please enter your Name!', 'golo-framework' ); ?></div>
                                </div>

                                <div class="form-group golo-field col-sm-4">
                                    <input class="form-control" name="sender_phone" type="text" placeholder="<?php esc_attr_e( 'Phone Number', 'golo-framework' ); ?> *">
                                    <div class="hidden phone-error form-error"><?php esc_html_e( 'Please enter your Phone!', 'golo-framework' ); ?></div>
                                </div>

                                <div class="form-group golo-field col-sm-4">
                                    <input class="form-control" name="sender_email" type="email" placeholder="<?php esc_attr_e( 'Email Address', 'golo-framework' ); ?> *">
                                    <div class="hidden email-error form-error" data-not-valid="<?php esc_attr_e( 'Your Email address is not Valid!', 'golo-framework' ) ?>" data-error="<?php esc_attr_e( 'Please enter your Email!', 'golo-framework' ) ?>"><?php esc_html_e( 'Please enter your Email!', 'golo-framework' ); ?></div>
                                </div>

                                <div class="form-group area-field golo-field col-sm-12">
                                    <textarea class="form-control" name="sender_msg" rows="5" placeholder="<?php esc_attr_e( 'Message', 'golo-framework' ); ?> *"><?php $title=get_the_title(); echo sprintf(esc_html__( 'Hello, I am interested in [%s]', 'golo-framework' ), esc_html($title)) ?></textarea>
                                    <div class="hidden message-error form-error"><?php esc_html_e( 'Please enter your Message!', 'golo-framework' ); ?></div>
                                </div>

                                <div class="bottom-form col-sm-12">
                                    <?php wp_nonce_field('golo_contact_agent_ajax_nonce', 'golo_security_contact_agent'); ?>
                                    <input type="hidden" name="action" id="contact_agent_with_place_url_action" value="golo_contact_agent_ajax">
                                    <button type="submit" class="agent-contact-btn btn gl-button"><?php esc_html_e( 'Submit Request', 'golo-framework' ); ?></button>
                                    <div class="form-messages"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
			</div>

		</div>

	</div>

</div>