<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ( !is_user_logged_in() ) {
    golo_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

global $current_user;
wp_get_current_user();
$default_image           = GOLO_THEME_URI . '/assets/images/default-user-image.png';
$user_id                 = $current_user->ID;
$user_login              = $current_user->user_login;
$user_firstname          = get_the_author_meta('first_name', $user_id);
$user_lastname           = get_the_author_meta('last_name', $user_id);
$user_email              = get_the_author_meta('user_email', $user_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $user_id);
$author_avatar_image_id  = get_the_author_meta('author_avatar_image_id', $user_id);
$description             = get_the_author_meta('description', $user_id);
$author_mobile_number    = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_mobile_number', $user_id);
$author_fax_number       = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_fax_number', $user_id);
$user_facebook_url       = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_facebook_url', $user_id);
$user_twitter_url        = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_twitter_url', $user_id);
$user_linkedin_url       = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_linkedin_url', $user_id);
$user_pinterest_url      = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_pinterest_url', $user_id);
$user_instagram_url      = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_instagram_url', $user_id);
$user_youtube_url        = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_youtube_url', $user_id);
$user_skype              = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_skype', $user_id);

if (!$author_avatar_image_url) {
    $author_avatar_image_url = $default_image;
}


$ajax_url     = admin_url( 'admin-ajax.php');
$upload_nonce = wp_create_nonce('place_allow_upload');

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'my-profile');
wp_localize_script(GOLO_PLUGIN_PREFIX . 'my-profile', 'golo_my_profile_vars',
    array(
        'ajax_url' => GOLO_AJAX_URL,
        'site_url' => get_site_url(),
    )
);
?>

