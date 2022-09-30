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
?>

<div class="field-clone field-faqs-clone">

    <?php 
        $place_faqs_tab = get_post_meta( $place_data->ID,GOLO_METABOX_PREFIX. 'faqs_tab', false );
            $faqs_tab  = (isset($place_faqs_tab) && is_array($place_faqs_tab) && count( $place_faqs_tab ) > 0)? $place_faqs_tab[0]: '';
            if( !empty($faqs_tab)) {
                $i = 0;
                foreach ($faqs_tab as $faqs) {
                if ($i == 0) {
    ?>

    <div class="clone-wrap">

        <div class="place-fields-wrap">
            <div class="place-fields-title">
                <h3><?php esc_html_e('Question', 'golo-framework'); ?></h3>
            </div>
            <div class="place-fields place-title">
                <div class="form-group">
                    <input type="text" class="form-control" id="faqs-title" name="faqs_title[]" placeholder="<?php esc_attr_e('Question', 'golo-framework'); ?>" value="<?php echo $faqs[ GOLO_METABOX_PREFIX . 'faqs_title' ]; ?>" autofill="off" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="place-fields-wrap">
            <div class="place-fields-title">
                <h3><?php esc_html_e('Answer', 'golo-framework'); ?></h3>
            </div>
            <div class="place-fields place-title">
                <div class="form-group">
                    <textarea name="faqs_desc[]" class="form-control" rows="3" id="item_desc"  placeholder="<?php esc_attr_e('Answer', 'golo-framework'); ?>"><?php echo $faqs[ GOLO_METABOX_PREFIX . 'faqs_description' ]; ?></textarea>
                </div>
            </div>
        </div>

        <div class="faqs-flex">

            <a href="#" class="remove-faqs">
                <i class="la la-trash-alt"></i>
            </a>

        </div>

    </div>

    <?php } $i++; } } ?>

</div>

<div class="add-faqs-list">
    <?php 
        if( !empty($faqs_tab)) {
            $i = 0;
            foreach ($faqs_tab as $faqs) {
            if ($i > 0) {
    ?>
    <div class="clone-wrap">

        <div class="place-fields-wrap">
            <div class="place-fields-title">
                <h3><?php esc_html_e('Question', 'golo-framework'); ?></h3>
            </div>
            <div class="place-fields place-title">
                <div class="form-group">
                    <input type="text" class="form-control" id="faqs-title" name="faqs_title[]" placeholder="<?php esc_attr_e('Question', 'golo-framework'); ?>" value="<?php echo $faqs[ GOLO_METABOX_PREFIX . 'faqs_title' ]; ?>" autofill="off" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="place-fields-wrap">
            <div class="place-fields-title">
                <h3><?php esc_html_e('Answer', 'golo-framework'); ?></h3>
            </div>
            <div class="place-fields place-title">
                <div class="form-group">
                    <textarea name="faqs_desc[]" class="form-control" rows="3" id="item_desc"  placeholder="<?php esc_attr_e('Answer', 'golo-framework'); ?>"><?php echo $faqs[ GOLO_METABOX_PREFIX . 'faqs_description' ]; ?></textarea>
                </div>
            </div>
        </div>

        <div class="faqs-flex">

            <a href="#" class="remove-faqs">
                <i class="la la-trash-alt"></i>
            </a>

        </div>

    </div>
    <?php } $i++; } } ?>
</div>
<a href="#addfaqs" class="add-faqs btn disabled">               
    <i class="la la-plus"></i>
    <span><?php esc_html_e('Add more', 'golo-framework'); ?></span>
</a>

<script>
    (function($) {
    "use strict";
        jQuery(document).ready(function () {

            $( '.add-faqs' ).on( 'click', function(e) {
                e.preventDefault();
                $( '.errors-log' ).text( '' );
                $( '.add-faqs' ).addClass( 'disabled' );
                var clone = $( '.field-faqs-clone' ).html();
                $( '.add-faqs-list' ).append(clone);
                $( '.clone-wrap' ).each( function(index) {
                    index += 1;
                    var _this = $( this );
                    var button_id = _this.find( '.golo-add-image' ).attr( 'id' );
                    $( this ).find( '.golo-add-image' ).attr( 'id', button_id + index );
                });
                $( '.add-faqs-list .clone-wrap:last-child' ).find( '.icon-delete' ).trigger( 'click' );
            });
            $('.remove-faqs').on('click', function (e){
                e.preventDefault();
                $(this).parents( '.clone-wrap' ).remove();
            });
            $(".add-faqs-list").bind("DOMSubtreeModified", function() {
                $('.remove-faqs').on('click', function (e){
                    e.preventDefault();
                    $(this).parents( '.clone-wrap' ).remove();
                });
            });
        });
    })(jQuery);
</script>