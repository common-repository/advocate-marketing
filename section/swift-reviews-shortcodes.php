<?php

/*
 *      Shortcode : [swift_review_form rating_type="" category=""]
 *      - Show review form
 *      - rating_type: Optional; 5stars/10stars/yes-no; default 5stars
 *      - category: Optional; category slug; insert review in given category otherwise Default category(reviews)
 */
add_shortcode('swift_review_form', 'swift_review_form_callback');

function swift_review_form_callback($atts) {

    $a = shortcode_atts(
            array(
        'rating_type' => '',
        'category' => '',
            ), $atts);
    extract($a);

    wp_enqueue_style('swiftcloud-plugin-tooltip', SWIFTREVIEWS__PLUGIN_URL . 'css/tooltipster.css', '', '', '');
    wp_enqueue_script('swift-form-jstz', SWIFTREVIEWS__PLUGIN_URL . "js/jstz.min.js", '', '', true);
    wp_enqueue_script('swiftcloud-tooltip-min', SWIFTREVIEWS__PLUGIN_URL . 'js/tooltipster.js', array('jquery'), '', true);

    $op = '';

//    $swift_formid = get_option('swiftreviews_helpdesk_form_id');
    $get_reviews_stars = get_option("swiftreviews_auto_publish_positive_reviews");
    $get_sc_referrals_form_id = get_option("swiftreviews_swiftcloud_referrals_form_id");
    $get_sc_anonymous_reivew_flag = get_option("swiftreviews_anonymous_review_flag");
    $get_swiftreviews_upsell = get_option("swiftreviews_upsell");

    $ratings = (isset($_GET['ratings']) && $_GET['ratings'] >= 0) ? ($_GET['ratings']) : '';
    $category = trim($category);
    $category_input = !empty($category) ? '<input type="hidden" name="swiftreview_category" value="' . $category . '"/>' : '';

    $op .= '<div class="swift-review-form-wrap">
            <div class="swift-review-form-content sr-form-1">
                <form method="post" id="swift-review-form" class="swift-review-form">';

    switch ($rating_type) {
        case 'yes-no': {
                /*
                 *        Yes/No rating
                 *            yes = 5 stars
                 *            meh = 3.5 stars
                 *            no  = 2.5 stars
                 */
                $op .= '<div class="sr-field sr-xs-center">';
                $op .= '<div class="sr-yesno-ratings">';
                $op .= '<input type="radio" id="star2half" class="star-no" name="swift_review_rating" value="2.5" ' . checked($ratings, 2.5, false) . ' /><label class="rating-no sr-tooltip" for="star2half" title="Below Expectations"><i class="fa fa-frown-o"></i> No</label>';
                $op .= '<input type="radio" id="star3half" class="star-meh" name="swift_review_rating" value="3.5" ' . checked($ratings, 3.5, false) . ' /><label class="rating-meh sr-tooltip" for="star3half" title="Meh"><i class="fa fa-meh-o"></i> Meh</label>';
                $op .= '<input type="radio" id="star5" class="star-yes" name="swift_review_rating" value="5" ' . checked($ratings, 5, false) . ' /><label class="rating-yes sr-tooltip" for="star5" title="Excellent"><i class="fa fa-smile-o"></i> Yes</label>';
                $op .= '</div>';
                $op .= '</div><input type="hidden" name="swift_review_type" value="yes-no"/>';
                break;
            }
        case '10stars': {
                /*
                 *      10 stars ratings
                 */
                $op .= '<div class="sr-field sr-xs-center">
                    <div class="rating-10stars">
                        <input type="radio" id="star0" name="swift_review_rating" value="0" ' . (strlen($ratings) > 0 && $ratings == 0 ? "checked='checked'" : "") . ' /><label class="full stars-0 sr-tooltip" for="star0" title="Terrible">0</label>
                        <input type="radio" id="starhalf" name="swift_review_rating" value="0.5" ' . checked($ratings, 0.5, false) . ' /><label class="half stars-0-5 sr-tooltip" for="starhalf" title="Terrible">1</label>
                        <input type="radio" id="star1" name="swift_review_rating" value="1" ' . checked($ratings, 1, false) . ' /><label class="full stars-1 sr-tooltip" for="star1" title="Terrible">2</label>
                        <input type="radio" id="star1half" name="swift_review_rating" value="1.5" ' . checked($ratings, 1.5, false) . ' /><label class="half stars-1-5 sr-tooltip" for="star1half" title="Serious problems">3</label>
                        <input type="radio" id="star2" name="swift_review_rating" value="2" ' . checked($ratings, 2, false) . ' /><label class="full stars-2 sr-tooltip" for="star2" title="Serious problems">4</label>
                        <input type="radio" id="star2half" name="swift_review_rating" value="2.5" ' . checked($ratings, 2.5, false) . ' /><label class="half stars-2-5 sr-tooltip" for="star2half" title="Below Expectations">5</label>
                        <input type="radio" id="star3" name="swift_review_rating" value="3" ' . checked($ratings, 3, false) . ' /><label class="full stars-3 sr-tooltip" for="star3" title="Below Expectations">6</label>
                        <input type="radio" id="star3half" name="swift_review_rating" value="3.5" ' . checked($ratings, 3.5, false) . ' /><label class="half stars-3-5 sr-tooltip" for="star3half" title="Meh. Just so-so">7</label>
                        <input type="radio" id="star4" name="swift_review_rating" value="4" ' . checked($ratings, 4, false) . '/><label class="full stars-4 sr-tooltip" for="star4" title="Good not great">8</label>
                        <input type="radio" id="star4half" name="swift_review_rating" value="4.5" ' . checked($ratings, 4.5, false) . ' /><label class="half stars-4-5 sr-tooltip" for="star4half" title="Great">9</label>
                        <input type="radio" id="star5" name="swift_review_rating" value="5" ' . checked($ratings, 5, false) . ' /><label class="full stars-5 sr-tooltip" for="star5" title="Excellent">10</label>
                        <div class="rating-text">
                            <div class="rating-text-left"><span><i class="fa fa-frown-o fa-lg"></i> Hate it</span></div>
                            <div class="rating-text-right"><span>Love It!! <i class="fa fa-smile-o fa-lg"></i></span></div>
                        </div>
                    </div>
              </div><input type="hidden" name="swift_review_type" value="10stars"/>';
                break;
            }
        default : {
                /*
                 *      5 stars ratings
                 */
                $op .= '<div class="sr-field">
                    <div class="rating">
                        <input type="radio" id="star5" name="swift_review_rating" value="5" ' . checked($ratings, 5, false) . ' /><label class="full sr-tooltip" for="star5" title="5 stars"></label>
                        <input type="radio" id="star4half" name="swift_review_rating" value="4.5" ' . checked($ratings, 4.5, false) . ' /><label class="half sr-tooltip" for="star4half" title="4.5 stars"></label>
                        <input type="radio" id="star4" name="swift_review_rating" value="4" ' . checked($ratings, 4, false) . '/><label class = "full sr-tooltip" for="star4" title="Pretty good - 4 stars"></label>
                        <input type="radio" id="star3half" name="swift_review_rating" value="3.5" ' . checked($ratings, 3.5, false) . ' /><label class="half sr-tooltip" for="star3half" title="3.5 stars"></label>
                        <input type="radio" id="star3" name="swift_review_rating" value="3" ' . checked($ratings, 3, false) . ' /><label class="full sr-tooltip" for="star3" title="3 stars"></label>
                        <input type="radio" id="star2half" name="swift_review_rating" value="2.5" ' . checked($ratings, 2.5, false) . ' /><label class="half sr-tooltip" for="star2half" title="2.5 stars"></label>
                        <input type="radio" id="star2" name="swift_review_rating" value="2" ' . checked($ratings, 2, false) . ' /><label class="full sr-tooltip" for="star2" title="2 stars"></label>
                        <input type="radio" id="star1half" name="swift_review_rating" value="1.5" ' . checked($ratings, 1.5, false) . ' /><label class="half sr-tooltip" for="star1half" title="1.5 stars"></label>
                        <input type="radio" id="star1" name="swift_review_rating" value="1" ' . checked($ratings, 1, false) . ' /><label class = "full sr-tooltip" for="star1" title="1 star"></label>
                        <input type="radio" id="starhalf" name="swift_review_rating" value="0.5" ' . checked($ratings, 0.5, false) . ' /><label class="half sr-tooltip" for="starhalf" title="0.5 stars"></label>
                        <input type="radio" id="star0" name="swift_review_rating" value="0" ' . checked($ratings, intval(0), false) . ' /><label class="full sr-tooltip star0" for="star0" title="0 stars"></label>

                        <div class="rating-text">
                            <div class="rating-text-left"><span><i class="fa fa-frown-o fa-lg"></i> Absolutely Terrible</span></div>
                            <div class="rating-text-right"><span>Excellent! <i class="fa fa-smile-o fa-lg"></i></span></div>
                        </div>
                    </div>
              </div><input type="hidden" name="swift_review_type" value="5stars"/>';
                break;
            }
    }

    $anonymous_review = $get_sc_anonymous_reivew_flag == 1 ? "sr-anonymous" : '';

    $domain_name = ($_SERVER['SERVER_NAME'] == "localhost") ? $_SERVER['SERVER_NAME'] . '.com' : $_SERVER['SERVER_NAME'];
    $anonymous_email = $get_sc_anonymous_reivew_flag == 1 ? strtolower(date("j-M-Y-G-i-s")) . "@" . $domain_name : '';
    $anonymous_name = $get_sc_anonymous_reivew_flag == 1 ? 'Anonymous' : '';

    $op .= '<div class="sr-field ' . $anonymous_review . '">
                        <label for="swift-review-reviewer-name">Name*</label>
                        <input type="text" name="swift_review_reviewer_name" class="sr-form-control" id="swift-review-reviewer-name" value="' . $anonymous_name . '"/>
                    </div>';
    $op .= '<div class="sr-field ' . $anonymous_review . '">
                        <label for="email">Email*</label>
                        <input type="text" name="swift_review_email" class="sr-form-control" id="swift-review-email" value="' . $anonymous_email . '"/>
                    </div>';
    $op .= '        <div class="sr-field">
                        <label for="swift-review-title">Summary*</label>
                        <input type="text" name="swift_review_title" class="sr-form-control" id="swift-review-title"/>
                    </div>
                    <div class="sr-field">
                        <label for="swift-review-text">What\'s the primary reason for your answer?</label>
                        <textarea name="swift_review_text" rows="6" class="sr-form-control" id="swift-review-text"></textarea>
                    </div>';


    if ($get_swiftreviews_upsell == 1) {
        $op .= '    <div class="sr-field sr-youtube-field">
                        <label for="sr-youtube-video"><i class="fa fa-smile-o"></i> We\'re happy you\'re happy. Can we entice you to upload a video testimonial from your phone to YouTube? <br /><a href="https://www.youtube.com/upload" target="_blank">Click here to upload a video</a> - short & sweet is fine, nothing fancy - it just helps us find more great people like yourself to help.</label>
                        <input type="text" name="sr-youtube-video" class="sr-form-control" id="sr-youtube-video"/>
                    </div>';
    }

    $swift_review_improvements_flag = (isset($_GET['ratings']) && !empty($_GET['ratings']) && $_GET['ratings'] <= $get_reviews_stars) ? "display:block" : "display:none";
    $op .= '<div class="sr-field swift_review_improvements" style="' . $swift_review_improvements_flag . '">
                        <label for="swiftreviews_improvements">What could be improved upon? (Private / Won\'t be Published)</label>
                        <textarea name="swiftreviews_improvements" rows="6" class="sr-form-control" id="swiftreviews_improvements"></textarea>
                    </div>';

    $op .= '<div class="sr-field submit-field">
                        ' . $category_input . wp_nonce_field('swift-review-submit-nonce', 'swiftreview_security') . '
                        <input type="hidden" name="hiddenSwiftFormID" id="hiddenSwiftFormID" value="' . $get_sc_referrals_form_id . '" />
                        <button type="button" class="swift-review-submit" id="swift-review-submit" name="swift_review_submit">Send Feedback <i class="fa fa-send"></i></button>
                    </div>
                </form>
            </div>';

    /* 1. IF formID = null; Show the form, but show an error below the submit button, so user can see it work faster. */
    if (empty($get_sc_referrals_form_id)) {
        $op .= '<span class="formIDError">Whoa there! The SwiftCloud Form IDs are missing. Please go to <a href="' . site_url() . '/wp-admin/admin.php?page=swift-reviews">the settings page</a> to finish setup.</span>';
    }

    /*
     * Positive review form
     */

    $get_swift_review_mode = get_option('swift_review_mode');

    $op .= '<div id="sr-positive" class="sr-form-2" style="display:none;">';
    if ($get_swift_review_mode != 'off') {

        switch ($get_swift_review_mode) {
            case "business_mode": {
                    /* Referrals form */
                    $op .= do_shortcode("[swift_review_referrals]");

                    /* Share buttons */
                    $op .= do_shortcode("[swift_review_social_share]");
                    break;
                }
            case "consumer_mode": {
                    /* Share buttons */
                    $op .= do_shortcode("[swift_review_social_share]");

                    /* Referrals form */
                    $op .= do_shortcode("[swift_review_referrals]");
                    break;
                }
            case "referrals_only": {
                    /* Referrals form */
                    $op .= do_shortcode("[swift_review_referrals]");
                    break;
                }
            case "social_syndication_only": {
                    /* Share buttons */
                    $op .= do_shortcode("[swift_review_social_share]");
                    break;
                }
            default : {
                    
                }
        }
    }

    /* Photo contest */
    $op .= do_shortcode("[swift_review_photo_contest]");

    /* Coupon- Discount */
    $op .= do_shortcode("[swift_review_coupon]");

    $op .= '</div>'; //positive sectoin div

    /*
     * Nagetive review form
     */

    $get_swiftreviews_feedback_section_html = get_option('swiftreviews_feedback_section_html');

    $op .= '<div id="sr-nagative" class="swift-negative-review-form-content sr-form-2" style="display:none;">
            <div class="sr-form-title">' . stripslashes($get_swiftreviews_feedback_section_html) . '</div>
            ' . do_shortcode("[swift_review_helpdesk_form]") . '</div>
          </div>';

    /**/
    $op .= '<div class="plugin-credit swiftcloud_credit">Powered by
            <a href="https://SwiftCRM.com/" target="_blank">SwiftCloud</a>&nbsp;
            <a href="https://wordpress.org/plugins/advocate-marketing/" target="_blank">Wordpress Customer Testimonials Plugin</a> &nbsp;/&nbsp;
            <a href="https://SwiftCRM.com/products/customer-satisfaction-software?utm_source=content&utm_medium=WP_plugin&utm_content=Viral_Link_Footer&utm_campaign=WP_plugin_Reviews&pr=210P29" target="_blank">Customer Satisfaction Software</a>
          </div>';
    return $op;
}

/*
 *       Shortcode : [swift_review_referrals]
 *       - show referrals form
 */
add_shortcode('swift_review_referrals', 'swift_review_referrals_shortcode_callback');

function swift_review_referrals_shortcode_callback() {
    $op = '';

    $get_sc_referrals_form_id = get_option("swiftreviews_swiftcloud_referrals_form_id");
    if (empty($get_sc_referrals_form_id)) {
        return '<p class="sr-error">Heads up! Your form will not display until you add a form ID number in the control panel.</p>';
    }
    $get_swiftreviews_phone = get_option('swiftreviews_phone');
    $get_swiftreviews_referral_section_html = get_option('swiftreviews_referral_section_html');

    $op .= '<div class="swift-positive-review-form-content">';
    $op .= '<div class="sr-form-title">' . stripslashes($get_swiftreviews_referral_section_html) . '</div>
                <form method="post" id="swift-positive-review-form" class="swift-positive-review-form">
                    <div class="sr-referrals-fields">';

    for ($cn = 1; $cn <= 3; $cn++) {
        $phoneStr = ($get_swiftreviews_phone == 1) ? '<input type="text" name="swift_additionalcontact_' . $cn . '_phone" class="swift-review-referrals-phone" placeholder="Phone"/>' : '';
        $op .= '
                        <div class="sr-ref-field sr-field">
                            <input type="text" name="swift_additionalcontact_' . $cn . '_name" class="swift-review-referrals-name" placeholder="Name"/>
                            <input type="text" name="swift_additionalcontact_' . $cn . '_email" role="40" class="swift-review-referrals" placeholder="Email"/>
                            ' . $phoneStr .
                ($cn == 3 ? '<button type="button" class="sr-add-field"><i class="fa fa-plus-circle fa-lg"></i></button>' : '') .
                '</div>';
    }
    $op .= '

                        <div class="sr-ref-field-buttons submit-field">
                            <input name="ip_address" id="ip_address" type="hidden" value="' . $_SERVER['SERVER_ADDR'] . '">
                            <input name="browser" id="SC_browser" type="hidden" value="' . $_SERVER['HTTP_USER_AGENT'] . '">
                            <input name="trackingvars" id="trackingvars" class="trackingvars" type="hidden">
                            <input name="extra_swift_review_title" class="swift_review_title" type="hidden">
                            <input name="name" class="swift_review_name" id="name" type="hidden">
                            <input name="email" id="email" class="swift_review_email" type="hidden">
                            <input id="SC_fh_timezone" class="SC_fh_timezone" type="hidden" value="" name="timezone">
                            <input id="SC_fh_language" class="SC_fh_language" type="hidden" value="" name="language">
                            <input id="SC_fh_capturepage" class="SC_fh_capturepage" type="hidden" value="" name="capturepage">
                            <input value="' . $get_sc_referrals_form_id . '" id="formid" name="formid" type="hidden">
                            <input type="hidden" name="vTags" id="vTags" value="#support #helpdesk #website_support">
                            <input id="sc_lead_referer" class="sc_lead_referer" type="hidden" value="" name="sc_lead_referer"/>
                            <input type="hidden" value="817" name="iSubscriber">
                            <input id="sc_referer_qstring" type="hidden" value="" name="sc_referer_qstring"/>
                            <input id="extra_total_referrer" type="hidden" value="3" name="extra_total_referrer"/>
                            ' . wp_nonce_field('swift-review-referrals-nonce', 'swiftreview_referrals_security') . '
                            <button type="button" class="swift-referrals-submit" id="swift-referrals-submit" name="swift_referrals_submit"><i class="fa fa-gift"></i> Send Gift & Introduce Us <i class="fa fa-send"></i></button>
                        </div>
                    </div>
                </form>
          </div>';
    return $op;
}

/*
 *       Shortcode : [swift_review_photo_contest]
 *       - show content and form(upload field,submit button) of photo contest
 */
add_shortcode('swift_review_photo_contest', 'swift_review_photo_contest_callback');

function swift_review_photo_contest_callback() {
    $op = '';

    $get_photo_contest_onoff = get_option("swiftreviews_upsell");
    if ($get_photo_contest_onoff == 1) {

        $get_photo_contest_formid = get_option('swiftreviews_photo_video_contest_form_id');
        if (empty($get_photo_contest_formid)) {
            return '<p class="sr-error">Heads up! Your form will not display until you add a form ID number in the control panel.</p>';
        }

        $get_photo_contest_html = get_option('swiftreviews_photo_video_contest_html');
        $get_swiftreviews_photo_video_contest_title = get_option('swiftreviews_photo_video_contest_title');

        $op .= '<div class="sr-photo-contest-wrap">';
        $op .= (!empty($get_swiftreviews_photo_video_contest_title)) ? '<h2 class="sr-photo-contest-title">' . $get_swiftreviews_photo_video_contest_title . '</h2>' : '';
        $op .= stripslashes($get_photo_contest_html) . "<br /><br />";
        $op .= '<div class="photo-contest-tabs-wrap">';
        $op .= '<div class="photo-contest-tabs">';
        $op .= '<ul><li class="ph-active ph-tab" data-content="#ph-upload"><i class="fa fa-upload"></i> Upload</li><li class="ph-tab" data-content="#ph-url"><i class="fa fa-youtube-play"></i> URL</li></ul>';
        $op .= '</div>';

        //photo contest
        $op .= '<div class="photo-contest-tab-content ph-active" id="ph-upload">';
        $op .= '<form method="post" enctype="multipart/form-data" name="frmPhotoContest" id="frmPhotoContest">';
        $op .= '<div id="sr_photo_contest_upload_area" class="droppable"><h3>Drop your files here <br/>-- OR --<br/> Click here</h3></div>';
        $op .= '<input name="name" class="swift_review_name" id="name" type="hidden">
                <input name="ip_address" id="ip_address" type="hidden" value="' . $_SERVER['SERVER_ADDR'] . '">
                <input name="browser" id="SC_browser" type="hidden" value="' . $_SERVER['HTTP_USER_AGENT'] . '">
                <input name="trackingvars" id="trackingvars" class="trackingvars" type="hidden">
                <input name="extra_swift_review_title" class="swift_review_title" type="hidden">
                <input name="email" id="email" class="swift_review_email" type="hidden">
                <input id="SC_fh_timezone" class="SC_fh_timezone" type="hidden" value="" name="timezone">
                <input id="SC_fh_language" class="SC_fh_language" type="hidden" value="" name="language">
                <input id="SC_fh_capturepage" class="SC_fh_capturepage" type="hidden" value="" name="capturepage">
                <input value="' . $get_photo_contest_formid . '" id="formid" name="formid" type="hidden">
                <input id="sc_lead_referer" class="sc_lead_referer" type="hidden" value="" name="sc_lead_referer"/>
                <input type="hidden" value="817" name="iSubscriber">
                <input type="hidden" name="vTags" id="vTags" value="#swift_reviews #photocontest #website_support">
                <input id="sc_referer_qstring" type="hidden" value="" name="sc_referer_qstring"/>';
        $op .= wp_nonce_field('swift-review-photo-contest-nonce', 'swiftreview_photo_contest_security');
        $op .= '<button type="button" name="sr_photo_contest_submit" id="sr_photo_contest_submit" class="sr_photo_contest_upload" value="Upload" />Send Contest Entry <i class="fa fa-send"></i></button>';
        $op .= '<br/><small>(only jpg, jpeg, png and gif images allowed.)</small>';
        $op .= '</form><div class="sr-preview-imgs"></div>';
        $op .= '</div>'; //tab1

        /* video url */
        $op .= '<div class="photo-contest-tab-content" id="ph-url" style="display: none;">';
        $op .= '<form method="post" enctype="multipart/form-data" name="frmVideoURL" id="frmVideoURL">';
        $op .= '<label for="sr_video_url">URL to Video or Photo</label>';
        $op .= '<input name="extra_sr_video_url" id="sr_video_url" class="sr_video_url" type="text" placeholder="">';
        $op .= '<input name="name" class="swift_review_name" id="name" type="hidden">
                <input name="email" id="email" class="swift_review_email" type="hidden">
                <input name="ip_address" id="ip_address" type="hidden" value="' . $_SERVER['SERVER_ADDR'] . '">
                <input name="browser" id="SC_browser" type="hidden" value="' . $_SERVER['HTTP_USER_AGENT'] . '">
                <input name="trackingvars" class="trackingvars" id="trackingvars" class="trackingvars" type="hidden">
                <input name="extra_swift_review_title" class="swift_review_title" type="hidden">
                <input id="SC_fh_timezone" class="SC_fh_timezone" type="hidden" value="" name="timezone">
                <input id="SC_fh_language" class="SC_fh_language" type="hidden" value="" name="language">
                <input id="SC_fh_capturepage" class="SC_fh_capturepage" type="hidden" value="" name="capturepage">
                <input value="' . $get_photo_contest_formid . '" id="formid" name="formid" type="hidden">
                <input id="sc_lead_referer" class="sc_lead_referer" type="hidden" value="" name="sc_lead_referer"/>
                <input type="hidden" value="817" name="iSubscriber">
                <input type="hidden" name="vTags" id="vTags" value="#swift_reviews #photocontest #website_support">
                <input id="sc_referer_qstring" type="hidden" value="" name="sc_referer_qstring"/>';
        $op .= wp_nonce_field('swift-review-video-url-nonce', 'swiftreview_video_url_security');
        $op .= '<div class="video-url-btn-section"><button type="button" name="sr_video_url_submit" id="sr_video_url_submit" class="sr_photo_contest_upload" value="Upload" />Send Contest Entry <i class="fa fa-send"></i></button></div>';
        $op .= '</form>';
        $op .= '</div>'; //tab2
        $op .= '</div>'; //tab wrap
        $op .= '</div>';
    }
    return $op;
}

/*
 *      Shortcode : [swift_review_coupon]
 *      Coupon/Discount
 */
add_shortcode('swift_review_coupon', 'swift_review_coupon_callback');

function swift_review_coupon_callback() {
    $op = '';
    $get_swiftreviews_coupon_discount = get_option('swiftreviews_coupon_discount');

    if ($get_swiftreviews_coupon_discount == 1) {
        $get_swiftreviews_coupon_discount_html = get_option('swiftreviews_coupon_discount_html');
        $op .= '<div class="sr-coupon-discount-wrap">';
        $op .= stripslashes($get_swiftreviews_coupon_discount_html);
        $op .= '</div>';
    }
    return $op;
}

/*
 *      Shortcode : [swift_review_social_share]
 *      FB, Twitter,G+ etc... share review
 */
add_shortcode("swift_review_social_share", "swift_review_social_share_callback");

function swift_review_social_share_callback() {
    //share modal
    $get_swiftreviews_listing_page = get_option('swiftreviews_listing_page');
    $review_link = get_permalink($get_swiftreviews_listing_page);

    $default_text = get_option('swiftreviews_social_share_default_text');
    //$default_text = !empty($default_text) ? stripslashes($default_text) : 'i just rated ' . get_bloginfo('name') . ' as  stars!';
    $social_providers = array(
        'google' => array('name' => 'Google', 'color' => '#dd4b39', 'icon' => '<i class="fa fa-google"></i>', 'container_id' => 'sr-gpluse-content', 'link' => 'https://plus.google.com/share?url=' . $review_link),
        'facebook' => array('name' => 'Facebook', 'color' => '#3b5998', 'icon' => '<i class="fa fa-facebook"></i>', 'container_id' => 'sr-fb-content', 'link' => 'https://www.facebook.com/sharer/sharer.php?u=' . $review_link),
        'twitter' => array('name' => 'Twitter', 'color' => '#55acee', 'icon' => '<i class="fa fa-twitter"></i>', 'container_id' => 'sr-twitter-content', 'link' => 'https://twitter.com/share?url=' . $review_link),
        'linkedin' => array('name' => 'LinkedIn', 'color' => '#007bb6', 'icon' => '<i class="fa fa-linkedin"></i>', 'container_id' => 'sr-linkedin-content', 'link' => '#'),
        'yelp' => array('name' => 'Yelp', 'color' => '#d32323', 'icon' => '<i class="fa fa-yelp"></i>', 'container_id' => 'sr-yelp-content', 'link' => '#'),
        'zillow' => array('name' => 'Zillow', 'color' => '#0074e4', 'icon' => '', 'container_id' => 'sr-zillow-content', 'link' => '#'),
        'pinterest' => array('name' => 'Pinterest', 'color' => '#cb2027', 'icon' => '<i class="fa fa-pinterest-p"></i>', 'container_id' => 'sr-pinterest-content', 'link' => '#'),
        'instagram' => array('name' => 'Instagram', 'color' => '#8a3ab9', 'icon' => '<i class="fa fa-instagram"></i>', 'container_id' => 'sr-instagram-content', 'link' => '#'),
    );

    $op = '<div class="sr-share-wrap">';
    $op .= '<h3><center>Share the Love!</center></h3>
          <p>We appreciate the feedback and kind words. Can we ask one more tiny favor? Share your review online - it\'s easy and quick and will help us find more great people like yourself and keep our costs low.<p/>
          <!--<button class="sr-round-icon sr-btn-share" data-toggle="modal" data-target="#sr-share-box">Click to share</button>-->
          <p>Sad but true: Unhappy people are statistically 800% (yep, 8 times!) more likely to share online - this system helps our online reputation reflect our real-world scores.</p>';

    $tabs = $container = '';
    $cnt = 1;

    foreach ($social_providers as $key => $scoial_pro) {
        $sr_social_prov = "social_share_" . $key;
        $sr_social_prov_url = "social_share_" . $key . "_url";
        $sr_social_prov_val = get_option("swiftreviews_" . $sr_social_prov);
        $sr_social_prov_url_val = get_option("swiftreviews_" . $sr_social_prov_url);

        $active_class = $cnt == 1 ? 'sr-active' : '';
        $cnt++;

        if ($sr_social_prov_val == 1 && !empty($sr_social_prov_url_val) && $sr_social_prov_url_val != "#") {
            $bg = $bdr_color = '';
            $bg = !empty($scoial_pro['color']) ? 'background:' . $scoial_pro['color'] . ';' : '';
            $bdr_color = !empty($scoial_pro['color']) ? 'border-color:' . $scoial_pro['color'] . ';' : '';

            $tabs .= '<li class="sr-tabs ' . $active_class . '" style="' . $bg . '" data-tab-content="#' . $scoial_pro['container_id'] . '">' . $scoial_pro['icon'] . ' ' . $scoial_pro['name'] . '</li>';

            $container .= '<div id="' . $scoial_pro['container_id'] . '" class="sr-tab-content ' . $active_class . '"><textarea class="sr-share-textarea" rows="2" style="' . $bdr_color . '">' . stripslashes($default_text . " " . $review_link) . '</textarea>';
            $container .= '<a class="sr-share-btn sr-round-icon" data-provider="' . $key . '" title="Click to share on ' . $scoial_pro['name'] . '" href="javascript:void(0);"  data-href="' . $sr_social_prov_url_val . '" target="_blank" style="' . $bg . 'text-decoration:none;box-shadow:none;">' . $scoial_pro['icon'] . ' Click to share on ' . $scoial_pro['name'] . '</a>';
            $container .= '</div>';
        }
    }
    $op .= '<div id="sr-share-box" class="sr-modal"><div class="modal-body">';
    $op .= '<ul>' . $tabs . '</ul>';
    $op .= $container;
    $op .= '</div></div>';
    $op .= '</div>'; //share warp

    return $op;
}

/*
 *      Shortcode : [swift_reviews_listing star_style="5stars/10stars" category="category slug"]
 *      - Display reviews listing.
 *      - star_style : optional; 5stars/10stars; Show stars style default no style
 *      - category : optional; category slug; Show reviews in a specific category
 */
add_shortcode('swift_reviews_listing', 'swift_review_listing_callback');

function swift_review_listing_callback($atts) {
    $op = '';
    $a = shortcode_atts(
            array(
        'category' => '',
        'star_style' => '',
            ), $atts);
    extract($a);

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
        "@type" => "Product",
        "name" => get_option('blogname'),
        "image" => get_option("swiftreview_microformat_logo"),
//    "url" => site_url(),
//    "priceRange" => "$$"
    );

    $sr_paged = (get_query_var('paged') ) ? get_query_var('paged') : 1;

    $args = array(
        'post_type' => 'swift_reviews',
        'post_status' => 'publish',
        'posts_per_page' => $swiftreviews_review_per_page,
        'paged' => $sr_paged,
        'orderby' => 'id',
        'order' => 'DESC'
    );

    if ($category) {
        $args['tax_query'] = array(array('taxonomy' => 'swift_reviews_category', 'field' => 'slug', 'terms' => $category));
    }

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

    $op .= '<div class="swift-review-listing">';

    while ($reviews->have_posts()) : $reviews->the_post();
        $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
        $reviewer_email = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_email', true);
        $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
        $reviewer_type = get_post_meta(get_the_ID(), 'swiftreviews_rating_type', true);
        $get_vote_counts = get_post_meta(get_the_ID(), "swift_reviews_votes", true);

        $op .= '<div class="sr-list-item">';

        //left side img
        $op .= '<div class="sr-item-left"><a href="' . get_permalink(get_the_ID()) . '"><img src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></a></div>';
        $op .= '<div class="sr-item-right">';
        //ratings
        $op .= '<div class="review-rates">';
        $op .= '<a href="' . get_permalink(get_the_ID()) . '">' . buildStarRating($star_style, $rating) . '</a>';
        $op .= "</div>";

        $review_body = get_the_content();
        $review_body = apply_filters('the_content', $review_body);
        $op .= '<div class="sr-summary"><a href="' . get_permalink(get_the_ID()) . '">' . ucfirst(get_the_title()) . '</a></div>';
        $op .= '<div class="sr-comments"><a href="' . get_permalink(get_the_ID()) . '">' . $review_body . '</a></div>';
        $op .= '<div class="sr_meta_info">';
        $op .= '<div class="sr-reviewer-name"><span class="reviewer-name">' . ucfirst($reviewer_name) . '</span> <br /> <span ' . ($swiftreview_date_flag ? "style='display: block;'" : "style='display: none;'") . '>' . get_the_time('l, F jS, Y', get_the_ID()) . "</span></div>";
        $op .= '<div class="swift-reviews-tags-wrap">' . get_the_term_list(get_the_ID(), 'swift_reviews_category', '<ul class="swift-reviews-tags-list"><li>', '</li><li>', '</li></ul>') . '</div>';
        $op .= '</div>';
        $op .= '</div></div>';

        $rev_arr[] = array(
            "@type" => "Review",
//            "name" => ucfirst($reviewer_name),
            "author" => array(
                "@type" => "Person",
                "name" => ucfirst($reviewer_name)
            ),
            "datePublished" => get_the_time('Y-m-d', get_the_ID()),
            "reviewBody" => strip_tags($review_body),
            "reviewRating" => array(
                "@type" => "Rating",
                "ratingValue" => $rating,
                "bestRating" => "5",
                "worstRating" => "0"
            )
        );
    endwhile;

    $op .= '</div>';    // .swift-review-listing

    $get_swiftreviews_review_form_page_id = get_option('swiftreviews_review_form_page');
    if ($get_swiftreviews_review_form_page_id) {
        $op .= '<div class="sr-review-page-link-section"><h3>Why not <a href="' . get_permalink($get_swiftreviews_review_form_page_id) . '">click here</a> to add your own review now?</h3></div>';
    }
    $op .= '<div class="sr-pagination"><div class="sr-pre">' . get_previous_posts_link("Previous", $reviews->max_num_pages) . '</div><div class="sr-next">' . get_next_posts_link("Next", $reviews->max_num_pages) . '</div></div>';

    wp_reset_postdata();

    $op .= '<div class="plugin-credit">Powered by
            <a href="https://SwiftCRM.com/" target="_blank">SwiftCloud</a>&nbsp;
            <a href="https://wordpress.org/plugins/advocate-marketing/" target="_blank">Wordpress Customer Testimonials Plugin</a>';
    $op .= '</div>';

    $schema_arr['review'] = $rev_arr;
    $schema_arr['aggregateRating'] = array(
        "@type" => "AggregateRating",
        "ratingValue" => number_format($aggregate_score, 2),
        "ratingCount" => $totalReviews,
        "reviewCount" => $totalReviews,
        "bestRating" => "5",
        "worstRating" => "0",
    );

    $op .= '<script type="application/ld+json">';
    $op .= json_encode($schema_arr);
    $op .= '</script>';

    return $op;
}

