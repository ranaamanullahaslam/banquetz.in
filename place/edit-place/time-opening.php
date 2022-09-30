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
global $place_data, $place_meta_data;
?>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_monday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_monday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_monday'][0] ); }else{ esc_attr_e('Monday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_monday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_monday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_monday_time'][0] ); } ?>" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_tuesday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_tuesday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_tuesday'][0] ); }else{ esc_attr_e('Tuesday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_tuesday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_tuesday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_tuesday_time'][0] ); } ?>" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_wednesday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_wednesday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_wednesday'][0] ); }else{ esc_attr_e('Wednesday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_wednesday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_wednesday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_wednesday_time'][0] ); } ?>" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_thursday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_thursday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_thursday'][0] ); }else{ esc_attr_e('Thursday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_thursday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_thursday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_thursday_time'][0] ); } ?>" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_friday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_friday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_friday'][0] ); }else{ esc_attr_e('Friday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_friday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_friday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_friday_time'][0] ); } ?>" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_saturday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_saturday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_saturday'][0] ); }else{ esc_attr_e('Saturday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_saturday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_saturday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_saturday_time'][0] ); } ?>" placeholder="<?php echo $time_default; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="place-fields-wrap">
    <div class="place-fields place-time-opening row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_sunday" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_sunday'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_sunday'][0] ); }else{ esc_attr_e('Sunday', 'golo-framework'); } ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="opening_sunday_time" value="<?php if( isset( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_sunday_time'] ) ) { echo esc_attr( $place_meta_data[GOLO_METABOX_PREFIX. 'opening_sunday_time'][0] ); } ?>" placeholder="<?php esc_attr_e('Closed', 'golo-framework'); ?>" />
            </div>
        </div>
    </div>
</div>