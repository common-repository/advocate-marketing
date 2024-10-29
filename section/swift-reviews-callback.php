<?php

/**
 * Front : Review insert post
 */
add_action('wp_ajax_swift_review_insert', 'swift_review_insert_callback');
add_action('wp_ajax_nopriv_swift_review_insert', 'swift_review_insert_callback');

function swift_review_insert_callback() {
    check_ajax_referer('swift-review-submit-nonce', 'swiftreview_security');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swift_review_insert') {
        $minRates = get_option('swiftreviews_auto_publish_positive_reviews');
        $default_category_id = get_option('swiftreviews_default_category');

        parse_str($_POST['data'], $review_data);

        $sr_social_providers = array('google' => 0, 'facebook' => 0, 'twitter' => 0, 'linkedin' => 0, 'pinterest' => 0, 'instagram' => 0);

        $review_title = sanitize_text_field(trim($review_data['swift_review_title']));
        $post_content = wp_kses_post('<p>' . $review_data['sr-youtube-video'] . '</p>' . $review_data['swift_review_text']);

        if (!empty($review_title)) {
            $sr_cat = '';
            if ($review_data['swiftreview_category']) {
                $sr_cat = sanitize_text_field($review_data['swiftreview_category']);
            } else {
                $default_category_data = get_term($default_category_id, 'swift_reviews_category');
                $sr_cat = sanitize_text_field($default_category_data->slug);
            }

            $review = array(
                'post_status' => 'publish',
                'post_type' => 'swift_reviews',
                'post_title' => $review_title,
                'post_content' => $post_content,
                'comment_status' => 'closed'
            );

            $review_id = wp_insert_post($review);

            if ($review_id) {
                update_post_meta($review_id, 'swiftreviews_reviewer_email', sanitize_email($review_data['swift_review_email']));
                update_post_meta($review_id, 'swiftreviews_reviewer_name', sanitize_text_field($review_data['swift_review_reviewer_name']));
                update_post_meta($review_id, 'swiftreviews_ratings', sanitize_text_field($review_data['swift_review_rating']));
                update_post_meta($review_id, 'swiftreviews_improvements', sanitize_text_field($review_data['swiftreviews_improvements']));
                update_post_meta($review_id, 'swiftreviews_rating_type', sanitize_text_field($review_data['swift_review_type']));
                wp_set_object_terms($review_id, $sr_cat, 'swift_reviews_category', true);
                update_post_meta($review_id, "swiftreviews_social_clicks", serialize($sr_social_providers)); //for social share
                setcookie('swift_reviews_publish', $review_id, time() + (10 * 365 * 24 * 60 * 60), "/", '');
            }

            if ($minRates <= $review_data['swift_review_rating']) {
                echo '1'; //Positive rates
            } else {
                echo "0";   //Negative rates
            }
        }
    }
    wp_die();
}

/**
 *      Referral
 */
add_action('wp_ajax_swift_review_referrals', 'swift_review_referrals_callback');
add_action('wp_ajax_nopriv_swift_review_referrals', 'swift_review_referrals_callback');

function swift_review_referrals_callback() {
    check_ajax_referer('swift-review-referrals-nonce', 'swiftreview_referrals_security');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swift_review_referrals') {

        if (isset($_COOKIE['swift_reviews_publish']) && !empty($_COOKIE['swift_reviews_publish'])) {
            $referrals = '';
            global $wpdb;
            $table_referrals = $wpdb->prefix . 'sr_referrals';
            $get_swiftreviews_phone_flag = get_option('swiftreviews_phone');
            parse_str($_POST['data'], $form_data);

            if (isset($form_data['extra_total_referrer']) && !empty($form_data['extra_total_referrer'])) {
                for ($cn = 1; $cn <= $form_data['extra_total_referrer']; $cn++) {
                    $ref_name = (isset($form_data['swift_additionalcontact_' . $cn . '_name'])) ? sanitize_text_field($form_data['swift_additionalcontact_' . $cn . '_name']) : "";
                    $ref_email = (isset($form_data['swift_additionalcontact_' . $cn . '_email'])) ? sanitize_text_field($form_data['swift_additionalcontact_' . $cn . '_email']) : "";
                    $ref_phone = ($get_swiftreviews_phone_flag == 1) ? ' | ' . sanitize_text_field($form_data['swift_additionalcontact_' . $cn . '_phone']) : '';

                    $referrals .= $ref_name . (!empty($ref_email) ? " (" . $ref_email . $ref_phone . ")," : "");
                    $form_data['referrals'] = rtrim($referrals, ",");

                    /* insert in referral tabel */
                    $ref_insert = $wpdb->insert($table_referrals, array(
                        'ref_post_id' => sanitize_text_field($_COOKIE['swift_reviews_publish']),
                        'ref_name' => $ref_name . (!empty($ref_email) ? " (" . $ref_email . ")" : ""),
                        'ref_email' => $ref_email,
                        'ref_phone' => $ref_phone,
                        'ref_referred_by_name' => sanitize_text_field($form_data['name']),
                        'ref_referred_by_email' => sanitize_email($form_data['email']),
                        'ref_date_time' => sanitize_text_field(date("Y-m-d H:i:s"))
                            ), array('%d', '%s', '%s', '%s', '%s', '%s', '%s'));
                }
            }

            $form_data['referer'] = home_url();
            $args = array(
                'body' => $form_data,
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array(),
            );
            wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);

            /* save referrals */
            update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_referrals', $form_data['referrals']);

            /* save hidden vars */
            unset($form_data['extra_swift_review_title']);
            unset($form_data['name']);
            unset($form_data['email']);
            unset($form_data['swiftreview_referrals_security']);
            unset($form_data['_wp_http_referer']);
            unset($form_data['referrals']);
            update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_hidden_vars', serialize($form_data));

            echo "1"; //get response
        }
    }
    wp_die();
}

