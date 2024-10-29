<?php
/*
 *      Settings > Photo Contest tab
 */

?>
<form name="FrmSRPhotoContestSettings" id="FrmSRPhotoContestSettings" method="post">
    <table class="form-table" id="tbl-photo-contest">
        <tr>
            <th><label for="swiftreviews_upsell">Upsell Photo Contest / Video Testimonials <span class="dashicons dashicons-editor-help ttip"  title="After a positive review, ask for a video testimonial or photo, often with some small contest-style thank-you prize."></span> </label></th>
            <td>
                <?php $upsellOnOff = ($get_swiftreviews_upsell == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" <?php echo $upsellOnOff; ?> name="swiftreviews_upsell" id="swiftreviews_upsell_onoff" />
            </td>
        </tr>
        <tr class="hide-me" style="<?php echo (($get_swiftreviews_upsell == 1) ? 'display: table-row;' : 'display:none'); ?>">
            <th><label for="swiftreviews_photo_video_contest_form_id">Photo Contest / Video Testimonials Form ID: </label></th>
            <td>
                <input type="text" value="<?php echo stripslashes($get_swiftreviews_photo_video_contest_form_id); ?>" id="swiftreviews_photo_video_contest_form_id" name="swiftreviews_photo_video_contest_form_id" />
            </td>
        </tr>
        <tr class="hide-me" style="<?php echo (($get_swiftreviews_upsell == 1) ? 'display: table-row;' : 'display:none'); ?>">
            <th><label for="swiftreviews_photo_video_contest_title">Photo Contest / Video Testimonials Title: </label></th>
            <td>
                <input type="text" value="<?php echo stripslashes($get_swiftreviews_photo_video_contest_title); ?>" id="swiftreviews_photo_video_contest_title" name="swiftreviews_photo_video_contest_title" class="regular-text" />
            </td>
        </tr>
        <tr class="hide-me" style="<?php echo (($get_swiftreviews_upsell == 1) ? 'display: table-row;' : 'display:none'); ?>">
            <th><label>Photo / Video Form ID + Photo Contest Text: </label></th>
            <td>
                <?php
                $photo_video_settings = array('editor_height' => 250, 'textarea_rows' => 12, 'teeny' => true, 'media_buttons' => false, 'quicktags' => true, 'textarea_name' => 'swiftreviews_photo_video_contest_html',);
                wp_editor(stripslashes($get_swiftreviews_photo_video_contest_html), 'referral_photo_video_id', $photo_video_settings);
                ?>
            </td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                <?php wp_nonce_field('save_swiftreviews_photocontest_settings', 'save_swiftreviews_photocontest_settings'); ?>
                <button type="submit" class="button-primary" id="sr-photo-contest-settings-btn" value="sr-photo-contest-settings" name="sr_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>