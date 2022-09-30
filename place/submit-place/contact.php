<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $hide_place_fields;
?>

<?php if (!in_array('email', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Email', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-email">
        <div class="form-group">
            <input type="email" class="form-control" name="place_email" placeholder="<?php esc_attr_e('Your email address', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('phone', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Phone number 1 (optional)', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-phone">
        <div class="form-group">
            <input type="text" class="form-control" name="place_phone" placeholder="<?php esc_attr_e('Your phone 1 number', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Phone number 2 (optional)', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-phone">
        <div class="form-group">
            <input type="text" class="form-control" name="place_phone2" placeholder="<?php esc_attr_e('Your phone 2 number', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!in_array('website', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Website (optional)', 'golo-framework'); ?></h3>
    </div>
    <div class="place-fields place-website">
        <div class="form-group">
            <input type="text" class="form-control" name="place_website" placeholder="<?php esc_attr_e('Your website url', 'golo-framework'); ?>" />
        </div>
    </div>
</div>
<?php endif; ?>