/*
 *      Negative review form save meta
 */
add_action('wp_ajax_swift_negative_reviews', 'swift_negative_reviews_callback');
add_action('wp_ajax_nopriv_swift_negative_reviews', 'swift_negative_reviews_callback');

function swift_negative_reviews_callback() {
    check_ajax_referer('swift-review-helpdesk-nonce', 'swiftreview_helpdesk_security');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swift_negative_reviews') {
        $data = $form_data = array();
        parse_str($_POST['data'], $form_data);

        if (isset($_COOKIE['swift_reviews_publish']) && !empty($_COOKIE['swift_reviews_publish'])) {

            $data['comments'] = wp_kses_post(addslashes($form_data['extra_sr_comments']));
            $data['phone'] = sanitize_text_field($form_data['phone']);

            update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_negative_reviews', json_encode($data));

            $current_ratings = $form_data['extra_current_reviews'];
            $get_auto_publish_negative_reviews = get_option("swiftreviews_auto_publish_negative_reviews");

            if ($get_auto_publish_negative_reviews >= $current_ratings) {
                $form_data['referer'] = home_url();
                $args = array(
                    'body' => $form_data,
                    'timeout' => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'cookies' => array(),
                );
                wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);
            }



            /* save hidden vars */
            unset($form_data['name']);
            unset($form_data['email']);
            unset($form_data['extra_sr_comments']);
            unset($form_data['phone']);
            unset($form_data['extra_swift_review_title']);
            unset($form_data['extra_current_reviews']);
            unset($form_data['swiftreview_helpdesk_security']);
            unset($form_data['_wp_http_referer']);
            update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_hidden_vars', serialize($form_data));

            echo "1";
        }
    }
    wp_die();
}

/*
 *      Photo contest
 */
add_action('wp_ajax_swiftreview_photocontest', 'swiftreview_photocontest_callback');
add_action('wp_ajax_nopriv_swiftreview_photocontest', 'swiftreview_photocontest_callback');

function swiftreview_photocontest_callback() {

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swiftreview_photocontest') {
        parse_str($_POST['form_data'], $form_data);
        if (isset($_COOKIE['swift_reviews_publish']) && !empty($_COOKIE['swift_reviews_publish'])) {
            $form_data['referer'] = home_url();
            $args = array(
                'body' => $form_data,
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array(),
            );
            wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);

            unset($form_data['name']);
            unset($form_data['extra_swift_review_title']);
            unset($form_data['email']);
            unset($form_data['swiftreview_photo_contest_security']);
            unset($form_data['_wp_http_referer']);
            unset($form_data['extra_sr_contest_photo']);
            update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_hidden_vars', serialize($form_data));

            echo 1;
        }
    }
    wp_die();
}

/*
 *      Upload photo contest images
 */
add_action('wp_ajax_swiftreview_save_photocontest_imgs', 'swiftreview_save_photocontest_imgs_callback');
add_action('wp_ajax_nopriv_swiftreview_save_photocontest_imgs', 'swiftreview_save_photocontest_imgs_callback');