<div class="golo-my-profile area-main-control">
        <div class="container">
            <div class="entry-my-profile entry-my-page">

                <?php 
                    global $current_user;

                    wp_get_current_user();
                    $user_id   = $current_user->ID;
                    $user_name = $current_user->display_name;
                    $user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
                    $package_title = '';
                    if( $user_package_id ) {
                        $package_title = get_the_title($user_package_id);
                    }
                    $paid_submission_type = golo_get_option('paid_submission_type','no');

                    $golo_package = new Golo_Package();
                    $get_expired_date = $golo_package->get_expired_date($user_package_id, $user_id);
                    $current_date = date('Y-m-d');

                    $d1 = strtotime( $get_expired_date );
                    $d2 = strtotime( $current_date );

                    if ($get_expired_date === 'Never Expires' || $get_expired_date === 'Unlimited') {
                        $d1 = 999999999999999999999999;
                    }
                ?>
                <div class="heading-page">
                    <h2 class="entry-title"><?php esc_html_e('Profile Setting', 'golo-framework'); ?></h2>
                    
                    <?php if ($paid_submission_type == 'per_package' && !in_array( 'customer', (array) $current_user->roles )) { ?>
                    <div class="entry-alert">
                        <span>
                            <?php if( $package_title && $d1 > $d2 ) { ?>
                                <?php echo sprintf( __( 'You are currently "%s" package.', 'golo-framework' ), '<strong>' . $package_title . '</strong>'); ?>
                            <?php }else{ ?>
                                <?php esc_html_e('Buy a package to add your place now.', 'golo-framework'); ?>
                            <?php } ?>
                        </span>

                        <a class="accent-color" href="<?php echo golo_get_permalink('packages'); ?>"><?php esc_html_e('Upgrade now', 'golo-framework'); ?></a>
                    </div>
                    <?php } ?>
                </div>

                <form action="#" class="form-profile">
                    
                    <h3><?php esc_html_e('Profile Info', 'golo-framework'); ?></h3>
                    
                    <div class="place-fields-wrap place-fields-file">
                        <div class="form-group">
                            <label><?php esc_html_e('Avatar', 'golo-framework'); ?></label>
                            <div id="avatar_image_errors_log"></div>
                            <div id="avatar_image_plupload_container" class="file-upload-block preview">
                                <div id="golo_author_avatar_image_view">
                                    <?php if($author_avatar_image_id) : ?>
                                    <figure class="media-thumb media-thumb-wrap">
                                        <img src="<?php echo esc_attr($author_avatar_image_url); ?>">
                                        <div class="media-item-actions">
                                            <a class="icon icon-delete" data-place-id="0" data-attachment-id="<?php echo esc_attr($author_avatar_image_id); ?>" href="#" ><i class="la la-trash-alt large"></i></a>
                                            <span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>
                                        </div>
                                    </figure>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="golo_select_avatar_image" title="<?php esc_attr_e('Choose image','golo-framework') ?>" class="golo_avatar_image golo-add-image">
                                    <i class="la la-upload large"></i>
                                </button>
                                <input type="hidden" class="author_avatar_image_url form-control" name="author_avatar_image_url" value="<?php echo esc_attr($author_avatar_image_url); ?>" id="author_avatar_image_url">
                                <input type="hidden" class="author_avatar_image_id" name="author_avatar_image_id" value="<?php echo esc_attr($author_avatar_image_id); ?>" id="author_avatar_image_id"/>
                            </div>
                        </div>
                    </div>

                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="user_firstname"><?php esc_html_e('First Name', 'golo-framework'); ?></label>
                            <input type="text" name="user_firstname" id="user_firstname" class="form-control" value="<?php echo esc_attr($user_firstname); ?>" placeholder="<?php esc_attr_e('Enter First Name', 'golo-framework'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_lastname"><?php esc_html_e('Last Name', 'golo-framework'); ?></label>
                            <input type="text" name="user_lastname" id="user_lastname" class="form-control" value="<?php echo esc_attr($user_lastname); ?>" placeholder="<?php esc_attr_e('Enter Last Name', 'golo-framework'); ?>">
                        </div>
                    </div>

                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="user_firstname"><?php esc_html_e('Mobile', 'golo-framework'); ?></label>
                            <input type="text" name="author_mobile_number" id="author_mobile_number" class="form-control" value="<?php echo esc_attr($author_mobile_number); ?>" placeholder="<?php esc_attr_e('Mobile number', 'golo-framework'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_lastname"><?php esc_html_e('Fax number', 'golo-framework'); ?></label>
                            <input type="text" name="author_fax_number" id="author_fax_number" class="form-control" value="<?php echo esc_attr($author_fax_number); ?>" placeholder="<?php esc_attr_e('Fax number', 'golo-framework'); ?>">
                        </div>
                    </div>
                    
                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="user_email"><?php esc_html_e('Email', 'golo-framework'); ?></label>
                            <input type="text" name="user_email" id="user_email" class="form-control" value="<?php echo esc_attr($user_email); ?>" placeholder="<?php esc_attr_e('Enter email', 'golo-framework'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_skype"><?php esc_html_e('Skype', 'golo-framework'); ?></label>
                            <input type="text" id="user_skype" name="user_skype" class="form-control" value="<?php echo esc_attr($user_skype); ?>" placeholder="<?php esc_attr_e('Enter skype url', 'golo-framework'); ?>">
                        </div>
                    </div>
                    
                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="user_facebook_url"><?php esc_html_e('Facebook URL', 'golo-framework'); ?></label>
                            <input type="text" id="user_facebook_url" name="user_facebook_url" value="<?php echo esc_attr($user_facebook_url); ?>" class="form-control" placeholder="<?php esc_attr_e('Enter facebook url', 'golo-framework'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_twitter_url"><?php esc_html_e('Twitter URL', 'golo-framework'); ?></label>
                            <input type="text" id="user_twitter_url" name="user_twitter_url" class="form-control" value="<?php echo esc_attr($user_twitter_url); ?>" placeholder="<?php esc_attr_e('Enter twitter url', 'golo-framework'); ?>">
                        </div>
                    </div>
                    
                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="user_instagram_url"><?php esc_html_e('Instagram URL', 'golo-framework'); ?></label>
                            <input type="text" id="user_instagram_url" name="user_instagram_url" class="form-control" value="<?php echo esc_attr($user_instagram_url); ?>" placeholder="<?php esc_attr_e('Enter instagram url', 'golo-framework'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_youtube_url"><?php esc_html_e('Youtube URL', 'golo-framework'); ?></label>
                            <input type="text" id="user_youtube_url" name="user_youtube_url" class="form-control" value="<?php echo esc_attr($user_youtube_url); ?>" placeholder="<?php esc_attr_e('Enter youtube url', 'golo-framework'); ?>">
                        </div>
                    </div>
                    
                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="user_pinterest_url"><?php esc_html_e('Pinterest URL', 'golo-framework'); ?></label>
                            <input type="text" id="user_pinterest_url" name="user_pinterest_url" class="form-control" value="<?php echo esc_attr($user_pinterest_url); ?>" placeholder="<?php esc_attr_e('Enter pinterest url', 'golo-framework'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_linkedin_url"><?php esc_html_e('Linkedin URL', 'golo-framework'); ?></label>
                            <input type="text" id="user_linkedin_url" name="user_linkedin_url" class="form-control" value="<?php echo esc_attr($user_linkedin_url); ?>" placeholder="<?php esc_attr_e('Enter linkedin url', 'golo-framework'); ?>">
                        </div>
                    </div>

                    <div class="place-fields-wrap">
                        <div class="form-group">
                            <label for="user_pinterest_url"><?php esc_html_e('Description', 'golo-framework'); ?></label>
                            <textarea name="user_description" id="user_description" cols="30" rows="5"><?php echo esc_html($description); ?></textarea>
                        </div>
                    </div>

                    <?php wp_nonce_field('golo_update_profile_ajax_nonce', 'golo_security_update_profile'); ?>
                    <button type="button" class="btn btn-primary btn-frontend  gl-button" id="golo_update_profile">
                        <span><?php esc_html_e('Update Profile', 'golo-framework'); ?></span>
                        <span class="btn-loading"><i class="la la-circle-notch la-spin large"></i></span>
                    </button>
                </form>

                <form action="#" class="form-change-password">
                    
                    <h3><?php esc_html_e('Change Password', 'golo-framework'); ?></h3>
                    
                    <div id="password_reset_msgs" class="golo_messages message"></div>

                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="oldpass"><?php esc_html_e('Old Password', 'golo-framework'); ?></label>
                            <input id="oldpass" value="" class="form-control" name="oldpass" type="password" placeholder="<?php esc_attr_e('Enter old password', 'golo-framework'); ?>">
                        </div>
                    </div>
                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="newpass"><?php esc_html_e('New Password ', 'golo-framework'); ?></label>
                            <input id="newpass" value="" class="form-control" name="newpass" type="password" placeholder="<?php esc_attr_e('Enter new password', 'golo-framework'); ?>">
                        </div>
                    </div>
                    <div class="place-fields-wrap form-2-col">
                        <div class="form-group">
                            <label for="confirmpass"><?php esc_html_e('Confirm Password', 'golo-framework'); ?></label>
                            <input id="confirmpass" value="" class="form-control" name="confirmpass" type="password" placeholder="<?php esc_attr_e('Enter confirm password', 'golo-framework'); ?>">
                        </div>
                    </div>
                    <?php wp_nonce_field('golo_change_password_ajax_nonce', 'golo_security_change_password'); ?>
                    <button type="button" class="btn btn-primary btn-frontend gl-button" id="golo_change_pass">
                        <span><?php esc_html_e('Update Password', 'golo-framework'); ?></span>
                        <span class="btn-loading"><i class="la la-circle-notch la-spin large"></i></span>
                    </button>
                </form>
            </div>
        </div>
