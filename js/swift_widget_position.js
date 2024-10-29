jQuery(document).ready(function() {
    /*
     left : swift_left_bottom
     right : swift_right_bottom
     center : swift_center_bottom
     */

    /* Left */
    if (jQuery(".swift_left_bottom").length > 0) {
        var left_count = 0;
        var w = 15;
        jQuery(".swift_left_bottom").each(function() {
            if (left_count > 0) {
                jQuery(this).css("left", w + "px");
            } else {
                jQuery(this).css("left", "15px");
            }
            left_count++;
            w = w + jQuery(this).outerWidth() + 15;

        });
    }

    /* Left Center */
    if (jQuery(".swift_left_center").length > 0) {
        var center_count = 0;
        var window_center = 0;
        var left_center_widget_width_total = 0;

        jQuery(".swift_left_center").each(function() {
            left_center_widget_width_total = left_center_widget_width_total + jQuery(this).outerHeight();
        });

        jQuery(".swift_left_center").each(function() {
            if (center_count > 0) {
                jQuery(this).css("top", window_center + "px");
            } else {
                window_center = (jQuery(window).height() - left_center_widget_width_total) / 2;
                jQuery(this).css("top", window_center + "px");
            }
            center_count++;
            window_center = window_center + jQuery(this).outerHeight() + 15;
        });
    }

    /* Right */
    if (jQuery(".swift_right_bottom").length > 0) {
        var right_count = 0;
        var w = 15;
        jQuery(".swift_right_bottom").each(function() {
            if (right_count > 0) {
                jQuery(this).css("right", w + "px");
            } else {
                jQuery(this).css("right", "15px");
            }
            right_count++;
            w = w + jQuery(this).outerWidth() + 15;
        });
    }

    /* Center */
    if (jQuery(".swift_center_bottom").length > 0) {
        var center_count = 0;
        var window_center = 0;
        var center_widget_width_total = 0;

        jQuery(".swift_center_bottom").each(function() {
            center_widget_width_total = center_widget_width_total + jQuery(this).outerWidth();
        });

        if (center_widget_width_total > 0) {
            jQuery(".swift_center_bottom").each(function() {
                if (center_count > 0) {
                    jQuery(this).css("left", window_center + "px");
                } else {
                    window_center = (jQuery(window).width() - (center_widget_width_total)) / 2;
                    jQuery(this).css("left", window_center + "px");
                }
                center_count++;
                window_center = window_center + jQuery(this).outerWidth() + 15;
            });
        }

    }

    /* Right Center */
    if (jQuery(".swift_right_center").length > 0) {
        var center_count = 0;
        var window_center = 0;
        var right_center_widget_height_total = 0;

        jQuery(".swift_right_center").each(function() {
            right_center_widget_height_total = right_center_widget_height_total + jQuery(this).outerHeight();
        });

        jQuery(".swift_right_center").each(function() {
            if (center_count > 0) {
                jQuery(this).css("top", window_center + "px");
            } else {
                window_center = (jQuery(window).height() - right_center_widget_height_total) / 2;
                jQuery(this).css("top", window_center + "px");
            }
            center_count++;
            window_center = window_center + jQuery(this).outerHeight() + 15;
        });
    }
});