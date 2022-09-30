<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

global $place_data, $place_meta_data, $hide_place_fields;
?>

<?php if (!in_array('additional', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields place-additional">
        <?php
        $additional_fields = golo_render_additional_fields();
        if(count($additional_fields)>0) {
            foreach ($additional_fields as $key => $field) {
                switch ($field['type']) {
                    case 'text':
                        ?>
                        <div class="form-group">
                            <div class="place-fields-title"><h3><?php echo esc_html($field['title']); ?></h3></div>
                            <input type="text" id="<?php echo esc_attr($field['id']); ?>" class="form-control" name="<?php echo esc_attr($field['id']); ?>" value="<?php if (isset($place_meta_data[$field['id']])) {
                                       echo sanitize_text_field($place_meta_data[$field['id']][0]);
                                   } ?>">
                        </div>
                        <?php
                        break;
                    case 'url':
                        ?>
                        <div class="form-group">
                            <div class="place-fields-title"><h3><?php echo esc_html($field['title']); ?></h3></div>
                            <input type="url" id="<?php echo esc_attr($field['id']); ?>" class="form-control" name="<?php echo esc_attr($field['id']); ?>" value="<?php if (isset($place_meta_data[$field['id']])) {
                                       echo sanitize_text_field($place_meta_data[$field['id']][0]);
                                   } ?>">
                        </div>
                        <?php
                        break;
                    case 'textarea':
                        ?>
                        <div class="form-group">
                            <div class="place-fields-title"><h3><?php echo esc_html($field['title']); ?></h3></div>
                            <textarea name="<?php echo esc_attr($field['id']); ?>" rows="3" id="<?php echo esc_attr($field['id']); ?>" class="form-control"><?php if (isset($place_meta_data[$field['id']])) {
                                    echo sanitize_text_field($place_meta_data[$field['id']][0]);
                                } ?></textarea>
                        </div>
                        <?php
                        break;
                    case 'select':
                        ?>
                        <div class="form-group">
                            <div class="place-fields-title"><h3><?php echo esc_html($field['title']); ?></h3></div>
                            <select name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" class="form-control">
                                <?php
                                foreach ($field['options'] as $opt_value):?>
                                    <option value="<?php echo esc_attr($opt_value); ?>" <?php if( isset( $place_meta_data[$field['id']] ) &&  $place_meta_data[$field['id']][0]==$opt_value) { echo 'selected';} ?>><?php echo esc_html($opt_value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        break;
                    case 'checkbox_list':
                        ?>
                        <div class="form-group">
                            <div class="place-fields-title"><h3><?php echo esc_html($field['title']); ?></h3></div>
                            <div class="golo-field-<?php echo esc_attr($field['id']); ?>">
                            <?php
                            $place_field= get_post_meta($place_data->ID, $field['id'], true);
                            if(empty($place_field))
                            {
                                $place_field=array();
                            }
                            foreach ($field['options'] as $opt_value):
                                if ( in_array( $opt_value, $place_field ) ):?>
                                    <div class="checkbox-inline inline"><input class="custom-checkbox" type="checkbox" name="<?php echo esc_attr($field['id']); ?>[]" value="<?php echo esc_attr($opt_value); ?>" checked><?php echo esc_html($opt_value); ?>
                                    </div>
                                <?php else:?>
                                    <div class="checkbox-inline inline"><input class="custom-checkbox" type="checkbox" name="<?php echo esc_attr($field['id']); ?>[]" value="<?php echo esc_attr($opt_value); ?>"><?php echo esc_html($opt_value); ?>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                            </div>
                        </div>
                        <?php
                        break;
                    case 'radio':
                        ?>
                        <div class="form-group">
                            <div class="place-fields-title"><h3><?php echo esc_html($field['title']); ?></h3></div>
                            <div class="golo-field-<?php echo esc_attr($field['id']); ?>">
                            <?php foreach ($field['options'] as $opt_value) : ?>
                                <div class="radio-inline inline"><input type="radio" name="<?php echo esc_attr($field['id']); ?>" value="<?php echo esc_attr($opt_value); ?>" <?php if( isset( $place_meta_data[$field['id']] ) &&  $place_meta_data[$field['id']][0]==$opt_value) { echo 'checked';} ?>><?php echo esc_html($opt_value); ?>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                        break;
                }
            }
        }
        ?>
    </div>
</div>
<?php endif; ?>