function swiftreview_save_photocontest_imgs_callback() {
    check_ajax_referer('swift-review-photo-contest-nonce', 'swiftreview_photo_contest_security');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swiftreview_save_photocontest_imgs') {
        $return_imgs = $base64input = array();

        if (isset($_COOKIE['swift_reviews_publish']) && !empty($_COOKIE['swift_reviews_publish'])) {

            if (!empty($_FILES['sr_photo_contest_upload'])) {
                if (!function_exists('wp_handle_upload')) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php');
                }

                $img_ext = array('image/jpg', 'image/png', 'image/jpeg', 'image/gif');
                $upload_overrides = array('test_form' => false);
                $files = $_FILES['sr_photo_contest_upload'];

                foreach ($files['name'] as $key => $value) {

                    if (in_array($files['type'][$key], $img_ext)) {

                        if ($files['name'][$key]) {

                            $random = mt_rand(100000, 999999);
                            $uploadedfile = array(
                                'name' => $random . "_" . $files['name'][$key],
                                'type' => $files['type'][$key],
                                'tmp_name' => $files['tmp_name'][$key],
                                'error' => $files['error'][$key],
                                'size' => $files['size'][$key]
                            );

                            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                            if ($movefile && !isset($movefile['error'])) {
                                if (empty($ufiles))
                                    $ufiles = array();

                                $ufiles[] = $movefile;
                                $return_imgs[] = '<img class="srTempImg" src="' . $movefile['url'] . '" alt="photo contest" style="width: 100%; height: auto; max-height: 100px; max-width: 100px;" />';
                                $img_file = file_get_contents($movefile['url']);
                                $base64url = 'data:' . $files['type'][$key] . ';base64,' . base64_encode($img_file);
                                $base64input[] = '<input type="hidden" name="extra_sr_contest_photo[]" class="sr_contest_photo" value="' . $base64url . '" />';

                                update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_contest_video_data', '');
                                update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_photo_contest_data', $ufiles);
                            }
                        }
                    }
                }//foreach
            }
            if (!empty($return_imgs)) {
                $data = array('success' => 'true', 'images' => implode(" ", $return_imgs), 'baseurl' => implode(" ", $base64input));
                echo json_encode($data);
            }
        }
    }
    wp_die();
}

/*
 *      Video url
 */
add_action('wp_ajax_swift_video_url', 'swift_video_url_callback');
add_action('wp_ajax_nopriv_swift_video_url', 'swift_video_url_callback');

function swift_video_url_callback() {
    check_admin_referer('swift-review-video-url-nonce', 'swiftreview_video_url_security');

    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swift_video_url') {
        $form_data = array();
        parse_str($_POST['data'], $form_data);

        if (isset($_COOKIE['swift_reviews_publish']) && !empty($_COOKIE['swift_reviews_publish'])) {
            if (!empty($form_data['extra_sr_video_url'])) {
                $video_url = !empty($form_data['extra_sr_video_url']) ? esc_url_raw($form_data['extra_sr_video_url']) : '';

                update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_photo_contest_data', '');
                update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_contest_video_data', $video_url);
            }

            $form_data['referer'] = home_url();
            $args = array(
                'body' => $form_data,
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array(),
            );
            wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);

            /* save hidden vars */
            unset($form_data['extra_sr_video_url']);
            unset($form_data['name']);
            unset($form_data['email']);
            unset($form_data['phone']);
            unset($form_data['extra_swift_review_title']);
            unset($form_data['swiftreview_video_url_security']);
            unset($form_data['_wp_http_referer']);
            update_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_hidden_vars', serialize($form_data));

            echo "1";
        }
    }
    wp_die();
}

/* Share url */
add_action('wp_ajax_swiftreviews_share_url', 'swiftreviews_share_url_callback');
add_action('wp_ajax_nopriv_swiftreviews_share_url', 'swiftreviews_share_url_callback');

