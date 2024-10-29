<?php
/*
 * Swift Reviews settings page
 */

add_action('init', 'swiftreview_settings_post_init');

function swiftreview_settings_post_init() {
    // General Setting tab ***********************************
    if (isset($_POST['save_swiftreviews_settings']) && wp_verify_nonce($_POST['save_swiftreviews_settings'], 'save_swiftreviews_settings')) {
        $reviews_onoff = sanitize_text_field(!empty($_POST['swiftreviews_auto_publish_reviews_on_off']) ? 1 : 0);
        $anonymous_review_flag = sanitize_text_field(!empty($_POST['swiftreviews_anonymous_review_flag']) ? 1 : 0);
        $positive_review = sanitize_text_field($_POST['swiftreviews_auto_publish_positive_reviews']);
        $swiftreviews_review_form_page = sanitize_text_field($_POST['swiftreviews_review_form_page']);
        $swiftreviews_listing_page = sanitize_text_field($_POST['swiftreviews_listing_page']);
        $feedback_confirmation_page = sanitize_text_field($_POST['swiftreviews_final_feedback_confirmation_page']);
        $negative_review = sanitize_text_field($_POST['swiftreviews_auto_publish_negative_reviews']);
        $swift_review_mode = sanitize_text_field($_POST['swift_review_mode']);
        $sc_referrals = sanitize_text_field($_POST['swiftreviews_swiftcloud_referrals_form_id']);
//    $helpdesk_form_id = sanitize_text_field($_POST['swiftreviews_helpdesk_form_id']);
        $swiftreview_microformat_logo = sanitize_text_field($_POST['sr_logo_url']);
        $swiftreview_date_flag = sanitize_text_field($_POST['sr_date_flag']);
        $swiftreviews_review_per_page = sanitize_text_field($_POST['swiftreviews_review_per_page']);

        $update1 = update_option('swiftreviews_auto_publish_reviews_on_off', $reviews_onoff);
        $update2 = update_option('swiftreviews_auto_publish_positive_reviews', $positive_review);
        $update3 = update_option('swiftreviews_review_form_page', $swiftreviews_review_form_page);
        $update4 = update_option('swiftreviews_listing_page', $swiftreviews_listing_page);
        $update5 = update_option('swiftreviews_final_feedback_confirmation_page', $feedback_confirmation_page);
        $update6 = update_option('swiftreviews_auto_publish_negative_reviews', $negative_review);
        $update7 = update_option('swift_review_mode', $swift_review_mode);
        $update8 = update_option('swiftreviews_swiftcloud_referrals_form_id', $sc_referrals);
        $update9 = update_option('swiftreviews_anonymous_review_flag', $anonymous_review_flag);
//    $update10 = update_option('swiftreviews_helpdesk_form_id', $helpdesk_form_id);
        $update11 = update_option('swiftreview_microformat_logo', $swiftreview_microformat_logo);
        $update12 = update_option('swiftreview_date_flag', $swiftreview_date_flag);
        $update13 = update_option('swiftreviews_review_per_page', $swiftreviews_review_per_page);

        if ($update1 || $update2 || $update3 || $update4 || $update5 || $update6 || $update7 || $update8 || $update9 || $update11 || $update12 || $update13) {
            wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-general-settings&update=1"));
            die;
        }
    }

    // Positive Reviews tab ***********************************
    if (isset($_POST['save_swiftreviews_positive_settings']) && wp_verify_nonce($_POST['save_swiftreviews_positive_settings'], 'save_swiftreviews_positive_settings')) {
        $swiftreviews_referral_section_html = wp_kses_post($_POST['swiftreviews_referral_section_html']);
        $swiftreviews_phone_flag = sanitize_text_field(!empty($_POST['swiftreviews_phone']) ? 1 : 0);

        $update3 = update_option('swiftreviews_referral_section_html', $swiftreviews_referral_section_html);
        $update4 = update_option('swiftreviews_phone', $swiftreviews_phone_flag);

        $update_flag = false;


        foreach ($social_sharing_providers as $ss_prov_key => $ss_prov_val) {
            $sr_ss_prov = "social_share_" . $ss_prov_key;
            $sr_ss_prov_url = "social_share_" . $ss_prov_key . "_url";
            $ss_provider = (isset($_POST[$sr_ss_prov]) && !empty($_POST[$sr_ss_prov])) ? 1 : 0;
            $ss_provider_url = (isset($_POST[$sr_ss_prov_url]) && !empty($_POST[$sr_ss_prov_url])) ? esc_url_raw($_POST[$sr_ss_prov_url]) : "";

            $update_prov = update_option('swiftreviews_' . $sr_ss_prov, sanitize_text_field($ss_provider));
            $update_url = update_option('swiftreviews_' . $sr_ss_prov_url, $ss_provider_url);
            if ($update_prov || $update_url) {
                $update_flag = true;
            }
        }
        $update_default_text = update_option('swiftreviews_social_share_default_text', sanitize_text_field($_POST['sr_social_sharing_default_text']));

        if ($update3 || $update4 || $update_flag) {
            wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-positive-review-settings&update=1"));
            die;
        }
    }

    // Negative Reviews tab ***********************************
    if (isset($_POST['save_swiftreviews_negative_settings']) && wp_verify_nonce($_POST['save_swiftreviews_negative_settings'], 'save_swiftreviews_negative_settings')) {
        $negative_redirect_page = sanitize_text_field($_POST['swiftreviews_negative_redirect_page']);
        $swiftreviews_feedback_section_html = wp_kses_post($_POST['swiftreviews_feedback_section_html']);

        $update1 = update_option('swiftreviews_negative_redirect_page', $negative_redirect_page);
        $update3 = update_option('swiftreviews_feedback_section_html', $swiftreviews_feedback_section_html);

        if ($update1 || $update3) {
            wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-negative-review-settings&update=1"));
            die;
        }
    }

    // Photo Contest tab ***********************************
    if (isset($_POST['save_swiftreviews_photocontest_settings']) && wp_verify_nonce($_POST['save_swiftreviews_photocontest_settings'], 'save_swiftreviews_photocontest_settings')) {
        $swiftreviews_upsell = sanitize_text_field(!empty($_POST['swiftreviews_upsell']) ? 1 : 0);
        $swiftreviews_photo_video_contest_form_id = sanitize_text_field($_POST['swiftreviews_photo_video_contest_form_id']);
        $swiftreviews_photo_video_contest_title = sanitize_text_field($_POST['swiftreviews_photo_video_contest_title']);
        $swiftreviews_photo_video_contest_html = wp_kses_post($_POST['swiftreviews_photo_video_contest_html']);

        $update1 = update_option('swiftreviews_upsell', $swiftreviews_upsell);
        $update2 = update_option('swiftreviews_photo_video_contest_form_id', $swiftreviews_photo_video_contest_form_id);
        $update3 = update_option('swiftreviews_photo_video_contest_title', $swiftreviews_photo_video_contest_title);
        $update4 = update_option('swiftreviews_photo_video_contest_html', $swiftreviews_photo_video_contest_html);

        if ($update1 || $update2 || $update3 || $update4) {
            wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-photo-contest-settings&update=1"));
            die;
        }
    }

    // Coupon / Discount tab ***********************************
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

    // Corner Widget tab ***********************************
    if (isset($_POST['save_swiftreviews_corner_widget']) && wp_verify_nonce($_POST['save_swiftreviews_corner_widget'], 'save_swiftreviews_corner_widget')) {

        $widget_onoff = sanitize_text_field(!empty($_POST['sr_widget_onoff']) ? 1 : 0);
        $widget_header_text = sanitize_text_field($_POST['sr_widget_header_text']);
        $widget_header_color = sanitize_text_field($_POST['sr_widget_header_color']);
        $widget_button_color = sanitize_text_field($_POST['sr_widget_button_color']);
        $widget_text_color = sanitize_text_field($_POST['sr_widget_text_color']);
        $widget_position = sanitize_text_field($_POST['sr_widget_position']);
        $widget_rating_style = sanitize_text_field($_POST['sr_widget_rating_style']);

        $update1 = update_option('swiftreviews_widget_onoff', $widget_onoff);
        $update2 = update_option('swiftreviews_widget_header_color', $widget_header_color);
        $update3 = update_option('swiftreviews_widget_button_color', $widget_button_color);
        $update4 = update_option('swiftreviews_widget_text_color', $widget_text_color);
        $update5 = update_option('swiftreviews_widget_position', $widget_position);
        $update6 = update_option('swiftreviews_widget_header_text', $widget_header_text);
        $update7 = update_option('swiftreviews_widget_rating_style', $widget_rating_style);

        if ($update1 || $update2 || $update3 || $update4 || $update5 || $update6 || $update7) {
            wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-corner-widget-settings&update=1"));
            die;
        }
    }

    // SEO settings tab ***********************************
    if (isset($_POST['save_sr_seo_settings']) && wp_verify_nonce($_POST['save_sr_seo_settings'], 'save_sr_seo_settings')) {
        $sr_seo_slug = sanitize_text_field($_POST['sr_seo_slug']);
        $update1 = update_option('sr_seo_slug', $sr_seo_slug);

        if ($update1) {
            wp_redirect(admin_url("admin.php?page=swift-reviews&tab=sr-seo-settings&update=1"));
            die;
        }
    }
}