/*
 *      Shortcode : [swift_positive_reviews star_style="5stars/10stars" category="category id"]
 *      - Display all positive reviews listing.
 *      - star_style : optional; 5stars/10stars; Show stars style default no style
 *      - category: category id; optional; Show positive reviews in a specific category
 */
add_shortcode('swift_positive_reviews', 'swift_positive_reviews_listing_callback');

function swift_positive_reviews_listing_callback($atts) {
    $op = '';
    $a = shortcode_atts(
            array(
        'category' => '',
        'star_style' => ''
            ), $atts);
    extract($a);

    $get_positive_reviews = get_option("swiftreviews_auto_publish_positive_reviews");
    $swiftreviews_review_per_page = (get_option("swiftreviews_review_per_page")) ? get_option("swiftreviews_review_per_page") : 10;
    $swiftreview_date_flag = get_option("swiftreview_date_flag");
    $logo_url = '';
    if ($swiftreview_microformat_logo = get_option("swiftreview_microformat_logo")) {
        $logo_url = $swiftreview_microformat_logo;
    }

    $sr_paged = (get_query_var('paged') ) ? get_query_var('paged') : 1;

    $rev_arr = array();
    $schema_arr = array(
        "@context" => "http://schema.org",
        "@type" => "Product",
        "name" => get_option('blogname'),
        "image" => get_option("swiftreview_microformat_logo"),
//    "url" => site_url(),
//    "priceRange" => "$$"
    );

    $args = array(
        'post_type' => 'swift_reviews',
        'post_status' => 'publish',
        'posts_per_page' => $swiftreviews_review_per_page,
        'paged' => $sr_paged,
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

    if ($category) {
        $args['tax_query'] = array(array('taxonomy' => 'swift_reviews_category', 'field' => 'slug', 'terms' => $category));
    }

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
    $op .= '<div class="swift-review-listing">';

    while ($reviews->have_posts()) : $reviews->the_post();
        $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
        $reviewer_email = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_email', true);
        $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
        $reviewer_type = get_post_meta(get_the_ID(), 'swiftreviews_rating_type', true);
        $reviewer_location = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_location', true);

        $op .= '<div class="sr-list-item">';

        //left side img
        $op .= '<div class="sr-item-left"><a href="' . get_permalink(get_the_ID()) . '"><img src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></a></div>';
        $op .= '<div class="sr-item-right">';
        //ratings
        $op .= '<div class="review-rates">';
        $op .= '<a href="' . get_permalink(get_the_ID()) . '">' . buildStarRating($star_style, $rating) . '</a>';
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
//            "name" => ucfirst($reviewer_name),
            "author" => array(
                "@type" => "Person",
                "name" => ucfirst($reviewer_name)
            ),
            "datePublished" => get_the_time('Y-m-d', get_the_ID()),
            "reviewBody" => strip_tags($review_body),
            "reviewRating" => array(
                "@type" => "Rating",
                "ratingValue" => $rating,
                "bestRating" => "5",
                "worstRating" => "0"
            )
        );
    endwhile;

    $op .= '</div>';    // .swift-review-listing



    /* pagination */
    $range = 2;
    $showitems = ($range * 2) + 1;

    global $paged;
    if (empty($paged))
        $paged = 1;

    $pages = $reviews->max_num_pages;
    if (!$pages) {
        $pages = 1;
    }

    if (1 != $pages) {
        $op .= "<div class='swift_pagination'>";
        if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
            $op .= "<a href='" . get_pagenum_link(1) . "'>&laquo;</a>";
        if ($paged > 1 && $showitems < $pages)
            $op .= "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a>";

        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                $op .= ($paged == $i) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a>";
            }
        }

        if ($paged < $pages && $showitems < $pages)
            $op .= "<a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a>";
        if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
            $op .= "<a href='" . get_pagenum_link($pages) . "'>&raquo;</a>";
        $op .= "</div>\n";
    }

