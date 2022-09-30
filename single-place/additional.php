<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$additional_fields = golo_render_additional_fields();
$additional_field = '';
foreach ($additional_fields as $key => $field):
	$additional_field = get_post_meta($place_id, $field['id'], true);

	if (!empty($additional_field)) {
		break;
	}
endforeach;

?>
<?php if (!empty($additional_field)) : ?>
<div class="place-additional place-area">
	<div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('More Information', 'golo-framework'); ?></h3>
    </div>
	
	<div class="entry-detail">
		<ul>
			<?php
		    if (count($additional_fields) > 0):
		        foreach ($additional_fields as $key => $field):
		            $place_field = get_post_meta($place_id, $field['id'], true);

		            if (!empty($place_field)):?>
		                <li>
		                    <strong><?php echo esc_html($field['title']); ?></strong>
		                <span><?php
		                    if ($field['type'] == 'checkbox_list') {
		                        $text = '';
		                        if (count($place_field) > 0) {
		                            foreach ($place_field as $value => $v) {
		                                $text .= $v . ', ';
		                            }
		                        }
		                        $text = rtrim($text, ', ');
		                        echo esc_html($text);
		                    } else if ($field['type'] == 'url') {
		                    	echo '<a href="' . esc_url($place_field) . '" target="_Blank">' . esc_url($place_field) . '</a>';
		                    } else {
		                        echo esc_html($place_field);
		                    }
		                    ?></span>
		                </li>
		                <?php
		            endif;
		        endforeach;
		    endif; 
		    ?>
		</ul>
	</div>
</div>
<?php endif; ?>