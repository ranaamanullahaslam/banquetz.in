<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
?>
<div class="place-fields-wrap">
    <div class="place-fields-title">
        <h3><?php esc_html_e( 'Private Note', 'golo-framework' ); ?></h3>
    </div>
    <div class="place-fields place-private-note">
        <div class="form-group">
            <label for="private_note"><?php esc_html_e('Create a private note for this place, it will not be displayed to public', 'golo-framework'); ?></label>
            <textarea name="private_note" rows="4" id="private_note" class="form-control"></textarea>
        </div>
    </div>
</div>