/*------------ swift form -----------------*/
var $compain_var = getUrlVars()['utm_source'];
/*Set cookie if compaign vars exists*/
if ($compain_var === undefined) {
    //do nothing
} else {
    setCookie('compain_var', window.location.href);
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}
/*Cookie functions*/
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
}
jQuery(document).ready(function () {
    if (jQuery(".sr-tooltip").length > 0) {
        jQuery(".sr-tooltip").tooltipster();
    }

    /* swift form */
    if (jQuery('.SC_fh_timezone').length > 0) {
        /*var offset = new Date().getTimezoneOffset();
         var minutes = Math.abs(offset);
         var hours = (minutes / 60);
         var prefix = offset < 0 ? '+' : '-';
         jQuery('#SC_fh_timezone').val('GMT' + prefix + hours);*/
        jQuery('#SC_fh_timezone').val(jstz.determine().name());
    }
    if (jQuery('.SC_fh_capturepage').length > 0) {
        jQuery('.SC_fh_capturepage').val(window.location.origin + window.location.pathname);
    }
    if (jQuery('.SC_fh_language').length > 0) {
        jQuery('.SC_fh_language').val(window.navigator.userLanguage || window.navigator.language);
    }
    jQuery("#referer").val(document.URL);
    /*check if cookie exists then add the values in variable*/
    if (getCookie('compain_var')) {
        jQuery('.trackingvars').val(getCookie('compain_var'));
    }


    /* get the user rating and show improments field */
    jQuery("input[name='swift_review_rating']").on('click', function () {
        // What could be improved upon? (Private / Won't be Published)
        if (jQuery("input[name='swift_review_rating']:checked").val() < 5) {
            jQuery(".swift_review_improvements").show();
        } else {
            jQuery(".swift_review_improvements").hide();
        }

        // 
        if (jQuery("input[name='swift_review_rating']:checked").val() > 4) {
            jQuery(".sr-youtube-field").show();
        } else {
            jQuery(".sr-youtube-field").hide();
        }
    });


    /* ---- Review form validation and submit ---- */
    jQuery("#swift-review-submit").on("click", function (e) {
        var swift_form_id = jQuery.trim(jQuery("#hiddenSwiftFormID").val());
        if (swift_form_id == '') {
            e.preventDefault;
            return false;
        }
        jQuery("#swift-review-submit i").removeClass();
        jQuery("#swift-review-submit i").addClass('fa fa-spinner fa-pulse fa-lg fa-fw');

        var error = '';
        jQuery(".sr-error").remove();
        if (jQuery("input[name=swift_review_rating]:checked").length <= 0) {
            jQuery(".rating,.rating-10stars").after('<span class="sr-error" style="color: red; margin-bottom: 10px; width: 100%; float: left;">Please click the stars above to set your review.</span>');
            error++;
        }
        if (jQuery.trim(jQuery("#swift-review-title").val()) === '') {
            jQuery("#swift-review-title").after('<span class="sr-error" style="color:red;margin-bottom:10px;">Title is required.</span>');
            error++;
        }
        if (jQuery.trim(jQuery("#swift-review-reviewer-name").val()) === '') {
            jQuery("#swift-review-reviewer-name").after('<span class="sr-error" style="color:red;margin-bottom:10px;">Name is required.</span>');
            error++;
        }
        if (jQuery.trim(jQuery("#swift-review-email").val()) === '') {
            jQuery("#swift-review-email").after('<span class="sr-error" style="color:red;margin-bottom:10px;">Email is required.</span>');
            error++;
        } else {
            var email = ValidateEmail(jQuery.trim(jQuery("#swift-review-email").val()));
            if (!email) {
                jQuery("#swift-review-email").after('<span class="sr-error" style="color:red;margin-bottom:10px;">Enter valid email.</span>');
                jQuery("#swift-review-email").focus();
                error++;
            } else {
                jQuery(".swift_review_email").val(jQuery("#swift-review-email").val());
                jQuery(".swift_review_name").val(jQuery("#swift-review-reviewer-name").val());
                jQuery(".swift_review_title").val(jQuery("#swift-review-title").val());
                jQuery(".swift_current_reviews").val(jQuery("input[name='swift_review_rating']:checked").val());
            }
        }

        if (error > 0) {
            jQuery(this).removeAttr("disabled");
            jQuery('html, body').animate({
                scrollTop: jQuery("#swift-review-form").offset().top
            }, 500);
            jQuery("#swift-review-submit i").removeClass();
            jQuery("#swift-review-submit i").addClass('fa fa-send');
            e.preventDefault();
        } else {
            jQuery(".sr-error").remove();
            jQuery(this).attr("disabled", "disabled");
            jQuery(".sr-form-control").attr("readonly", "readonly");

            var data = {
                'action': 'swift_review_insert',
                'data': jQuery("#swift-review-form").serialize(),
                'swiftreview_security': jQuery('#swiftreview_security').val()
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                jQuery("#swift-review-submit").html('Feedback Sent!');
                jQuery("#swift-review-submit i").addClass('fa fa-edit');
                if (response == 1) {
                    var first_name = jQuery("#swift-review-reviewer-name").val().split(' ').slice(0, -1).join(' ');
                    jQuery("#swift-review-form .submit-field").after("<h2 class='sr-thanks-name'>Thanks " + first_name + "!</h2>");
                    jQuery("#sr-positive").fadeIn();
                    //jQuery(".sr-share-textarea").val('i just rated Swift Plugins as ' + jQuery(".rating input[name='swift_review_rating']:checked").val() + ' stars!');
                    jQuery('html, body').animate({
                        scrollTop: jQuery("#sr-positive").offset().top - 30
                    }, 1000);
                } else {
                    jQuery("#sr-nagative").fadeIn();
                    jQuery('html, body').animate({
                        scrollTop: jQuery("#sr-nagative").offset().top - 30
                    }, 1000);
                }
            });
        }
    });

    /* ---- Positive review form ---- */
    //    Add new field
    jQuery(".sr-add-field").on('click', function () {
        var fieldHtml = '';

        var randID = Math.floor(Math.random() * 9000) + 1000;
        var phoneOnOff = jQuery(".sr-referrals-fields").find('.swift-review-referrals-phone').length;
        var phoneField = (phoneOnOff > 0) ? '<input class="" name="swift_additionalcontact_1_phone" placeholder="Phone" type="text">' : '';

        fieldHtml += '<div class="sr-ref-field sr-field new-sr-field" id="' + randID + '">';
        fieldHtml += '<input type="text" name="swift_additionalcontact_1_name" class="swift-review-referrals-name" placeholder="Name"/>';
        fieldHtml += '<input type="text" name="swift_additionalcontact_1_email" role="40" class="swift-review-referrals" placeholder="Email"/>';
        fieldHtml += phoneField;
        fieldHtml += '<button type="button" class="sr-remove-field" data-row-id="' + randID + '"><i class="fa fa-minus-circle fa-lg"></i></button>';
        fieldHtml += '</div>';

        jQuery('.sr-ref-field').eq(1).after(fieldHtml);
        jQuery('#extra_total_referrer').val(jQuery(this).parents('.sr-referrals-fields').find('.sr-ref-field').length);

        jQuery(this).parents('.sr-referrals-fields').find('.sr-ref-field').each(function (i) {
            i++;
            jQuery(this).find('.swift-review-referrals-name').attr('name', 'swift_additionalcontact_' + i + '_name');
            jQuery(this).find('.swift-review-referrals').attr('name', 'swift_additionalcontact_' + i + '_email');
            jQuery(this).find('.swift-review-referrals-phone').attr('name', 'swift_additionalcontact_' + i + '_phone');
        });
    });

    // remove field
    jQuery(document).on('click', ".sr-remove-field", function () {
        var row_id = jQuery(this).attr('data-row-id');
        var ref_fields = jQuery(this).parents('.sr-referrals-fields');
        jQuery("#" + row_id).remove();
        jQuery('#extra_total_referrer').val(jQuery(ref_fields).find('.sr-ref-field').length);
        jQuery(ref_fields).find('.sr-ref-field').each(function (i) {
            i++;
            jQuery(this).find('.swift-review-referrals-name').attr('name', 'swift_additionalcontact_' + i + '_name');
            jQuery(this).find('.swift-review-referrals').attr('name', 'swift_additionalcontact_' + i + '_email');
            jQuery(this).find('.swift-review-referrals-phone').attr('name', 'swift_additionalcontact_' + i + '_phone');
        });
    });


    // validation
    jQuery("#swift-referrals-submit").on("click", function (e) {

        jQuery(".sr-error").remove();
        jQuery(".ref-loader").remove();
        jQuery("#swift-referrals-submit").after('<i class="ref-loader fa fa-spinner fa-pulse fa-lg fa-fw" style="position: absolute; margin: 10px 0px 0px 5px;"></i>');

        var error = '';
        var errorMsgPhone = '';
        var errorMsgEmail = '';
        var errorMsgName = '';
        jQuery(".swift-review-referrals,.swift-review-referrals-name,.swift-review-referrals-phone").removeClass('sr-error-bdr');
        /* validation */
//        jQuery(".swift-review-referrals-name").each(function() {
        if (jQuery.trim(jQuery(".swift-review-referrals-name").eq(0).val()) === '') {
            jQuery(".swift-review-referrals-name").eq(0).addClass('sr-error-bdr');
            errorMsgName = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter name.<br/></span>';
            error++;
        }
//        });

//        jQuery(".swift-review-referrals").each(function() {
        var ref_email = jQuery.trim(jQuery(".swift-review-referrals").eq(0).val());
        var emailRef = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/;
        if (ref_email === '') {
            jQuery(".swift-review-referrals").eq(0).addClass('sr-error-bdr');
            errorMsgEmail = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter email.<br/></span>';
            error++;
        } else {
            if (!emailRef.test(ref_email)) {
                jQuery(".swift-review-referrals").eq(0).addClass('sr-error-bdr');
                errorMsgEmail = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter valid email.<br/></span>';
                error++;
            }
        }
//        });

//        jQuery('.swift-review-referrals-phone').each(function() {
        if (jQuery('.swift-review-referrals-phone').length > 0) {
            var ref_phone = jQuery.trim(jQuery('.swift-review-referrals-phone').eq(0).val());
            var phoneReg = /^(\([0-9]{3}\)|[0-9]{3}-)[0-9]{3}-[0-9]{4}$/;
            if (ref_phone === '') {
                jQuery('.swift-review-referrals-phone').eq(0).addClass('sr-error-bdr');
                errorMsgPhone = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter phone.<br/></span>';
                error++;
            } else {
                if (!phoneReg.test(ref_phone)) {
                    jQuery('.swift-review-referrals-phone').eq(0).addClass('sr-error-bdr');
                    errorMsgPhone = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter valid phone.<br/></span>';
                    error++;
                }
            }
        }
//        });

        if (error > 0) {
            jQuery(this).removeAttr("disabled");
            jQuery("#swift-referrals-submit").before(errorMsgName + " " + errorMsgEmail + " " + errorMsgPhone);
            jQuery(".ref-loader").remove();
            e.preventDefault();
        } else {
            jQuery(".swift-review-referrals").removeClass('sr-error-bdr');
            jQuery(".swift-review-referrals-name").removeClass('sr-error-bdr');
            jQuery(".sr-error").remove();
            jQuery(this).attr("disabled", "disabled");
            var data = {
                'action': 'swift_review_referrals',
                'data': jQuery("#swift-positive-review-form").serialize(),
                'swiftreview_referrals_security': jQuery('#swiftreview_referrals_security').val()
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                if (response == 1) {
                    jQuery(".ref-loader").remove();
                    jQuery("#swift-referrals-submit").text("DONE!");
                    jQuery(".sr-ref-field-buttons").after("<p class='sr-success' style='text-align:center;margin-top:15px;'>Thanks.</p>");
                } else {
                    jQuery(".ref-loader").remove();
                    jQuery("#swift-referrals-submit").removeAttr("disabled");
                }
            });
        }
    });

    /* ----Negative form ---- */
    jQuery("#sr_helpdesk_submit").on("click", function (e) {
        jQuery(this).attr("disabled", "disabled");
        jQuery("#sr_helpdesk_submit i").removeClass();
        jQuery("#sr_helpdesk_submit i").addClass('fa fa-spinner fa-pulse fa-lg fa-fw');
        jQuery(".sr-error").remove();

        var error = '';

        if (jQuery.trim(jQuery("#sr_comments").val()) == '') {
            jQuery("#sr_comments").after('<span class="sr-error" style="color:red;margin-bottom:10px;">Field is required.</span>');
            error++;
        }

        if (error > 0) {
            jQuery("#sr_helpdesk_submit i").removeClass();
            jQuery("#sr_helpdesk_submit i").addClass('fa fa-send');
            jQuery("#sr_helpdesk_submit").removeAttr("disabled");
            e.preventDefault();
        } else {
            jQuery(this).attr("disabled", "disabled");

            var data = {
                'action': 'swift_negative_reviews',
                'data': jQuery("#swiftreview_helpdesk_form").serialize(),
                'swiftreview_helpdesk_security': jQuery('#swiftreview_helpdesk_security').val()
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                if (response == 1) {
                    jQuery("#sr_helpdesk_submit i").removeClass();
                    jQuery("#swiftreview_helpdesk_form").after("<p class='sr-success' style='text-align: center; margin: 0px;'>Thank you.</p>");
                } else {
                    jQuery("#sr_helpdesk_submit i").removeClass();
                }
            });
        }
    });

    //share on twitter
    jQuery("#sr-share-box .sr-twitter-icon").on("click", function (e) {
        e.preventDefault();
        var link = jQuery(this).attr("href");
        var text = jQuery(".sr-twitter-text").val();
        text = (text !== '') ? '&text=' + text : '';
        var tweet_link = encodeURI(link + text);
        window.open(tweet_link, '_blank');
    });
    // share on googlepluse
    jQuery("#sr-google-box .sr-google-icon").on("click", function (e) {
        e.preventDefault();
        var link = jQuery(this).attr("href");
        var google_link = encodeURI(link);
        window.open(google_link, '_blank');
    });
    // share on facebook
    jQuery("#sr-fb-box .sr-fb-icon").on("click", function (e) {
        e.preventDefault();
        var link = jQuery(this).attr("href");
        var fb_link = encodeURI(link);
        window.open(fb_link, '_blank');
    });


    /**
     *      Photo contest form submit
     */
    jQuery("#sr_photo_contest_submit").on("click", function (e) {
        var error = '';

        jQuery("#sr_photo_contest_submit i").removeClass();
        jQuery("#sr_photo_contest_submit i").addClass('fa fa-spinner fa-pulse fa-lg fa-fw');

        jQuery(".sr-error").remove();
        var uploadField = jQuery(".sr_contest_photo");

        // empty validation
        if (uploadField.length === 0) {
            jQuery("#sr_photo_contest_submit").before('<p class="sr-error">Please upload image.</p>');
            error++;
        }

        if (error > 0) {
            jQuery("#sr_photo_contest_submit i").removeClass();
            jQuery("#sr_photo_contest_submit i").addClass('fa fa-send');
            e.preventDefault();
        } else {
            jQuery(".sr-error").remove();
            jQuery("#sr_photo_contest_submit").attr("disabled", "disabled");

            var data = {
                action: 'swiftreview_photocontest',
                form_data: jQuery("#frmPhotoContest").serialize(),
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                jQuery("#sr_photo_contest_submit i").removeClass();
                if (response == 1) {
                    jQuery("#sr_photo_contest_upload").attr("disabled", "disabled");
                    jQuery("#sr_photo_contest_submit").before("<p class='sr-success' style='text-align: center; margin: 0px;'>Thank you.</p>");
                } else {
                    jQuery("#sr_photo_contest_submit i").addClass('fa fa-send');
                    jQuery("#sr_photo_contest_submit").removeAttr("disabled");
                }
            });
        }
    });

    /* share tabs */
    jQuery(".sr-tabs").on("click", function () {
        var tabContainer = jQuery(this).attr("data-tab-content");
        jQuery("#sr-share-box").find(".sr-active").removeClass('sr-active');
        jQuery(tabContainer).fadeIn();
        jQuery(".sr-tab-content").not(tabContainer).hide();
        jQuery(this, tabContainer).addClass("sr-active");
    });

    /* Share button */
    jQuery(".sr-share-btn").on("click", function (e) {
        e.preventDefault();
        var link = jQuery(this).attr("data-href");
        if (link != '' && link != "#") {
            var data = {
                'action': 'swiftreviews_share_url',
                'data': link,
                'dataProvider': jQuery(this).attr("data-provider")
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                if (response !== '0') {
                    window.open(response, '_blank');
                }
            });
        }
    });

    jQuery(".ph-tab").on("click", function (e) {
        var tabContentId = jQuery(this).attr("data-content");

        jQuery(".photo-contest-tabs").find(".ph-active").removeClass('ph-active');
        jQuery(".photo-contest-tab-content").find(".ph-active").removeClass('ph-active');

        jQuery(".photo-contest-tab-content").hide();
        jQuery(tabContentId).fadeIn();
        jQuery(tabContentId).addClass('ph-active');
        jQuery(this).addClass('ph-active');
    });

    jQuery("#sr_video_url_submit").on("click", function (e) {
        var error = '';
        jQuery(".sr-error").remove();
        jQuery("#sr_video_url_submit i").removeClass();
        jQuery("#sr_video_url_submit i").addClass('fa fa-spinner fa-pulse fa-lg fa-fw');

        if (jQuery.trim(jQuery("#sr_video_url").val()) == '') {
            jQuery("#sr_video_url").after('<span class="sr-error" style="color:red;margin-bottom:10px;">Field is required.</span>');
            error++;
        }

        if (error > 0) {
            jQuery("#sr_video_url_submit i").removeClass();
            jQuery("#sr_video_url_submit i").addClass('fa fa-send');
            e.preventDefault();
        } else {
            jQuery(".sr-error").remove();
            jQuery("#sr_video_url_submit").attr("disabled", "disabled");

            var data = {
                'action': 'swift_video_url',
                'data': jQuery("#frmVideoURL").serialize(),
                'swiftreview_video_url_security': jQuery("#swiftreview_video_url_security").val()
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                jQuery("#sr_video_url_submit i").removeClass();
                if (response == 1) {
                    jQuery(".sr_contest_photo").val("");//empty photo data url
                    jQuery(".srTempImg").remove();// remove preview image
                    jQuery("#sr_video_url_submit").after("<p class='sr-success' style='text-align: center; margin: 0px;'>Thank you.</p>");
                } else {
                    jQuery("#sr_video_url_submit i").addClass('fa fa-send');
                    jQuery("#sr_video_url_submit").removeAttr("disabled");
                }
            });
        }
    });

    /* widget corner */
    jQuery(".sr-widget-trigger").on("click", function () {
        jQuery(".sr-widget-container").slideToggle('slow', function () {
            jQuery(".sr-widget-trigger i").toggleClass("fa-angle-up fa-angle-down");
        });
    });

    /* Vote UP & Vote Down */
    jQuery(".sr-vote-up").on("click", function () {
        var thisWrap = jQuery(this);
        var review_id = jQuery(this).attr("data-review-id");

        jQuery(".vote-loader").remove();
        jQuery('#sr-cnt-' + review_id).html('<i class="vote-loader fa fa-spinner fa-pulse fa-fw" style="color:#686868 !important;font-size: 20px !important;"></i>');

        var data = {
            'action': 'swift_review_vote_up',
            'data': review_id
        };
        jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
            var res = jQuery.parseJSON(response);
            if (res.status == 'true') {
                jQuery(".vote-loader").remove();
                jQuery('#sr-cnt-' + res.review_id).html('');
                jQuery('#sr-cnt-' + res.review_id).html(res.vote_counts);
                jQuery(thisWrap).parent().addClass("sr-voted");
            }
        });
    });
    jQuery(".sr-vote-down").on("click", function () {
        var thisWrap = jQuery(this);
        var review_id = jQuery(this).attr("data-review-id");

        jQuery(".vote-loader").remove();
        jQuery('#sr-cnt-' + review_id).html('<i class="vote-loader fa fa-spinner fa-pulse fa-fw" style="color:#686868 !important;font-size: 20px !important;"></i>');

        var data = {
            'action': 'swift_review_vote_down',
            'data': review_id
        };
        jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
            var res = jQuery.parseJSON(response);
            if (res.status == 'true') {
                jQuery(".vote-loader").remove();
                jQuery('#sr-cnt-' + res.review_id).html('');
                jQuery('#sr-cnt-' + res.review_id).html(res.vote_counts);
            } else {
                jQuery(".vote-loader").remove();
                jQuery('#sr-cnt-' + res.review_id).html('0');
            }
        });
    });

    /* Refer to friend */
    // validation
    jQuery("#swiftreview-refer-to-friend-submit").on("click", function (e) {

        jQuery(".sr-error").remove();
        jQuery(".ref-loader").remove();
        jQuery("#swiftreview-refer-to-friend-submit").after('<i class="ref-loader fa fa-spinner fa-pulse fa-lg fa-fw" style="position: absolute; margin: 10px 0px 0px 5px;"></i>');

        var error = '';
        var errorMsgPhone = '';
        var errorMsgEmail = '';
        var errorMsgName = '';
        jQuery(".swift-review-referrals,.swift-review-referrals-name,.swift-review-referrals-phone").removeClass('sr-error-bdr');
        /* validation */
        //name
        if (jQuery.trim(jQuery("#name").val()) === '') {
            jQuery("#name").addClass('sr-error-bdr');
            jQuery("#name").after('<span class="sr-error" style="color:red;">Name is required.</span>');
            error++;
        }
        //email
        if (jQuery.trim(jQuery("#email").val()) === '') {
            jQuery("#email").addClass('sr-error-bdr');
            jQuery("#email").after('<span class="sr-error" style="color:red;">Email is required.</span>');
            error++;
        } else {
            var emailRef = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/;
            if (!emailRef.test(jQuery("#email").val())) {
                jQuery("#email").addClass('sr-error-bdr');
                jQuery("#email").after('<span class="sr-error" style="color:red;">Enter valid email.</span>');
                error++;
            }
        }

        jQuery(".swift-review-referrals-name").each(function () {
            if (jQuery.trim(jQuery(this).val()) === '') {
                jQuery(this).addClass('sr-error-bdr');
                errorMsgName = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter name.<br/></span>';
                error++;
            }
        });

        jQuery(".swift-review-referrals").each(function () {
            var ref_email = jQuery.trim(jQuery(this).val());
            var emailRef = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/;
            if (ref_email === '') {
                jQuery(this).addClass('sr-error-bdr');
                errorMsgEmail = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter email.<br/></span>';
                error++;
            } else {
                if (!emailRef.test(ref_email)) {
                    jQuery(this).addClass('sr-error-bdr');
                    errorMsgEmail = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter valid email.<br/></span>';
                    error++;
                }
            }
        });

        jQuery('.swift-review-referrals-phone').each(function () {
            var ref_phone = jQuery.trim(jQuery(this).val());
            var phoneReg = /^(\([0-9]{3}\)|[0-9]{3}-)[0-9]{3}-[0-9]{4}$/;
            if (ref_phone === '') {
                jQuery(this).addClass('sr-error-bdr');
                errorMsgPhone = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter phone.<br/></span>';
                error++;
            } else {
                if (!phoneReg.test(ref_phone)) {
                    jQuery(this).addClass('sr-error-bdr');
                    errorMsgPhone = '<span class="sr-error" style="color:red;margin-bottom:10px;">Enter valid phone.<br/></span>';
                    error++;
                }
            }
        });

        if (error > 0) {
            jQuery(this).removeAttr("disabled");
            jQuery("#swiftreview-refer-to-friend-submit").before(errorMsgName + " " + errorMsgEmail + " " + errorMsgPhone);
            jQuery(".ref-loader").remove();
            e.preventDefault();
        } else {
            jQuery(".swift-review-referrals").removeClass('sr-error-bdr');
            jQuery(".swift-review-referrals-name").removeClass('sr-error-bdr');
            jQuery(".sr-error").remove();
            jQuery(this).attr("disabled", "disabled");
            var data = {
                'action': 'swiftreview_refer_to_friend',
                'data': jQuery("#swiftreview-refer-to-friend-form").serialize(),
                'swiftreview_refer_to_friend_security': jQuery('#swiftreview_refer_to_friend_security').val()
            };
            jQuery.post(swiftreviews_ajax_object.ajax_url, data, function (response) {
                if (response == 1) {
                    jQuery(".ref-loader").remove();
                    jQuery("#swiftreview-refer-to-friend-submit").text("DONE!");
                    jQuery(".swiftreview-refer-to-friend-btn").after("<p class='sr-success' style='text-align:center;margin-top:15px;'>Thanks.</p>");
                } else {
                    jQuery(".ref-loader").remove();
                    jQuery("#swiftreview-refer-to-friend-submit").removeAttr("disabled");
                }
            });
        }
    });

    // for review archive page
    if (jQuery(".swift-reviews-archive-container").length > 0) {
        var nav = jQuery("body").find("nav").css("position");
        var header = jQuery("body").find("header").css("position");
        var padding = '';

        if (header === 'fixed' || header === 'absolute') {
            padding = jQuery("body").find("header").height() + 25;
        } else if (nav === 'fixed') {
            padding = jQuery("body").find("nav").height() + 25;
        }
        if (padding !== '') {
            jQuery(".swift-reviews-archive-container").css('padding-top', padding);
        }
    }

    // set fixed height for each review slider
