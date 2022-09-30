<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$additional_fields = golo_render_additional_fields();
$golo_my_places_page_id  = Golo_Helper::golo_get_option('golo_my_places_page_id', 0);
wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'frontend');
wp_enqueue_script('jquery-validate');
wp_localize_script(GOLO_PLUGIN_PREFIX . 'frontend', 'golo_submit_vars',
    array(
        'ajax_url'  => GOLO_AJAX_URL,
        'not_found' => esc_html__("We didn't find any results, you can retry with other keyword.", 'golo-framework'),
        'not_place' => esc_html__('No place found', 'golo-framework'),
        'my_places' => get_page_link( $golo_my_places_page_id ),
        'additional_fields' => $additional_fields,
    )
);

global $current_user,$hide_place_fields, $hide_place_group_fields;
wp_get_current_user();
$user_id = $current_user->ID;
$paid_submission_type = golo_get_option('paid_submission_type','no');

$hide_place_fields = golo_get_option('hide_place_fields', array());
if (!is_array($hide_place_fields)) {
    $hide_place_fields = array();
}

$hide_place_group_fields = golo_get_option('hide_place_group_fields', array());
if (!is_array($hide_place_group_fields)) {
    $hide_place_group_fields = array();
}

global $current_user;
wp_get_current_user();
$user_id = $current_user->ID;
$paid_submission_type = golo_get_option('paid_submission_type', 'no');
$user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
$package_available_listings = get_user_meta($user_id, GOLO_METABOX_PREFIX . 'package_number_listings', true);
$package_unlimited_listing = get_post_meta($user_package_id, GOLO_METABOX_PREFIX . 'package_unlimited_listing', true);

$classes = $notice_text = $shortcode = ''; 

$golo_package = new Golo_Package();
$get_expired_date = $golo_package->get_expired_date($user_package_id, $user_id);
$current_date = date('Y-m-d');

$d1 = strtotime( $get_expired_date );
$d2 = strtotime( $current_date );

if ($get_expired_date === 'Never Expires' || $get_expired_date === 'Unlimited') {
    $d1 = 999999999999999999999999;
}

if ($paid_submission_type == 'no') {
    if (in_array( 'customer', (array) $current_user->roles )) {
        $classes = 'no-steps'; 
        $notice_text = esc_html__("Sorry, you can't view this page as Guest, register Owner account to get access.", 'golo-framework');
    }
} else {
    if (in_array( 'customer', (array) $current_user->roles )) {
        $classes = 'no-steps'; 
        $notice_text = esc_html__("Sorry, you can't view this page as Guest, register Owner account to get access.", 'golo-framework');
    } elseif ((in_array( 'subscriber', (array) $current_user->roles ) && $user_package_id == '') || $d1 < $d2) {
        $classes = 'no-steps'; 
        $notice_text = esc_html__("You have not purchased the package. Buy a package to add your location now.", 'golo-framework');
        $shortcode = '1';
    } elseif (in_array( 'subscriber', (array) $current_user->roles ) && $package_available_listings <= 0 && $package_unlimited_listing != '1') {
        $classes = 'no-steps'; 
        $notice_text = esc_html__("You have reached your location limit. Please come back later!", 'golo-framework');
    }
}

?>

