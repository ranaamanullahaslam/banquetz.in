<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$image_no_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
$default_image = golo_get_option('default_place_image','');
$place_id = get_the_ID();
$menu_tab = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'menu_tab', true);
$menu_enable = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'menu_enable', true);
$menu_types_name = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'menu_types_name', true);

// Remove duplicate type names (not been done in submit/edit place)
if (is_array($menu_types_name)) {	
	$menu_types_name = array_unique($menu_types_name);
}

if( $menu_enable === '1' ) :
	if( !empty($menu_tab) ) :
		if( count($menu_tab) > 0 ) : ?>
		<div class="place-menu place-area">
			<div class="entry-heading">
		        <h3 class="entry-title"><?php esc_html_e('Menu', 'golo-framework'); ?></h3>
		    </div>

		    <?php if ($menu_types_name) { ?>
		    <div class="tab-group">
		    	<ul>
		    		<?php
		    			$i = 1;
		    			foreach ($menu_types_name as $key => $value) {
		    		?>
		    		<li class="<?php if( $i == '1' ) { echo 'active'; } ?>"><a href="#menu<?php echo $key; ?>"><?php echo $value; ?></a></li>
		    		<?php $i++; } ?>
		    	</ul>
		    	<div class="tab-inner">
		    	<?php
		    		$i = 1;
	    			foreach ($menu_types_name as $key => $value) {

	    		?>
	    		<div class="tab <?php if( $i == '1' ) { echo 'active'; } ?>" id="menu<?php echo $key; ?>">
	    			<div class="entry-detail list-menu grid columns-2 columns-sm-1">
						<?php 
							$j = 0;

							foreach ( $menu_tab as $index => $menu ) :

							$menu_item_type 	= $menu[ GOLO_METABOX_PREFIX . 'menu_item_type' ];
							if ($menu_item_type == $value) {
								$j++;
							}
							if( $j >= 5 ) break;
							$image_id         	= $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id'];
							$menu_title       	= $menu[ GOLO_METABOX_PREFIX . 'menu_title' ];
							$menu_price       	= $menu[ GOLO_METABOX_PREFIX . 'menu_price' ];
							$menu_description 	= $menu[ GOLO_METABOX_PREFIX . 'menu_description' ];
							
							if ($menu_item_type == $value) {

								if( golo_image_resize_id( $image_id, 140, 140, true ) ) {
									$image_src        = golo_image_resize_id( $image_id, 140, 140, true );
								} else { 
									if($default_image != '')
									{
										if(is_array($default_image) && $default_image['url'] != '')
										{
											$image_src = $default_image['url'];
										}
									} else {
										$image_src = $image_no_src;
									}
								}

							?>

							<div class="item">
								<?php if(!empty( $image_src )): ?>
									<div class="menu-image golo-light-gallery">
										<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php the_title_attribute(); ?>">
									</div>
								<?php endif; ?>
								
								<div class="menu-detail">
									<div class="left">
										<?php if ( ! empty( $menu_title ) ): ?>
											<div class="menu-title">
												<h4><?php echo esc_html( $menu_title ); ?></h4>
											</div>
										<?php endif; ?>

										<?php if ( isset( $menu_description ) && ! empty( $menu_description ) ): ?>
											<div class="menu-description">
												<p><?php echo wpautop( $menu_description ); ?></p>
											</div>
										<?php endif; ?>
									</div>
									
									<div class="right">
										<div class="menu-price"><?php echo esc_html( $menu_price ); ?></div>
									</div>
								</div>
							</div>
						<?php } endforeach; ?>
					</div>
					<div class="hidden-content entry-detail list-menu grid columns-2 columns-sm-1">
						<?php 
							$j = 0;
							foreach ( $menu_tab as $index => $menu ) :
							$menu_item_type 	= $menu[ GOLO_METABOX_PREFIX . 'menu_item_type' ];
							if ($menu_item_type == $value) {
								$j++;
							}
							if( $j >= 5 ) :
								$image_id         = $menu[ GOLO_METABOX_PREFIX . 'menu_image' ]['id'];
								$menu_title       = $menu[ GOLO_METABOX_PREFIX . 'menu_title' ];
								$menu_price       = $menu[ GOLO_METABOX_PREFIX . 'menu_price' ];
								$menu_description = $menu[ GOLO_METABOX_PREFIX . 'menu_description' ];
								if ($menu_item_type == $value) {

									if( golo_image_resize_id( $image_id, 140, 140, true ) ) {
										$image_src        = golo_image_resize_id( $image_id, 140, 140, true );
									} else { 
										if($default_image != '')
										{
											if(is_array($default_image) && $default_image['url'] != '')
											{
												$image_src = $default_image['url'];
											}
										} else {
											$image_src = $image_no_src;
										}
									}
								?>

								<div class="item">
									<?php if(!empty( $image_src )): ?>
										<div class="menu-image golo-light-gallery">
											<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php the_title_attribute(); ?>">
										</div>
									<?php endif; ?>
									
									<div class="menu-detail">
										<div class="left">
											<?php if ( ! empty( $menu_title ) ): ?>
												<div class="menu-title">
													<h4><?php echo esc_html( $menu_title ); ?></h4>
												</div>
											<?php endif; ?>

											<?php if ( isset( $menu_description ) && ! empty( $menu_description ) ): ?>
												<div class="menu-description">
													<p><?php echo wpautop( $menu_description ); ?></p>
												</div>
											<?php endif; ?>
										</div>
										
										<div class="right">
											<div class="menu-price"><?php echo esc_html( $menu_price ); ?></div>
										</div>
									</div>
								</div>
							<?php } endif; ?>
						<?php endforeach; ?>
					</div>
					<?php if( $j >= 5 ) : ?>
					<div class="toggle-desc">
				    	<a class="show-more" href="#"><?php esc_html_e('Show more', 'golo-framework'); ?></a>
				    	<a class="hide-all" href="#"><?php esc_html_e('Hide all', 'golo-framework'); ?></a>
				    </div>
				    <?php endif; ?>
	    		</div>

	    		<?php $i++; } ?>
	    		</div>
		    </div>
			<?php } ?>
		</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>