//    $op .= '<div class="sr-pagination"><div class="sr-pre">' . get_previous_posts_link("Previous", $reviews->max_num_pages) . '</div><div class="sr-next">' . get_next_posts_link("Next", $reviews->max_num_pages) . '</div></div>';

    wp_reset_postdata();


    $get_swiftreviews_review_form_page_id = get_option('swiftreviews_review_form_page');
    if ($get_swiftreviews_review_form_page_id) {
        $op .= '<div class="sr-review-page-link-section"><h3>Why not <a href="' . get_permalink($get_swiftreviews_review_form_page_id) . '">click here</a> to add your own review now?</h3></div>';
    }

    $op .= '<div class="plugin-credit">Powered by
            <a href="https://SwiftCRM.com/" target="_blank">SwiftCloud</a>&nbsp;
            <a href="https://wordpress.org/plugins/advocate-marketing/" target="_blank">Wordpress Customer Testimonials Plugin</a>';
    $op .= '</div>';

    $schema_arr['review'] = $rev_arr;
    $schema_arr['aggregateRating'] = array(
        "@type" => "AggregateRating",
        "ratingValue" => number_format($aggregate_score, 2),
        "ratingCount" => $totalReviews,
        "reviewCount" => $totalReviews,
        "bestRating" => "5",
        "worstRating" => "0",
    );

    $op .= '<script type="application/ld+json">';
    $op .= json_encode($schema_arr);
    $op .= '</script>';

    return $op;
}

