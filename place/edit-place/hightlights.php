<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
global $place_data, $place_meta_data;
?>

<div class="place-fields-wrap">
    <div class="place-fields place-amenities">
        <div class="form-group form-check">
            <ul class="custom-scrollbar">
                <?php
                $tax_terms = get_the_terms($place_data->ID, 'place-amenities');
                $amenities_slug = array();
                if($tax_terms) {
                    foreach ($tax_terms as $tax_term) {
                        $amenities_slug[] = $tax_term->slug;
                    }
                }
                $place_amenities = get_categories(array(
                    'taxonomy'   => 'place-amenities',
                    'hide_empty' => 0,
                    'orderby'    => 'term_id',
                    'order'      => 'ASC'
                ));
                if ($place_amenities) :
                    foreach ($place_amenities as $place_amenity) {
                    ?>
                        <li>
                            <input type="checkbox" id="golo_<?php echo esc_attr($place_amenity->slug); ?>" class="custom-checkbox input-control" name="place_amenities" value="<?php echo esc_attr($place_amenity->term_id); ?>" <?php if(in_array($place_amenity->slug, $amenities_slug)) : echo 'checked';endif; ?> />
                            <label for="golo_<?php echo esc_attr($place_amenity->slug); ?>"><?php echo esc_html($place_amenity->name); ?></label>
                        </li>
                    <?php } ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>