function swiftreviews_share_url_callback() {
    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swiftreviews_share_url') {
        $link = sanitize_text_field($_POST['data']);
        $return_link = '0';

        if (!empty($link) || $link != "#") {
            $get_swiftreviews_listing_page = get_option('swiftreviews_listing_page');
            $review_page_link = get_permalink($get_swiftreviews_listing_page);

            //make share link
            $check_link = strpos($link, "?");
            if ($check_link == false) {
                $return_link = $link . "?utm_medium=" . $_COOKIE['swift_reviews_publish'] . "&utm_campaign=Viral_CAVE_WPplugin&url=" . $review_page_link;
            } else {
                $return_link = $link . "&utm_medium=" . $_COOKIE['swift_reviews_publish'] . "&utm_campaign=Viral_CAVE_WPplugin&url=" . $review_page_link;
            }

            // save click count
            $data_provider = sanitize_text_field($_POST['dataProvider']);
            $sr_social_providers = get_post_meta($_COOKIE['swift_reviews_publish'], "swiftreviews_social_clicks", true);
            $sr_social_providers_list = (unserialize($sr_social_providers));

            if (!empty($sr_social_providers_list)) {
                if (array_key_exists($data_provider, $sr_social_providers_list)) {
                    $temp = $sr_social_providers_list[$data_provider] + 1;
                    $sr_social_providers_list[$data_provider] = $temp;

                    update_post_meta($_COOKIE['swift_reviews_publish'], "swiftreviews_social_clicks", serialize($sr_social_providers_list));
                }
            }
        }
        echo $return_link;
    }
    wp_die();
}

/* Review Vote UP */
add_action('wp_ajax_swift_review_vote_up', 'swift_review_vote_up_callback');
add_action('wp_ajax_nopriv_swift_review_vote_up', 'swift_review_vote_up_callback');

function swift_review_vote_up_callback() {
    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swift_review_vote_up') {
        $review_id = sanitize_text_field($_POST['data']);

        if (!empty($review_id)) {

            $get_vote_counts = get_post_meta($review_id, "swift_reviews_votes", true);
            if ($get_vote_counts > 0) {
                $vote_counts = $get_vote_counts + 1;
            } else {
                $vote_counts = 1;
            }
            $update_vote_count = update_post_meta($review_id, "swift_reviews_votes", $vote_counts);

            if (!empty($update_vote_count)) {
                $return = array('status' => 'true', 'vote_counts' => $vote_counts, 'review_id' => $review_id);
                print_r(json_encode($return));
            }
        }
    }
    wp_die();
}

/* Review Vote Down */
add_action('wp_ajax_swift_review_vote_down', 'swift_review_vote_down_callback');
add_action('wp_ajax_nopriv_swift_review_vote_down', 'swift_review_vote_down_callback');

function swift_review_vote_down_callback() {
    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swift_review_vote_down') {
        $review_id = sanitize_text_field($_POST['data']);

        if (!empty($review_id)) {
            $get_vote_counts = get_post_meta($review_id, "swift_reviews_votes", true);

            if ($get_vote_counts > 0) {
                $vote_counts = $get_vote_counts - 1;
                $update_vote_count = update_post_meta($review_id, "swift_reviews_votes", $vote_counts);
            }

            if (!empty($update_vote_count)) {
                $return = array('status' => 'true', 'vote_counts' => $vote_counts, 'review_id' => $review_id);
            } else {
                $return = array('status' => 'false', 'vote_counts' => $vote_counts, 'review_id' => $review_id);
            }
            print_r(json_encode($return));
        }
    }
    wp_die();
}

/**
 *      swiftreview_refer_to_friend
 */
add_action('wp_ajax_swiftreview_refer_to_friend', 'swiftreview_refer_to_friend_callback');
add_action('wp_ajax_nopriv_swiftreview_refer_to_friend', 'swiftreview_refer_to_friend_callback');

function swiftreview_refer_to_friend_callback() {
    check_ajax_referer('swift-review-refer-to-friend-nonce', 'swiftreview_refer_to_friend_security');
    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'swiftreview_refer_to_friend') {

        $referrals = '';
        global $wpdb;
        $table_referrals = $wpdb->prefix . 'sr_referrals';

        parse_str($_POST['data'], $form_data);

        $get_swiftreviews_phone_flag = get_option('swiftreviews_phone');

        foreach ($form_data['extra_swift_review_referrals_name'] as $key => $nameVal) {
            $phone = $get_swiftreviews_phone_flag == 1 ? ' | ' . sanitize_text_field($form_data['extra_swift_review_referrals_phone'][$key]) : '';
            $referrals .= sanitize_text_field($nameVal) . "(" . sanitize_text_field($form_data['extra_swift_review_referrals_mail'][$key]) . $phone . "),";

            $form_data['referrals'] = rtrim($referrals, ",");
        }

        unset($form_data['extra_swift_review_referrals_name']);
        unset($form_data['extra_swift_review_referrals_mail']);
        unset($form_data['extra_swift_review_referrals_phone']);

        $form_data['referer'] = home_url();
        $args = array(
            'body' => $form_data,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
        );
        wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);

        echo "1"; //get response
    }
    wp_die();
}
