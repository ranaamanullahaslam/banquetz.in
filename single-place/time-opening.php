<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$opening_monday         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_monday', true);
$opening_monday_time    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_monday_time', true);
$opening_tuesday        = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_tuesday', true);
$opening_tuesday_time   = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_tuesday_time', true);
$opening_wednesday      = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_wednesday', true);
$opening_wednesday_time = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_wednesday_time', true);
$opening_thursday       = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_thursday', true);
$opening_thursday_time  = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_thursday_time', true);
$opening_friday         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_friday', true);
$opening_friday_time    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_friday_time', true);
$opening_saturday       = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_saturday', true);
$opening_saturday_time  = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_saturday_time', true);
$opening_sunday         = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_sunday', true);
$opening_sunday_time    = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_sunday_time', true);

$arr_monday_time    = explode('-', $opening_monday_time);
$arr_tuesday_time   = explode('-', $opening_tuesday_time);
$arr_wednesday_time = explode('-', $opening_wednesday_time);
$arr_thursday_time  = explode('-', $opening_thursday_time);
$arr_friday_time    = explode('-', $opening_friday_time);
$arr_saturday_time  = explode('-', $opening_saturday_time);
$arr_sunday_time    = explode('-', $opening_sunday_time);

$storeSchedule = [
    'Mon' => $opening_monday_time,
    'Tue' => $opening_tuesday_time,
    'Wed' => $opening_wednesday_time,
    'Thu' => $opening_thursday_time,
    'Fri' => $opening_friday_time,
    'Sat' => $opening_saturday_time,
    'Sun' => $opening_sunday_time
];

$place_timezone = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_timezone', true);        
$tzstring = convert_place_timezone($place_timezone);
$dt = new DateTime("now", new DateTimeZone($tzstring) );
$timestamp = strtotime($dt->format("Y-m-d H:i:s"));
$current_day = date('D', $timestamp);

$status = golo_status_time_place($storeSchedule);

?>

<?php if( $opening_monday_time || $opening_tuesday_time || $opening_wednesday_time || $opening_thursday_time || $opening_friday_time || $opening_saturday_time || $opening_sunday_time ) : ?>
<div class="place-time-opening place-area">
	<div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('Opening Hours', 'golo-framework'); ?></h3>
    </div>
    <div class="entry-detail">

        <?php if( $opening_monday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_monday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_monday_time) ?>
                <?php if( $current_day == 'Mon' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if( $opening_tuesday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_tuesday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_tuesday_time) ?>
                <?php if( $current_day == 'Tue' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if( $opening_wednesday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_wednesday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_wednesday_time) ?>
                <?php if( $current_day == 'Wed' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if( $opening_thursday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_thursday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_thursday_time) ?>
                <?php if( $current_day == 'Thu' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if( $opening_friday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_friday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_friday_time) ?>
                <?php if( $current_day == 'Fri' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if( $opening_saturday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_saturday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_saturday_time) ?>
                <?php if( $current_day == 'Sat' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if( $opening_sunday_time ) : ?>
        <div class="block-detail">
            <span><?php echo esc_html($opening_sunday); ?>:</span>
            <span class="open-time">
                <?php echo esc_html($opening_sunday_time) ?>
                <?php if( $current_day == 'Sun' ) : ?>
                    <?php echo wp_kses_post($status); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>