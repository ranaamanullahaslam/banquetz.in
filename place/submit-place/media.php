<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $hide_place_fields;

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-ui-sortable');
$ajax_url     = admin_url( 'admin-ajax.php');
$upload_nonce = wp_create_nonce('place_allow_upload');
$image_max_file_size = golo_get_option('image_max_file_size', '1000kb');
$max_place_gallery_images = golo_get_option('max_place_gallery_images', 5);
?>

<?php if (!in_array('featured_image', $hide_place_fields)) : ?>
<div class="place-fields-wrap place-fields-media">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Featured image', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-fields-file place-featured-image">
        <div class="form-group">
            <div id="featured_image_errors_log" class="errors-log"></div>
            <div id="featured_image_plupload_container" class="file-upload-block preview">
                <div id="golo_place_featured_image_view"></div>
                <button type="button" id="golo_select_featured_image" title="<?php esc_attr_e('Choose image','golo-framework') ?>" class="golo_featured_image golo-add-image">
                    <i class="la la-upload large"></i>
                </button>
                <input type="hidden" class="featured_image_url form-control" name="place_featured_image_url" value="" id="featured_image_url">
                <input type="hidden" class="featured_image_id" name="place_featured_image_id" value="" id="featured_image_id"/>
            </div>
            <div class="field-note"><?php echo sprintf( __( 'Maximum file size: %s.', 'golo-framework' ), $image_max_file_size); ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('gallery_image', $hide_place_fields)) : ?>
