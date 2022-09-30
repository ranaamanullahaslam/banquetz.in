<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $place_data, $place_meta_data, $hide_place_fields;


wp_enqueue_script('plupload');
wp_enqueue_script('jquery-ui-sortable');
$ajax_url     = admin_url( 'admin-ajax.php');
$upload_nonce = wp_create_nonce('place_allow_upload');
$image_max_file_size = golo_get_option('image_max_file_size', '1000kb');
$menu_types = array('' => 'None');
$posts = get_posts([
    'post_type' => 'place',
    'post_status' => 'publish',
    'numberposts' => -1
]);

if ( $posts ) : 
    foreach ($posts as $post) {
        $types = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'menu_types_name', true);
        if ($types) {
            foreach ($types as $key => $value) {
                if (!in_array($value, $menu_types)) {
                    $menu_types[$value] = $value;
                }
            }
        }
    }
endif;
?>

<div class="field-clone field-menu-clone">
    <?php 
        $place_menu_tab = get_post_meta( $place_data->ID,GOLO_METABOX_PREFIX. 'menu_tab', false );
            $menu_tab  = (isset($place_menu_tab) && is_array($place_menu_tab) && count( $place_menu_tab ) > 0)? $place_menu_tab[0]: '';
            if( !empty($menu_tab)) {
                $i = 0;
                foreach ($menu_tab as $menu) {
                if ($i == 0) {
                    $image_attributes = wp_get_attachment_image_src( $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id'] );
    ?>
    <div class="clone-wrap">

        <div class="place-fields-wrap">
            <div class="place-fields">
                <div class="form-group row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="place-fields-title" for="menu-name"><?php esc_html_e('Name', 'golo-framework'); ?></label>
                            <input type="text" class="form-control" id="menu-name" name="menu_name[]" placeholder="<?php esc_attr_e('Item Name', 'golo-framework'); ?>" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_title' ]; ?>" autofill="off" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="place-fields-title" for="menu-price"><?php esc_html_e('Price', 'golo-framework'); ?></label>
                            <input type="text" id="menu-price" class="form-control" name="menu_price[]" placeholder="<?php esc_attr_e('Item Price', 'golo-framework'); ?>" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_price' ]; ?>" autofill="off" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="place-fields-title" for="menu-type"><?php esc_html_e('Type', 'golo-framework'); ?></label>

                            <select name="menu_type[]" id="menu-type" class="form-control nice-select wide">
                                <?php
                                    if ($menu_types) {
                                        foreach ($menu_types as $key => $value) {
                                            if ( $menu[ GOLO_METABOX_PREFIX . 'menu_item_type' ] === $value ) {
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }
                                            echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="place-fields-wrap">
            <div class="place-fields-title">
                <h3><?php esc_html_e('Description', 'golo-framework'); ?></h3>
            </div>
            <div class="place-fields place-title">
                <div class="form-group">
                    <textarea name="item_desc[]" class="form-control" rows="3" id="item_desc" placeholder="<?php esc_attr_e('Item Description', 'golo-framework'); ?>"><?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_description' ]; ?></textarea>
                </div>
            </div>
        </div>

        <div class="menu-flex">

            <div class="place-fields-wrap place-fields-media">
                <div class="place-fields-title">
                    <h3><?php esc_html_e('Item image', 'golo-framework'); ?></h3>
                </div>
                <div class="place-fields place-fields-file place-featured-image">
                    <div class="form-group">
                        <div id="menu_image_errors_log" class="errors-log"></div>
                        <div id="menu_image_plupload_container" class="file-upload-block preview">
                            <div class="golo_menu_menu_image_view">
                                <?php if($image_attributes) : ?>
                                    <figure class="media-thumb media-thumb-wrap">
                                        <img src="<?php echo $image_attributes[0]; ?>">
                                        <div class="media-item-actions">
                                            <a class="icon icon-delete" data-place-id="<?php echo esc_attr($place_data->ID); ?>" data-attachment-id="<?php echo esc_attr($menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id']); ?>" href="#" ><i class="la la-trash-alt large"></i></a>
                                            <span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>
                                        </div>
                                    </figure>
                                <?php endif; ?>
                            </div>
                            <button type="button" id="golo_item_menu_image" title="<?php esc_attr_e('Choose image','golo-framework') ?>" class="golo_menu_image golo-add-image">
                                <i class="la la-upload large"></i>
                            </button>
                            <input type="hidden" class="menu_image_url form-control" name="place_menu_image_url[]" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['url']; ?>" id="menu_image_url">
                            <input type="hidden" class="menu_image_id" name="place_menu_image_id[]" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id']; ?>" id="menu_image_id"/>
                        </div>
                        <div class="field-note"><?php echo sprintf( __( 'Maximum file size: %s.', 'golo-framework' ), $image_max_file_size); ?></div>
                        <div class="field-note"><?php echo sprintf( __( 'Please fill out the complete field before adding more.', 'golo-framework' ), $image_max_file_size); ?></div>
                    </div>
                </div>
            </div>

            <a href="#" class="remove-menu">
                <i class="la la-trash-alt"></i>
            </a>

        </div>

    </div>
    <?php } $i++; } } ?>
</div>

<div class="add-menu-list">
    <?php 
        if( !empty($menu_tab)) {
            $i = 0;
            foreach ($menu_tab as $menu) {
            if ($i > 0) {
                $image_attributes = wp_get_attachment_image_src( $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id'] );
    ?>
    <div class="clone-wrap">

        <div class="place-fields-wrap">
            <div class="place-fields">
                <div class="form-group row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="place-fields-title"><?php esc_html_e('Name', 'golo-framework'); ?></label>
                            <input type="text" class="form-control menu-name" name="menu_name[]" placeholder="<?php esc_attr_e('Item Name', 'golo-framework'); ?>" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_title' ]; ?>" autofill="off" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="place-fields-title" for="menu-price"><?php esc_html_e('Price', 'golo-framework'); ?></label>
                            <input type="text" id="menu-price" class="form-control" name="menu_price[]" placeholder="<?php esc_attr_e('Item Price', 'golo-framework'); ?>" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_price' ]; ?>" autofill="off" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="place-fields-title" for="menu-type"><?php esc_html_e('Type', 'golo-framework'); ?></label>
                            <select name="menu_type[]" id="menu-type" class="form-control nice-select wide">
                                <option value=""><?php esc_html_e( 'None', 'golo' ); ?></option>
                                <?php
                                    if ($menu_types) {
                                        foreach ($menu_types as $key => $value) {
                                            if ( $menu[ GOLO_METABOX_PREFIX . 'menu_item_type' ] === $value ) {
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }
                                            echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="place-fields-wrap">
            <div class="place-fields-title">
                <h3><?php esc_html_e('Description', 'golo-framework'); ?></h3>
            </div>
            <div class="place-fields place-title">
                <div class="form-group">
                    <textarea name="item_desc[]" class="form-control" rows="3" id="item_desc" placeholder="<?php esc_attr_e('Item Description', 'golo-framework'); ?>"><?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_description' ]; ?></textarea>
                </div>
            </div>
        </div>

        <div class="menu-flex">

            <div class="place-fields-wrap place-fields-media">
                <div class="place-fields-title">
                    <h3><?php esc_html_e('Item image', 'golo-framework'); ?></h3>
                </div>
                <div class="place-fields place-fields-file place-featured-image">
                    <div class="form-group">
                        <div id="menu_image_errors_log" class="errors-log"></div>
                        <div id="menu_image_plupload_container" class="file-upload-block preview">
                            <div class="golo_menu_menu_image_view">
                                <?php if($image_attributes) : ?>
                                    <figure class="media-thumb media-thumb-wrap">
                                        <img src="<?php echo $image_attributes[0]; ?>">
                                        <div class="media-item-actions">
                                            <a class="icon icon-delete" data-place-id="<?php echo esc_attr($place_data->ID); ?>" data-attachment-id="<?php echo esc_attr($menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id']); ?>" href="#" ><i class="la la-trash-alt large"></i></a>
                                            <span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>
                                        </div>
                                    </figure>
                                <?php endif; ?>
                            </div>
                            <button type="button" id="golo_item_menu_image<?php echo $i; ?>" title="<?php esc_attr_e('Choose image','golo-framework') ?>" class="golo_menu_image golo-add-image">
                                <i class="la la-upload large"></i>
                            </button>
                            <input type="hidden" class="menu_image_url form-control" name="place_menu_image_url[]" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['url']; ?>" id="menu_image_url">
                            <input type="hidden" class="menu_image_id" name="place_menu_image_id[]" value="<?php echo $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id']; ?>" id="menu_image_id"/>
                        </div>
                        <div class="field-note"><?php echo sprintf( __( 'Maximum file size: %s.', 'golo-framework' ), $image_max_file_size); ?></div>
                        <div class="field-note"><?php echo sprintf( __( 'Please fill out the complete field before adding more.', 'golo-framework' ), $image_max_file_size); ?></div>
                    </div>
                </div>
            </div>

            <a href="#" class="remove-menu">
                <i class="la la-trash-alt"></i>
            </a>

        </div>

    </div>
    <?php } $i++; } } ?>
</div>
<a href="#addmenu" class="add-menu btn">               
    <i class="la la-plus"></i>
    <span><?php esc_html_e('Add more', 'golo-framework'); ?></span>
</a>

<script>
    (function($) {
    "use strict";
        jQuery(document).ready(function () {

            function add_image() {

                $( '.golo-add-image' ).on( 'click', function() {
                    
                    var wrap = $( this ).parents( '.clone-wrap' );
                    var id_browse_button = $( this ).attr( 'id' );
                    var container = $( this ).parents( '.file-upload-block' ).attr( 'id' );
                    var errors = $( this ).parents( '.form-group' ).find( '.errors-log' ).attr( 'id' );
                    var uploader_menu_image = new plupload.Uploader({
                        browse_button: id_browse_button,
                        file_data_name: 'place_upload_file',
                        container: container,
                        url: '<?php echo esc_url($ajax_url); ?>' + "?action=golo_place_img_upload_ajax&nonce=" + '<?php echo esc_attr($upload_nonce); ?>',
                        filters: {
                            mime_types: [
                                {title: '<?php esc_html_e('Valid file formats', 'golo-framework'); ?>', extensions: "jpg,jpeg,gif,png"}
                            ],
                            max_file_size: '<?php echo esc_html($image_max_file_size); ?>',
                            prevent_duplicates: false
                        }
                    });
                    uploader_menu_image.init();

                    uploader_menu_image.bind('UploadProgress', function (up, file) {
                        document.getElementById(id_browse_button).innerHTML = '<span><i class="la la-circle-notch la-spin large"></i></span>';
                    });

                    uploader_menu_image.bind('FilesAdded', function (up, files) {
                        var maxfiles = 1;
                        up.refresh();
                        uploader_menu_image.start();
                    });
                    uploader_menu_image.bind('Error', function (up, err) {
                        document.getElementById(errors).innerHTML += "Error #" + err.code + ": " + err.message + "<br/>";
                    });
                    uploader_menu_image.bind('FileUploaded', function (up, file, ajax_response) {
                        document.getElementById(id_browse_button).innerHTML = '<i class="la la-upload large"></i>';
                        var response = $.parseJSON(ajax_response.response);
                        if (response.success) {
                            wrap.find('.menu_image_url').val(response.full_image);
                            wrap.find('.menu_image_id').val(response.attachment_id);
                            var $html = 
                                '<figure class="media-thumb media-thumb-wrap">' +
                                '<img src="' + response.full_image + '">' +
                                '<div class="media-item-actions">' +
                                '<a class="icon icon-delete" data-place-id="0"  data-attachment-id="' + response.attachment_id + '" href="#" ><i class="la la-trash-alt large"></i></a>' +
                                '<span style="display: none;" class="icon icon-loader"><i class="la la-circle-notch la-spin large"></i></span>' +
                                '</div>' +
                                '</figure>';
                            wrap.find('.golo_menu_menu_image_view').html($html);
                            golo_place_gallery_event('thumb');

                            $('#menu_image_url-error').hide();

                        }
                        $( '.add-menu' ).removeClass( 'disabled' );
                    });
                    
                });
            }

            add_image();

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

            $( '.add-menu' ).on( 'click', function(e) {
                e.preventDefault();
                $( '.errors-log' ).text( '' );
                $( '.add-menu' ).addClass( 'disabled' );
                var clone = $( '.field-menu-clone' ).html();
                $( '.add-menu-list' ).append(clone);
                $( '.clone-wrap' ).each( function(index) {
                    index += 1;
                    var _this = $( this );
                    var button_id = _this.find( '.golo-add-image' ).attr( 'id' );
                    $( this ).find( '.golo-add-image' ).attr( 'id', button_id + index );
                });
                $( '.add-menu-list .clone-wrap' ).last().find('.golo_menu_menu_image_view').empty();
                add_image();
                $( '.add-menu-list .clone-wrap:last-child' ).find( '#menu-name' ).val('');
                $( '.add-menu-list .clone-wrap:last-child' ).find( '#menu-price' ).val('');
                $( '.add-menu-list .clone-wrap:last-child' ).find( '#item_desc' ).val('');
            });
            $(".add-menu-list").bind("DOMSubtreeModified", function() {
                $('.remove-menu').on('click', function (e){
                    e.preventDefault();
                    $(this).parents( '.clone-wrap' ).remove();
                });

            });
        });
    })(jQuery);
</script>