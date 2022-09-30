<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ( !is_user_logged_in() ) {
    golo_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}


$place_id = isset($_GET['place_id']) ? golo_clean(wp_unslash($_GET['place_id'])) : '';

if (!empty($place_id)) {
    golo_get_template('place/place-edit.php');
}else{

	$posts_per_page = '5';
	$delete_place_confirmation = esc_html__('Delete this item?', 'golo-framework');

	wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'my-place');
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style('wp-jquery-ui-dialog');
	
	wp_localize_script(GOLO_PLUGIN_PREFIX . 'my-place', 'golo_my_place_vars',
	    array(
			'ajax_url'    => GOLO_AJAX_URL,
			'not_place'   => esc_html__('No place found', 'golo-framework'),
			'item_amount' => $posts_per_page,
			'delete_confirmation' => $delete_place_confirmation
	    )
	);

	$default_city = golo_get_option('default_city', '');

	$place_classes = array('golo-places','grid','columns-4');

	$tax_query = $meta_query = array();

	global $current_user;
	wp_get_current_user();
	$user_id = $current_user->ID;
	$golo_profile = new Golo_Profile();

	$args = array(
		'post_type'           => 'place',
		'post_status'         => array('publish', 'expired', 'pending', 'hidden'),
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $posts_per_page,
		'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
		'orderby'             => 'date',
		'order'               => 'desc',
		'author'              => $user_id,
	);

	$data = new WP_Query($args);
	?>


	<div class="golo-my-places area-main-control">
		
		<div class="container">

			<?php if ( !in_array( 'customer', (array) $current_user->roles ) ) { ?>

			<div class="panel-head">

				<?php 
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
	                <h2 class="entry-title"><?php esc_html_e('Places', 'golo-framework'); ?></h2>
					
					<?php if ($paid_submission_type == 'per_package') { ?>
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

				<div class="place-filter">
					<div class="left-filter">
						<div class="place-city">
					    	<select name="place_city" class="form-control search-control nice-select wide">
		                        <option value=""><?php esc_html_e('All Cities', 'golo-framework'); ?></option>
		                        <?php golo_get_taxonomy_slug('place-city', $default_city); ?>
		                    </select>
					    </div>

						<div class="place-categories">
							<select name="place_categories" class="form-control search-control nice-select wide">
		                        <option value=""><?php esc_html_e('All Categories', 'golo-framework'); ?></option>
		                        <?php golo_get_taxonomy('place-categories', false, false); ?>
		                    </select>
					    </div>
				    </div>

				    <div class="right-filter">
				    	<div class="block-search search-input">
							<div class="icon-search">
								<i class="la la-search large"></i>
							</div>
							<input class="input-search search-control" name="place_search" type="text" placeholder="<?php esc_attr_e('Search', 'golo-framework'); ?>" />
						</div>
				    </div>
				</div>
			</div>

		    <?php if ($data->have_posts()) { ?>
		    <div class="entry-my-table">
		        <table id="my-places" class="golo-table">
		        	<thead>
		        		<tr>
		        			<th class="place-id"><?php esc_html_e('ID', 'golo-framework'); ?></th>
		        			<th class="place-thumb"><?php esc_html_e('Thumb', 'golo-framework'); ?></th>
		        			<th class="place-name"><?php esc_html_e('Place name', 'golo-framework'); ?></th>
		        			<th class="place-categories"><?php esc_html_e('City', 'golo-framework'); ?></th>
		        			<th class="place-category"><?php esc_html_e('Category', 'golo-framework'); ?></th>
		        			<th class="place-featured"><?php esc_html_e('Featured', 'golo-framework'); ?></th>
		        			<th class="place-status"><?php esc_html_e('Status', 'golo-framework'); ?></th>
		        			<th class="place-control"><?php esc_html_e('Action', 'golo-framework'); ?></th>
		        		</tr>
		        	</thead>
					
					<tbody>
				        <?php while ($data->have_posts()): $data->the_post(); ?>
							<?php 
								$id = get_the_ID();
								$status = get_post_status($id);
								$place_categories = get_the_terms( $id, 'place-categories');
								$prop_featured = get_post_meta($id, GOLO_METABOX_PREFIX . 'place_featured', true);
								$place_city = get_the_terms( $id, 'place-city');
								if( $place_city ) {
									$city_id   = $place_city[0]->term_id;
									$city_name = $place_city[0]->name;
									$city_slug = $place_city[0]->slug;
								} 
								$default_image = golo_get_option('default_place_image', '');
								if($default_image['url'] != '')
				                {
				                    if(is_array($default_image) && $default_image['url'] != '')
				                    {
				                        $no_image_src = $default_image['url'];
				                    }
				                } else {
				                	$no_image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';

				                }

							?>
							<tr>
								<td class="place-id" data-title="<?php esc_html_e('ID', 'golo-framework'); ?>">
									<span class="mb-intro"><?php esc_html_e('ID', 'golo-framework'); ?></span>
									<span><?php echo esc_html($id); ?></span>
								</td>
								<td class="place-thumb" data-title="<?php esc_html_e('Thumb', 'golo-framework'); ?>">
									<span class="mb-intro"><?php esc_html_e('Thumbnail:', 'golo-framework'); ?></span>
									<a href="<?php echo get_the_permalink($id); ?>">
										<?php if( has_post_thumbnail($id) ) { ?>
		                                    <?php echo get_the_post_thumbnail($id, 'thumbnail'); ?>
		                                <?php }else{ ?>
		                                    <img src="<?php echo esc_url($no_image_src); ?>" alt="<?php the_title_attribute(); ?>">
		                                <?php } ?>
									</a>
								</td>
								<td class="place-name" data-title="<?php esc_html_e('Place name', 'golo-framework'); ?>">
									<span class="mb-intro"><?php esc_html_e('Name:', 'golo-framework'); ?></span>
									<h3 class="place-title">
										<a href="<?php echo get_the_permalink($id); ?>">
											<?php echo get_the_title($id); ?>
										</a>
									</h3>
								</td>
								<td class="place-city" data-title="<?php esc_html_e('City', 'golo-framework'); ?>">
									<span class="mb-intro"><?php esc_html_e('City:', 'golo-framework'); ?></span>
									<div>
										<?php if( $place_city ) { ?>
											<a href="<?php echo get_term_link( $city_slug, 'place-city'); ?>"><?php echo esc_html($city_name); ?></a>
										<?php }else{ ?>
											<span><?php esc_attr_e('_', 'golo-framework'); ?></span>
										<?php } ?>
									</div>
								</td>
								<td class="place-categories list-item" data-title="<?php esc_html_e('Category', 'golo-framework'); ?>">
									<span class="mb-intro"><?php esc_html_e('Categories:', 'golo-framework'); ?></span>
									<div>
										<?php 
										if( $place_categories ) {
								            foreach ($place_categories as $cate) {
								                $cate_link = get_term_link($cate, 'place-categories');
								                ?>
								                    <a href="<?php echo esc_url($cate_link); ?>?city=<?php echo esc_attr($city_slug); ?>"><?php echo esc_html($cate->name); ?></a>
								                <?php
								            }
							            }else{
							            	?>
												<span><?php esc_attr_e('_', 'golo-framework'); ?></span>
							            	<?php
							            }
							            ?>
						            </div>
								</td>
								<td class="place-featured" data-title="<?php esc_html_e('Featured', 'golo-framework'); ?>">
									<?php if( $prop_featured ) { ?>
	                                	<span class="has-featured"><i class="las la-star icon-large"></i></span>
	                                <?php }else{ ?>
										<span><i class="lar la-star icon-large"></i></span>
	                                <?php } ?>
								</td>
								<td class="place-status status <?php echo esc_attr($status); ?>" data-title="<?php esc_html_e('Status', 'golo-framework'); ?>">
									<?php 
									$current_status = $status;
	                                if( $current_status == 'publish' ) {
	                                    $current_status = esc_html__('Approved', 'golo-framework');
	                                }

	                                if( $current_status == 'pending' ) {
	                                    $current_status = esc_html__('Pending', 'golo-framework');
	                                }
	                                ?>
	                                <div><?php echo esc_html($current_status); ?></div>
								</td>
								<td class="place-control" data-title="<?php esc_html_e('Action', 'golo-framework'); ?>">
									<?php 
									$my_place_link = golo_get_permalink('my_places');
									$payment_status = get_post_meta($id, GOLO_METABOX_PREFIX . 'payment_status', true);
	                                switch ($status) { 

	                                	case 'publish' :
	                                        if ($paid_submission_type == 'per_package') {
	                                            $current_package_key = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_key', $user_id);
	                                            $place_package_key = get_post_meta($id, GOLO_METABOX_PREFIX . 'package_key', true);

	                                            $check_package = $golo_profile->user_package_available($user_id);
	                                            if($check_package != -1 && $check_package != 0)
	                                            {
	                                                ?>
														<a class="btn-edit hint--top" href="<?php echo esc_url($my_place_link); ?>?place_id=<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e( 'Edit', 'golo-framework' ); ?>"><i class="la la-edit large"></i></a>
	                                                <?php
	                                            }
	                                            $package_num_featured_listings = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_number_featured', $user_id);
	                                            if ($package_num_featured_listings > 0 && ($prop_featured != 1) && ($check_package != -1)  && ($check_package != 0)) {
	                                                ?>
														<a class="btn-mark-featured hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Make Featured', 'golo-framework' ); ?>"><i class="la la-star-o large"></i></a>
	                                                <?php
	                                            }
	                                            if($check_package != -1 && $check_package != 0)
	                                            {
	                                                ?>
														<a class="btn-hide hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Hide', 'golo-framework' ); ?>"><i class="la la-eye-slash large"></i></a>
	                                                <?php
	                                            }
	                                        }else{
	                                            if ($prop_featured != 1) {
	                                                ?>
														<a class="btn-mark-featured hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Make Featured', 'golo-framework' ); ?>"><i class="la la-star large"></i></a>
	                                                <?php
	                                            }
	                                            ?>
													<a class="btn-edit hint--top" href="<?php echo esc_url($my_place_link); ?>?place_id=<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e( 'Edit', 'golo-framework' ); ?>"><i class="la la-edit large"></i></a>
													
													<a class="btn-hide hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Hide', 'golo-framework' ); ?>"><i class="la la-eye-slash large"></i></a>
	                                            <?php
	                                        }

	                                        break;
	                                    case 'expired' :
	                                        if ($paid_submission_type == 'per_package') {
	                                            $check_package = $golo_profile->user_package_available($user_id);
	                                            if($check_package == 1)
	                                            {

	                                                ?>
														<a class="btn-reactivate-place hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Reactivate Place', 'golo-framework' ); ?>"><i class="la la-sync large"></i></a>
	                                                <?php
	                                            }
	                                        }else{
	                                        	?>
													<a class="btn-reactivate-place hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Reactivate Place', 'golo-framework' ); ?>"><i class="la la-sync large"></i></a>
	                                            <?php
	                                        }
	                                        break;
	                                    case 'pending' :
	                                        ?>
												<a class="btn-edit hint--top" href="<?php echo esc_url($my_place_link); ?>?place_id=<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e( 'Edit', 'golo-framework' ); ?>"><i class="la la-edit large"></i></a>
	                                        <?php
	                                        break;
	                                    case 'hidden' :
	                                        ?>
												<a class="btn-show hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Show', 'golo-framework' ); ?>"><i class="la la-eye large"></i></a>
	                                        <?php
	                                        break;

									} 
									?>
									<a class="btn-delete hint--top" place-id="<?php echo esc_attr($id); ?>" href="#" aria-label="<?php esc_attr_e( 'Delete', 'golo-framework' ); ?>"><i class="la la-trash-alt large"></i></a>
								</td>
							</tr>
				        <?php endwhile; ?>
					</tbody>
		        </table>

		        <div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>
			</div>
		    <?php } else { ?>
		        <div class="item-not-found"><?php esc_html_e('No item found', 'golo-framework'); ?></div>
		    <?php } ?>

		    <?php
		        $max_num_pages = $data->max_num_pages;
		        golo_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'layout' => 'number'));
		        wp_reset_postdata();
		    ?>

		    <?php } else { ?>

		    	<p><?php esc_html_e('You can not access this page!', 'golo-framework'); ?></p>

		    <?php } ?>

		</div>

	</div>

<?php } ?>