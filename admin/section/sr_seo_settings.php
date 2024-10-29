<?php
/**
 *      SEO settings
 */
$sr_seo_slug = (get_option('sr_seo_slug')) ? get_option('sr_seo_slug') : "reviews";
?>
<div class="wrap">
    <h2 class="sc-page-title">SEO Settings</h2>
    <hr/>
    <div class="inner_content">
        <form name="FrmSwiftSEOSettings" id="FrmSwiftSEOSettings" method="post">
            <table class="form-table">
                <tr>
                    <th><label for="sr_seo_slug">SEO Slug</label></th>
                    <td><?php echo home_url('/'); ?><input type="text" id="sr_seo_slug" name="sr_seo_slug" value="<?php echo $sr_seo_slug; ?>" placeholder="reviews" />/CPT-title-slug-here</td>
                </tr>
                <tr>
                    <th colspan="2">
                        <?php wp_nonce_field('save_sr_seo_settings', 'save_sr_seo_settings'); ?>
                        <button type="submit" class="button-primary" id="save_sr_seo_settings_btn" value="sr_seo_settings" name="btn_sr_seo_settings">Save Settings</button>
                    </th>
                </tr>
            </table>
        </form>
    </div>
</div>