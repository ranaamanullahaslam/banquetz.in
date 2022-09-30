<?php
global $wpdb;

$rating = $total_reviews = $total_stars = 0;

$no_avatar_src = '';

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$place_id     = get_the_ID();
$place_rating = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_rating', true);


$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
$get_comments   = $wpdb->get_results($comments_query);

$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND comment.user_id = $user_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");

$my_review_place_service_rating       = '';
$my_review_place_money_rating         = '';
$my_review_place_location_rating      = '';
$my_review_place_cleanliness_rating   = '';

if (!is_null($get_comments)) {

    $place_service_rating       = array();
    $place_money_rating         = array();
    $place_location_rating      = array();
    $place_cleanliness_rating   = array();

    foreach ($get_comments as $comment) {

        if (intval(get_comment_meta($comment->comment_ID, 'place_service_rating', true)) != 0) {
            $place_service_rating[]         = intval(get_comment_meta($comment->comment_ID, 'place_service_rating', true));
        }

        if (intval(get_comment_meta($comment->comment_ID, 'place_money_rating', true)) != 0) {
            $place_money_rating[]         = intval(get_comment_meta($comment->comment_ID, 'place_money_rating', true));
        }

        if (intval(get_comment_meta($comment->comment_ID, 'place_location_rating', true)) != 0) {
            $place_location_rating[]         = intval(get_comment_meta($comment->comment_ID, 'place_location_rating', true));
        }

        if (intval(get_comment_meta($comment->comment_ID, 'place_cleanliness_rating', true)) != 0) {
            $place_cleanliness_rating[]         = intval(get_comment_meta($comment->comment_ID, 'place_cleanliness_rating', true));
        }

        if ($comment->comment_approved == 1) {
            if( !empty($comment->meta_value) && $comment->meta_value != 0.00 ){
                $total_reviews++;
            }
            if( $comment->meta_value > 0 ){
                $total_stars += $comment->meta_value;
            }
        }

        if ($comment->comment_ID === $my_review->comment_ID) {
            $my_review_place_service_rating = intval(get_comment_meta($comment->comment_ID, 'place_service_rating', true));
            $my_review_place_money_rating = intval(get_comment_meta($comment->comment_ID, 'place_money_rating', true));
            $my_review_place_location_rating = intval(get_comment_meta($comment->comment_ID, 'place_location_rating', true));
            $my_review_place_cleanliness_rating = intval(get_comment_meta($comment->comment_ID, 'place_cleanliness_rating', true));
        }

    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }

    if (!empty($place_service_rating)) {
        $service_rating = array_sum($place_service_rating) / count($place_service_rating);
        $service_rating = number_format((float)$service_rating, 2, '.', '');
        $service_rating_percent = ($service_rating / 5)*100;
        if ($service_rating_percent >= 0 && $service_rating_percent <= 30) {
            $service_rating_class = 'low';
        } else if ($service_rating_percent >= 31 && $service_rating_percent <= 70) {
            $service_rating_class = 'mid';
        } else if ($service_rating_percent >= 71 && $service_rating_percent <= 100) {
            $service_rating_class = 'high';
        }
    } else {
        $service_rating = 0;
        $service_rating_percent = 0;
    }

    if (!empty($place_money_rating)) {
        $money_rating = array_sum($place_money_rating) / count($place_money_rating);
        $money_rating = number_format((float)$money_rating, 2, '.', '');
        $money_rating_percent = ($money_rating / 5)*100;
        if ($money_rating_percent >= 0 && $money_rating_percent <= 30) {
            $money_rating_class = 'low';
        } else if ($money_rating_percent >= 31 && $money_rating_percent <= 70) {
            $money_rating_class = 'mid';
        } else if ($money_rating_percent >= 71 && $money_rating_percent <= 100) {
            $money_rating_class = 'high';
        }
    } else {
        $money_rating = 0;
        $money_rating_percent = 0;
    }

    if (!empty($place_location_rating)) {
        $location_rating = array_sum($place_location_rating) / count($place_location_rating);
        $location_rating = number_format((float)$location_rating, 2, '.', '');
        $location_rating_percent = ($location_rating / 5)*100;
        if ($location_rating_percent >= 0 && $location_rating_percent <= 30) {
            $location_rating_class = 'low';
        } else if ($location_rating_percent >= 31 && $location_rating_percent <= 70) {
            $location_rating_class = 'mid';
        } else if ($location_rating_percent >= 71 && $location_rating_percent <= 100) {
            $location_rating_class = 'high';
        }
    } else {
        $location_rating = 0;
        $location_rating_percent = 0;
    }

    if (!empty($place_cleanliness_rating)) {
        $cleanliness_rating = array_sum($place_cleanliness_rating) / count($place_location_rating);
        $cleanliness_rating = number_format((float)$cleanliness_rating, 2, '.', '');
        $cleanliness_rating_percent = ($cleanliness_rating / 5)*100;
        if ($cleanliness_rating_percent >= 0 && $cleanliness_rating_percent <= 30) {
            $cleanliness_rating_class = 'low';
        } else if ($cleanliness_rating_percent >= 31 && $cleanliness_rating_percent <= 70) {
            $cleanliness_rating_class = 'mid';
        } else if ($cleanliness_rating_percent >= 71 && $cleanliness_rating_percent <= 100) {
            $cleanliness_rating_class = 'high';
        }
    } else {
        $cleanliness_rating = 0;
        $cleanliness_rating_percent = 0;
    }

}