/*
 *       Shortcode : [reviewer_name]
 *          - Display reviewer name
 */

add_shortcode("reviewer_name", "swift_review_reviewer_name");

function swift_review_reviewer_name() {
    if (isset($_COOKIE['swift_reviews_publish']) && !empty($_COOKIE['swift_reviews_publish'])) {
        $name = get_post_meta($_COOKIE['swift_reviews_publish'], 'swiftreviews_reviewer_name', true);
        return (!empty($name) ? ucfirst($name) : '');
    }
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 */
function sr_get_gravatar($post_id) {
    if (has_post_thumbnail($post_id)) {
        $url = (get_the_post_thumbnail_url($post_id, 'thumbnail'));
    } else {
        $url = SWIFTREVIEWS__PLUGIN_URL . 'images/swiftreview_user_avatar.png';
    }
//    $url = 'https://www.gravatar.com/avatar/';
//    $url .= md5(strtolower(trim($email)));
//    $url .= "?s=$s&d=$d&r=$r";
//    if ($img) {
//        $url = '<img src="' . $url . '"';
//        foreach ($atts as $key => $val)
//            $url .= ' ' . $key . '="' . $val . '"';
//        $url .= ' />';
//    }
    return $url;
}

/*
 *       - same shortcode: swift_review_referrals
 *       Shortcode : [swift_review_refer_to_friend]
 *       - show referrals form
 */
add_shortcode('swift_review_refer_to_friend', 'swift_review_refer_to_friend_shortcode_callback');
if (!function_exists('swift_review_refer_to_friend_shortcode_callback')) {

    function swift_review_refer_to_friend_shortcode_callback() {
        $op = '';

        $get_sc_referrals_form_id = get_option("swiftreviews_swiftcloud_referrals_form_id");
        if (empty($get_sc_referrals_form_id)) {
            return '<p class="sr-error">Heads up! Your form will not display until you add a form ID number in the control panel.</p>';
        }
        $get_swiftreviews_phone = get_option('swiftreviews_phone');
        $get_swiftreviews_referral_section_html = get_option('swiftreviews_referral_section_html');

        $phoneStr = $get_swiftreviews_phone == 1 ? '<input type="text" name="extra_swift_review_referrals_phone[]" class="swift-review-referrals-phone" placeholder="Phone"/>' : '';

        $op .= '<div class="swift-positive-review-form-content">';
        $op .= '<div class="sr-form-title">' . stripslashes($get_swiftreviews_referral_section_html) . '</div>
                <form method="post" id="swiftreview-refer-to-friend-form" class="swift-positive-review-form">
                    <div class="sr-field-group">
                        <div class="sr-field-w-50 sr-field-name"><input type="text" name="name" id="name" class="sr-field-control" placeholder="Enter Your Name"/></div><div class="sr-field-w-50 sr-field-email"><input type="email" name="email" id="email" class="sr-field-control" placeholder="Enter Your Email"/></div>
                    </div>
                    <div class="sr-referrals-fields">';
        for ($cn = 1; $cn <= 3; $cn++) {
            $phoneStr = ($get_swiftreviews_phone == 1) ? '<input type="text" name="swift_additionalcontact_' . $cn . '_phone" class="swift-review-referrals-phone" placeholder="Phone"/>' : '';
            $op .= '
                        <div class="sr-ref-field sr-field">
                            <input type="text" name="swift_additionalcontact_' . $cn . '_name" class="swift-review-referrals-name" placeholder="Name"/>
                            <input type="text" name="swift_additionalcontact_' . $cn . '_email" role="40" class="swift-review-referrals" placeholder="Email"/>
                            ' . $phoneStr .
                    ($cn == 3 ? '<button type="button" class="sr-add-field"><i class="fa fa-plus-circle fa-lg"></i></button>' : '') .
                    '</div>';
        }
        $op .= '
                        <div class="sr-ref-field-buttons submit-field">
                            <input name="ip_address" id="ip_address" type="hidden" value="' . $_SERVER['SERVER_ADDR'] . '">
                            <input name="browser" id="SC_browser" type="hidden" value="' . $_SERVER['HTTP_USER_AGENT'] . '">
                            <input name="trackingvars" id="trackingvars" class="trackingvars" type="hidden">
                            <input id="SC_fh_timezone" class="SC_fh_timezone" type="hidden" value="" name="timezone">
                            <input id="SC_fh_language" class="SC_fh_language" type="hidden" value="" name="language">
                            <input id="SC_fh_capturepage" class="SC_fh_capturepage" type="hidden" value="" name="capturepage">
                            <input value="' . $get_sc_referrals_form_id . '" id="formid" name="formid" type="hidden">
                            <input type="hidden" name="vTags" id="vTags" value="#support #helpdesk #website_support">
                            <input id="sc_lead_referer" class="sc_lead_referer" type="hidden" value="" name="sc_lead_referer"/>
                            <input type="hidden" value="817" name="iSubscriber">
                            <input id="sc_referer_qstring" type="hidden" value="" name="sc_referer_qstring"/>
                            ' . wp_nonce_field('swift-review-refer-to-friend-nonce', 'swiftreview_refer_to_friend_security') . '
                            <button type="button" class="swiftreview-refer-to-friend-btn" id="swiftreview-refer-to-friend-submit" name="swiftreview_refer_to_friend_submit"><i class="fa fa-gift"></i> Send Gift & Introduce Us <i class="fa fa-send"></i></button>
                        </div>
                    </div>
                </form>
          </div>';
        return $op;
    }

}


/*
 *      [swift_review_slider title="FAQ Title" menu="" category="" style="" no_of_review=""]
 *      - This shortcode will show Review slider from menu id.
 *      - title  = Revie Title
 *      - menu = Menu Id
 */
add_shortcode('swift_review_slider', 'swift_review_slider_callback');

function swift_review_slider_callback($ls_atts) {
    ob_start();
    extract(shortcode_atts(array('title' => '', 'menu' => '', 'category' => '', 'style' => '', 'no_of_review' => ''), $ls_atts));
    $sr_slider_top = $sr_slider_bottom = $sr_slide_output = '';

    if (isset($menu) && !empty($menu)) {
        $sr_slider_menu = wp_get_nav_menu_items($menu);
        if (isset($sr_slider_menu) && !empty($sr_slider_menu)) {
            wp_enqueue_style('swift-review-slider', plugins_url('../css/swift-review-slider.css', __FILE__), '', '', '');
            wp_enqueue_style('swift-review-slick-carousel', plugins_url('../css/slick.css', __FILE__), '', '', '');
            wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../css/slick-theme.css', __FILE__), '', '', '');
            wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../js/slick.min.js', __FILE__), array('jquery'), '', true);

            $sr_slide_output = (isset($title) && !empty($title)) ? '<h2>' . $title . '</h2>' : '';

            // get first & last user avatar
            $sr_first_reviewer_avatar = SWIFTREVIEWS__PLUGIN_URL . "/images/swiftreview_user_avatar.png";
            $sr_last_reviewer_avatar = SWIFTREVIEWS__PLUGIN_URL . "/images/swiftreview_user_avatar.png";
            if (isset($sr_slider_menu[1]) && !empty($sr_slider_menu[1]) && isset($sr_slider_menu[1]->object_id) && !empty($sr_slider_menu[1]->object_id)) {
                $sr_first_review_author = $sr_slider_menu[1]->object_id;
                $sr_first_reviewer_email = get_post_meta($sr_first_review_author, 'swiftreviews_reviewer_email', true);
                $sr_first_reviewer_avatar = sr_get_gravatar($sr_first_review_author);
            }
            if (isset($sr_slider_menu[count($sr_slider_menu) - 1]->object_id) && !empty($sr_slider_menu[count($sr_slider_menu) - 1]->object_id)) {
                $sr_last_review_author = $sr_slider_menu[count($sr_slider_menu) - 1]->object_id;
                $sr_last_reviewer_email = get_post_meta($sr_last_review_author, 'swiftreviews_reviewer_email', true);
                $sr_last_reviewer_avatar = sr_get_gravatar($sr_last_review_author);
            }

            foreach ($sr_slider_menu as $sr_slide) {
                if (isset($sr_slide->object_id) && !empty($sr_slide->object_id)) {
                    $review_info = get_post($sr_slide->object_id);
                    if ($review_info) {
                        $rating = get_post_meta($review_info->ID, 'swiftreviews_ratings', true);
                        $reviewer_name = get_post_meta($review_info->ID, 'swiftreviews_reviewer_name', true);
                        $reviewer_email = get_post_meta($review_info->ID, 'swiftreviews_reviewer_email', true);

                        $sr_slider_bottom .= '<div class="item">';
                        $sr_slider_bottom .= '<div class="review-rates">';
                        $sr_slider_bottom .= buildStarRating('', $rating, false);
                        $sr_slider_bottom .= '</div>';
                        $sr_slider_bottom .= '<div class="client_name">' . $reviewer_name . '</div>';
                        $sr_slider_bottom .= nl2br(swift_reviews_get_excerpt(100, $review_info->ID, false));
                        $sr_slider_bottom .= '</div>';

                        $sr_slider_top .= '<div class="item">';
                        $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                        $sr_slider_top .= '</div>';
                    }
                }
            }

            $sr_slide_id = uniqid();
            $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top">' . $sr_slider_top . '</div>';
            $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_thumb" class="swift-review-slides">' . $sr_slider_bottom . '</div>';
            $sr_slide_output .= '
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                jQuery("#swift_review_slide_' . $sr_slide_id . '_thumb").slick({
                                    accessibility: true,
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    arrows: false,
                                    fade: true,
                                    dots: false,
                                    adaptiveHeight: false,
                                    asNavFor: "#swift_review_slide_' . $sr_slide_id . '_text"
                                });
                                jQuery("#swift_review_slide_' . $sr_slide_id . '_text").slick({
                                    slidesToShow: 5,
                                    slidesToScroll: 3,
                                    asNavFor: "#swift_review_slide_' . $sr_slide_id . '_thumb",
                                    dots: false,
                                    centerMode: true,
                                    adaptiveHeight: false,
                                    focusOnSelect: true,
                                    responsive: [{
                                        breakpoint: 768,
                                        settings: {
                                            slidesToShow: 3,
                                            slidesToScroll: 1,
                                            infinite: true,
                                            dots: false
                                        }
                                    },
                                    {
                                          breakpoint: 480,
                                          settings: {
                                            slidesToShow: 1,
                                            slidesToScroll: 1,
                                            infinite: true,
                                            dots: false
                                        }
                                    }]
                                });
                            });
                        </script>';
        }
    } else {
        $title = (!empty($title) ) ? esc_attr($title) : '';
        $number = (!empty($no_of_review) ) ? absint($no_of_review) : -1;
        $sr_style = (!empty($style) ) ? $style : 'sr_style_1';

        $slider_reviews_args = array(
            'post_type' => 'swift_reviews',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        );
        if (isset($category) && !empty($category)) {
            $slider_reviews_args['tax_query'] = array(array('taxonomy' => 'swift_reviews_category', 'field' => 'slug', 'terms' => $category));
        }
        $r = new WP_Query($slider_reviews_args);

        if ($sr_style === 'sr_style_2') {
            if ($r->have_posts()) {
                wp_enqueue_style('swift-review-slider', plugins_url('../css/swift-review-slider.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-carousel', plugins_url('../css/slick.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../css/slick-theme.css', __FILE__), '', '', '');
                wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../js/slick.min.js', __FILE__), array('jquery'), '', true);

                $sr_slider_top = $sr_slider_bottom = $sr_slide_output = '';
                if ($title) {
                    $sr_slide_output = '<h2>' . $title . '</h2>';
                }

                $sr_first_reviewer_avatar = SWIFTREVIEWS__PLUGIN_URL . "/images/swiftreview_user_avatar.png";
                $sr_last_reviewer_avatar = SWIFTREVIEWS__PLUGIN_URL . "/images/swiftreview_user_avatar.png";

                while ($r->have_posts()) : $r->the_post();
                    // get first post
                    if ($r->current_post == 1) {
                        $sr_first_reviewer_avatar = sr_get_gravatar(get_the_ID());
                    }
                    // get last post
                    if ($r->current_post == ($r->post_count - 1)) {
                        $sr_last_reviewer_avatar = sr_get_gravatar(get_the_ID());
                    }

                    setup_postdata($r);
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
                    $review_text = swift_reviews_get_excerpt(100, get_the_ID(), false);

                    $sr_slider_bottom .= '<div class="item">';
                    $sr_slider_bottom .= '<div class="review-rates">';
                    $sr_slider_bottom .= buildStarRating('', $rating, false);
                    $sr_slider_bottom .= '</div>';
                    $sr_slider_bottom .= '<div class="client_name">' . $reviewer_name . '</div>';
//                    $sr_slider_bottom .= apply_filters('the_content', get_post_field('post_content', get_the_ID()));
                    $sr_slider_bottom .= '<div class="review-details">';
                    $sr_slider_bottom .= '<p><a rel="ugc" href="' . get_permalink(get_the_ID()) . '">' . $review_text . '</a></p>';
                    $sr_slider_bottom .= '</div>';
                    $sr_slider_bottom .= '</div>';

                    $sr_slider_top .= '<div class="item">';
                    $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                    $sr_slider_top .= '</div>';
                endwhile;
                wp_reset_postdata();

                if (isset($_SESSION['swift_review_slide_cnt']) && !empty($_SESSION['swift_review_slide_cnt'])) {
                    $swift_review_slide_cnt = $_SESSION['swift_review_slide_cnt'];
                    $swift_review_slide_cnt++;
                } else {
                    $swift_review_slide_cnt = 1;
                    $_SESSION['swift_review_slide_cnt'] = 1;
                }
                $_SESSION['swift_review_slide_cnt'] = $swift_review_slide_cnt;
                $sr_slide_id = uniqid();

                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top">' . $sr_slider_top . '</div>';
                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_thumb" class="swift-review-slides swift_review_style_2">' . $sr_slider_bottom . '</div>';
                add_action('wp_footer', 'swift_review_style_2', 50, 1);
                do_action('wp_footer', $sr_slide_id);

//                $sr_slide_output .= '
//                    <script type="text/javascript">
//                jQuery(document).ready(function ($) {
//                    console.log("swift_review_style_2: ' . $sr_slide_id . '");
//                    jQuery("#swift_review_slide_' . $sr_slide_id . '_thumb").not(".slick-initialized").slick({
//                        slidesToShow: 1,
//                        slidesToScroll: 1,
//                        arrows: false,
//                        fade: true,
//                        dots: false,
//                        adaptiveHeight: false,
//                        asNavFor: "#swift_review_slide_' . $sr_slide_id . '_text",
//                            centerMode: true,
//                    });
//                    jQuery("#swift_review_slide_' . $sr_slide_id . '_text").not(".slick-initialized").slick({
//                        slidesToShow: 5,
//                        slidesToScroll: 3,
//                        asNavFor: "#swift_review_slide_' . $sr_slide_id . '_thumb",
//                        dots: false,
//                        centerMode: true,
//                        adaptiveHeight: false,
//                        focusOnSelect: true,
//                        responsive: [{
//                            breakpoint: 768,
//                            settings: {
//                                slidesToShow: 3,
//                                slidesToScroll: 1,
//                                infinite: true,
//                                dots: false
//            }
//                        },
//                        {
//                              breakpoint: 480,
//                              settings: {
//                                slidesToShow: 1,
//                                slidesToScroll: 1,
//                                infinite: true,
//                                dots: false
//                            }
//                        }]
//                    });
//                });
//            </script>';
//                add_action('wp_footer', 'swift_review_style_2', 50, 1);
//                do_action('wp_footer', $sr_slide_id);
            }
        } else if ($sr_style === 'sr_style_3') {
            if ($r->have_posts()) {
                wp_enqueue_style('swift-review-slider', plugins_url('../css/swift-review-slider.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-carousel', plugins_url('../css/slick.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../css/slick-theme.css', __FILE__), '', '', '');
                wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../js/slick.min.js', __FILE__), array('jquery'), '', true);

                $sr_slider_top = $sr_slide_output = '';
                if ($title) {
                    $sr_slide_output = '<h2>' . $title . '</h2>';
                }

                while ($r->have_posts()) : $r->the_post();
                    setup_postdata($r);
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
                    $reviewer_location = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_location', true);
                    $review_text = swift_reviews_get_excerpt(100, get_the_ID(), false);

                    $sr_slider_top .= '<div class="item">';
                    $sr_slider_top .= buildStarRating('', $rating, false);
                    $sr_slider_top .= '<div class="review-rates">';
                    $sr_slider_top .= '<div class="review-details">';
                    $sr_slider_top .= '<p><a rel="ugc" href="' . get_permalink(get_the_ID()) . '">' . (strlen($review_text) > 140 ? substr($review_text, 0, 140) . '</a><a href="' . get_permalink(get_the_ID()) . '" class="sr_read_more" target="_blank" aria-describedby="Read more about ' . $reviewer_name . '\'s review">...read more...</a>' : $review_text) . '</p>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '<div class="client_details">';
                    $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                    $sr_slider_top .= '<div class="client_name">' . $reviewer_name . '<div class="location">' . $reviewer_location . '</div></div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                endwhile;
                wp_reset_postdata();

                if (isset($_SESSION['swift_review_slide_cnt']) && !empty($_SESSION['swift_review_slide_cnt'])) {
                    $swift_review_slide_cnt = $_SESSION['swift_review_slide_cnt'];
                    $swift_review_slide_cnt++;
                } else {
                    $swift_review_slide_cnt = 1;
                    $_SESSION['swift_review_slide_cnt'] = 1;
                }
                $_SESSION['swift_review_slide_cnt'] = $swift_review_slide_cnt;
                $sr_slide_id = uniqid();

                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top swift_review_style_3">' . $sr_slider_top . '</div>';
//                $sr_slide_output .= '
//                    <script type="text/javascript">
//                jQuery(document).ready(function ($) {
//                console.log("swift_review_style_3: ' . $sr_slide_id . '");
//                    jQuery("#swift_review_slide_' . $sr_slide_id . '_text").not(".slick-initialized").slick({
//                        infinite: true,
//                        slidesToShow: 4,
//                        slidesToScroll: 1,
//                        autoplay: true,
//                        autoplaySpeed: 3000,
//                        dots: true,
//                        responsive: [
//                        {
//                          breakpoint: 1024,
//                          settings: {
//                            slidesToShow: 3,
//                            slidesToScroll: 1,
//                            infinite: true,
//                            dots: true,
//                            arrows: false
//                          }
//                        },
//                        {
//                          breakpoint: 600,
//                          settings: {
//                            slidesToShow: 2,
//                            slidesToScroll: 1,
//                            arrows: false
//                          }
//                        },
//                        {
//                          breakpoint: 480,
//                          settings: {
//                            slidesToShow: 1,
//                            slidesToScroll: 1,
//                            arrows: false
//                          }
//                        }
//                        // You can unslick at a given breakpoint now by adding:
//                        // settings: "unslick"
//                        // instead of a settings object
//                      ]
//                    });
//                });
//            </script>
//                        ';

                add_action('wp_footer', 'swift_review_style_3', 50, 1);
                do_action('wp_footer', $sr_slide_id);
            }
        } else if ($sr_style === 'sr_style_4') {
            if ($r->have_posts()) {
                wp_enqueue_style('swift-review-slider', plugins_url('../css/swift-review-slider.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-carousel', plugins_url('../css/slick.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../css/slick-theme.css', __FILE__), '', '', '');
                wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../js/slick.min.js', __FILE__), array('jquery'), '', true);

                $sr_slider_top = $sr_slide_output = '';
                if ($title) {
                    $sr_slide_output = '<h2>' . $title . '</h2>';
                }

                while ($r->have_posts()) : $r->the_post();
                    setup_postdata($r);
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
                    $reviewer_location = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_location', true);
                    $review_text = swift_reviews_get_excerpt(100, get_the_ID(), false);

                    $sr_slider_top .= '<div class="item">';
                    $sr_slider_top .= '<div class="review-rates">';
                    $sr_slider_top .= '<div class="review-details">';
                    $sr_slider_top .= buildStarRating('', $rating, false);
                    $sr_slider_top .= '<p><a rel="ugc" href="' . get_permalink(get_the_ID()) . '">' . $review_text . '</a></p>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '<div class="client_details">';
                    $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                    $sr_slider_top .= '<div class="client_name">' . $reviewer_name . '<div class="location">' . $reviewer_location . '</div></div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                endwhile;
                wp_reset_postdata();

                if (isset($_SESSION['swift_review_slide_cnt']) && !empty($_SESSION['swift_review_slide_cnt'])) {
                    $swift_review_slide_cnt = $_SESSION['swift_review_slide_cnt'];
                    $swift_review_slide_cnt++;
                } else {
                    $swift_review_slide_cnt = 1;
                    $_SESSION['swift_review_slide_cnt'] = 1;
                }
                $_SESSION['swift_review_slide_cnt'] = $swift_review_slide_cnt;
                $sr_slide_id = uniqid();

                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top swift_review_style_4">' . $sr_slider_top . '</div>';
                add_action('wp_footer', 'swift_review_style_4', 50, 1);
                do_action('wp_footer', $sr_slide_id);

//                add_action('wp_footer', 'swift_review_style_4', 50, 1);
//                do_action('wp_footer', $sr_slide_id);
            }
        } else {
            /**
             * Filters the arguments for the Recent Jobs widget.
             *
             * @param array $args An array of arguments used to retrieve the recent posts.
             */
            if ($r->have_posts()) :
                $sr_slide_output = '';
                $show_date = 0;
                if ($title) {
                    $sr_slide_output = '<h2>' . $title . '</h2>';
                }

                $sr_slide_output .= '<ul class="swift_review_latest_reviews">';
                while ($r->have_posts()) : $r->the_post();
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $review_link = get_permalink(get_the_ID());
                    $sr_slide_output .= '<li>';
                    $sr_slide_output .= '<div class="swift-review-widget-stars sr-ratings">';
                    $sr_slide_output .= '<div class="swift-review-widget-avatar">';
                    $sr_slide_output .= '<a href="' . $review_link . '">';
                    $sr_slide_output .= '<img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . get_the_title() . '" />';
                    $sr_slide_output .= '</a>';
                    $sr_slide_output .= '</div>';
                    $sr_slide_output .= '<div class="swift-review-widget-clientname">';
                    $sr_slide_output .= '<a rel="ugc" href="' . $review_link . '">';
                    $sr_slide_output .= get_the_title();
                    $sr_slide_output .= '<div class="stars-out-of">';
                    $sr_slide_output .= buildStarRating('', $rating, false);
                    $sr_slide_output .= '</div>';
                    $sr_slide_output .= ($show_date) ? '<span class="swift-review-widget-date">' . get_the_time('l, F jS, Y', get_the_ID()) . '</span>' : '';
                    $sr_slide_output .= '</a>';
                    $sr_slide_output .= '</div>';
                    $sr_slide_output .= '</div>';
                    $sr_slide_output .= '</li>';
                endwhile;
                $sr_slide_output .= '</ul>';
                wp_reset_postdata();
            endif;
        }
    }


    return $sr_slide_output;
}
