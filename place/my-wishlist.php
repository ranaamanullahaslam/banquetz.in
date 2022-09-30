<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ( !is_user_logged_in() ) {
    golo_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

global $current_user;
wp_get_current_user();
$user_id = $current_user->ID;
$my_wishlist             = get_user_meta($user_id, GOLO_METABOX_PREFIX . 'place_whishlist', true);
$posts_per_page          = golo_get_option('my_wishlist_total_post', '8');
$custom_place_image_size = golo_get_option('archive_place_image_size', '540x480' );
if(empty($my_wishlist))
{
    $my_wishlist = array(0);
}
$args = array(
    'post_type'           => 'place',
    'post__in'            => $my_wishlist,
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => $posts_per_page,
    'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
$wishlist = new WP_Query($args);

$archive_class   = array();
$archive_class[] = 'archive-place';
$archive_class[] = 'grid';
$archive_class[] = 'columns-4 columns-md-3 columns-sm-2 columns-xs-1';

?>

<div class="golo-my-wishlist area-main-control">
    <div class="container">
        <div class="entry-my-wishlist entry-my-page">

            <?php 
                $user_name = $current_user->display_name;
                $user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
                $package_title = '';
                if( $user_package_id ) {
                    $package_title = get_the_title($user_package_id);
                }
                $paid_submission_type = golo_get_option('paid_submission_type', 'no');

                $golo_package = new Golo_Package();
                $get_expired_date = $golo_package->get_expired_date($user_package_id, $user_id);
                $current_date = date('Y-m-d');

                $d1 = strtotime( $get_expired_date );
                $d2 = strtotime( $current_date );

                if ($get_expired_date === 'Never Expires' || $get_expired_date === 'Unlimited') {
                    $d1 = 999999999999999999999999;
                }

            ?>
            <div class="heading-page">
                <h2 class="entry-title"><?php esc_html_e('Wishlist', 'golo-framework'); ?></h2>
                
                <?php if ($paid_submission_type == 'per_package' && !in_array( 'customer', (array) $current_user->roles )) { ?>
                <div class="entry-alert">
                    <span>
                        <?php if( $package_title && $d1 > $d2 ) { ?>
                            <?php echo sprintf( __( 'You are currently "%s" package.', 'golo-framework' ), '<strong>' . $package_title . '</strong>'); ?>
                        <?php }else{ ?>
                            <?php esc_html_e('Buy a package to add your place now.', 'golo-framework'); ?>
                        <?php } ?>
                    </span>

                    <a class="accent-color" href="<?php echo golo_get_permalink('packages'); ?>"><?php esc_html_e('Upgrade now', 'golo-framework'); ?></a>
                </div>
                <?php } ?>
            </div>
            
            <div class="<?php echo join(' ', $archive_class); ?>">

                <?php if ( $wishlist->have_posts() ) { ?>

                    <?php while ( $wishlist->have_posts() ) : $wishlist->the_post(); ?>

                        <?php golo_get_template('content-place.php', array(
                            'custom_place_image_size' => $custom_place_image_size
                        )); ?>

                    <?php endwhile; ?>

                <?php } else { ?>

                    <div class="item-not-found"><?php esc_html_e('No item found', 'golo-framework'); ?></div>

                <?php } ?>

            </div>

            <?php
                $max_num_pages = $wishlist->max_num_pages;
                golo_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'normal', 'layout' => 'number'));
                wp_reset_postdata();
            ?>
            
        </div>
    </div>
</div>