?>
<div class="place-reviews place-area">
    <div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('Review', 'golo-framework'); ?></h3>
        <span class="rating-count">
            <span><?php echo esc_html($rating); ?></span>
            <i class="la la-star medium"></i>
        </span>
        <span class="review-count"><?php printf(_n('Base on %s Review', 'Base on %s Reviews', $total_reviews, 'golo-framework'), $total_reviews); ?></span>
    </div>
    <div class="entry-overview">
        <div class="rating-bars">
            <div class="rating-bars-item">
                <span class="rating-bars-name">
                    <?php esc_html_e('Service', 'golo-framework'); ?>
                    <i class="tip" data-tip-content="<?php esc_html_e('Quality of customer service and attitude to work with you', 'golo-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Quality of customer service and attitude to work with you', 'golo-framework'); ?></div>
                    </i>
                </span>
                <span class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr( $service_rating_class ); ?>" data-rating="<?php echo esc_attr( $service_rating ); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr( $service_rating_percent ); ?>%;"></span>
                    </span>
                    <strong><?php echo esc_attr( $service_rating ); ?></strong>
                </span>
            </div>
            <div class="rating-bars-item">
                <span class="rating-bars-name">
                    <?php esc_html_e('Value for Money', 'golo-framework'); ?>
                    <i class="tip" data-tip-content="<?php esc_html_e('Overall experience received for the amount spent', 'golo-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Overall experience received for the amount spent', 'golo-framework'); ?></div>
                    </i>
                </span>
                <span class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr( $money_rating_class ); ?>" data-rating="<?php echo esc_attr( $money_rating ); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr( $money_rating_percent ); ?>%;"></span>
                    </span>
                    <strong><?php echo esc_attr( $money_rating ); ?></strong>
                </span>
            </div>
            <div class="rating-bars-item">
                <span class="rating-bars-name">
                    <?php esc_html_e('Location', 'golo-framework'); ?>
                    <i class="tip" data-tip-content="<?php esc_html_e('Visibility, commute or nearby parking spots', 'golo-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Visibility, commute or nearby parking spots', 'golo-framework'); ?></div>
                    </i>
                </span>
                <span class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr( $location_rating_class ); ?>" data-rating="<?php echo esc_attr( $location_rating ); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr( $location_rating_percent ); ?>%;"></span>
                    </span>
                    <strong><?php echo esc_attr( $location_rating ); ?></strong>
                </span>
            </div>
            <div class="rating-bars-item">
                <span class="rating-bars-name">
                    <?php esc_html_e('Cleanliness', 'golo-framework'); ?>
                    <i class="tip" data-tip-content="<?php esc_html_e('The physical condition of the business', 'golo-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('The physical condition of the business', 'golo-framework'); ?></div>
                    </i>
                </span>
                <span class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr( $cleanliness_rating_class ); ?>" data-rating="<?php echo esc_attr( $cleanliness_rating ); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr( $cleanliness_rating_percent ); ?>%;"></span>
                    </span>
                    <strong><?php echo esc_attr( $cleanliness_rating ); ?></strong>
                </span>
            </div>
        </div>
    </div> 
    <div class="entry-detail">
        <ul class="reviews-list">
            <?php if (!is_null($get_comments)) {
                foreach ($get_comments as $comment) {
                    $comment_id        = $comment->comment_ID;
                    $author_avatar_url = get_avatar_url($comment->user_id, ['size' => '50']);
                    $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $comment->user_id);
                    if( !empty($author_avatar_image_url) ){
                        $author_avatar_url = $author_avatar_image_url;
                    }
                    $user_link = get_author_posts_url($comment->user_id);

                    $comment_thumb = get_comment_meta($comment->comment_ID, 'comment_thumb', true);

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
                                <span class="review-date"><?php echo golo_get_comment_time($comment->comment_ID); ?></span>
                            </div>
                        </div>

                        <div class="entry-comment">
                            <p class="review-content"><?php echo wp_kses_post($comment->comment_content); ?></p>
                            <?php 
                                if ($comment_thumb) :
                            ?>
                                <ul>
                                    <?php
                                        foreach ($comment_thumb as $key => $value) :
                                        $image_attributes = wp_get_attachment_image_src( $value, 'full' );
                                    ?>
                                    <li><a href="<?php echo $image_attributes[0]; ?>" target="_Blank"><img src="<?php echo $image_attributes[0]; ?>" /></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        
                        <?php if( is_user_logged_in() ){ ?>
                        <div class="entry-nav">
                            <div class="reply">
                                <a href="#">                           
                                    <i class="la la-comment medium"></i>
                                    <span><?php esc_html_e('Reply', 'golo-framework'); ?></span>
                                </a>
                            </div>

                            <?php if ($comment->comment_approved == 0) { ?>
                                <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'golo-framework'); ?> </span>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        
                        <?php 
                            $args = array(
                                'status' => 'approve', 
                                'number' => '',
                                'order'  => 'ASC',
                                'parent' => $comment->comment_ID
                            );
                            $child_comments = get_comments($args);
                        ?>
                        <?php if($child_comments) : ?>
                        <ol class="children">
                            <?php foreach($child_comments as $child_comment) { ?>
                                <?php 
                                    $child_avatar_url       = get_avatar_url($child_comment->user_id, ['size' => '50']);
                                    $child_link             = get_author_posts_url($child_comment->user_id);
                                    $child_avatar_image_url = get_the_author_meta('author_avatar_image_url', $child_comment->user_id);
                                    if( isset($child_avatar_image_url) ){
                                        $child_avatar_url = $child_avatar_image_url;
                                    }
                                ?>
                                <li class="author-review">
                                    <div class="entry-head">
                                        <div class="entry-avatar">
                                            <figure>
                                                <?php
                                                if (!empty($child_avatar_url)) {
                                                    ?>
                                                    <a href="<?php echo esc_url($child_link); ?>">
                                                        <img src="<?php echo esc_url($child_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                                     </a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="<?php echo esc_url($child_link); ?>">
                                                        <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                                    <?php
                                                }
                                                ?>
                                            </figure>
                                        </div>
                                        <div class="entry-info">
                                            <div class="entry-name">
                                                <h4 class="author-name"><a href="<?php echo esc_url($child_link); ?>"><?php the_author_meta('display_name', $child_comment->user_id); ?></a></h4>
                                            </div>
                                            <span class="review-date"><?php echo golo_get_comment_time($child_comment->comment_ID); ?></span>
                                        </div>
                                    </div>

                                    <div class="entry-comment">
                                        <p class="review-content"><?php echo esc_html($child_comment->comment_content); ?></p>
                                    </div>
                                    
                                    <?php if ($child_comment->comment_approved == 0) { ?>
                                    <div class="entry-nav">
                                        <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'golo-framework'); ?> </span>
                                    </div>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ol>
                        <?php endif; ?>
                        
                        <div class="form-reply" data-id="<?php echo esc_attr($comment->comment_ID); ?>"></div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <div class="add-new-review">
            <?php
            if( !is_user_logged_in() ){
                ?>
                <div class="login-for-review account logged-out">
                    <a href="#popup-form" class="btn-login"><?php esc_html_e('Login', 'golo-framework'); ?></a>
                    <span><?php esc_html_e('to review', 'golo-framework'); ?></span>
                </div>
                <?php
            }else{
                ?>
                <h4 class="review-title"><?php esc_html_e('Write a Review', 'golo-framework'); ?></h4>
                <?php
                $current_user = wp_get_current_user();
                $user_name    = $current_user->display_name;
                $avatar_url   = get_avatar_url($current_user->ID);
                $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
                if( !empty($author_avatar_image_url) ){
                    $avatar_url = $author_avatar_image_url;
                }
                if (is_null($my_review)) {
                    ?>
                    <form method="post" class="reviewForm" enctype="multipart/form-data" action="#">
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Service', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('Quality of customer service and attitude to work with you', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Quality of customer service and attitude to work with you', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_service5" name="rating_service" value="5"/><label for="rating_service5" title="5 stars"></label>
                                <input type="radio" id="rating_service4" name="rating_service" value="4"/><label for="rating_service4" title="4 stars"></label>
                                <input type="radio" id="rating_service3" name="rating_service" value="3"/><label for="rating_service3" title="3 stars"></label>
                                <input type="radio" id="rating_service2" name="rating_service" value="2"/><label for="rating_service2" title="2 stars"></label>
                                <input type="radio" id="rating_service1" name="rating_service" value="1"/><label for="rating_service1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Value for Money', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('Overall experience received for the amount spent', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Overall experience received for the amount spent', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_money5" name="rating_money" value="5"/><label for="rating_money5" title="5 stars"></label>
                                <input type="radio" id="rating_money4" name="rating_money" value="4"/><label for="rating_money4" title="4 stars"></label>
                                <input type="radio" id="rating_money3" name="rating_money" value="3"/><label for="rating_money3" title="3 stars"></label>
                                <input type="radio" id="rating_money2" name="rating_money" value="2"/><label for="rating_money2" title="2 stars"></label>
                                <input type="radio" id="rating_money1" name="rating_money" value="1"/><label for="rating_money1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Location', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('Visibility, commute or nearby parking spots', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Visibility, commute or nearby parking spots', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_location5" name="rating_location" value="5"/><label for="rating_location5" title="5 stars"></label>
                                <input type="radio" id="rating_location4" name="rating_location" value="4"/><label for="rating_location4" title="4 stars"></label>
                                <input type="radio" id="rating_location3" name="rating_location" value="3"/><label for="rating_location3" title="3 stars"></label>
                                <input type="radio" id="rating_location2" name="rating_location" value="2"/><label for="rating_location2" title="2 stars"></label>
                                <input type="radio" id="rating_location1" name="rating_location" value="1"/><label for="rating_location1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Cleanliness', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('The physical condition of the business', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('The physical condition of the business', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_cleanliness5" name="rating_cleanliness" value="5"/><label for="rating_cleanliness5" title="5 stars"></label>
                                <input type="radio" id="rating_cleanliness4" name="rating_cleanliness" value="4"/><label for="rating_cleanliness4" title="4 stars"></label>
                                <input type="radio" id="rating_cleanliness3" name="rating_cleanliness" value="3"/><label for="rating_cleanliness3" title="3 stars"></label>
                                <input type="radio" id="rating_cleanliness2" name="rating_cleanliness" value="2"/><label for="rating_cleanliness2" title="2 stars"></label>
                                <input type="radio" id="rating_cleanliness1" name="rating_cleanliness" value="1"/><label for="rating_cleanliness1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group form-media">
                            <label for="file">
                                <input class="uploadImage" type="file" name="files[]" accept="image/*, application/pdf" id="file" multiple="">
                                <span class="name"><?php esc_attr_e('Add Photos', 'golo-framework'); ?></span>
                                <span class="fileList"></span>
                            </label>
                        </div>
                        <div class="form-group custom-area">
                            <textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'golo-framework'); ?>"></textarea>
                            <?php if( isset($avatar_url) ) : ?>
                            <div class="current-user-avatar">
                                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="golo-submit-place-rating btn-golo btn btn-default"><span><?php esc_html_e('Submit Review', 'golo-framework'); ?></span></button>
                        <?php wp_nonce_field('golo_submit_review_ajax_nonce', 'golo_security_submit_review'); ?>
                        <input type="hidden" name="action" value="golo_place_submit_review_ajax">
                        <input type="hidden" name="place_id" value="<?php the_ID(); ?>">
                    </form>
                    <?php
                } else {
                    ?>
                    <form method="post" class="reviewForm" enctype="multipart/form-data" action="#">
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Service', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('Quality of customer service and attitude to work with you', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Quality of customer service and attitude to work with you', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_service5" name="rating_service" <?php if ($my_review_place_service_rating === 5) { echo 'checked'; } ?> value="5"/><label for="rating_service5" title="5 stars"></label>
                                <input type="radio" id="rating_service4" name="rating_service" <?php if ($my_review_place_service_rating === 4) { echo 'checked'; } ?> value="4"/><label for="rating_service4" title="4 stars"></label>
                                <input type="radio" id="rating_service3" name="rating_service" <?php if ($my_review_place_service_rating === 3) { echo 'checked'; } ?> value="3"/><label for="rating_service3" title="3 stars"></label>
                                <input type="radio" id="rating_service2" name="rating_service" <?php if ($my_review_place_service_rating === 2) { echo 'checked'; } ?> value="2"/><label for="rating_service2" title="2 stars"></label>
                                <input type="radio" id="rating_service1" name="rating_service" <?php if ($my_review_place_service_rating === 1) { echo 'checked'; } ?> value="1"/><label for="rating_service1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Value for Money', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('Overall experience received for the amount spent', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Overall experience received for the amount spent', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_money5" name="rating_money" <?php if ($my_review_place_money_rating === 5) { echo 'checked'; } ?> value="5"/><label for="rating_money5" title="5 stars"></label>
                                <input type="radio" id="rating_money4" name="rating_money" <?php if ($my_review_place_money_rating === 4) { echo 'checked'; } ?> value="4"/><label for="rating_money4" title="4 stars"></label>
                                <input type="radio" id="rating_money3" name="rating_money" <?php if ($my_review_place_money_rating === 3) { echo 'checked'; } ?> value="3"/><label for="rating_money3" title="3 stars"></label>
                                <input type="radio" id="rating_money2" name="rating_money" <?php if ($my_review_place_money_rating === 2) { echo 'checked'; } ?> value="2"/><label for="rating_money2" title="2 stars"></label>
                                <input type="radio" id="rating_money1" name="rating_money" <?php if ($my_review_place_money_rating === 1) { echo 'checked'; } ?> value="1"/><label for="rating_money1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Location', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('Visibility, commute or nearby parking spots', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Visibility, commute or nearby parking spots', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_location5" name="rating_location" <?php if ($my_review_place_location_rating === 5) { echo 'checked'; } ?> value="5"/><label for="rating_location5" title="5 stars"></label>
                                <input type="radio" id="rating_location4" name="rating_location" <?php if ($my_review_place_location_rating === 4) { echo 'checked'; } ?> value="4"/><label for="rating_location4" title="4 stars"></label>
                                <input type="radio" id="rating_location3" name="rating_location" <?php if ($my_review_place_location_rating === 3) { echo 'checked'; } ?> value="3"/><label for="rating_location3" title="3 stars"></label>
                                <input type="radio" id="rating_location2" name="rating_location" <?php if ($my_review_place_location_rating === 2) { echo 'checked'; } ?> value="2"/><label for="rating_location2" title="2 stars"></label>
                                <input type="radio" id="rating_location1" name="rating_location" <?php if ($my_review_place_location_rating === 1) { echo 'checked'; } ?> value="1"/><label for="rating_location1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group star-rating">
                            <div class="rate-title">
                                <span><?php esc_html_e('Cleanliness', 'golo-framework'); ?></span>
                                <i class="tip" data-tip-content="<?php esc_html_e('The physical condition of the business', 'golo-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('The physical condition of the business', 'golo-framework'); ?></div>
                                </i>
                            </div>
                            <fieldset class="rate">
                                <input type="radio" id="rating_cleanliness5" name="rating_cleanliness" <?php if ($my_review_place_cleanliness_rating === 5) { echo 'checked'; } ?> value="5"/><label for="rating_cleanliness5" title="5 stars"></label>
                                <input type="radio" id="rating_cleanliness4" name="rating_cleanliness" <?php if ($my_review_place_cleanliness_rating === 4) { echo 'checked'; } ?> value="4"/><label for="rating_cleanliness4" title="4 stars"></label>
                                <input type="radio" id="rating_cleanliness3" name="rating_cleanliness" <?php if ($my_review_place_cleanliness_rating === 3) { echo 'checked'; } ?> value="3"/><label for="rating_cleanliness3" title="3 stars"></label>
                                <input type="radio" id="rating_cleanliness2" name="rating_cleanliness" <?php if ($my_review_place_cleanliness_rating === 2) { echo 'checked'; } ?> value="2"/><label for="rating_cleanliness2" title="2 stars"></label>
                                <input type="radio" id="rating_cleanliness1" name="rating_cleanliness" <?php if ($my_review_place_cleanliness_rating === 1) { echo 'checked'; } ?> value="1"/><label for="rating_cleanliness1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group form-media">
                            <label for="file">
                                <input class="uploadImage" type="file" name="files[]" accept="image/*, application/pdf" id="file" multiple="">
                                <span class="name"><?php esc_attr_e('Add Photos', 'golo-framework'); ?></span>
                                <span class="fileList"></span>
                            </label>
                        </div>
                        <div class="form-group custom-area">
                            <textarea class="form-control" rows="6" name="message" placeholder="<?php esc_attr_e('Your review...', 'golo-framework'); ?>"><?php echo wp_kses_post($my_review->comment_content); ?></textarea>
                            <?php if( isset($avatar_url) ) : ?>
                            <div class="current-user-avatar">
                                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="golo-submit-place-rating btn-golo btn btn-default"><span><?php esc_html_e('Update Review', 'golo-framework'); ?></span></button>
                        <?php wp_nonce_field('golo_submit_review_ajax_nonce', 'golo_security_submit_review'); ?>
                        <input type="hidden" name="action" value="golo_place_submit_review_ajax">
                        <input type="hidden" name="place_id" value="<?php the_ID(); ?>">
                    </form>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <div class="duplicate-form-reply hide none">
        <div class="entry-head">
            <h4 class="review-title"><?php esc_html_e('Reply', 'golo-framework'); ?></h4>
            <a href="#" class="cancel-reply">
                <i class="la la-times"></i>
                <span><?php esc_html_e('Cancel reply', 'golo-framework'); ?></span>   
            </a>
        </div>
        <?php 
        $current_user = wp_get_current_user();
        $user_name    = $current_user->display_name;
        $avatar_url   = get_avatar_url($current_user->ID);
        $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
        if( !empty($author_avatar_image_url) ){
            $avatar_url = $author_avatar_image_url;
        }
        ?>
        <form method="post" class="repreviewForm" action="#">
            <div class="form-group custom-area">
                <textarea class="form-control" rows="5" name="message" placeholder="<?php esc_attr_e('Add a comment...', 'golo-framework'); ?>"></textarea>
                <?php if( isset($avatar_url) ) : ?>
                <div class="current-user-avatar">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                </div>
                <?php endif; ?>
                <label id="message-error" class="error" for="message"><?php esc_attr_e('This field is required', 'golo-framework'); ?></label>
            </div>
            <button type="submit" class="golo-submit-place-reply btn-golo btn btn-default"><?php esc_html_e('Send', 'golo-framework'); ?></button>
            <?php wp_nonce_field('golo_submit_reply_ajax_nonce', 'golo_security_submit_reply'); ?>
            <input type="hidden" name="action" value="golo_place_submit_reply_ajax">
            <input type="hidden" name="place_id" value="<?php the_ID(); ?>">
            <input type="hidden" name="comment_id" value="">
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {

            $('input:file').change(function(){
                $('.fileList span').remove();
                for(var i = 0 ; i < this.files.length ; i++){
                    var fileName = this.files[i].name;
                    $('.fileList').append('<span>' + fileName + '</span>');
                }
            });

            $('.entry-nav .reply').on('click', function(e) {
                e.preventDefault();
                $('.author-review').removeClass('active');
                $('.author-review .form-reply').html('');
                var $this      = $(this);
                var form_reply = $('.duplicate-form-reply').html();
                var comment_id = $this.parents('.author-review').find('.form-reply').data('id');
                $('.add-new-review').hide();
                $this.parents('.author-review').addClass('active');
                $this.parents('.author-review').find('.form-reply').html(form_reply);
                $this.parents('.author-review').find('.form-reply input[name="comment_id"]').val(comment_id);
            });

            $('body').on('click', '.form-reply .golo-submit-place-reply', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $form = $this.parents('form');
                var message = $form.find( 'textarea' ).val();
                if (message == '') {
                    $form.find( '#message-error' ).fadeIn();
                } else {
                    $form.find( '#message-error' ).fadeOut();

                    $.ajax({
                        type: 'POST',
                        url: '<?php echo esc_url(GOLO_AJAX_URL); ?>',
                        data: $form.serialize(),
                        dataType: 'json',
                        beforeSend: function() {
                            $this.attr('disabled', true);
                            $this.children('i').remove();
                            $this.append('<i class="fa-left la la-circle-notch la-spin large"></i>');
                        },
                        success: function() {
                            window.location.reload();
                        },
                        complete: function() {
                            $this.children('i').removeClass('la la-circle-notch la-spin large');
                            $this.children('i').addClass('fa fa-check');
                        }
                    });
                }
            });

            $('body').on('click', '.cancel-reply', function(e) {
                e.preventDefault();
                $('.author-review').removeClass('active');
                $('.author-review .form-reply').html('');
                $('.add-new-review').show();
            });
            
            
            $.validator.setDefaults({
                debug: true,
                success: "valid"
            });

            $('.reviewForm').validate({
                rules: {
                    message: {
                        required: true,
                    },
                },
                messages: {
                    message: {
                        required: "This field is required"
                    }
                },
                errorPlacement: function(error, element) {
                    if ( element.is(":radio") ) 
                    {
                        error.appendTo( element.parents('fieldset') );
                    }
                    else 
                    { // This is the default behavior 
                        error.insertAfter( element );
                    }
                },
                submitHandler: function(form) {
                    var $this = $('.reviewForm').find( '.golo-submit-place-rating' );
                    var $form = $('.reviewForm');

                    var formdata = false;
                    if (window.FormData){
                        formdata = new FormData($form[0]);
                    }

                    $.ajax({
                        type: 'POST',
                        url: '<?php echo esc_url(GOLO_AJAX_URL); ?>',
                        data: formdata ? formdata : $form.serialize(),
                        enctype: 'multipart/form-data',
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $this.children('i').remove();
                            $this.append('<i class="fa-left la la-circle-notch la-spin large"></i>');
                        },
                        success: function(data) {
                            window.location.reload();
                        },
                        complete: function() {
                            $this.children('i').removeClass('la la-circle-notch la-spin large');
                            $this.children('i').addClass('fa fa-check');
                        }
                    });
                }
            });
        });
    </script>
</div>