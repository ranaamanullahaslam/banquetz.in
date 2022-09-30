<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
$enable_time_format_24 = golo_get_option('enable_time_format_24', 0);
if ($enable_time_format_24 == 1) {
    $time_default = __( '9:00 - 17:00 OR 9:00 - 11:00 & 14:00 - 17:00', 'golo-framework' );
} else {
    $time_default = __( '9:00 AM - 5:00 PM OR 9:00 AM - 11:00 AM & 2:00 PM - 5:00 PM', 'golo-framework' );
}
?>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_monday" value="<?php esc_attr_e('Monday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_monday_time" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_tuesday" value="<?php esc_attr_e('Tuesday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_tuesday_time" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_wednesday" value="<?php esc_attr_e('Wednesday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_wednesday_time" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_thursday" value="<?php esc_attr_e('Thursday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_thursday_time" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_friday" value="<?php esc_attr_e('Friday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_friday_time" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_saturday" value="<?php esc_attr_e('Saturday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_saturday_time" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_sunday" value="<?php esc_attr_e('Sunday', 'golo-framework'); ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_sunday_time" placeholder="<?php esc_attr_e('Closed', 'golo-framework'); ?>" />
            </div>
        </div>
    </div>
</div>