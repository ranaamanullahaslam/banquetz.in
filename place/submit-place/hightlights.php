<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
?>

<div class="place-fields-wrap">
    <div class="place-fields place-amenities">
        <div class="form-group form-check">
            <ul class="custom-scrollbar">
                <?php
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
                            <input type="checkbox" id="golo_<?php echo esc_attr($place_amenity->slug); ?>" class="custom-checkbox input-control" name="place_amenities" value="<?php echo esc_attr($place_amenity->term_id); ?>" />
                            <label for="golo_<?php echo esc_attr($place_amenity->slug); ?>"><?php echo esc_html($place_amenity->name); ?></label>
                        </li>
                    <?php } ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>