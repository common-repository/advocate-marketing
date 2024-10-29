<?php
/*
 *      Settings > Negativ Reviews tab
 */
?>
<form name="FrmSRNegativeSettings" id="FrmSRNegativeSettings" method="post">
    <table class="form-table">
        <tr style="display: none;">
            <th><label for="swiftreviews_negative_redirect_page">Negative Reviews Redirect Page: </label></th>
            <td>
                <select name="swiftreviews_negative_redirect_page" id="swiftreviews_negative_redirect_page">
                    <option value="0">--Select Page--</option>
                    <?php
                    if ($pages) {
                        foreach ($pages as $page) {
                            ?>
                            <option <?php selected($get_negative_redirect_page, $page->ID); ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                            <?php
                        }//First if
                    }// First loop
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="swiftreviews_feedback_section_html">Feedback section text: </label></th>
            <td>
                <?php
                $feedback_settings = array('editor_height' => 250, 'textarea_rows' => 12, 'teeny' => true, 'media_buttons' => false, 'quicktags' => true, 'textarea_name' => 'swiftreviews_feedback_section_html',);
                wp_editor(stripslashes($get_swiftreviews_feedback_section_html), 'feedback_section_id', $feedback_settings);
                ?>
            </td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                <?php wp_nonce_field('save_swiftreviews_negative_settings', 'save_swiftreviews_negative_settings'); ?>
                <button type="submit" class="button-primary" id="sr-negative-review-settings-btn" value="sr-negative-review-settings" name="sr_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>