</div>

<script>
    (function($) {
    "use strict";
        jQuery(document).ready(function () {
            var avatar_image = function () {
                var uploader_avatar_image = new plupload.Uploader({
                    browse_button: 'golo_select_avatar_image',
                    file_data_name: 'place_upload_file',
                    container: 'avatar_image_plupload_container',
                    url: '<?php echo esc_url($ajax_url); ?>' + "?action=golo_place_img_upload_ajax&nonce=" + '<?php echo esc_attr($upload_nonce); ?>',
                    filters: {
                        mime_types: [
                            {title: '<?php esc_html_e('Valid file formats', 'golo-framework'); ?>', extensions: "jpg,jpeg,gif,png"}
                        ],
                        max_file_size: '3072kb',
                        prevent_duplicates: true
                    }
                });
                uploader_avatar_image.init();

                uploader_avatar_image.bind('UploadProgress', function (up, file) {
                    document.getElementById("golo_select_avatar_image").innerHTML = '<span><i class="la la-circle-notch la-spin large"></i></span>';
                });

                uploader_avatar_image.bind('FilesAdded', function (up, files) {
                    var maxfiles = 1;
                    up.refresh();
                    uploader_avatar_image.start();
                });
                uploader_avatar_image.bind('Error', function (up, err) {
                    document.getElementById('avatar_image_errors_log').innerHTML += "<br/>" + "Error #" + err.code + ": " + err.message;
                });
                uploader_avatar_image.bind('FileUploaded', function (up, file, ajax_response) {
                    document.getElementById("golo_select_avatar_image").innerHTML = '<i class="la la-upload large"></i>';
                    var response = $.parseJSON(ajax_response.response);
                    if (response.success) {
                        $('.author_avatar_image_url').val(response.full_image);
                        $('.author_avatar_image_id').val(response.attachment_id);
                        var $html = 
                            '<figure class="media-thumb media-thumb-wrap">' +
                            '<img src="' + response.full_image + '">' +
                            '<div class="media-item-actions">' +
                            '<a class="icon icon-delete" data-place-id="0"  data-attachment-id="' + response.attachment_id + '" href="#" ><i class="la la-trash-alt large"></i></a>' +
                            '<span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>' +
                            '</div>' +
                            '</figure>';
                        $('#golo_author_avatar_image_view').html($html);
                        golo_place_gallery_event('thumb');
                    }
                });
            };
            avatar_image();

            // Place Thumbnails
            var golo_place_gallery_event = function ($type) {
                $('body').on('click', '.icon-delete', function (e) {
                    e.preventDefault();
                    var $this         = $(this),
                        icon_delete   = $this,
                        thumbnail     = $this.closest('.media-thumb-wrap'),
                        place_id      = $this.data('place-id'),
                        attachment_id = $this.data('attachment-id');

                    icon_delete.html('<i class="la la-circle-notch la-spin large"></i>');

                    $.ajax({
                        type: 'post',
                        url: '<?php echo esc_url($ajax_url); ?>',
                        dataType: 'json',
                        data: {
                            'action': 'remove_place_img_ajax',
                            'place_id' : place_id,
                            'user_id': <?php echo $current_user->ID; ?>,
                            'attachment_id': attachment_id,
                            'type': $type,
                            'removeNonce': '<?php echo esc_attr($upload_nonce); ?>'
                        },
                        success: function (response) {
                            if (response.success) {
                                thumbnail.remove();
                                thumbnail.hide();
                            }
                            icon_delete.html('<i class="la la-circle-notch la-spin large"></i>');
                            $( '.author_avatar_image_url' ).val( response.url );
                        },
                        error: function () {
                            icon_delete.html('<i class="la la-trash-alt large"></i>');
                        }
                    });
                });
            }
            golo_place_gallery_event('thumb');
        });
    })(jQuery);
</script>