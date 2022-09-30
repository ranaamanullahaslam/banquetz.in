<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $place_data, $place_meta_data;

wp_enqueue_script('plupload');
$ajax_url     = admin_url( 'admin-ajax.php');
$upload_nonce = wp_create_nonce('place_allow_upload');

$cf7_chosen_form = golo_get_option('cf7_contact_form', '');

if (!empty($cf7_chosen_form)) {
	$cf7_chosen_form = explode(',', $cf7_chosen_form);
}

$cf7_field = get_option('field-name');
$cf7_list  = get_posts(array(
    'post_type'     => 'wpcf7_contact_form',
    'numberposts'   => -1
));

$cf7_forms   = array('' => 'None');
$cf7_default = '';

if( !empty($cf7_list[0]->ID) ) {
    $cf7_default = $cf7_list[0]->ID;
}

foreach ($cf7_list as $cf7) {
    $cf7_forms[$cf7->ID] = $cf7->post_title. " (". $cf7->ID .")";
}

if( isset($place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_banner']) ) {
	$booking_banner_image = unserialize($place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_banner'][0]);
}

?>

<div class="place-fields-wrap">
    <div class="place-fields-title">
    	<div class="field-radio">
    		<div class="form-field <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == '' ) { echo 'checked'; } ?>" data-id="booking-url">
		    	<input type="radio" id="booking_type_none" name="place_booking_type" value="" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == '' ) { echo 'checked'; } ?>>
		    	<label for="booking_type_none"><?php esc_html_e('None', 'golo-framework'); ?></label>
		    </div>
		    <div class="form-field <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'info' ) { echo 'checked'; } ?>" data-id="booking-info">
		    	<input type="radio" id="booking_type_info" name="place_booking_type" value="info" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'info' ) { echo 'checked'; } ?>>
		    	<label for="booking_type_info"><?php esc_html_e('Booking Contact', 'golo-framework'); ?></label>
		    </div>
	        <div class="form-field <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'link' ) { echo 'checked'; } ?>" data-id="booking-url">
		    	<input type="radio" id="booking_type_link" name="place_booking_type" value="link" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'link' ) { echo 'checked'; } ?>>
		    	<label for="booking_type_link"><?php esc_html_e('Booking Affiliate', 'golo-framework'); ?></label>
		    </div>
		    <div class="form-field <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'banner' ) { echo 'checked'; } ?>" data-id="booking-banner">
		    	<input type="radio" id="booking_type_banner" name="place_booking_type" value="banner" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'banner' ) { echo 'checked'; } ?>>
	    		<label for="booking_type_banner"><?php esc_html_e('Banner Link', 'golo-framework'); ?></label>
		    </div>
		    <div class="form-field <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'form' ) { echo 'checked'; } ?>" data-id="booking-form">
		    	<input type="radio" id="booking_type_form" name="place_booking_type" value="form" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'form' ) { echo 'checked'; } ?>>
	    		<label for="booking_type_form"><?php esc_html_e('Booking Form', 'golo-framework'); ?></label>
		    </div>
		    <div class="form-field <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'contact' ) { echo 'checked'; } ?>" data-id="booking-contact">
		    	<input type="radio" id="booking_type_contact" name="place_booking_type" value="contact" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'contact' ) { echo 'checked'; } ?>>
	    		<label for="booking_type_contact"><?php esc_html_e('Contact Form', 'golo-framework'); ?></label>
		    </div>
	    </div>
    </div>

    <div class="tab-content">

		<div class="inner-content <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'link' ) { echo 'active'; } ?>" id="booking-url">
			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Booking URL', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields">
			        <div class="form-group">
			            <input type="text" class="form-control" name="place_booking" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking'][0] ); } ?>" placeholder="<?php esc_attr_e('Link', 'golo-framework'); ?>"/>
			        </div>
			    </div>
			</div>

			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Booking Site', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields">
			        <div class="form-group">
			            <input type="text" class="form-control" name="place_booking_site" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_site'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_site'][0] ); } ?>" placeholder="<?php esc_attr_e('Site URL', 'golo-framework'); ?>" />
			        </div>
			    </div>
			</div>

			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Booking URL 2', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields">
			        <div class="form-group">
			            <input type="text" class="form-control" name="place_booking_2" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_2'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_2'][0] ); } ?>" placeholder="<?php esc_attr_e('Link', 'golo-framework'); ?>"/>
			        </div>
			    </div>
			</div>

			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Booking Site 2', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields">
			        <div class="form-group">
			            <input type="text" class="form-control" name="place_booking_site_2" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_site_2'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_site_2'][0] ); } ?>" placeholder="<?php esc_attr_e('Site URL', 'golo-framework'); ?>" />
			        </div>
			    </div>
			</div>
		</div>
		
		<div class="inner-content <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'banner' ) { echo 'active'; } ?>" id="booking-banner">
			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Image', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields place-fields-file place-booking-image">
			        <div class="form-group">
			            <div id="booking_image_errors_log" class="errors-log"></div>
			            <div id="booking_image_plupload_container" class="file-upload-block preview">
			                <div id="golo_place_booking_image_view">
			                    <?php if($booking_banner_image['id']) : ?>
			                        <figure class="media-thumb media-thumb-wrap">
			                            <img src="<?php echo esc_url($booking_banner_image['url']); ?>">
			                            <div class="media-item-actions">
			                                <a class="icon icon-delete" data-place-id="<?php echo esc_attr($place_data->ID); ?>" data-attachment-id="<?php echo esc_attr($booking_banner_image['id']); ?>" href="#" ><i class="la la-trash-alt large"></i></a>
			                                <span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>
			                            </div>
			                        </figure>
			                    <?php endif; ?>
			                </div>
			                <button type="button" id="golo_select_booking_image" title="<?php esc_attr_e('Choose image','golo-framework') ?>" class="golo_booking_image golo-add-image">
			                    <i class="la la-upload large"></i>
			                </button>
			                <input type="hidden" class="booking_image_url form-control" name="place_booking_image_url" value="<?php if( isset( $booking_banner_image['url'] ) ) { echo esc_attr( $booking_banner_image['url'] ); } ?>" id="booking_image_url">
			                <input type="hidden" class="booking_image_id" name="place_booking_image_id" value="<?php if( isset( $booking_banner_image['id'] ) ) { echo esc_attr( $booking_banner_image['id'] ); } ?>" id="booking_image_id"/>
			            </div>
			            <div class="field-note"><?php esc_html_e('Maximum file size: 1 MB.', 'golo-framework'); ?></div>
			        </div>
			    </div>
			</div>

			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Banner URL', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields">
			        <div class="form-group">
			            <input type="text" class="form-control" name="place_booking_banner_url" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_banner_url'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_banner_url'][0] ); } ?>" placeholder="<?php esc_attr_e('Link', 'golo-framework'); ?>" />
			        </div>
			    </div>
			</div>
		</div>

		<div class="inner-content <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_type'][0] == 'contact' ) { echo 'active'; } ?>" id="booking-contact">
			<div class="place-fields-wrap">
			    <div class="place-fields-title">
			        <h3><?php esc_html_e('Booking Form', 'golo-framework'); ?></h3>
			    </div>
			    <div class="place-fields">
			        <div class="form-group">
			            <select name="place_booking_form" class="form-control nice-select wide">
			            	<?php foreach ($cf7_forms as $key => $value) { ?>
			            		<option value="<?php echo esc_attr($key); ?>" <?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_form'] ) && $place_meta_data[GOLO_METABOX_PREFIX. 'place_booking_form'][0] == $key ) { echo 'selected'; } ?>><?php echo esc_html($value);?></option>
			            	<?php } ?>
		                </select>
			        </div>
			    </div>
			</div>
		</div>

	</div>
