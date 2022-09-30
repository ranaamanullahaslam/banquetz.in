<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$place_id = get_the_ID();
$faqs_tab = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'faqs_tab', true);
$faqs_enable = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'faqs_enable', true);

if( $faqs_enable === '1' ) :
	if( !empty($faqs_tab) ) :
		if( count($faqs_tab) > 0 ) : ?>
		<div class="place-faqs place-area">
			<div class="entry-heading">
		        <h3 class="entry-title"><?php esc_html_e("FAQ's", 'golo-framework'); ?></h3>
		    </div>
			
			<div class="entry-detail list-faqs">
				<?php foreach ( $faqs_tab as $index => $faqs ) :
					$faqs_title       = $faqs[ GOLO_METABOX_PREFIX . 'faqs_title' ];
					$faqs_description = $faqs[ GOLO_METABOX_PREFIX . 'faqs_description' ];
					?>

					<div class="block-panel">
						<div class="block-tab">
							<?php if ( ! empty( $faqs_title ) ): ?>
								<div class="faqs-title">
									<h4><?php echo esc_html( $faqs_title ); ?></h4>
								</div>
							<?php endif; ?>
						</div>
						
						<div class="block-content">
							<?php if ( isset( $faqs_description ) && ! empty( $faqs_description ) ): ?>
								<div class="faqs-description">
									<p><?php echo sanitize_text_field( $faqs_description ); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>