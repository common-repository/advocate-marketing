jQuery(document).ready(function ($) {
    // Positive form
    jQuery("#FrmSRGeneralSettings").submit(function (e) {
        var valid = 0;
        jQuery(".message").remove();
        if (jQuery.trim(jQuery("#swiftreviews_swiftcloud_referrals_form_id").val()) === '') {
            jQuery("#first-inner-content").before('<div class="message notice notice-error below-h2"><p>Main SwiftCloud Form ID is required to enable this function.</p></div>');
            valid++;
        }

//        if (jQuery.trim(jQuery("#swiftreviews_helpdesk_form_id").val()) === '') {
//            jQuery("#first-inner-content").before('<div class="message notice notice-error below-h2"><p>SwiftCloud Form ID for Negative Reviews is required to enable this function.</p></div>');
//            valid++;
//        }

        if (valid > 0) {
            jQuery('html, body').animate({
                scrollTop: jQuery(".wrap").offset().top
            }, 1000);
            return  false;
        }
    });

    //Photo contest
    jQuery("#FrmSRPhotoContestSettings").submit(function (e) {
        var valid = 0;
        jQuery(".message").remove();
        if (jQuery('#swiftreviews_upsell_onoff:checkbox').is(':checked')) {
            if (jQuery.trim(jQuery("#swiftreviews_photo_video_contest_form_id").val()) === '') {
                jQuery("#first-inner-content").before('<div class="message notice notice-error below-h2"><p>Photo Contest Form ID is required to enable this function.</p></div>');
                jQuery("#swiftreviews_photo_video_contest_form_id").focus();
                valid++;
            }
        }
        if (valid > 0) {
            jQuery('html, body').animate({
                scrollTop: jQuery(".wrap").offset().top
            }, 1000);
            return false;
        }
    });

    // Tooltip
    if (jQuery(".ttip").length > 0) {
        jQuery(".ttip").tooltip();
    }

    /* plugin activation notice dismis.*/
    jQuery(".swift-review-notice .notice-dismiss").on('click', function () {
        var data = {
            'action': 'swift_review_dismiss_notice'
        };
        jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {

        });
    });

    /* Email swipes tabs*/
    jQuery(".sr-es-tabs li").on("click", function (e) {
        var tabContentId = jQuery(this).attr("data-content");

        jQuery(".sr-es-tabs").find(".sr-active").removeClass();

        jQuery(".sr-tab-content").hide();
        jQuery(tabContentId).fadeIn();
        jQuery(tabContentId).addClass('sr-active');
        jQuery(this).addClass('sr-active');

    });
    /* Email template change*/
    jQuery("#sr-template-selectbox").on("change", function () {
        var url = jQuery(this).attr("data-url");
        var templateID = jQuery(this).val();
        window.location.href = url + "&template=" + templateID;
    });

    /* Reports */
    // change reports by category
    jQuery("#swiftreviews_cat").on("change", function () {
        var catid = jQuery(this).val();
        var current_page_url = window.location.href.split('?')[0];
        if (catid !== '') {
            window.location.href = current_page_url + "?post_type=swift_reviews&page=swiftreviews_reports&catid=" + catid;
        } else {
            window.location.href = current_page_url + "?post_type=swift_reviews&page=swiftreviews_reports";
        }
    });
});//main

/*Select all text on single click*/
function selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
    }
}