</div>

<script>
    (function($) {
    "use strict";
        jQuery(document).ready(function () {
            var booking_image = function () {
                var uploader_booking_image = new plupload.Uploader({
                    browse_button: 'golo_select_booking_image',
                    file_data_name: 'place_upload_file',
                    container: 'booking_image_plupload_container',
                    url: '<?php echo esc_url($ajax_url); ?>' + "?action=golo_place_img_upload_ajax&nonce=" + '<?php echo esc_attr($upload_nonce); ?>',
                    filters: {
                        mime_types: [
                            {title: '<?php esc_html_e('Valid file formats', 'golo-framework'); ?>', extensions: "jpg,jpeg,gif,png"}
                        ],
                        max_file_size: '1024kb',
                        prevent_duplicates: true
                    }
                });
                uploader_booking_image.init();

                uploader_booking_image.bind('UploadProgress', function (up, file) {
                    document.getElementById("golo_select_booking_image").innerHTML = '<span><i class="la la-circle-notch la-spin large"></i></span>';
                });

                uploader_booking_image.bind('FilesAdded', function (up, files) {
                    var maxfiles = 1;
                    up.refresh();
                    uploader_booking_image.start();
                });
                uploader_booking_image.bind('Error', function (up, err) {
                    document.getElementById('booking_image_errors_log').innerHTML += "Error #" + err.code + ": " + err.message + "<br/>";
                });
                uploader_booking_image.bind('FileUploaded', function (up, file, ajax_response) {
                    document.getElementById("golo_select_booking_image").innerHTML = '<i class="la la-upload large"></i>';
                    var response = $.parseJSON(ajax_response.response);
                    if (response.success) {
                        $('.booking_image_url').val(response.full_image);
                        $('.booking_image_id').val(response.attachment_id);
                        var $html = 
                            '<figure class="media-thumb media-thumb-wrap">' +
                            '<img src="' + response.full_image + '">' +
                            '<div class="media-item-actions">' +
                            '<a class="icon icon-delete" data-place-id="0"  data-attachment-id="' + response.attachment_id + '" href="#" ><i class="la la-trash-alt large"></i></a>' +
                            '<span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>' +
                            '</div>' +
                            '</figure>';
                        $('#golo_place_booking_image_view').html($html);

                        $('#booking_image_url-error').hide();
                    }
                });
            };
            booking_image();
        });
    })(jQuery);
</script>