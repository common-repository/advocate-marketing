<?php
/**
 *  On plugin activation notice
 */
// check is plugin active
if (version_compare($GLOBALS['wp_version'], SWIFTREVIEWS__MINIMUM_WP_VERSION, '>=')) {
    add_action('admin_notices', 'swift_review_admin_notice');
}

function swift_review_admin_notice() {
    if (!get_option('swift_review_notice') && !get_option('swiftreviews_pages')) {
        ?>
        <div class="notice notice-success is-dismissible swift-review-notice">
            <p><b>SwiftCloud Reviews Plugin</b></p>
            <form method="post">
                <p class="sr-notice-msg"><?php _e('Want to auto-create the following pages to quickly get you set up? ', 'swift-review'); ?></p>
                <ul>
                    <li>How'd we do?</li>
                    <li>Reviews List</li>
                    <li>Thanks for Reviewing (Final Confirmation)</li>
                </ul>
                <?php wp_nonce_field('swiftreview_autogen_pages', 'swiftreview_autogen_pages'); ?>
                <button type="submit" value="yes" name="sr_autogen_yes" class="button button-green"><i class="fa fa-check"></i> Yes</button>  <button type="submit" name="sr_autogen_no" value="no" class="button button-default button-red"><i class="fa fa-ban"></i> No</button>
            </form>
        </div>
        <?php
    }
}

/**/
add_action('admin_menu', 'swiftreviews_control_panel');

function swiftreviews_control_panel() {
    $icon_url = plugins_url('/images/swiftcloud.png', __FILE__);
    $menu_capability = 'manage_options';
    $parent_menu_slug = 'swift-reviews';
    $cpt_menu_slug = 'edit.php?post_type=swift_reviews';

    add_menu_page('Swift Reviews', 'Swift Reviews', $menu_capability, $parent_menu_slug, 'swiftreviews_settings_callback', $icon_url, 26);
    add_submenu_page($parent_menu_slug, "Swift Reviews Settings", "Settings", $menu_capability, $parent_menu_slug, null);
    //cpt menu
    add_submenu_page($parent_menu_slug, "All Reviews", "All Reviews", $menu_capability, "edit.php?post_type=swift_reviews", null);
    add_submenu_page($parent_menu_slug, "Add Review", "Add Review", $menu_capability, "post-new.php?post_type=swift_reviews", null);
    add_submenu_page($parent_menu_slug, "Categories", "Categories", $menu_capability, "edit-tags.php?taxonomy=swift_reviews_category&post_type=swift_reviews", null);
    //other menu
    add_submenu_page($parent_menu_slug, "Referrals", "Referrals", $menu_capability, "swiftreviews_referrals_page", 'swiftreviews_referrals_page_callback');
    add_submenu_page($parent_menu_slug, "Email Swipes", "Email Swipes", $menu_capability, "swiftreviews_email_swipes", 'swiftreviews_email_swipes_callback');
    add_submenu_page($parent_menu_slug, "Swift Reviews Help", "Help / Setup", $menu_capability, "swiftreviews_help", 'swiftreviews_help_callback');
    add_submenu_page($parent_menu_slug, "Swift Reviews Reports", "Reports", $menu_capability, "swiftreviews_reports", 'swiftreviews_reports_callback');
    add_submenu_page($parent_menu_slug, "Sharing", "Sharing", $menu_capability, "swiftreviews_sharing", 'swiftreviews_sharing_callback');
    add_submenu_page($parent_menu_slug, "Export", "Export", $menu_capability, "swiftreviews_export", 'swiftreviews_export_callback');
    add_submenu_page($parent_menu_slug, "Updates & Tips", "Updates & Tips", 'manage_options', 'swiftreviews_dashboard', 'swiftreviews_dashboard_callback');
}

/**
 *      Set current menu selected
 */
add_filter('parent_file', 'swiftreviews_set_current_menu');
if (!function_exists('swiftreviews_set_current_menu')) {

    function swiftreviews_set_current_menu($parent_file) {
        global $submenu_file, $current_screen, $pagenow;

        if ($current_screen->post_type == 'swift_reviews') {
            if ($pagenow == 'post.php') {
                $submenu_file = "edit.php?post_type=" . $current_screen->post_type;
            }
            if ($pagenow == 'edit-tags.php') {
                if ($current_screen->taxonomy == 'swift_reviews_category') {
                    $submenu_file = "edit-tags.php?taxonomy=swift_reviews_category&post_type=" . $current_screen->post_type;
                }
            }
            $parent_file = 'swift-reviews';
        }
        return $parent_file;
    }

}


/**/

