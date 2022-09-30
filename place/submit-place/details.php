<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
wp_enqueue_script('jquery-ui-sortable');
?>

<div class="place-fields-wrap">
    <div class="place-fields place-additional-details">
        <div class="form-group">
            <table class="additional-block">
                <thead>
                <tr>
                    <td class="golo-column-action"></td>
                    <td><label class="place-fields-title"><?php esc_html_e('Title', 'golo-framework'); ?></label></td>
                    <td><label class="place-fields-title"><?php esc_html_e('Value', 'golo-framework'); ?></label></td>
                    <td class="golo-column-action"></td>
                </tr>
                </thead>
                <tbody id="golo_additional_details">
                    <tr>
                        <td class="action-field"><span class="sort-additional-row"><i class="fal fa-bars"></i></span></td>
                        <td><input class="form-control" type="text" name="additional_detail_title[' + row_num + ']" id="additional_detail_title_' + row_num + '" value="" placeholder="<?php esc_attr_e('Deposit', 'golo-framework'); ?>"></td>
                        <td><input class="form-control" type="text" name="additional_detail_value[' + row_num + ']" id="additional_detail_value_' + row_num + '" value="" placeholder="<?php esc_attr_e('20%', 'golo-framework'); ?>"></td>
                        <td class="delete-field">
                            <span data-remove="' + row_num + '" class="remove-additional-detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16">
                                    <g fill="#5D5D5D" fill-rule="nonzero">
                                        <path d="M14.964 2.32h-4.036V0H4.105v2.32H.07v1.387h1.37l.924 12.25H12.67l.925-12.25h1.369V2.319zm-9.471-.933H9.54v.932H5.493v-.932zm5.89 13.183H3.65L2.83 3.707h9.374l-.82 10.863z"/>
                                        <path d="M6.961 6.076h1.11v6.126h-1.11zM4.834 6.076h1.11v6.126h-1.11zM9.089 6.076h1.11v6.126h-1.11z"/>
                                    </g>
                                </svg>
                            </span>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td colspan="3">
                        <button type="button" data-increment="-1" class="add-additional-detail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <g fill="#2D2D2D" fill-rule="evenodd">
                                    <path d="M7 0h2v16H7z"/>
                                    <path d="M16 7v2H0V7z"/>
                                </g>
                            </svg>
                            <?php esc_html_e('Add New', 'golo-framework'); ?>  
                        </button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    (function($) {
    "use strict";
        jQuery(document).ready(function () {
            $('.add-additional-detail').on('click', function (e) {
                e.preventDefault();
                var row_num = $(this).data("increment") + 1;
                $(this).data('increment', row_num);
                $(this).attr({
                    "data-increment": row_num
                });

                var new_detail = '<tr>' +
                    '<td class="action-field">' +
                    '<span class="sort-additional-row"><i class="fal fa-bars"></i></span>' +
                    '</td>' +
                    '<td>' +
                    '<input class="form-control" type="text" name="additional_detail_title[' + row_num + ']" id="additional_detail_title_' + row_num + '" value="">' +
                    '</td>' +
                    '<td>' +
                    '<input class="form-control" type="text" name="additional_detail_value[' + row_num + ']" id="additional_detail_value_' + row_num + '" value="">' +
                    '</td>' +
                    '<td class="delete-field">' +
                    '<span data-remove="' + row_num + '" class="remove-additional-detail"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16"><g fill="#5D5D5D" fill-rule="nonzero"><path d="M14.964 2.32h-4.036V0H4.105v2.32H.07v1.387h1.37l.924 12.25H12.67l.925-12.25h1.369V2.319zm-9.471-.933H9.54v.932H5.493v-.932zm5.89 13.183H3.65L2.83 3.707h9.374l-.82 10.863z"/><path d="M6.961 6.076h1.11v6.126h-1.11zM4.834 6.076h1.11v6.126h-1.11zM9.089 6.076h1.11v6.126h-1.11z"/></g></svg></span>' +
                    '</td>' +
                    '</tr>';
                $('#golo_additional_details').append(new_detail);
                golo_remove_additional_detail();
            });

            var golo_remove_additional_detail = function () {
                $('.remove-additional-detail').on('click', function (event) {
                    event.preventDefault();
                    var $this = $(this),
                        parent = $this.closest('.additional-block'),
                        button_add = parent.find('.add-additional-detail'),
                        increment = parseInt(button_add.data('increment')) - 1;

                    $this.closest('tr').remove();
                    button_add.data('increment', increment);
                    button_add.attr('data-increment', increment);
                    golo_execute_additional_order();
                });
            };

            var golo_execute_additional_order = function () {
                var $i = 0;
                $('tr', '#golo_additional_details').each(function () {
                    var input_title = $('input[name*="additional_detail_title"]', $(this)),
                        input_value = $('input[name*="additional_detail_value"]', $(this));
                    input_title.attr('name', 'additional_detail_title[' + $i + ']');
                    input_title.attr('id', 'additional_detail_title_' + $i);
                    input_value.attr('name', 'additional_detail_value[' + $i + ']');
                    input_value.attr('id', 'additional_detail_value_' + $i);
                    $i++;
                });
            };

            $('#golo_additional_details').sortable({
                revert: 100,
                placeholder: "detail-placeholder",
                handle: ".sort-additional-row",
                cursor: "move",
                stop: function (event, ui) {
                    golo_execute_additional_order();
                }
            });
        });
    })(jQuery);
</script>