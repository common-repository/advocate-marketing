<?php

/*
 *      SwiftReviews helpdesk shortcode
 *      Shortcode: [swift_review_helpdesk_form]
 */

add_shortcode('swift_review_helpdesk_form', 'swiftreview_helpdesk_form_callback');

function swiftreview_helpdesk_form_callback() {
    wp_enqueue_script('swift-form-jstz', SWIFTREVIEWS__PLUGIN_URL . "js/jstz.min.js", '', '', true);
    $get_helpdesk_form_id = get_option("swiftreviews_swiftcloud_referrals_form_id");
    $get_final_feedback_confirmation_page = get_option("swiftreviews_final_feedback_confirmation_page");

    if (empty($get_helpdesk_form_id)) {
        return '<p class="sr-error">Heads up! Your form will not display until you add a form ID number in the control panel.</p>';
    }

    $op = '';
    $op.='<div class="sr-helpdesk">
                <form id="swiftreview_helpdesk_form" method="post" action="https://portal.swiftcrm.com/f/fhx.php">
                    <div class="sr-field" id="sr-name-container" style="display: none">
                        <label for="name">Name</label>
                        <input name="name" class="swift_review_name" id="name" type="hidden">
                    </div>
                    <div class="sr-field" id="sr-email-container" style="display: none">
                        <label for="email">Email Address</label>
                        <input name="email" class="swift_review_email" id="email" type="text">
                    </div>
                    <div class="sr-field" id="sr-field1-container">
                        <!--<label for="sr_comments">Ouch! What happened?</label>-->
                        <textarea name="extra_sr_comments" id="sr_comments" rows="5" cols="20"></textarea>
                    </div>
                    <div id="sr-phone-container" class="sr-field">
                        <label for="phone">Optional: Phone</label>
                        <input name="phone" id="phone" type="text">
                    </div>

                    <input type="hidden" name="ip_address" id="ip_address" value="' . $_SERVER['SERVER_ADDR'] . '">
                    <input type="hidden" name="browser" id="SC_browser" value="' . $_SERVER['HTTP_USER_AGENT'] . '">
                    <input type="hidden" name="trackingvars" class="trackingvars" id="trackingvars" >
                    <input type="hidden" name="extra_swift_review_title" class="swift_review_title" >
                    <input type="hidden" name="extra_current_reviews" class="swift_current_reviews" >
                    <input type="hidden" name="timezone" value="" id="SC_fh_timezone" class="SC_fh_timezone">
                    <input type="hidden" name="language" id="SC_fh_language" class="SC_fh_language" value="" >
                    <input type="hidden" name="capturepage" id="SC_fh_capturepage" class="SC_fh_capturepage" value="">
                    <input type="hidden" name="formid" value="' . $get_helpdesk_form_id . '" id="formid" />
                    <input type="hidden" name="vTags" id="vTags" value="#support #helpdesk #website_support">
                    <input type="hidden" name="vThanksRedirect" value="' . get_permalink($get_final_feedback_confirmation_page) . '">
                    <input type="hidden" name="sc_lead_referer" id="sc_lead_referer" value=""/>
                    <input type="hidden" name="iSubscriber" value="817" >
                    <input type="hidden" name="sc_referer_qstring" value="" id="sc_referer_qstring" />
                    <div class="sr_btn_section submit-field">
                    ' . wp_nonce_field('swift-review-helpdesk-nonce', 'swiftreview_helpdesk_security') . '

                        <div id="btnContainer"></div>
                        <script type="text/javascript">
                            var button = document.createElement("button");
                            button.innerHTML = "Send to Management <i class=\'fa fa-send\'></i>";
                            var body = document.getElementById("btnContainer");body.appendChild(button);
                            button.id = "sr_helpdesk_submit";
                            button.name = "sr_helpdesk_submit";
                            button.className = "sr-btn";
                            button.value = "send";
                        </script>
                        <noscript>
                            <p style=\'color:red;font-size:18px;\'>JavaScript must be enabled to submit this form. Please check your browser settings and reload this page to continue.</p>
                        </noscript>
                    </div>
                </form>
          </div>';
    // <button type="button" id="sr_helpdesk_submit" class="sr-btn">Send to Management <i class="fa fa-send"></i></button>

    return $op;
}