function swiftreviews_admin_enqueue($hook) {
    wp_enqueue_style('swiftreviews-admin-style', plugins_url('/css/swiftreviews-admin-style.css', __FILE__), '', '', '');
    wp_enqueue_style('swift-cloud-jquery-ui', plugins_url('/css/jquery-ui.min.css', __FILE__), '', '', '');
    wp_enqueue_style('swiftcloud-fontawesome', plugins_url('/../css/font-awesome.min.css', __FILE__), '', '', '');
    wp_enqueue_style('swift-cloud-admin-style', plugins_url('/css/sc_admin.css', __FILE__), '', '', '');

    wp_enqueue_script('jquery-ui-tooltip');
    wp_enqueue_script('swiftreviews-admin-script', plugins_url('/js/swiftreviews-admin-script.js', __FILE__), array('jquery', 'swift-toggle'), '', true);
    wp_localize_script('swiftreviews-admin-script', 'swiftreviews_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_script('swift-cloud-toggle-custom', plugins_url('/js/sc_admin.js', __FILE__), array('jquery'), '', true);

    //only for setting page
    if (isset($_GET['page']) && $_GET['page'] == 'swift-reviews') {
        wp_enqueue_style('swift-toggle-style', plugins_url('/css/sc_rcswitcher.css', __FILE__), '', '', '');
        wp_enqueue_style('swiftcloud-colorpicker-style', plugins_url('/css/sc_spectrum.css', __FILE__), '', '', '');
        wp_enqueue_script('swift-toggle', plugins_url('/js/sc_rcswitcher.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('swiftcloud-colorpicker', plugins_url('/js/sc_spectrum.js', __FILE__), array('jquery'), '', true);

        wp_enqueue_media();
        wp_enqueue_script('sr-upload-media', plugins_url('/js/swiftreviews_admin_media_upload.js', __FILE__), array('jquery'), '', true);
    }
}

add_action('admin_enqueue_scripts', 'swiftreviews_admin_enqueue');

include "section/cpt-swift-reviews.php";
include "section/swift_dashboard.php";
include "section/swiftreviews_settings.php";

include "section/swiftreviews-referrals.php";
include "section/swiftreviews-email-swipes.php";
include "section/swiftreviews-help-setup.php";
include "section/swiftreviews-reports.php";
include "section/swiftreviews-export.php";
include "section/swiftreviews-sharing.php";
include "section/swiftreviews-widget-latest-reviews.php";
include "section/swiftreviews_youtube_handler.php";

/* init */
add_action("init", "swift_review_admin_forms_submit");

function swift_review_admin_forms_submit() {
    /* on plugin active auto generate pages and options */
    if (isset($_POST['swiftreview_autogen_pages']) && wp_verify_nonce($_POST['swiftreview_autogen_pages'], 'swiftreview_autogen_pages')) {
        if ($_POST['sr_autogen_yes'] == 'yes') {
            swiftreviews_initial_data();
        }
        update_option('swift_review_notice', true);
    }
}

/* Dismiss notice callback */
add_action('wp_ajax_swift_review_dismiss_notice', 'swift_review_dismiss_notice_callback');
add_action('wp_ajax_nopriv_swift_review_dismiss_notice', 'swift_review_dismiss_notice_callback');

function swift_review_dismiss_notice_callback() {
    update_option('swift_review_notice', true);
}

/* CODE TO DYNAMICALLY FILL CATEGORY FOR REVIEWS */

function twd_list_ajax() {
    // check for nonce
    check_ajax_referer('twd-nonce', 'security');
    $posts = twd_posts('post');
    return $posts;
}

add_action('wp_ajax_twd_cpt_list', 'twd_list_ajax');

/**
 * Function to output button list ajax script
 * @since  1.6
 * @return string
 */
function twd_cpt_list() {
    // create nonce
    global $pagenow;
    if ($pagenow == 'post-new.php' || $pagenow == 'post.php') {
        $nonce = wp_create_nonce('twd-nonce');
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var data = {
                    'action': 'twd_cpt_list', // wp ajax action
                    'security': '<?php echo $nonce; ?>' // nonce value created earlier
                };
                // fire ajax
                jQuery.post(ajaxurl, data, function (response) {
                    // if nonce fails then not authorized else settings saved
                    if (response === '-1') {
                        // do nothing
                        console.log('error');
                    } else {
                        if (typeof (tinyMCE) != 'undefined') {
                            if (tinyMCE.activeEditor != null) {
                                tinyMCE.activeEditor.settings.cptPostsList = response;
                            }
                        }
                    }
                });
            });
        </script>
        <?php
    }
}

add_action('admin_footer', 'twd_cpt_list');

function twd_posts() {
    $list = array();
    $list[] = array('text' => 'Select Category', 'value' => 0);
    $srCats = get_terms('swift_reviews_category', 'hide_empty=0');
    foreach ($srCats as $srcats) {
        $list[] = array(
            'text' => $srcats->name,
            'value' => $srcats->slug
        );
    }
    wp_send_json($list);
}

/* CODE TO DYNAMICALLY FILL CATEGORY FOR REVIEWS - END */