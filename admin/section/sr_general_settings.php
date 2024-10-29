<?php
/*
 *      Settings > General Setting tab
 */
wp_enqueue_script('swift-form-jstz', SWIFTREVIEWS__PLUGIN_URL . 'admin/js/jstz.min.js', '', '', true);
wp_enqueue_style('swift-dashboard', SWIFTREVIEWS__PLUGIN_URL . 'admin/css/swift-dashboard.css', '', '', '');
wp_enqueue_script(SWIFTREVIEWS_PLUGIN_PREFIX . 'dashboard-script', SWIFTREVIEWS__PLUGIN_URL . 'admin/js/swift-dashboard.js', array('jquery'), '', true);


$OnOff = ($get_auto_publish_onoff !== false) ? (($get_auto_publish_onoff == 1) ? 'checked="checked"' : '') : 'checked="checked"';
$sr_anonymous_reviews_flag = ($get_anonymous_review_flag !== false) ? (($get_anonymous_review_flag == 1) ? 'checked="checked"' : '') : 'checked="checked"';
$sr_date_flag = ($get_swiftreviews_date_flag !== false) ? (($get_swiftreviews_date_flag == 1) ? 'checked="checked"' : '') : 'checked="checked"';
?>
<form name="FrmSRGeneralSettings" id="FrmSRGeneralSettings" method="post">
    <table class="form-table">
        <tr>
            <td colspan="2"><!--License block-->
                <?php include 'swift_license.php'; ?>
                <!--License block--></td>
        </tr>
        <tr>
            <th><label for="swiftreviews_swiftcloud_referrals_form_id"><a href="https://crm.swiftcrm.com/drive/" target="_blank">Main SwiftCloud Form ID</a> <span class="dashicons dashicons-editor-help ttip"  title="Required - this is is the web-form and actions for positive reviews only."></span> </label></th>
            <td>
                <input type="text" value="<?php echo stripslashes($get_sc_referrals_form_id); ?>" id="swiftreviews_swiftcloud_referrals_form_id" name="swiftreviews_swiftcloud_referrals_form_id" />
            </td>
        </tr>
