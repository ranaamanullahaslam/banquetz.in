<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$place = '';
?>

<div class="place-submitted-content">
    <div class="golo-message alert alert-success">
        <?php
        switch ($place->post_status) :
            case 'publish' :
                if($action=='new')
                {
                    printf(wp_kses_post(__('<strong>Success!</strong> Your place was submitted successfully. To view your place listing <a class="accent-color" href="%s">click here</a>.', 'golo-framework')), get_permalink($place->ID));
                }
                else
                {
                    printf(wp_kses_post(__('<strong>Success!</strong> Your changes have been saved. To view your place listing <a class="accent-color" href="%s">click here</a>.', 'golo-framework')), get_permalink($place->ID));
                }
                break;
            case 'pending' :
                if($action=='new')
                {
                    printf(wp_kses_post(__('<strong>Success!</strong> Your place was submitted successfully. Once approved, your listing will be visible on the site.', 'golo-framework')), get_permalink($place->ID));
                }
                else{
                    echo  wp_kses_post(__('<strong>Success!</strong> Your changes have been saved. Once approved, your listing will be visible on the site.', 'golo-framework'));
                }
                break;
            default :
                do_action('golo_place_submitted_content_' . str_replace('-', '_', sanitize_title($place->post_status)), $place);
                break;
        endswitch;
        ?> 
    </div>
</div>