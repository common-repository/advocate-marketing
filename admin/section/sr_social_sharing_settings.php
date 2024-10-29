<?php
/*
 *      Settings > Social Sharing tab
 */
$social_sharing_providers = array(
    'google' => array('label' => 'Google'),
    'facebook' => array('label' => 'Facebook'),
    'twitter' => array('label' => 'Twitter'),
    'linkedin' => array('label' => 'LinkedIn'),
    'pinterest' => array('label' => 'Pinterest'),
    'instagram' => array('label' => 'Instagram'),
);
if (isset($_POST['save_swiftreviews_social_share_settings']) && wp_verify_nonce($_POST['save_swiftreviews_social_share_settings'], 'save_swiftreviews_social_share_settings')) {
    $update_flag = false;
    foreach ($social_sharing_providers as $ss_prov_key => $ss_prov_val):

        $sr_ss_prov = "social_share_" . $ss_prov_key;
        $sr_ss_prov_url = "social_share_" . $ss_prov_key . "_url";
        $ss_provider = (isset($_POST[$sr_ss_prov]) && !empty($_POST[$sr_ss_prov])) ? 1 : 0;
        $ss_provider_url = (isset($_POST[$sr_ss_prov_url]) && !empty($_POST[$sr_ss_prov_url])) ? esc_url_raw($_POST[$sr_ss_prov_url]) : "";

        $update_prov = update_option('swiftreviews_' . $sr_ss_prov, sanitize_text_field($ss_provider));
        $update_url = update_option('swiftreviews_' . $sr_ss_prov_url, $ss_provider_url);
        if ($update_prov || $update_url) {
            $update_flag = true;
        }

    endforeach;
    $update_default_text = update_option('swiftreviews_social_share_default_text', sanitize_text_field($_POST['sr_social_sharing_default_text']));

    if ($update_flag || $update_default_text) {
        wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-social-sharing-settings&update=1"));
        die;
    }
}

$sr_ss_default_text = get_option('swiftreviews_social_share_default_text');
?>
<form name="FrmSRSocialSharingSettings" id="FrmSRSocialSharingSettings" method="post">
    <table class="form-table">
        <tr>
            <th><label for="sr_social_sharing_default_text">Social Share Default Text</label></th>
            <td><textarea id="sr_social_sharing_default_text" name="sr_social_sharing_default_text" rows="5" cols="60"><?php echo stripslashes($sr_ss_default_text); ?></textarea></td>
        </tr>
    </table>
    <table class="widefat fixed striped tbl-sr-social-share" id="tbl-sr-social-share">
        <thead>
            <tr>
                <th width="30%">Providers</th>
                <th width="15%">Status</th>
                <th width="55%">URL to your profile / review page</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($social_sharing_providers as $ss_pro_key => $ss_pro_val): ?>
                <?php
                $sr_ss_pro = "social_share_" . $ss_pro_key;
                $sr_ss_pro_url = "social_share_" . $ss_pro_key . "_url";
                $sr_ss_pro_val = get_option("swiftreviews_" . $sr_ss_pro);
                $sr_ss_pro_url_val = get_option("swiftreviews_" . $sr_ss_pro_url);
                ?>
                <tr>
                    <td><?php echo $ss_pro_val['label']; ?></td>
                    <td>
                        <?php $ss_pro_key_flag = ($sr_ss_pro_val == 1) ? 'checked="checked"' : ''; ?>
                        <input type="checkbox" class="syn_provider" value="1" data-ontext="ON" data-offtext="OFF" <?php echo $ss_pro_key_flag; ?> name="<?php echo $sr_ss_pro; ?>" id="<?php echo $sr_ss_pro; ?>" />
                    </td>
                    <td><input type="text" name="<?php echo $sr_ss_pro_url; ?>" id="<?php echo $sr_ss_pro_url; ?>" value="<?php echo $sr_ss_pro_url_val; ?>" class="sr_social_share_provider_url regular-text" style="<?php echo ($sr_ss_pro_val != 1) ? 'display: none;' : ''; ?>" placeholder="http://" /></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <th colspan="3" style="text-align: center;">
                    <?php wp_nonce_field('save_swiftreviews_social_share_settings', 'save_swiftreviews_social_share_settings'); ?>
                    <button type="submit" class="button-primary" id="sr-social-share-settings" value="sr-social-share-settings" name="sr_social_share_btn">Save Settings</button>
                </th>
            </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.tbl-sr-social-share :checkbox').each(function() {
            jQuery(this).rcSwitcher({
                autoFontSize: true
            }).on({
                'turnon.rcSwitcher': function(e, data) {
                    jQuery(this).parent().next().find(".sr_social_share_provider_url").fadeIn();
                },
                'turnoff.rcSwitcher': function(e, data) {
                    jQuery(this).parent().next().find(".sr_social_share_provider_url").fadeOut();
                }
            });
        });

    });
</script>