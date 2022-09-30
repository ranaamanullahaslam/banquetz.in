<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $hide_place_fields;
?>

<?php if (!in_array('facebook', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Facebook', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-facebook">
        <div class="form-group">
            <input type="text" class="form-control" name="place_facebook" placeholder="<?php esc_attr_e('Facebook URL', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('instagram', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Instagram', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-instagram">
        <div class="form-group">
            <input type="text" class="form-control" name="place_instagram" placeholder="<?php esc_attr_e('Instagram URL', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('twitter', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Twitter', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-instagram">
        <div class="form-group">
            <input type="text" class="form-control" name="place_twitter" placeholder="<?php esc_attr_e('Twitter URL', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>