//    if(jQuery(".swift-review-slides").length > 0){
//        jQuery(".swift-review-slides").each(function(){
//            slider_height = 0;
//            jQuery(this).find('.item').each(function(){
//                if(jQuery(this).outerHeight() > slider_height){
//                    slider_height = jQuery(this).outerHeight();
//                }
//            });
//            jQuery(this).find('.item').css('height', slider_height);
//        });
//    }

});//ready

function adjustIframes() {
    jQuery('.sr-list-item iframe, .swift-review-slides .slick-slide iframe').each(function () {
        var
                  $this = jQuery(this),
                  proportion = $this.data('proportion'),
                  w = $this.attr('width'),
                  actual_w = $this.width();

        if (!proportion) {
            proportion = $this.attr('height') / w;
            $this.data('proportion', proportion);
        }

        if (actual_w != w) {
            $this.css('height', Math.round(actual_w * proportion) + 'px');
        }
    });
}
jQuery(window).on('resize load', adjustIframes);
setTimeout(function () {
    adjustIframes();
}, 1000);

function removeMe(ID) {
    jQuery("#" + ID).remove();
}

function ValidateEmail(mail) {
    if (/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/.test(mail))
    {
        return (true);
    }
    return (false);
}

/* x */
function readURL(input) {
    var allFiles = input.files;
    jQuery(".srTempImg").remove();
    for (var x = 0; x < allFiles.length; x++) {
        var file = allFiles[x];

        var validExtensions = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'];
        if (jQuery.inArray(file.type, validExtensions) == -1) {
            jQuery('#sr_contest_photo').val('');
        } else {
            var reader = new FileReader();
            reader.onload = function (e) {
                jQuery('#sr_video_url').val('');//empty video url
                jQuery('#sr_photo_contest_submit').before('<input type="hidden" name="extra_sr_contest_photo[]" class="sr_contest_photo" value="' + e.target.result + '" />');
            }
            reader.readAsDataURL(file);
        }
    }
}

