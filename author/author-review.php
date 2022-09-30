<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wp_query, $wpdb;
$current_author = $wp_query->get_queried_object();
$user_id = $current_author->ID;

$count_reviews = get_total_reviews_by_user($user_id);

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
    $post_id        = $comment->comment_post_ID;
    $comment_name   = $comment->comment_author;
    $post_author_id = get_post_field( 'post_author', $post_id );
    if( $comment_name == $current_author->user_login ) {
        $has_review = true;
    }
}
?>

<div class="author-reviews">
	<div class="author-reviews-inner">
		<div class="block-heading">
			<h3><?php echo sprintf( __( 'Reviews (%1$s)', 'golo-framework' ), $count_reviews); ?></h3>
		</div>
		
		<div class="area-content">
		<?php if( $has_review ) { ?>
			<ul class="listing-detail reviews-list custom-scrollbar">
			    <?php 
			        foreach ($get_comments as $comment) {
						$comment_id     = $comment->comment_ID;
						$post_id        = $comment->comment_post_ID;
						$comment_name   = $comment->comment_author;
						$status         = get_post_status($post_id);
						$post_author_id = get_post_field( 'post_author', $post_id );
						$user_link      = get_author_posts_url($comment->user_id);

			            $author_avatar_url       = get_avatar_url($comment->user_id, ['size' => '50']);
			            $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $comment->user_id);
			            if( !empty($author_avatar_image_url) ){
			                $author_avatar_url = $author_avatar_image_url;
			            }
			            $author_avatar_url = golo_image_resize_url($author_avatar_url, 50, 50, true);

			            if( $comment_name == $current_author->user_login && $status == 'publish' ) {
			                if ($i++ > 20) break;
			            ?>
			                <li class="author-review">
			                    <div class="entry-head">
			                        <div class="entry-avatar">
			                            <figure>
			                                <?php
			                                if ( !empty($author_avatar_url['url']) ) {
			                                    ?>
			                                    <a href="<?php echo esc_url($user_link); ?>">
			                                        <img src="<?php echo esc_url($author_avatar_url['url']); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
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
			                                <h3 class="author-name"><a href="<?php echo esc_url($user_link); ?>"><?php the_author_meta('display_name', $comment->user_id); ?></a></h3>
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
			                        <span><?php esc_html_e('On', 'golo-framework'); ?></span>
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