<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$type_single_place = golo_get_option('type_single_place', 'type-1' );
$place_amenities   = get_the_terms( $place_id, 'place-amenities');
// $place_amenities   = get_the_terms( $place_id, 'place-amenities');
// $all_amenities = get_terms(array(
//     'taxonomy'   => 'place-amenities',
//     'hide_empty' => 0,
//     'orderby'    => 'term_order',
//     'order'      => 'ASC',
// ));

// $place_amenitiess = array();
// foreach( $all_amenities as $aa ){
//     if( in_array($aa, $place_amenities) ){
//         array_push($place_amenitiess, $aa);
//     }
// }
$number = 4;

?>

<?php if( $place_amenities ) : ?>
<div class="place-amenities place-area">
	<div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('Hightlight', 'golo-framework'); ?></h3>
    </div>
    <div class="entry-detail">
        <div class="list-amenities">
    	<?php
        $amenities_terms_id = array();
        if ( !is_wp_error($place_amenities) ) {
            foreach ($place_amenities as $amenity) {
                $amenities_terms_id[] = intval($amenity->term_id);
            }
        }
        $all_amenities = get_categories(array(
            'taxonomy'   => 'place-amenities',
            'hide_empty' => 0,
            'orderby'    => 'term_id',
            'order'      => 'ASC',
            'number'     => $number,
        ));
        $show_amenities = array();

        if ($place_amenities) {
            echo '<ul class="show-grid">';
                foreach ($place_amenities as $index => $amenity) {
                    $term_link = get_term_link($amenity, 'place-amenities');
                    $term_icon = get_term_meta($amenity->term_id, 'place_amenities_icon', true );
                    $icon_src = '';
                    if ($term_icon && !empty($term_icon['url'])) {
                        $icon_src = $term_icon['url'];
                    }
                    if( $index < 4 ) {
                        $show_amenities[] = $amenity->term_id;
                    ?>
                        <li class="amenity-checked">
                            <?php if( $icon_src ) : ?>
                                <img src="<?php echo esc_url($icon_src); ?>" alt="<?php echo esc_html($amenity->name); ?>">
                            <?php endif; ?>
                            <span><?php echo esc_html($amenity->name); ?></span>
                        </li>
                    <?php
                    }
                };
            echo '</ul>';
        };
        ?>

        <?php 
            $total_amenities = get_categories(array(
                'taxonomy'   => 'place-amenities',
                'hide_empty' => 0,
                'orderby'    => 'term_id',
                'order'      => 'ASC',
            ));
            $count_hidden   = array();
            $tax_terms      = get_the_terms($place_id, 'place-amenities');
            $amenities_slug = array();
            foreach ($tax_terms as $tax_term) {
                $amenities_slug[] = $tax_term->slug;
            }
            $hidden_amenities = get_categories(array(
                'taxonomy'   => 'place-amenities',
                'hide_empty' => 0,
                'orderby'    => 'term_id',
                'order'      => 'ASC',
                'exclude'    => $show_amenities,
            ));
            if ($hidden_amenities) :
                foreach ($hidden_amenities as $hidden_amenity) {
                    if(in_array($hidden_amenity->slug, $amenities_slug)) {
                        $count_hidden[] = $hidden_amenity->term_id;
                    }
                }
            endif;
        ?>
        
        <?php if (count($amenities_slug) > 4) : ?>
        <div class="hidden-amenities">
            <?php if( count($amenities_slug) > 4 ) : ?>
            <a class="golo-on-popup" href="#popup-amenities"><?php echo sprintf( __( '+(%s)', 'golo-framework' ), count($count_hidden)); ?></a>
            <?php endif; ?>    

            <div id="popup-amenities" class="golo-popup entry-hidden-amenities">
                <div class="bg-overlay"></div>
                <div class="inner-popup">
                    <a href="#" class="btn-close">
                        <i class="la la-times large"></i>
                    </a>
                    
                    <div class="entry-heading">
                        <h3 class="entry-title"><?php esc_html_e('Hightlight', 'golo-framework'); ?></h3>
                    </div>
                    <ul class="grid columns-4">
                    <?php 
                        $tax_terms      = get_the_terms($place_id, 'place-amenities');
                        $amenities_slug = array();
                        foreach ($tax_terms as $tax_term) {
                            $amenities_slug[] = $tax_term->slug;
                        }
                        $hidden_amenities = get_categories(array(
                            'taxonomy'   => 'place-amenities',
                            'hide_empty' => 0,
                            'orderby'    => 'term_id',
                            'order'      => 'ASC',
                            'exclude'    => $show_amenities,
                        ));
                        if ($hidden_amenities) :
                            foreach ($hidden_amenities as $hidden_amenity) {
                                if(in_array($hidden_amenity->slug, $amenities_slug)) {
                                    $term_link = get_term_link($hidden_amenity, 'place-amenities');
                                    $term_icon = get_term_meta($hidden_amenity->term_id, 'place_amenities_icon', true );
                                    $icon_src  = '';
                                    if ($term_icon && !empty($term_icon['url'])) {
                                        $icon_src = $term_icon['url'];
                                    }
                                    ?>
                                        <li class="amenity-checked">
                                            <?php if( $icon_src ) : ?>
                                                <img src="<?php echo esc_url($icon_src); ?>" alt="<?php echo esc_html($hidden_amenity->name); ?>">
                                            <?php endif; ?>
                                            <span><?php esc_html_e($hidden_amenity->name); ?></span>
                                        </li>
                                    <?php
                                }
                            }
                        endif;
                    ?>
                    </ul>
                </div>
            </div>
            
        </div>

        <?php endif; ?>
        
        </div>

    </div>
</div>
<?php endif; ?>