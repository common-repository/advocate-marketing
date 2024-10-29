<?php
/*
 *      Settings > Positive Reveiws tab
 */
$social_sharing_providers = array(
    'google' => array('label' => 'Google'),
    'facebook' => array('label' => 'Facebook'),
    'twitter' => array('label' => 'Twitter'),
    'linkedin' => array('label' => 'LinkedIn'),
    'yelp' => array('label' => 'Yelp'),
    'zillow' => array('label' => 'Zillow'),
    'pinterest' => array('label' => 'Pinterest'),
    'instagram' => array('label' => 'Instagram'),
);
$sr_ss_default_text = get_option('swiftreviews_social_share_default_text');
?>
<form name="FrmSRPositiveSettings" id="FrmSRPositiveSettings" method="post">
    <table class="form-table" id="tbl-positive-reviews">
        <tr>
            <th><label>Referrals section text: </label></th>
            <td>
                <?php
                $referral_settings = array('editor_height' => 250, 'textarea_rows' => 12, 'teeny' => true, 'media_buttons' => false, 'quicktags' => true, 'textarea_name' => 'swiftreviews_referral_section_html',);
                wp_editor(stripslashes($get_swiftreviews_referral_section_html), 'referral_section_id', $referral_settings);
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="swiftreviews_phone_onoff">Get Phone #'s if Possible <span class="dashicons dashicons-editor-help ttip"  title="Only recommended if you are going to actually call them i.e. business or professional services. Not recommended for e-commerce stores or online retailers."></span> </label></th>
            <td>
                <?php $phoneOnOff = ($get_swiftreviews_phone == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" <?php echo $phoneOnOff; ?> name="swiftreviews_phone" id="swiftreviews_phone_onoff" />
            </td>
        </tr>
        <tr>
            <th><label for="sr_social_sharing_default_text">Social Share Default Text</label></th>
            <td><textarea id="sr_social_sharing_default_text" name="sr_social_sharing_default_text" rows="5" cols="60"><?php echo stripslashes($sr_ss_default_text); ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="widefat fixed striped tbl-sr-social-share" id="tbl-sr-social-share">
                    <thead>
                        <tr>
                            <th width="30%">&nbsp;Providers</th>
                            <th width="15%">Status</th>
                            <th width="55%">URL of your business profile on that network</th>
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
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                <?php wp_nonce_field('save_swiftreviews_positive_settings', 'save_swiftreviews_positive_settings'); ?>
                <button type="submit" class="button-primary" id="sr-positive-review-settings-btn" value="sr-positive-review-settings" name="sr_settings">Save Settings</button>
            </th>
        </tr>
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