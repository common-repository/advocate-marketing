<?php
/*
 *      Settings > Syndication tab
 */
$syndication_providers = array(
    'google' => 'Google',
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
    'bing' => 'Bing Places',
    'linkedin' => 'LinkedIn',
    'bbb' => 'BBB',
    'yahoo' => 'Yahoo Local',
    'amazon' => 'Amazon',
    'yelp' => 'Yelp',
    'zillow' => 'Zillow',
    'angie' => 'Angie\'s List',
    'consumer' => 'Consumer Reports',
    'citysearch' => 'City Search',
    'dexknows' => 'Dex Knows',
    'realself' => 'RealSelf',
    'tripadvisor' => 'Tripadvisor',
    'g2crowd' => 'G2 Crowd',
    'trustradius' => 'TrustRadius',
    'glassdoor' => 'GlassDoor',
    'wordpress' => 'Wordpress',
);
if (isset($_POST['save_swiftreviews_syndication_settings']) && wp_verify_nonce($_POST['save_swiftreviews_syndication_settings'], 'save_swiftreviews_syndication_settings')) {
    $update_flag = false;
    foreach ($syndication_providers as $syn_key => $syn_val):
        $syn_prov_key = "syn_" . $syn_key;
        $syn_prov_url = "syn_" . $syn_key . "_url";
        $syn_prov = (isset($_POST[$syn_prov_key]) && !empty($_POST[$syn_prov_key])) ? 1 : 0;
        $syn_prov_url_val = (isset($_POST[$syn_prov_url]) && !empty($_POST[$syn_prov_url])) ? esc_url_raw($_POST[$syn_prov_url]) : "";

        $update_prov = update_option('swiftreviews_' . $syn_prov_key, sanitize_text_field($syn_prov));
        $update_url = update_option('swiftreviews_' . $syn_prov_url, $syn_prov_url_val);
        if ($update_prov || $update_url) {
            $update_flag = true;
        }
    endforeach;

    if ($update_flag) {
        wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-syndication-settings&update=1"));
        die;
    }
}
?>
<form name="FrmSRSyndicationSettings" id="FrmSRSyndicationSettings" method="post">
    <table class="widefat fixed striped tbl-syndication" id="tbl-syndication">
        <thead>
            <tr>
                <th width="30%">Providers</th>
                <th width="15%">Status</th>
                <th width="55%">URL to your profile / review page</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($syndication_providers as $syn_key => $syn_val): ?>
                <?php
                $syn_prov = "syn_" . $syn_key;
                $syn_prov_url = "syn_" . $syn_key . "_url";
                $syn_prov_val = get_option("swiftreviews_" . $syn_prov);
                $syn_prov_url_val = get_option("swiftreviews_" . $syn_prov_url);
                ?>
                <tr>
                    <td><?php echo $syn_val; ?></td>
                    <td>
                        <?php $syn_key_flag = ($syn_prov_val == 1) ? 'checked="checked"' : ''; ?>
                        <input type="checkbox" class="syn_provider" value="1" data-ontext="ON" data-offtext="OFF" <?php echo $syn_key_flag; ?> name="<?php echo $syn_prov; ?>" id="<?php echo $syn_prov; ?>" />
                    </td>
                    <td><input type="text" name="<?php echo $syn_prov_url; ?>" id="<?php echo $syn_prov_url; ?>" value="<?php echo $syn_prov_url_val; ?>" class="syn_provider_url regular-text" style="<?php echo ($syn_prov_val != 1) ? 'display: none;' : ''; ?>" placeholder="http://" /></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <th colspan="3" style="text-align: center;">
                    <?php wp_nonce_field('save_swiftreviews_syndication_settings', 'save_swiftreviews_syndication_settings'); ?>
                    <button type="submit" class="button-primary" id="sr-syndication-settings" value="sr-syndication-settings" name="sr_settings">Save Settings</button>
                </th>
            </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.tbl-syndication :checkbox').each(function() {
            jQuery(this).rcSwitcher({
                autoFontSize: true
            }).on({
                'turnon.rcSwitcher': function(e, data) {
                    jQuery(this).parent().next().find(".syn_https").fadeIn();
                    jQuery(this).parent().next().find(".syn_provider_url").fadeIn();
                },
                'turnoff.rcSwitcher': function(e, data) {
                    jQuery(this).parent().next().find(".syn_https").fadeOut();
                    jQuery(this).parent().next().find(".syn_provider_url").fadeOut();
                }
            });
        });

    });
</script>