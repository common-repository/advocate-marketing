<?php
/*
 *      Coupon / Discount
 */
if (isset($_POST['save_swiftreviews_coupon_settings']) && wp_verify_nonce($_POST['save_swiftreviews_coupon_settings'], 'save_swiftreviews_coupon_settings')) {

    $swiftreviews_coupon_discount = sanitize_text_field(!empty($_POST['swiftreviews_coupon_discount']) ? 1 : 0);
    $swiftreviews_coupon_discount_html = wp_kses_post($_POST['swiftreviews_coupon_discount_html']);

    $update1 = update_option('swiftreviews_coupon_discount', $swiftreviews_coupon_discount);
    $update2 = update_option('swiftreviews_coupon_discount_html', $swiftreviews_coupon_discount_html);

    if ($update1 || $update2) {
        wp_redirect(admin_url("admin.php?page=swift-reviews&update=1&tab=sr-coupon-discount-settings"));
        die;
    }
}
?>
<form name="FrmSRCouponSettings" id="FrmSRCouponSettings" method="post">
    <table class="form-table" id="tbl-coupon">
        <tr>
            <th><label for="swiftreviews_coupon_discount_onoff">Coupon / Discount</label></th>
            <td>
                <?php $couponOnOff = ($get_swiftreviews_coupon_discount == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" <?php echo $couponOnOff; ?> name="swiftreviews_coupon_discount" id="swiftreviews_coupon_discount_onoff" />
            </td>
        </tr>
        <tr class="hide-me" style="<?php echo (($get_swiftreviews_coupon_discount == 1) ? 'display: table-row;' : 'display:none'); ?>">
            <th><label>Coupon/Discount section text: </label></th>
            <td>
                <?php
                $coupon_discount_settings = array('editor_height' => 250, 'textarea_rows' => 12, 'teeny' => true, 'media_buttons' => false, 'quicktags' => true, 'textarea_name' => 'swiftreviews_coupon_discount_html',);
                wp_editor(stripslashes($get_swiftreviews_coupon_discount_html), 'swiftreviews_coupon_discount_html_id', $coupon_discount_settings);
                ?>
            </td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                <?php wp_nonce_field('save_swiftreviews_coupon_settings', 'save_swiftreviews_coupon_settings'); ?>
                <button type="submit" class="button-primary" id="sr-coupon-discount-settings-btn" value="sr-coupon-discount-settings" name="sr_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>