<section class="golo-place-multi-step <?php echo $classes; ?>">
    <?php
    $layout = golo_get_option('place_form_sections', array('general', 'hightlights', 'menu', 'location', 'contact', 'additional', 'socials', 'time-opening', 'media', 'faqs', 'booking') );
    unset($layout['sort_order']);
    $keys   = array_keys($layout);
    $total  = count($keys);
    $form   = 'submit-place';
    $action = 'add_place';
    ?>
    <div class="golo-steps">
        <div class="listing-menu nav-scroll">
            <ul>
            <?php
            $i = 0;
            $step_name = '';
            foreach ($layout as $value):
                $i++;
                switch ($value) {
                    case 'general':
                        $icon      = '<i class="la la-cog"></i>';
                        $step_name = esc_html__('General', 'golo-framework');
                        break;
                    case 'hightlights':
                        $icon      = '<i class="la la-wifi"></i>';
                        $step_name = esc_html__('Hightlights', 'golo-framework');
                        break;
                    case 'menu':
                        $icon      = '<i class="las la-bars"></i>';
                        $step_name = esc_html__('Menu', 'golo-framework');
                        break;
                    case 'location':
                        $icon      = '<i class="la la-map-marker"></i>';
                        $step_name = esc_html__('Location', 'golo-framework');
                        break;
                    case 'contact':
                        $icon      = '<i class="la la-phone"></i>';
                        $step_name = esc_html__('Contact info', 'golo-framework');
                        break;
                    case 'additional':
                        $icon      = '<i class="la la-edit"></i>';
                        $step_name = esc_html__('Additional Fields', 'golo-framework');
                        break;
                    case 'socials':
                        $icon      = '<i class="la la-link"></i>';
                        $step_name = esc_html__('Social networks', 'golo-framework');
                        break;
                    case 'time-opening':
                        $icon      = '<i class="la la-business-time"></i>';
                        $step_name = esc_html__('Opening hours', 'golo-framework');
                        break;
                    case 'media':
                        $icon      = '<i class="la la-image"></i>';
                        $step_name = esc_html__('Media', 'golo-framework');
                        break;
                    case 'faqs':
                        $icon      = '<i class="las la-question-circle"></i>';
                        $step_name = esc_html__('FAQs', 'golo-framework');
                        break;
                    case 'booking':
                        $icon      = '<i class="la la-calendar-check"></i>';
                        $step_name = esc_html__('Booking Type', 'golo-framework');
                        break;
                }
                ?>

                <?php if (!in_array($value, $hide_place_group_fields)) : ?>
                <li <?php if( $i == 1 ) : echo 'class="active"';endif; ?>>
                    <a href="#<?php echo esc_attr($value); ?>" title="<?php echo esc_attr($step_name); ?>">
                        <span class="icon"><?php echo wp_kses_post($icon); ?></span>
                        <span><?php echo esc_html($step_name); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                
            <?php endforeach;?>
            </ul>
        </div>
    </div>
    
    <div class="golo-steps-content">
        <h2><?php esc_html_e('Add new place', 'golo-framework'); ?></h2>

        <?php if (!empty($notice_text)) { ?>
            <p class="notice"><i class="la la-exclamation-circle"></i><?php echo $notice_text; ?></p>
            <?php
                if ($shortcode == '1') {
                    echo do_shortcode('[golo_packages]');
                }
            ?>
        <?php } else { ?>

        <form action="#" method="post" id="submit_place_form" class="place-manager-form" enctype="multipart/form-data" data-titleerror="<?php echo esc_html__('Please enter place name', 'golo-framework'); ?>" data-deserror="<?php echo esc_html__('Please enter place description', 'golo-framework'); ?>" data-caterror="<?php echo esc_html__('Please choosen category', 'golo-framework'); ?>" data-typeerror="<?php echo esc_html__('Please choosen type', 'golo-framework'); ?>" data-maperror="<?php echo esc_html__('Please enter place address', 'golo-framework'); ?>" data-imgerror="<?php echo esc_html__('Please upload featured image', 'golo-framework'); ?>">
            <?php
            foreach ($layout as $value) {
                $index = array_search($value,$keys);
                $prev_key = $next_key = $step_name = '';
                switch ($value) {
                    case 'general':
                        $step_name = esc_html__('General', 'golo-framework');
                        break;
                    case 'hightlights':
                        $step_name = esc_html__('Hightlights', 'golo-framework');
                        break;
                    case 'menu':
                        $step_name = esc_html__('Menu', 'golo-framework');
                        break;
                    case 'location':
                        $step_name = esc_html__('Location', 'golo-framework');
                        break;
                    case 'contact':
                        $step_name = esc_html__('Contact info', 'golo-framework');
                        break;
                    case 'additional':
                        $step_name = esc_html__('Additional fields', 'golo-framework');
                        break;
                    case 'socials':
                        $step_name = esc_html__('Social networks', 'golo-framework');
                        break;
                    case 'time-opening':
                        $step_name = esc_html__('Opening hours', 'golo-framework');
                        break;
                    case 'details':
                        $step_name = esc_html__('Additional details', 'golo-framework');
                        break;
                    case 'media':
                        $step_name = esc_html__('Media', 'golo-framework');
                        break;
                    case 'faqs':
                        $step_name = esc_html__('FAQs', 'golo-framework');
                        break;
                    case 'booking':
                        $step_name = esc_html__('Booking Type ?', 'golo-framework');
                        break;
                    case 'private_note':
                        $step_name = esc_html__('Private note', 'golo-framework');
                        break;
                }
                if( $index > 0 )
                {
                    $prev_key = $keys[$index - 1];
                }
                if( $index < $total - 1 ){
                    $next_key = $keys[$index + 1];
                }
                ?>

                <?php if (!in_array($value, $hide_place_group_fields)) : ?>
                <div class="group-field" id="<?php echo esc_attr($value); ?>">
                    <h3><?php echo esc_html($step_name); ?></h3>
                    <?php golo_get_template('place/submit-place/' . $value . '.php'); ?>
                </div>
                <?php endif; ?>

            <?php } ?>
            
            <?php if ( !is_user_logged_in() ) { ?>
                <?php $enable_login_to_submit = golo_get_option('enable_login_to_submit', '1'); ?>
                <?php if( $enable_login_to_submit == '1' ) { ?>
                    <div class="btn-submit-place golo-button account logged-out">
                        <a href="#popup-form" class="btn-login"><?php esc_html_e('Login to Submit','golo-framework'); ?></a>
                    </div>
                <?php }else{ ?>
                    <button type="submit" class="button btn-submit-place gl-button" name="submit_place">
                        <span><?php esc_html_e('Submit', 'golo-framework'); ?></span>
                        <span class="btn-loading"><i class="la la-circle-notch la-spin large"></i></span>
                    </button>
                <?php } ?>

            <?php }else{ ?>
                
                <?php 
                    $has_package = true;
                    if ($paid_submission_type == 'per_package') {
                        $current_package_key = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_key', $user_id);
                        $place_package_key = get_post_meta($user_id, GOLO_METABOX_PREFIX . 'package_key', true);
                        $golo_profile = new Golo_Profile();
                        $check_package = $golo_profile->user_package_available($user_id);
                        if( ($check_package == -1) || ($check_package == 0) )
                        {
                            $has_package = false;
                        }
                    }
                ?>
                <?php if( $has_package ) { ?>
                    <button type="submit" class="button btn-submit-place gl-button" name="submit_place">
                        <span><?php esc_html_e('Submit', 'golo-framework'); ?></span>
                        <span class="btn-loading"><i class="la la-circle-notch la-spin large"></i></span>
                    </button>
                <?php }else{ ?>
                    <div class="package-out-stock">
                        <span><?php esc_html_e('Upgrade package to add place!', 'golo-framework'); ?></span>
                        <div class="golo-button">
                            <a href="<?php echo golo_get_permalink('packages'); ?>"><?php esc_html_e('Upgrade now', 'golo-framework'); ?></a>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php wp_nonce_field('golo_submit_place_action', 'golo_submit_place_nonce_field'); ?>

            <input type="hidden" name="place_form" value="<?php echo esc_attr($form); ?>"/>
            <input type="hidden" name="place_action" value="<?php echo esc_attr($action) ?>"/>
            <input type="hidden" name="place_id" value="<?php echo esc_attr($place_id); ?>"/>
        </form>

        <?php
            }
        ?>
    </div>
</section>