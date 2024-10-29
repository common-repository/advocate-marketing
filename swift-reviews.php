<?php

/*
  Plugin Name: SwiftCloud Reputation Management - RAVE
  Plugin URL: https://SwiftCRM.com
  Description: SwiftCloud RAVE - Reviews & Advocate Viral Engagement System to add reviews-based marketing.
  Version: 3.1
  Author: Team SwiftCloud
  Author URI: https://SwiftCRM.com/
  Text Domain: swift-reviews
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    _e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'swift-reviews');
    exit;
}

define('SWIFTREVIEWS_VERSION', '3.1');
define('SWIFTREVIEWS__MINIMUM_WP_VERSION', '5.3');
define('SWIFTREVIEWS__PLUGIN_URL', plugin_dir_url(__FILE__));
define('SWIFTREVIEWS__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWIFTREVIEWS_PLUGIN_PREFIX', 'swiftreviews_');

register_activation_hook(__FILE__, 'swiftreviews_install');

function swiftreviews_install() {
    if (version_compare($GLOBALS['wp_version'], SWIFTREVIEWS__MINIMUM_WP_VERSION, '<')) {
        add_action('admin_notices', 'swiftreviews_version_admin_notice');

        function swiftreviews_version_admin_notice() {
            echo '<div class="notice notice-error is-dismissible sc-admin-notice"><p>' . sprintf(esc_html__('Swift Reviews %s requires WordPress %s or higher.', 'swift-reviews'), SWIFTREVIEWS_VERSION, SWIFTREVIEWS__MINIMUM_WP_VERSION) . '</p></div>';
        }

        add_action('admin_init', 'swiftreviews_deactivate_self');

        function swiftreviews_deactivate_self() {
            if (isset($_GET["activate"]))
                unset($_GET["activate"]);
            deactivate_plugins(plugin_basename(__FILE__));
        }

        return;
    }
    update_option('swift_reviews_version', SWIFTREVIEWS_VERSION);
    swiftreviews_pre_load_data();
}

function swiftreviews_pre_load_data() {
    /* Email Swipes table */
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_email_swipes_template = $wpdb->prefix . 'sr_email_swipes_template';
    $create_table = "CREATE TABLE IF NOT EXISTS `$table_email_swipes_template` (
      `es_id` int(11) NOT NULL AUTO_INCREMENT,
      `es_name` varchar(100) NOT NULL,
      `es_replace_keyword` TEXT NOT NULL,
      `es_subject` varchar(255) NOT NULL,
      `es_content` LONGTEXT NOT NULL,
      PRIMARY KEY (`es_id`)
      ) $charset_collate ;";
    dbDelta($create_table);

    /* Referrals table */
    $table_referrals = $wpdb->prefix . 'sr_referrals';
    $create_table_referrals = "CREATE TABLE IF NOT EXISTS `$table_referrals` (
      `ref_id` int(11) NOT NULL AUTO_INCREMENT,
      `ref_post_id` int(11) NOT NULL,
      `ref_name` varchar(255) NOT NULL,
      `ref_email` varchar(255) NOT NULL,
      `ref_phone` varchar(100) NOT NULL,
      `ref_referred_by_name` varchar(255) NOT NULL,
      `ref_referred_by_email` varchar(255) NOT NULL,
      `ref_date_time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY (`ref_id`)
      ) $charset_collate ;";
    dbDelta($create_table_referrals);

    /**
     *  Default option set when plugin active
     */
    $default_option = array();

    $default_option["swiftreviews_widget_onoff"] = sanitize_text_field("0");
    $default_option["swiftreviews_anonymous_review_flag"] = sanitize_text_field("1");
    $default_option["swiftreviews_widget_header_text"] = sanitize_text_field("Are you happy with what you see on this page?");
    $default_option["swiftreviews_auto_publish_positive_reviews"] = sanitize_text_field("4.5");
    $default_option["swiftreviews_auto_publish_negative_reviews"] = sanitize_text_field("2.5");
    $default_option["swift_review_mode"] = sanitize_text_field("business_mode");
    $default_option["swiftreviews_referral_section_html"] = wp_kses_post('<h3 style="text-align:center">We depend on referrals to great people like you.</h3><p style="text-align: center;"> Who among your co-workers and friends can we introduce ourselves to?<br/> We\'ll just send a gentle introduction to how we can help.</p><p style="text-align: center; font-size: 12px; color: gry;">Privacy respected, and no hard selling -we\'ll be cool! Thanks in advance.</p><div class="gift_details"><p><i class="fa fa-gift"></i> Free Videos - How to Sell For Top Dollar + Bargain Real Estate Secrets</p></div>');
    $default_option["swiftreviews_feedback_section_html"] = wp_kses_post('<h3 style="text-align: center;">Is there anything we could do to make this situation better?</h3><p>This review will be sent to management, and we\'ll open a trouble ticket to see if there\'s anything we can do to improve the situation.</p>');
    $default_option["swiftreviews_photo_video_contest_html"] = wp_kses_post('<p class="">Show off your creativity & Join Our Photo & Video Contest! Share your experiences on video, a video of our products, or a photo showing our product to enter. We\'ll feature winning photos on our newsletter and website, and love customers forever when they\'re willing to share their experience with our company on webcam or smart phone video for just a few minutes. We choose winners periodically, and every entry gets a small surprise gift just for entering! We\'ll express our thanks to the winners with $100 store credit for top prize and $35 in store credit for runners-up.</p><small>Max 3 entries per person. Winners chosen by staff based on merit at our sole discretion. Employees prohibited from winning. Multiple entries allowed & welcome!</small>');
    $default_option["swiftreviews_social_share_default_text"] = sanitize_text_field("When you need a true professional problem solver, check out " . get_bloginfo('name') . " - here's my latest review:");
    $default_option["syn_google"] = sanitize_text_field("1");
    $default_option["syn_facebook"] = sanitize_text_field("1");
    $default_option["syn_twitter"] = sanitize_text_field("1");
    $default_option["swiftreviews_license"] = sanitize_text_field("lite"); //lite/pro

    foreach ($default_option as $def_opt_key => $def_opt_val) {
        update_option($def_opt_key, $def_opt_val);
    }

    $couponContent = '';
    $couponHTML = file_get_contents(SWIFTREVIEWS__PLUGIN_URL . "section/review-coupon.html");
    if ($couponHTML) {
        $couponContent = str_replace("{SITEURL}", home_url(), $couponHTML);
        $couponContent = str_replace("{SITENAME}", get_bloginfo('name'), $couponContent);
        update_option('swiftreviews_coupon_discount_html', wp_kses_post($couponContent));
    }
}

/**
 *      Insert data after user permission.
 */
function swiftreviews_initial_data() {
    global $wpdb;
    $sreviews_pages_id = '';

    /**
     *   Preload pages
     */
    $write_review_page_content = wp_kses_post('<h3>How\'d we do?</h3><p style="margin:0 0 5px 0">We appreciate and value your feedback! Please take just a moment to let us know how we did.</p><br/>[swift_review_form]');
    $reviews_page_content = wp_kses_post('[swift_positive_reviews]');
    $reviews_rss_feed_content = wp_kses_post('This page is being used for RSS Feed');

    /* pages list */
    $pages_array = array(
        "write-review" => array("title" => "How'd we do?", "content" => $write_review_page_content, "slug" => "write-review", "option" => "swiftreviews_review_form_page"),
        "reviews-list" => array("title" => "Reviews", "content" => $reviews_page_content, "slug" => "our-reviews", "option" => "swiftreviews_listing_page"),
        "feedback_confirmation" => array("title" => "Final Feedback Confirmation", "content" => '', "slug" => "final-feedback-confirmation", "option" => "swiftreviews_final_feedback_confirmation_page"),
        "review-feed" => array("title" => sanitize_text_field("Reviews Feed"), "content" => $reviews_rss_feed_content, "slug" => "reviews-feed", "option" => "swiftreviews_feed_page_id", "template" => "rss-review-feed.php"),
    );

    foreach ($pages_array as $key => $page) {
        $page_data = array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_title' => sanitize_text_field($page['title']),
            'post_name' => $page['slug'],
            'post_content' => $page['content'],
            'comment_status' => 'closed'
        );

        $page_id = wp_insert_post($page_data);

        if (!empty($page['option'])) {
            update_option($page['option'], $page_id);
        }
        $sreviews_pages_id .= $page_id . ",";
    }

    if (!empty($sreviews_pages_id)) {
        update_option('swiftreviews_pages', sanitize_text_field(rtrim($sreviews_pages_id, ",")));
    }

    /*
     *      Email Swipes content
     */

    /* Email template array:  key = DB template name; value = HTML File name */

    $email_template_html = array(
        "5 Star Style" => "review-email-template.html",
        "Advocate Promoter Style" => "review-email-template-1.html",
        "Direct Referral Request" => "review-email-template-2.html"
    );
    $fileCnt = 1;

    // get review listing page link to replace on template
    $get_swiftreviews_listing_page = get_option('swiftreviews_listing_page');
    $review_link = get_permalink($get_swiftreviews_listing_page);

    foreach ($email_template_html as $key => $fileName) {
        $mailContent = '';
        $mailBody = file_get_contents(SWIFTREVIEWS__PLUGIN_URL . "section/" . $fileName);
        $get_swiftreviews_review_form_page = get_option('swiftreviews_review_form_page');

        if ($get_swiftreviews_review_form_page) {
            $mailContent = str_replace("{#}", get_permalink($get_swiftreviews_review_form_page), $mailBody);
            $mailContent = str_replace("{sitename}", get_bloginfo('name'), $mailContent);
            $mailContent = str_replace("{plugin_uri}", SWIFTREVIEWS__PLUGIN_URL, $mailContent);
            //$mailContent = str_replace("{swiftreviews_listing_page}", $review_link, $mailContent);
        }
        $mailContent = wp_kses_post($mailContent);

        $wpdb->query($wpdb->prepare("
            INSERT INTO `" . $wpdb->prefix . 'sr_email_swipes_template' . "`
                (`es_id`,`es_name`, `es_replace_keyword`, `es_subject`, `es_content`)
                VALUES (%d, %s, %s, %s, %s)", array($fileCnt, $key, "", "Swift Review Ratings", $mailContent)
                )
        );
        $fileCnt++;
    }
}

/*
 * plugin load
 */
add_action('wp_loaded', 'swiftreviews_update_db_check');

function swiftreviews_update_db_check() {
    if (get_option("swift_reviews_version") != SWIFTREVIEWS_VERSION) {
        swiftreviews_install();
    }
}

add_action('upgrader_process_complete', 'swiftreviews_update_process');

function swiftreviews_update_process($upgrader_object, $options = '') {
    $current_plugin_path_name = plugin_basename(__FILE__);

    if (isset($options) && !empty($options) && $options['action'] == 'update' && $options['type'] == 'plugin' && $options['bulk'] == false && $options['bulk'] == false) {
        foreach ($options['packages'] as $each_plugin) {
            if ($each_plugin == $current_plugin_path_name) {
                swiftreviews_install();
                swiftreviews_initial_data();
            }
        }
    }
}

/**
 *      Deactive plugin
 */
register_deactivation_hook(__FILE__, 'swiftreveiws_deactive_plugin');

function swiftreveiws_deactive_plugin() {
    
}

/**
 *      Uninstall plugin
 */
register_uninstall_hook(__FILE__, 'swiftreveiws_uninstall_callback');
if (!function_exists('swiftreveiws_uninstall_callback')) {

    function swiftreveiws_uninstall_callback() {
        global $wpdb;

        delete_option("swift_reviews_version");
        delete_option("swift_review_notice");
        delete_option("swiftreviews_review_form_page");
        delete_option("swiftreviews_listing_page");
        delete_option("swiftreviews_positive_redirect_page");
        delete_option("swiftreviews_final_feedback_confirmation_page");

        // delete pages
        $pages = get_option('swiftreviews_pages');
        if ($pages) {
            $pages = explode(",", $pages);
            foreach ($pages as $pid) {
                wp_delete_post($pid, true);
            }
        }
        delete_option("swiftreviews_pages");

        /* Drop tables */
        $table_email_swipes_template = $wpdb->prefix . 'sr_email_swipes_template';
        $wpdb->query("DROP TABLE IF EXISTS $table_email_swipes_template");

        $table_referrals = $wpdb->prefix . 'sr_referrals';
        $wpdb->query("DROP TABLE IF EXISTS $table_referrals");

        /*
         * Delete cpt reviews and terms
         */
        /* taxonomy */
        foreach (array('swift_reviews_category') as $taxonomy) {
            $wpdb->delete(
                    $wpdb->term_taxonomy, array('taxonomy' => $taxonomy)
            );
        }

        /* Delete reviews posts */
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ('swift_reviews');");
        $wpdb->query("DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;");

        /* Deregister swift reviews cpt */
        if (function_exists('unregister_post_type')) {
            unregister_post_type('swift_reviews');
        }
    }

}


//Enqueue scripts and styles.
add_action('wp_enqueue_scripts', 'swiftreviews_enqueue_scripts_styles');

function swiftreviews_enqueue_scripts_styles() {
    wp_enqueue_style('swiftcloud-fontawesome', plugins_url('/css/font-awesome.min.css', __FILE__), '', '', '');
    wp_enqueue_style('swiftreviews-custom', plugins_url('/css/swift-review-style.css', __FILE__), '', '', '');
    wp_enqueue_script('swiftreviews-custom-script', plugins_url('/js/swiftreviews-custom-script.js', __FILE__), array('jquery'), '', true);
    wp_localize_script('swiftreviews-custom-script', 'swiftreviews_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'file_path' => plugins_url('/section/swift-reviews-callback.php', __FILE__)));
}

//Load admin modules
require_once('admin/swift-reviews-admin.php');
require_once('shortcode-generator/sr-shortcode-generator.php');

include 'section/swift-reviews-function.php';
include 'swift-review-pagetemplater.php';
include "section/swift-reviews-helpdesk.php";
include "section/swift-reviews-shortcodes.php";
include "section/swift-reviews-callback.php";
include "section/swift-review-corner-widget.php";
include 'section/swift-form-error-popup.php';

// Add review custom post type to feed
function reviewfeed_request($qv) {
    if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = array('post', 'swift_reviews', 'press_release', 'event_marketing', 'swift_jobs', 'vhcard', 'swiftseo');
    return $qv;
}

add_filter('request', 'reviewfeed_request');

/* Add SwiftReview microdata to footer  */
add_action('wp_footer', 'swift_review_microdata', 10);

function swift_review_microdata() {
    $op = '';
    // only add review micro data for homepage or front page.
    if (is_home() || is_front_page()) {
        $get_positive_reviews = get_option("swiftreviews_auto_publish_positive_reviews");
        $swiftreviews_review_per_page = (get_option("swiftreviews_review_per_page")) ? get_option("swiftreviews_review_per_page") : 10;
        $swiftreview_date_flag = get_option("swiftreview_date_flag");
        $logo_url = '';
        if ($swiftreview_microformat_logo = get_option("swiftreview_microformat_logo")) {
            $logo_url = $swiftreview_microformat_logo;
        }

        $rev_arr = array();
        $schema_arr = array(
            "@context" => "http://schema.org",
            "@type" => "LocalBusiness",
            "name" => get_bloginfo('name'),
            "url" => get_permalink(get_the_ID()),
            "image" => $logo_url,
            "priceRange" => "$$"
        );

        $args = array(
            'post_type' => 'swift_reviews',
            'post_status' => 'publish',
            'posts_per_page' => $swiftreviews_review_per_page,
            'paged' => -1,
            'orderby' => 'id',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => 'swiftreviews_ratings',
                    'value' => $get_positive_reviews,
                    'compare' => '>=',
                ),
            ),
        );

        /* get aggregateRating */
        $totalAggregate = $totalReviews = 0;
        $allPosts = get_posts($args);
        foreach ($allPosts as $aPost) {
            $rating = get_post_meta($aPost->ID, 'swiftreviews_ratings', true);
            $totalAggregate = $totalAggregate + $rating;
            $totalReviews++;
        }
        $aggregate_score = !empty($totalAggregate) ? round($totalAggregate / $totalReviews, 2) : '';

        $reviews = new WP_Query($args);
        $op .= '<div class="swift-review-listing" style="display:none;">';

        while ($reviews->have_posts()) : $reviews->the_post();
            $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
            $reviewer_email = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_email', true);
            $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
            $reviewer_type = get_post_meta(get_the_ID(), 'swiftreviews_rating_type', true);
            $reviewer_location = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_location', true);

            $op .= '<div class="sr-list-item">';
            //left side img
            $op .= '<div class="sr-item-left"><a href="' . get_permalink(get_the_ID()) . '"><img src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" style="display: none;" /></a></div>';
            $op .= '<div class="sr-item-right">';
            //ratings
            $op .= '<div class="review-rates">';
            $op .= '<a href="' . get_permalink(get_the_ID()) . '">' . buildStarRating('', $rating) . '</a>';
            $op .= "</div>";

            $review_body = get_the_content();
            $review_body = apply_filters('the_content', $review_body);
            $op .= '<div class="sr-summary"><span><a href="' . get_permalink(get_the_ID()) . '">' . ucfirst(get_the_title()) . '</a></span></div>';
            $op .= '<div class="sr-comments"><a href="' . get_permalink(get_the_ID()) . '">' . $review_body . '</a></div>';
            $op .= '<div class="sr_meta_info">';
            $op .= '<div class="sr-reviewer-name"><span class="reviewer-name">' . ucfirst($reviewer_name) . (!empty($reviewer_location) ? " - " . $reviewer_location : "") . '</span> <br /> <span ' . ($swiftreview_date_flag ? "style='display: block;'" : "style='display: none;'") . '>' . get_the_time('l, F jS, Y', get_the_ID()) . "</span></div>";
            $op .= '<div class="swift-reviews-tags-wrap">' . get_the_term_list(get_the_ID(), 'swift_reviews_category', '<ul class="swift-reviews-tags-list"><li>', '</li><li>', '</li></ul>') . '</div>';
            $op .= '</div>';
            $op .= '</div></div>';

            $rev_arr[] = array(
                "@type" => "Review",
                "author" => ucfirst($reviewer_name),
                "datePublished" => get_the_time('Y-m-d', get_the_ID()),
                "reviewBody" => strip_tags($review_body),
                "reviewRating" => array(
                    "@type" => "Rating",
                    "bestRating" => "5",
                    "ratingValue" => $rating,
                    "worstRating" => "0"
                )
            );
        endwhile;
        wp_reset_postdata();

        $op .= '</div>';    // .swift-review-listing

        $schema_arr['review'] = $rev_arr;
        $schema_arr['aggregateRating'] = array(
            "@type" => "AggregateRating",
            "ratingValue" => number_format($aggregate_score, 2),
            "ratingCount" => $totalReviews
        );
        $op .= '<script type="application/ld+json">';
        $op .= json_encode($schema_arr);
        $op .= '</script>';
    }
    echo $op;
}