/* image drag drop*/
window.onload = function () {
    var ele_one = document.getElementById('sr_photo_contest_upload_area');
    if (typeof ele_one != "undefined" && ele_one !== null) {
        var element = document.querySelector('#sr_photo_contest_upload_area');
        makeDroppable(element, callback);
    }
}
function callback(files) {
    var flag = '';
    for (var x = 0; x < files.length; x++) {
        var file = files[x];
        var validExtensions = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'];
        if (jQuery.inArray(file.type, validExtensions) == -1) {
            jQuery('#sr_contest_photo').val('');
            flag++;
        }
    }

    if (flag > 0) {
        jQuery("#sr_photo_contest_submit").before('<p class="sr-error">Please upload valid image.</p>');
        return false;
    } else {
        var fData = new FormData();
        jQuery.each(files, function (key, value) {
            fData.append('sr_photo_contest_upload[' + key + ']', value);
        });
        fData.append('action', 'swiftreview_save_photocontest_imgs');
        fData.append('swiftreview_photo_contest_security', jQuery("#swiftreview_photo_contest_security").val());
        // Here, we simply log the Array of files to the console.
        jQuery.ajax({
            url: swiftreviews_ajax_object.ajax_url,
            type: "POST",
            data: fData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                jQuery(".sr-error").remove();
                jQuery('.sr_contest_photo').remove();
                var res = jQuery.parseJSON(response);
                var imgs = res.images;
                var baseurl_input = res.baseurl;
                if (imgs !== '') {
                    jQuery(".sr-preview-imgs").html(imgs);
                    jQuery('#sr_photo_contest_submit').before(baseurl_input);
                    //jQuery("#sr_photo_contest_submit").after("<p class='sr-success' style='text-align: center; display: inline-block; margin: 10px;'>Thanks for your interest.</p>");
                }
            }});
    }
}

function makeDroppable(element, callback) {

    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('multiple', true);
    input.setAttribute('name', 'sr_photo_contest_upload');
    input.setAttribute('class', 'sr_photo_contest_upload');
    input.setAttribute('id', 'sr_photo_contest_upload');
    input.style.display = 'none';

    input.addEventListener('change', triggerCallback);
    element.appendChild(input);

    element.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.add('dragover');
    });

    element.addEventListener('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.remove('dragover');
    });

    element.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.remove('dragover');
        triggerCallback(e);
    });

    element.addEventListener('click', function () {
        input.value = null;
        input.click();
    });

    function triggerCallback(e) {
        var files;
        if (e.dataTransfer) {
            files = e.dataTransfer.files;
        } else if (e.target) {
            files = e.target.files;
        }
        callback.call(null, files);
    }
}