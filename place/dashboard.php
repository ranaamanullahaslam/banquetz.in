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
$user_id   = $current_user->ID;
$user_name = $current_user->display_name;
$user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
$package_title = '';
if( $user_package_id ) {
    $package_title = get_the_title($user_package_id);
}
$paid_submission_type = golo_get_option('paid_submission_type','no');

if( empty($user_name) ) {
    $user_name = $current_user->user_login;
}

$golo_package = new Golo_Package();
$get_expired_date = $golo_package->get_expired_date($user_package_id, $user_id);
$current_date = date('Y-m-d');

$d1 = strtotime( $get_expired_date );
$d2 = strtotime( $current_date );

if ($get_expired_date === 'Never Expires' || $get_expired_date === 'Unlimited') {
    $d1 = 999999999999999999999999;
}

?>

<div class="golo-dashboard area-main-control">
    <div class="container">
        <?php if ( !in_array( 'customer', (array) $current_user->roles ) ) { ?>
            <div class="entry-my-page">

                <div class="heading-page">
                    <h2 class="entry-title"><?php echo sprintf( __( 'Welcome back! %s', 'golo-framework' ), $user_name); ?></h2>
                    
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
                
                <?php if ( ($paid_submission_type !== 'no' && !in_array( 'customer', (array) $current_user->roles )) || ($d1 < $d2)) : ?>
                <div class="banner-alert">

                    <!-- <a class="btn-close" href="#"><i class="la la-times large"></i></a> -->
                    
                    <div class="entry-detail">
                        <h2><?php esc_html_e('Choose a plan to submit your place!', 'golo-framework'); ?></h2>

                        <div class="golo-button">
                            <a href="<?php echo golo_get_permalink('packages'); ?>"><?php esc_html_e('Upgrade now', 'golo-framework'); ?></a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!in_array( 'customer', (array) $current_user->roles )) { ?>

                <div class="total-action">
                    <ul class="grid columns-4 columns-md-2">
                        <li class="places">
                            <div class="entry-detail">
                                <h3 class="entry-title"><?php esc_html_e('Actived Places', 'golo-framework'); ?></h3>
                                <span class="entry-number"><?php echo golo_total_actived_places(); ?></span>
                            </div>
                        </li>
                        <li class="bookings">
                            <div class="entry-detail">
                                <h3 class="entry-title"><?php esc_html_e('Bookings Made', 'golo-framework'); ?></h3>
                                <span class="entry-number"><?php echo golo_total_user_booking(); ?></span>
                            </div>
                        </li>
                        <li class="reviews">
                            <div class="entry-detail">
                                <h3 class="entry-title"><?php esc_html_e('Total Reviews', 'golo-framework'); ?></h3>
                                <span class="entry-number"><?php echo golo_total_user_review(); ?></span>
                            </div>
                        </li>
                        <li class="views">
                            <div class="entry-detail">
                                <h3 class="entry-title"><?php esc_html_e('Total Views', 'golo-framework'); ?></h3>
                                <span class="entry-number"><?php echo golo_total_view_places(); ?></span>
                            </div>
                        </li>
                    </ul>
                </div>

                <?php } ?>

                <div class="recent-action">
                    <div class="grid columns-3 columns-md-2 columns-sm-1">
                        <div class="entry-col recent-bookings">
                            <div class="inner-col">
                                <div class="entry-head">
                                    <h3 class="entry-title"><?php esc_html_e('Recent Bookings', 'golo-framework'); ?></h3>
                                </div>

                                <div class="entry-detail">
                                    
                                        <?php 
                                        $meta_query = array();

                                        $args_booking = array(
                                            'post_type'      => 'booking',
                                            'post_status'    => array('publish', 'pending', 'canceled'),
                                            'posts_per_page' => 10,
                                            'orderby'        => array(
                                                'menu_order' => 'ASC',
                                                'date'       => 'DESC',
                                            ),
                                        );

                                        $meta_query[] = array(
                                            'key'     => GOLO_METABOX_PREFIX. 'booking_item_author',
                                            'value'   => $user_id,
                                            'type'    => 'NUMERIC',
                                            'compare' => '=',
                                        );

                                        $args_booking['meta_query'] = array(
                                            'relation' => 'AND',
                                            $meta_query
                                        );

                                        $data_booking = new WP_Query($args_booking);
                                        $total_post = $data_booking->found_posts;

                                        if( $total_post > 0 ){

                                            ?>

                                            <ul class="listing-detail custom-scrollbar">

                                            <?php
                                            while ( $data_booking->have_posts() ) : $data_booking->the_post();

                                                $id      = get_the_ID();
                                                $status  = get_post_status($id);
                                                $item_id = get_post_meta($id, GOLO_METABOX_PREFIX . 'booking_item_id', true);

                                                $custom_status = $status;
                                                if( $status == 'publish' ) {
                                                    $custom_status = esc_html__('Approved', 'golo-framework');
                                                }

                                                if( $status == 'pending' ) {
                                                    $custom_status = esc_html__('Pending', 'golo-framework');
                                                }

                                                if( $status == 'canceled' ) {
                                                    $custom_status = esc_html__('Canceled', 'golo-framework');
                                                }
                                                ?>

                                                <li>
                                                    <table>
                                                        <tr>
                                                            <td class="name"><?php esc_html_e('Date:', 'golo-framework'); ?></td>
                                                            <td><?php echo get_the_time('M j, Y G:i'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="name"><?php esc_html_e('Place:', 'golo-framework'); ?></td>
                                                            <td><a href="<?php echo get_the_permalink($item_id); ?>"><?php echo get_the_title($item_id); ?></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="name"><?php esc_html_e('Status:', 'golo-framework'); ?></td>
                                                            <td><span class="status <?php echo esc_attr($status); ?>"><?php echo esc_html($custom_status); ?></span></td>
                                                        </tr>
                                                    </table>   
                                                </li>

                                                <?php

                                            endwhile;

                                            wp_reset_postdata();
                                            ?>
                                                
                                            </ul>
                                                
                                            <?php

                                        }else{

                                            ?>
                                                <span class="no-item"><?php esc_html_e('No recent bookings', 'golo-framework'); ?></span>
                                            <?php
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>

                        <div class="entry-col place-reviews">
                            <div class="inner-col">
                                <div class="entry-head">
                                    <h3 class="entry-title"><?php esc_html_e('New Reviews', 'golo-framework'); ?></h3>
                                </div>

                                <div class="entry-detail">
                                    <?php 
                                    // Rating
                                    global $wpdb;

                                    $rating = $total_reviews = $total_stars = 0;
                                    $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
                                    $get_comments   = $wpdb->get_results($comments_query);
                                    $my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
                                    if (!is_null($get_comments)) {
                                        foreach ($get_comments as $comment) {
                                            if ($comment->comment_approved == 1) {
                                                $total_reviews++;
                                                if( $comment->meta_value > 0 ){
                                                    $total_stars += $comment->meta_value;
                                                }
                                            }
                                        }

                                        if ($total_reviews != 0) {
                                            $rating = number_format($total_stars / $total_reviews, 1);
                                        }
                                    }
                                    ?>

                                    <?php 
                                    if (!is_null($get_comments)) { 
                                        $i = 0;

                                        $sort = array();
                                        foreach ($get_comments as $key => $comment) {
                                            $sort[$key] = strtotime($comment->comment_date);
                                        }
                                        array_multisort($sort, SORT_DESC, $get_comments);
                                    }

                                    $has_review = false;
                                    foreach ($get_comments as $comment) {
                                        $post_id                = $comment->comment_post_ID;
                                        $comment_user_id        = $comment->user_id;
                                        $post_author_id = get_post_field( 'post_author', $post_id );
                                        if( $comment_user_id == $user_id ) {
                                            $has_review = true;
                                        }
                                    }

                                    if( $has_review ) {
                                    ?>
                                    <ul class="listing-detail reviews-list custom-scrollbar">
                                        <?php 
                                            foreach ($get_comments as $comment) {
                                                $comment_id             = $comment->comment_ID;
                                                $post_id                = $comment->comment_post_ID;
                                                $comment_user_id        = $comment->user_id;
                                                $post_author_id = get_post_field( 'post_author', $post_id );
                                                $user_link      = get_author_posts_url($comment->user_id);

                                                $author_avatar_url       = get_avatar_url($comment->user_id, ['size' => '50']);
                                                $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $comment->user_id);
                                                if( !empty($author_avatar_image_url) ){
                                                    $author_avatar_url = $author_avatar_image_url;
                                                }

                                                if( $post_author_id == $user_id ) {
                                                    if ($i++ > 10) break;
                                                ?>
                                                    <li class="author-review">
                                                        <div class="entry-head">
                                                            <div class="entry-avatar">
                                                                <figure>
                                                                    <?php
                                                                    if (!empty($author_avatar_url)) {
                                                                        ?>
                                                                        <a href="<?php echo esc_url($user_link); ?>">
                                                                            <img src="<?php echo esc_url($author_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                                                         </a>
                                                                        <?php
                                                                    } else {
                                                                        ?>
                                                                        <a href="<?php echo esc_url($user_link); ?>">
                                                                            <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </figure>
                                                            </div>
                                                            <div class="entry-info">
                                                                <div class="entry-name">
                                                                    <h4 class="author-name"><a href="<?php echo esc_url($user_link); ?>"><?php the_author_meta('display_name', $comment->user_id); ?></a></h4>
                                                                    <?php if( $comment->meta_value > 0 ) : ?>
                                                                    <div class="author-rating">
                                                                        <span class="star <?php if( $comment->meta_value >= 1 ) : echo 'checked';endif; ?>">
                                                                            <i class="la la-star"></i>
                                                                        </span>
                                                                        <span class="star <?php if( $comment->meta_value >= 2 ) : echo 'checked';endif; ?>">
                                                                            <i class="la la-star"></i>
                                                                        </span>
                                                                        <span class="star <?php if( $comment->meta_value >= 3 ) : echo 'checked';endif; ?>">
                                                                            <i class="la la-star"></i>
                                                                        </span>
                                                                        <span class="star <?php if( $comment->meta_value >= 4 ) : echo 'checked';endif; ?>">
                                                                            <i class="la la-star"></i>
                                                                        </span>
                                                                        <span class="star <?php if( $comment->meta_value == 5 ) : echo 'checked';endif; ?>">
                                                                            <i class="la la-star"></i>
                                                                        </span>
                                                                    </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <span class="review-date"><?php echo esc_html($comment->comment_date); ?></span>
                                                            </div>
                                                        </div>

                                                        <div class="entry-comment">
                                                            <p class="review-content"><?php echo wp_trim_words($comment->comment_content, 20); ?></p>
                                                        </div>

                                                        <div class="entry-bottom">
                                                            <span><?php esc_html_e('Place:', 'golo-framework'); ?></span>
                                                            <a href="<?php echo get_the_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a>
                                                        </div>
                                                    </li>
                                                <?php
                                                }
                                            }
                                        ?>
                                    </ul>
                                    <?php }else{ ?>
                                        <span class="no-item"><?php esc_html_e('No recent reviews', 'golo-framework'); ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="entry-col recent-notification">
                            <div class="inner-col">
                                <div class="entry-head">
                                    <h3 class="entry-title"><?php esc_html_e('Notifications', 'golo-framework'); ?></h3>

                                    <a href="#" class="view-detail error-color"><?php esc_html_e('Clear All', 'golo-framework'); ?></a>
                                </div>

                                <div class="entry-detail">
                                    <ul class="entry-list-notifications listing-detail custom-scrollbar">
                                        <li><?php esc_html_e('Coming soon :)', 'golo-framework'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php } else { ?>

            <p><?php esc_html_e('You can not access this page!', 'golo-framework'); ?></p>

        <?php } ?>
    </div>
</div>