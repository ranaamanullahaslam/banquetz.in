<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $hide_place_fields;

?>

<?php if (!in_array('place_name', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Place Name*', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-title">
        <div class="form-group">
            <input type="text" id="place_title" class="form-control" name="place_title" placeholder="<?php esc_attr_e('What the name of place', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('place_price', $hide_place_fields) || !in_array('place_price_unit', $hide_place_fields) || !in_array('place_price_ranger', $hide_place_fields) ) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Price', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-price row">
        <?php if (!in_array('place_price', $hide_place_fields)) : ?>
        <div class="col-sm-4">
            <div class="form-group">
                <input type="number" id="place_price_short" class="form-control" name="place_price_short" value="" placeholder="<?php esc_attr_e('Only Numbers', 'golo-framework'); ?>">
            </div>
        </div>
        <?php endif; ?>

        <?php if (!in_array('place_price_unit', $hide_place_fields)) : ?>
        <div class="col-sm-4">
            <div class="form-group">
                <select name="place_price_unit" class="form-control nice-select wide">
                    <option value="h"><?php esc_html_e('Hour', 'golo-framework');?></option>
                    <option value="plate"><?php esc_html_e('Per Plate', 'golo-framework');?></option>
                    <option value="m"><?php esc_html_e('Month', 'golo-framework');?></option>
                </select>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!in_array('place_price_ranger', $hide_place_fields)) : ?>
        <?php 
        $currency_sign = golo_get_option('currency_sign', '$');
        $low_price     = golo_get_option('low_price', '$');
        $medium_price  = golo_get_option('medium_price', '$$');
        $high_price    = golo_get_option('high_price', '$$$');
        ?>
        <div class="col-sm-4">
            <div class="form-group">
                <select name="place_price_range" class="form-control nice-select wide">
                    <option value="0"><?php esc_html_e('None', 'golo-framework');?></option>
                    <option value="1"><?php esc_html_e('Free', 'golo-framework');?></option>
                    <option value="2"><?php echo esc_html($low_price); ?></option>
                    <option value="3"><?php echo esc_html($medium_price); ?></option>
                    <option value="4"><?php echo esc_html($high_price); ?></option>
                </select>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('place_des', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Description', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-description">
        <div class="form-group">
            <?php
            $content   = '';
            $editor_id = 'place_des';
            $settings  = array(
                'wpautop'       => true,
                'media_buttons' => false,
                'textarea_name' => $editor_id,
                'textarea_rows' => get_option('default_post_edit_rows', 6),
                'tabindex'      => '',
                'editor_css'    => '',
                'editor_class'  => '',
                'teeny'         => false,
                'dfw'           => false,
                'tinymce'       => true,
                'quicktags'     => true
            );
            wp_editor($content, $editor_id, $settings); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('place_category', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Category*', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-categories">
        <div class="form-group form-select">
            <select data-placeholder="<?php esc_attr_e('Select categories', 'golo-framework'); ?>" multiple="multiple" class="golo-select2 form-control" name="place_categories">
                <?php golo_get_taxonomy('place-categories', false, false); ?>
            </select>
            <i class="la la-angle-down small"></i>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('place_type', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Place type*', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-type">
        <div class="form-group form-select">
            <select data-placeholder="<?php esc_attr_e('Select type', 'golo-framework'); ?>" multiple="multiple" class="golo-select2 form-control" name="place_type">
                <?php golo_get_taxonomy('place-type', false, false); ?>
            </select>
            <i class="la la-angle-down small"></i>
        </div>
    </div>
</div>
<?php endif; ?>