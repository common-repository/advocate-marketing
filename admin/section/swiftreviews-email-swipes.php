<?php
/*
 * Swift Reviews Email Swipes
 */

function swiftreviews_email_swipes_callback() {
    global $wpdb;
    $table_email_swipes_template = $wpdb->prefix . 'sr_email_swipes_template';

    if (isset($_POST['save_swiftreviews_email_swipes']) && wp_verify_nonce($_POST['save_swiftreviews_email_swipes'], 'save_swiftreviews_email_swipes')) {

        $email_subject = sanitize_text_field($_POST['swiftreviews_es_subject']);
        $email_body = wp_kses_post($_POST['swiftreviews_es_content']);
        $template_id = sanitize_text_field($_POST['sr_es_template_id']);

        if (!empty($email_body) && !empty($template_id)) {
            $update = $wpdb->query($wpdb->prepare(
                            "UPDATE $table_email_swipes_template SET
                                `es_subject`='%s',`es_content`='%s' WHERE `es_id`=%d", $email_subject, $email_body, $template_id
                    )
            );
            if ($update) {
                wp_redirect(admin_url("admin.php?page=swiftreviews_email_swipes&update=1"));
                exit;
            }
        }
    }
    $all_templates = $wpdb->get_results("SELECT * FROM `$table_email_swipes_template`");

    $template_id = (isset($_GET['template']) && !empty($_GET['template'])) ? sanitize_text_field($_GET['template']) : 1;
    $template = $wpdb->get_row("SELECT * FROM `$table_email_swipes_template` WHERE `es_id`=$template_id");
    ?>
    <div class="wrap">
        <h2 class="inline">Swift Reviews Email Swipes</h2>
        <?php if (!empty($all_templates)) { ?>
            <div class="email-swipes-version">
                <select id="sr-template-selectbox" data-url='<?php echo admin_url("admin.php?page=swiftreviews_email_swipes"); ?>'>
                    <?php foreach ($all_templates as $t) { ?>
                        <option <?php echo selected($template_id, $t->es_id); ?> value="<?php echo $t->es_id; ?>"><?php echo $t->es_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
        <div class="clear"></div>
        <hr/>
        <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
            <div id="message" class="notice notice-success is-dismissible below-h2">
                <p>Setting updated successfully.</p>
            </div>
            <?php
        }
        ?>

        <?php
        if (empty($template)) {
            echo "<h2>No template found!</h2>";
            return;
        }
        ?>
        <div class="sr-es-tabs">
            <ul>
                <li data-content='#sr-es-preview' class="sr-active">Preview</li>
                <li data-content='#sr-es-edit'>Edit</li>
            </ul>
        </div>
        <div class="inner_content sr-tab-content" id="sr-es-edit" style="display: none;">
            <form name="FrmSwiftReviewsEmailSwipes" id="FrmSwiftReviewsEmailSwipes" method="post">
                <table  id="tbl-positive-reviews" style="width: 100%;">
    <!--                    <tr>
                        <th><label for="swiftreviews_es_subject">Subject : </label></th>
                        <td>
                            <input type="text" class="regular-text" value="<?php //echo $template->es_subject;                   ?>" id="swiftreviews_es_subject" name="swiftreviews_es_subject" required="required" />
                        </td>
                    </tr>-->
                    <tr>
                        <td style="padding-bottom: 0;"><label><b>Edit:</b></label></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 0;">
                            <?php
                            $get_swiftreviews_es_content = stripslashes($template->es_content);
                            $referral_settings = array('editor_height' => 350, 'teeny' => true, 'media_buttons' => false, 'quicktags' => true, 'textarea_name' => 'swiftreviews_es_content');
                            wp_editor(stripslashes($get_swiftreviews_es_content), 'es_content_id', $referral_settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" value="<?php echo stripslashes($template->es_id); ?>" name="sr_es_template_id" id="sb_email_template_id"/>
                            <?php wp_nonce_field('save_swiftreviews_email_swipes', 'save_swiftreviews_email_swipes'); ?>
                            <input type="submit" class="button-primary" value="Save Settings" name="swiftreviews_email_swipes" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="inner_content sr-tab-content sr-active" id="sr-es-preview">
            <table  id="tbl-positive-reviews" style="width: 100%;">
                <tr>
                    <td>
                        <div class="sr-copy-section">
                            <div class="sr-copy-text" id="sr-copy-text" onclick="selectText('sr-copy-text');">
                                <?php
                                if (!empty($template->es_content)) {
                                    echo stripslashes($template->es_content);
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}