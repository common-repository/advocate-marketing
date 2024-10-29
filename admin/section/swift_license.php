<?php
/*
 *      License section
 */
/**/
if (isset($_POST['swiftreviews_license_nonce']) && wp_verify_nonce($_POST['swiftreviews_license_nonce'], 'swiftreviews_license_nonce')) {
    $sr_license_flag = sanitize_text_field(!empty($_POST['swiftreviews_license'])) == 1 ? "pro" : "lite";
    $update1 = update_option("swiftreviews_license", $sr_license_flag);

    $sr_license_email = (isset($_POST['swiftreviews_pro_license']) && !empty($_POST['swiftreviews_pro_license'])) ? esc_attr($_POST['swiftreviews_pro_license']) : "";
    $update2 = update_option("swiftreviews_pro_license_email", $sr_license_email);

    if ($update1 || $update2) {
        wp_redirect(admin_url("admin.php?page=swift-reviews&update=1"));
        die;
    }
}

$license_flag = (get_option("swiftreviews_license") == "pro") ? 'checked="checked"' : '';
$license_toggle = (get_option("swiftreviews_license") == "pro") ? '' : 'pro-license-email';
$sr_license_email_required = (get_option("swiftreviews_license") == "pro") ? 'required="required"' : '';
$sr_license_email = get_option("swiftreviews_pro_license_email");
?>
<div class="inner_content">
    <div class="sc-license-wrap bg-light-yellow">
        <h4>License: Now running the <input type="checkbox" value="1" data-ontext="Pro" data-offtext="Lite" name="swiftreviews_license" id="swiftreviews_license" <?php echo $license_flag; ?>> Version.</h4>
        <div class="pro-license-wrap <?php echo $license_toggle; ?>">
            <form id="frmReviewsProLicense" method="post">
                <?php wp_nonce_field('swiftreviews_license_nonce', 'swiftreviews_license_nonce'); ?>
                <input type="text" name="swiftreviews_pro_license" id="swiftreviews_pro_license" class="regular-text" <?php echo $sr_license_email_required; ?> value="<?php echo $sr_license_email; ?>" />
                <button type="submit" id="btn_event_pro_license" class="button button-pro-license"><span class="dashicons dashicons-unlock"></span> Connect / Enable</button>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        /* License togggle*/
        if (jQuery("#swiftreviews_license").length > 0) {
            jQuery('#swiftreviews_license').rcSwitcher().on({
                width: 80,
                height: 24,
                autoFontSize: true,
                'turnon.rcSwitcher': function (e, dataObj) {
                    jQuery(".pro-license-wrap").removeClass('pro-license-email');
                    jQuery("#swiftreviews_pro_license").attr('required', 'required');
                },
                'turnoff.rcSwitcher': function (e, dataObj) {
                    jQuery(".pro-license-wrap").addClass('pro-license-email');
                    jQuery("#swiftreviews_pro_license").removeAttr('required');
                }
            });
        }
    });
</script>