<!--        <tr>
            <th><label for="swiftreviews_helpdesk_form_id"><a href="https://crm.swiftcrm.com/drive/" target="_blank">SwiftCloud Form ID for Negative Reviews</a> <span class="dashicons dashicons-editor-help ttip"  title='Typically fed to Swift Help Desk so you can investigate and try to make it right before the user complains online, damaging your reputation.'></span> </label></th>
            <td>
                <input type="text" value="<?php echo stripslashes($get_helpdesk_form_id); ?>" id="swiftreviews_helpdesk_form_id" name="swiftreviews_helpdesk_form_id" />
            </td>
        </tr>-->
        <tr>
            <td colspan="2" style="padding-left: 0;">
                Consider any review
                <select id="swiftreviews_auto_publish_positive_reviews" name="swiftreviews_auto_publish_positive_reviews">
                    <option <?php selected($get_positive_reviews, "0.5"); ?> value="0.5">0.5 of 5 or 1 of 10</option>
                    <option <?php selected($get_positive_reviews, "1"); ?> value="1">1.0 of 5 or 2 of 10</option>
                    <option <?php selected($get_positive_reviews, "1.5"); ?> value="1.5">1.5 of 5 or 3 of 10</option>
                    <option <?php selected($get_positive_reviews, "2"); ?> value="2">2.0 of 5 or 4 of 10</option>
                    <option <?php selected($get_positive_reviews, "2.5"); ?> value="2.5">2.5 of 5 or 5 of 10</option>
                    <option <?php selected($get_positive_reviews, "3"); ?> value="3">3.0 of 5 or 6 of 10</option>
                    <option <?php selected($get_positive_reviews, "3.5"); ?> value="3.5">3.5 of 5 or 7 of 10</option>
                    <option <?php selected($get_positive_reviews, "4"); ?> value="4">4.0 of 5 or 8 of 10</option>
                    <option <?php selected($get_positive_reviews, "4.5"); ?> value="4.5">4.5 of 5 or 9 of 10</option>
                    <option <?php selected($get_positive_reviews, "5"); ?> value="5">5.0 of 5 or 10 of 10</option>
                </select> or higher to be positive.<span class="dashicons dashicons-editor-help ttip"  title="For anything lower than this rating, we'll open a trouble ticket, try to make the user feel heard. We recommend doing all you can so they don't share online. For anything this rating or higher, we'll try to get them to share their review and get referrals for you."></span>
                Auto-Publish Positive Reviews is <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swiftreviews_auto_publish_reviews_on_off" id="sreviews_onoff" <?php echo $OnOff; ?>>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left: 0">
                Open a helpdesk ticket for any review
                <select id="swiftreviews_auto_publish_negative_reviews" name="swiftreviews_auto_publish_negative_reviews">
                    <option <?php selected($get_negative_reviews, "0.5"); ?> value="0.5">0.5 of 5 or 1 of 10</option>
                    <option <?php selected($get_negative_reviews, "1"); ?> value="1">1.0 of 5 or 2 of 10</option>
                    <option <?php selected($get_negative_reviews, "1.5"); ?> value="1.5">1.5 of 5 or 3 of 10</option>
                    <option <?php selected($get_negative_reviews, "2"); ?> value="2">2.0 of 5 or 4 of 10</option>
                    <option <?php selected($get_negative_reviews, "2.5"); ?> value="2.5">2.5 of 5 or 5 of 10</option>
                    <option <?php selected($get_negative_reviews, "3"); ?> value="3">3.0 of 5 or 6 of 10</option>
                    <option <?php selected($get_negative_reviews, "3.5"); ?> value="3.5">3.5 of 5 or 7 of 10</option>
                    <option <?php selected($get_negative_reviews, "4"); ?> value="4">4.0 of 5 or 8 of 10</option>
                    <option <?php selected($get_negative_reviews, "4.5"); ?> value="4.5">4.5 of 5 or 9 of 10</option>
                    <option <?php selected($get_negative_reviews, "5"); ?> value="5">5.0 of 5 or 10 of 10</option>
                </select> or lower
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left: 0;">
                <div class="sr-border-block">
                    <h3>Style & Mode Settings</h3>
                    <label class="sr-radios-label" for="businessMode"><input <?php echo checked($get_swift_review_mode, "business_mode"); ?> type="radio" value="business_mode" class="sr-radios" name="swift_review_mode" id="businessMode" />Business Mode: Referrals then Social / Syndication <span class="dashicons dashicons-editor-help ttip"  title="If referrals are most important, get them first. Typically used for professional services i.e. real estate."></span></label>
                    <label class="sr-radios-label" for="consumerMode"><input <?php echo checked($get_swift_review_mode, "consumer_mode"); ?> type="radio" value="consumer_mode" class="sr-radios" name="swift_review_mode" id="consumerMode" />Consumer Mode: Social Syndication then Referrals <span class="dashicons dashicons-editor-help ttip"  title="If online reputation is most important, get that first. Typically used for e-commerce or online reputation firewalling."></span></label>
                    <label class="sr-radios-label"for="referralsOnly"><input <?php echo checked($get_swift_review_mode, "referrals_only"); ?> type="radio" value="referrals_only" class="sr-radios" name="swift_review_mode" id="referralsOnly" />Referrals Only (No Social) <span class="dashicons dashicons-editor-help ttip"  title="If you don't want reviews shared, this will still work to get you direct referrals."></span></label>
                    <label class="sr-radios-label" for="socilaSyandOnly"><input <?php echo checked($get_swift_review_mode, "social_syndication_only"); ?> type="radio" value="social_syndication_only" class="sr-radios" name="swift_review_mode" id="socilaSyandOnly" />Social / Syndication Only (No Referrals) <span class="dashicons dashicons-editor-help ttip" title="Often used for e-commerce if you're not going to follow up, or medical where privacy of referrals may not allow email."></span></label>
                    <label class="sr-radios-label" for="off"><input <?php echo checked($get_swift_review_mode, "off"); ?> type="radio" value="off" class="sr-radios" name="swift_review_mode" id="off" />Off (Feedback Only Mode) - Both Social & Syndication Off <span class="dashicons dashicons-editor-help ttip" title="Used for gathering feedback only."></span></label>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="swiftreviews_anonymous_reviews">Allow Anonymous Reviews</label></th>
            <td><input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swiftreviews_anonymous_review_flag" id="sr_anonymous_reviews_flag" <?php echo $sr_anonymous_reviews_flag; ?>></td>
        </tr>
        <tr>
            <th><label for="swiftreviews_review_form_page">Review Form Page: </label></th>
            <td>
                <select name="swiftreviews_review_form_page" id="swiftreviews_review_form_page">
                    <option value="">--Select Page--</option>
                    <?php
                    if ($pages) {
                        foreach ($pages as $page) {
                            ?>
                            <option <?php selected($get_swiftreviews_review_form_page, $page->ID); ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                            <?php
                        }//First if
                    }// First loop
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="swiftreviews_listing_page">Reviews Listing Page: </label></th>
            <td>
                <select name="swiftreviews_listing_page" id="swiftreviews_listing_page">
                    <option value="">--Select Page--</option>
                    <?php
                    if ($pages) {
                        foreach ($pages as $page) {
                            ?>
                            <option <?php selected($get_swiftreviews_listing_page, $page->ID); ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="swiftreviews_final_feedback_confirmation_page">Final Feedback Confirmation Page: </label></th>
            <td>
                <select name="swiftreviews_final_feedback_confirmation_page" id="swiftreviews_final_feedback_confirmation_page">
                    <option value="0">--Select Page--</option>
                    <?php
                    if ($pages) {
                        foreach ($pages as $page) {
                            ?>
                            <option <?php selected($get_final_feedback_confirmation_page, $page->ID); ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                            <?php
                        }//First if
                    }// First loop
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sr_logo_url">Enter a URL or Upload logo <span class="dashicons dashicons-editor-help ttip" title="Upload image 250x60px dimensions in JPEG, PNG or GIF format. This will be used for Microformat data."></span></label></th>
            <td>
                <input id="sr_logo_url" type="text" size="36" name="sr_logo_url" class="regular-text" value="<?php echo esc_url($swiftreview_microformat_logo); ?>" placeholder="URL" />
                <input id="sr_upload_image_button" class="button button-primary" type="button" value="Upload Image" />
            </td>
        </tr>
        <tr>
            <th><label for="sr_date_flag">Show review date on listing</label></th>
            <td><input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="sr_date_flag" id="sr_date_flag" <?php echo $sr_date_flag; ?>></td>
        </tr>
        <tr>
            <th><label for="sr_review_per_page">Review per page</label></th>
            <td><input type="text" value="<?php echo stripslashes($swiftreviews_review_per_page); ?>" id="swiftreviews_review_per_page" name="swiftreviews_review_per_page" /></td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                <?php wp_nonce_field('save_swiftreviews_settings', 'save_swiftreviews_settings'); ?>
                <button type="submit" class="button-primary" id="sr-general-settings-btn" value="sr-general-settings" name="sr_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>