function swiftreviews_settings_callback() {

    $get_positive_reviews = get_option("swiftreviews_auto_publish_positive_reviews");
    $get_anonymous_review_flag = get_option("swiftreviews_anonymous_review_flag");
    $get_negative_reviews = get_option("swiftreviews_auto_publish_negative_reviews");
//    $get_helpdesk_form_id = get_option("swiftreviews_helpdesk_form_id");
    $get_auto_publish_onoff = get_option("swiftreviews_auto_publish_reviews_on_off");
    $get_sc_referrals_form_id = get_option("swiftreviews_swiftcloud_referrals_form_id");
    $get_negative_redirect_page = get_option("swiftreviews_negative_redirect_page");
    $get_final_feedback_confirmation_page = get_option("swiftreviews_final_feedback_confirmation_page");
    $swiftreview_microformat_logo = get_option("swiftreview_microformat_logo");
    $get_swiftreviews_date_flag = (get_option("swiftreview_date_flag")) ? get_option("swiftreview_date_flag") : 1;
    $swiftreviews_review_per_page = get_option("swiftreviews_review_per_page");
    $get_swiftreviews_upsell = get_option("swiftreviews_upsell");
    $get_swiftreviews_photo_video_contest_form_id = get_option("swiftreviews_photo_video_contest_form_id");
    $get_swiftreviews_referral_section_html = get_option('swiftreviews_referral_section_html');
    $get_swiftreviews_feedback_section_html = get_option('swiftreviews_feedback_section_html');
    $get_swiftreviews_photo_video_contest_html = get_option('swiftreviews_photo_video_contest_html');
    $get_swiftreviews_review_form_page = get_option('swiftreviews_review_form_page');
    $get_swiftreviews_listing_page = get_option('swiftreviews_listing_page');
    $get_swiftreviews_photo_video_contest_title = get_option('swiftreviews_photo_video_contest_title');
    $get_swiftreviews_phone = get_option('swiftreviews_phone');
    $get_swiftreviews_coupon_discount_html = get_option('swiftreviews_coupon_discount_html');
    $get_swiftreviews_coupon_discount = get_option('swiftreviews_coupon_discount');
    $get_swift_review_mode = get_option('swift_review_mode');

    // Syndication
    $syn_google = $args = array(
        'sort_order' => 'ASC',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_value' => '',
        'child_of' => 0,
        'parent' => -1,
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    ?>
    <div class="wrap">
        <h2>RAVE - Reviews & Reputation Advocate & Viral Engagement System Settings</h2><hr/>
        <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
            <div id="message" class="notice is-dismissible notice-success below-h2">
                <p>Setting updated successfully.</p>
            </div>
            <?php
        }
        ?>
        <div class="inner_content" id="first-inner-content">
            <h2 class="nav-tab-wrapper" id="sr-setting-tabs">
                <a class="nav-tab custom-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == "sr-general-settings") ? 'nav-tab-active' : ''; ?>" id="sr-general-settings-tab" href="#sr-general-settings">General Settings</a>
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-positive-review-settings") ? 'nav-tab-active' : ''; ?>" id="sr-positive-review-tab" href="#sr-positive-review-settings">Positive Reviews</a>
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-negative-review-settings") ? 'nav-tab-active' : ''; ?>" id="sr-negative-review-tab" href="#sr-negative-review-settings">Negative Reviews</a>
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-purls-settings") ? 'nav-tab-active' : ''; ?>" id="sr-purls-tab" href="#sr-purls-settings">PURLs</a>
    <!--                <a class="nav-tab custom-tab <?php // echo ($_GET['tab'] == "sr-syndication-settings") ? 'nav-tab-active' : '';      ?>" id="sr-syndication-tab" href="#sr-syndication-settings">Syndication</a>-->
                <!--<a class="nav-tab custom-tab <?php // echo ($_GET['tab'] == "sr-social-sharing-settings") ? 'nav-tab-active' : '';      ?>" id="sr-social-sharing-tab" href="#sr-social-sharing-settings">Social Sharing</a>-->
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-photo-contest-settings") ? 'nav-tab-active' : ''; ?>" id="sr-photo-contest-tab" href="#sr-photo-contest-settings">Photo Contest</a>
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-coupon-discount-settings") ? 'nav-tab-active' : ''; ?>" id="sr-coupon-discount-tab" href="#sr-coupon-discount-settings">Coupon / Discount</a>
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-corner-widget-settings") ? 'nav-tab-active' : ''; ?>" id="sr-corner-widget-tab" href="#sr-corner-widget-settings">Corner Reviews Widget</a>
                <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-seo-settings") ? 'nav-tab-active' : ''; ?>" id="sr-seo-tab" href="#sr-seo-settings">SEO</a>
            </h2>

            <div class="tabwrapper">
                <div id="sr-general-settings" class="panel <?php echo (!isset($_GET['tab']) || $_GET['tab'] == "sr-general-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_general_settings.php'; ?>
                </div>

                <div id="sr-positive-review-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-positive-review-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_positive_reviews_settings.php'; ?>
                </div>

                <div id="sr-negative-review-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-negative-review-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_negative_reviews_settings.php'; ?>
                </div>

                <div id="sr-purls-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-purls-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_purls_settings.php'; ?>
                </div>

                    <!--                <div id="sr-syndication-settings" class="panel <?php //echo ($_GET['tab'] == "sr-syndication-settings") ? 'active' : '';      ?>">
                <?php // include 'sr_syndication_settings.php';  ?>
                                    </div>-->

                    <!--                <div id="sr-social-sharing-settings" class="panel <?php // echo ($_GET['tab'] == "sr-social-sharing-settings") ? 'active' : '';      ?>">
                <?php // include 'sr_social_sharing_settings.php';  ?>
                                    </div>-->

                <div id="sr-photo-contest-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-photo-contest-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_photo_contest_settings.php'; ?>
                </div>

                <div id="sr-coupon-discount-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-coupon-discount-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_coupon_settings.php'; ?>
                </div>

                <div id="sr-corner-widget-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-corner-widget-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_corner_widget_settings.php'; ?>
                </div>

                <div id="sr-seo-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sr-seo-settings") ? 'active' : ''; ?>">
                    <?php include 'sr_seo_settings.php'; ?>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                jQuery('#sreviews_onoff').rcSwitcher();

                jQuery('#swiftreviews_phone_onoff').rcSwitcher();

                jQuery('#swiftreviews_coupon_discount_onoff').rcSwitcher().on({
                    'turnon.rcSwitcher': function (e, dataObj) {
                        jQuery(".hide-me").fadeIn();
                    },
                    'turnoff.rcSwitcher': function (e, dataObj) {
                        jQuery(".hide-me").fadeOut();
                    }
                });

                jQuery('#swiftreviews_upsell_onoff').rcSwitcher().on({
                    'turnon.rcSwitcher': function (e, dataObj) {
                        jQuery(".hide-me").fadeIn();
                    },
                    'turnoff.rcSwitcher': function (e, dataObj) {
                        jQuery(".hide-me").fadeOut();
                    }
                });

                jQuery('#sr_anonymous_reviews_flag, #sr_date_flag').rcSwitcher();

            });
        </script>
    </div>
    <?php
}