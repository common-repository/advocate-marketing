<?php
/*
 *      Settings > Corner Widget tab
 */
$sr_widget_onoff = get_option('swiftreviews_widget_onoff');
$sr_widget_header_text = get_option('swiftreviews_widget_header_text');
$sr_widget_header_color = get_option('swiftreviews_widget_header_color');
$sr_widget_button_color = get_option('swiftreviews_widget_button_color');
$sr_widget_text_color = get_option('swiftreviews_widget_text_color');
$sr_widget_position = get_option('swiftreviews_widget_position');
$sr_widget_rating_style = get_option('swiftreviews_widget_rating_style');
$display = ($sr_widget_onoff == 1) ? "block" : "none";
?>
<form name="FrmCornerWidget" id="FrmCornerWidget" method="post">
    <table class="form-table tbl1">
        <tr>
            <th><label for="sr_widget_onoff">Corner Widget </label></th>
            <td>
                <?php $widgetOnOff = ($sr_widget_onoff == 1 ? 'checked="checked"' : ""); ?>
                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="sr_widget_onoff" id="sr_widget_onoff" class="sr_widget_onoff" <?php echo $widgetOnOff; ?>>
            </td>
        </tr>
    </table>
    <table class="form-table tbl2" style="margin-top: 0;display: <?php echo $display; ?>;">
        <tr>
            <th><label>Widget Header Text</label></th>
            <td><input type="text" value="<?php echo stripslashes($sr_widget_header_text); ?>" name="sr_widget_header_text" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label>Widget Rating Style</label></th>
            <td>
                <select id="sr_widget_rating_style" name="sr_widget_rating_style">
                    <option <?php selected($sr_widget_rating_style, '10stars'); ?> value="10stars">0-10 Rating</option>
                    <option <?php selected($sr_widget_rating_style, '5stars'); ?> value="5stars">5 Stars</option>
                    <option <?php selected($sr_widget_rating_style, 'smiley-frow'); ?> value="smiley-frow">5 Smiley / Frown</option>
                    <option <?php selected($sr_widget_rating_style, 'yes-no'); ?> value="yes-no">Yes / No</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sr_widget_header_color">Widget Header Color</label></th>
            <td><input type="text" id="sr_widget_header_color" value="<?php echo $sr_widget_header_color; ?>" name="sr_widget_header_color" class="colorbox" placeholder="#ffe701"/></td>
        </tr>
        <tr>
            <th><label for="sr_widget_button_color">Button Color</label></th>
            <td><input type="text" id="sr_widget_button_color" value="<?php echo $sr_widget_button_color; ?>" name="sr_widget_button_color" class="colorbox" placeholder="#072c52"/></td>
        </tr>
        <tr>
            <th><label for="sr_widget_text_color">Text Color</label></th>
            <td><input type="text" id="sr_widget_text_color" value="<?php echo $sr_widget_text_color; ?>" name="sr_widget_text_color" class="colorbox" placeholder="#072c52"/></td>
        </tr>
        <tr>
            <th><label for="sr_widget_position">Widget Position</label></th>
            <td>
                <select id="sr_widget_position" name="sr_widget_position">
                    <option <?php echo selected($sr_widget_position, "left"); ?> value="left">Left</option>
                    <option <?php echo selected($sr_widget_position, "center"); ?> value="center">Center</option>
                    <option <?php echo selected($sr_widget_position, "right"); ?> value="right">Right</option>
                </select>
            </td>
        </tr>
    </table>
    <table class="form-table tbl3">
        <tr>
            <th colspan="2" style="text-align: center;">
                <?php wp_nonce_field('save_swiftreviews_corner_widget', 'save_swiftreviews_corner_widget'); ?>
                <button type="submit" class="button-primary" id="sr-corner-widget-settings-btn" value="sr-corner-widget-settings" name="sr_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>
<script>
    jQuery(document).ready(function($) {
        jQuery('#sr_widget_onoff:checkbox').rcSwitcher().on({
            'turnon.rcSwitcher': function(e, dataObj) {
                jQuery(".tbl2").fadeIn();
            },
            'turnoff.rcSwitcher': function(e, dataObj) {
                jQuery(".tbl2").fadeOut();
            }
        });

        jQuery("#sr_widget_header_color").spectrum({
            preferredFormat: "hex",
            color: "<?php echo $sr_widget_header_color ? $sr_widget_header_color : "#FF7200"; ?>",
            showAlpha: true,
            showButtons: false,
            showInput: true
        });
        jQuery("#sr_widget_button_color").spectrum({
            preferredFormat: "hex",
            color: "<?php echo $sr_widget_button_color ? $sr_widget_button_color : "#072C52"; ?>",
            showAlpha: true,
            showButtons: false,
            showInput: true
        });
        jQuery("#sr_widget_text_color").spectrum({
            preferredFormat: "hex",
            color: "<?php echo $sr_widget_text_color ? $sr_widget_text_color : "#333"; ?>",
            showAlpha: true,
            showButtons: false,
            showInput: true
        });
    });
</script>