<div class="place-fields-wrap place-fields-media">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Gallery Images (optional)', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-fields-file place-gallery-image">
        <div class="form-group">
            <div class="media-gallery">
                <div id="place_gallery_thumbs_container"></div>
            </div>
            <div id="golo_gallery_errors_log" class="errors-log"></div>
            <div class="golo-place-gallery">
                <div id="golo_gallery_plupload_container" class="media-drag-drop">
                    <h4>
                        <i class="la la-upload large"></i>
                        <?php esc_html_e('Drag and drop file here', 'golo-framework'); ?>
                    </h4>
                    <span><?php esc_html_e('or', 'golo-framework'); ?></span>
                    <button type="button" id="golo_select_gallery_images" class="btn btn-primary"><?php esc_html_e('Select Images', 'golo-framework'); ?></button>
                </div>
            </div>
            <div class="field-note"><?php echo sprintf( __( 'Maximum file size: %s.', 'golo-framework' ), $image_max_file_size); ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('video', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Video (optional)', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-video">
        <div class="form-group">
            <input type="text" id="place_video_url" class="form-control" name="place_video_url" placeholder="<?php esc_attr_e('Youtube video url', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    (function($) {
    "use strict";
        jQuery(document).ready(function () {
            var featured_image = function () {
                var uploader_featured_image = new plupload.Uploader({
                    browse_button: 'golo_select_featured_image',
                    file_data_name: 'place_upload_file',
                    container: 'featured_image_plupload_container',
                    url: '<?php echo esc_url($ajax_url); ?>' + "?action=golo_place_img_upload_ajax&nonce=" + '<?php echo esc_attr($upload_nonce); ?>',
                    filters: {
                        mime_types: [
                            {title: '<?php esc_html_e('Valid file formats', 'golo-framework'); ?>', extensions: "jpg,jpeg,gif,png"}
                        ],
                        max_file_size: '<?php echo esc_html($image_max_file_size); ?>',
                        prevent_duplicates: true
                    }
                });
                uploader_featured_image.init();

                uploader_featured_image.bind('UploadProgress', function (up, file) {
                    document.getElementById("golo_select_featured_image").innerHTML = '<span><i class="la la-circle-notch la-spin large"></i></span>';
                });

                uploader_featured_image.bind('FilesAdded', function (up, files) {
                    var maxfiles = 1;
                    up.refresh();
                    uploader_featured_image.start();
                });
                uploader_featured_image.bind('Error', function (up, err) {
                    document.getElementById('featured_image_errors_log').innerHTML += "Error #" + err.code + ": " + err.message + "<br/>";
                });
                uploader_featured_image.bind('FileUploaded', function (up, file, ajax_response) {
                    document.getElementById("golo_select_featured_image").innerHTML = '<i class="la la-upload large"></i>';
                    var response = $.parseJSON(ajax_response.response);

                    if (response.success) {

                        $('.featured_image_url').val(response.full_image);
                        $('.featured_image_id').val(response.attachment_id);
                        var $html = 
                            '<figure class="media-thumb media-thumb-wrap">' +
                            '<img src="' + response.full_image + '">' +
                            '<div class="media-item-actions">' +
                            '<a class="icon icon-delete" data-place-id="0"  data-attachment-id="' + response.attachment_id + '" href="#" ><i class="la la-trash-alt large"></i></a>' +
                            '<span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>' +
                            '</div>' +
                            '</figure>';
                            console.log($html);
                        $('#golo_place_featured_image_view').html($html);
                        golo_place_gallery_event('thumb');

                        $('#featured_image_url-error').hide();
                    }
                });
            };
            featured_image();

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
                            'place_id': place_id,
                            'attachment_id': attachment_id,
                            'type': $type,
                            'removeNonce': '<?php echo esc_attr($upload_nonce); ?>'
                        },
                        success: function (response) {
                            if (response.success) {
                                thumbnail.remove();
                                thumbnail.hide();

                                $('#featured_image_url-error').show();
                            }
                            icon_delete.html('<i class="la la-circle-notch la-spin large"></i>');
                        },
                        error: function () {
                            icon_delete.html('<i class="la la-trash-alt large"></i>');
                        }
                    });
                });
            }

            golo_place_gallery_event('gallery');

            // Place Gallery images
            var golo_place_gallery_images = function () {

                $("#place_gallery_thumbs_container").sortable();

                /* initialize uploader */
                var uploader = new plupload.Uploader({
                    browse_button: 'golo_select_gallery_images',
                    file_data_name: 'place_upload_file',
                    container: 'golo_gallery_plupload_container',
                    drop_element: 'golo_gallery_plupload_container',
                    multi_selection: true,
                    url: "<?php echo esc_url($ajax_url); ?>?action=golo_place_img_upload_ajax&nonce=<?php echo esc_attr($upload_nonce); ?>",
                    filters: {
                        mime_types: [
                            {title: '<?php esc_html_e('Valid file formats', 'golo-framework'); ?>', extensions: "jpg,jpeg,gif,png"}
                        ],
                        max_file_size: '<?php echo esc_html($image_max_file_size); ?>',
                        prevent_duplicates: true
                    }
                });
                uploader.init();

                uploader.bind('FilesAdded', function (up, files) {
                    var placeThumb = "";
                    var maxfiles = '<?php echo esc_html($max_place_gallery_images); ?>';
                    if (up.files.length > maxfiles) {
                        up.splice(maxfiles);
                        alert('no more than ' + maxfiles + ' file(s)');
                        return;
                    }
                    plupload.each(files, function (file) {
                        placeThumb += '<div id="holder-' + file.id + '" class="col-sm-2 media-thumb-wrap"></div>';
                    });
                    document.getElementById('place_gallery_thumbs_container').innerHTML += placeThumb;
                    up.refresh();
                    uploader.start();
                });

                uploader.bind('UploadProgress', function (up, file) {
                    document.getElementById("holder-" + file.id).innerHTML = '<span><i class="la la-circle-notch la-spin large"></i></span>';
                });

                uploader.bind('Error', function (up, err) {
                    document.getElementById('golo_gallery_errors_log').innerHTML += "Error: " + err.message + "<br/>";
                });

                uploader.bind('FileUploaded', function (up, file, ajax_response) {
                    var response = $.parseJSON(ajax_response.response);
                    if (response.success) {
                        var $html =
                            '<figure class="media-thumb">' +
                            '<img src="' + response.url + '"/>' +
                            '<div class="media-item-actions">' +
                            '<a class="icon icon-delete" data-place-id="0"  data-attachment-id="' + response.attachment_id + '" href="#" ><i class="la la-trash-alt large"></i></a>' +
                            '<input type="hidden" class="place_image_ids" name="place_image_ids[]" value="' + response.attachment_id + '"/>' +
                            '<span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>' +
                            '</div>' +
                            '</figure>';

                        document.getElementById("holder-" + file.id).innerHTML = $html;
                        golo_place_gallery_event('gallery');
                    }
                });
            };
            golo_place_gallery_images();
        });
    })(jQuery);
</script>