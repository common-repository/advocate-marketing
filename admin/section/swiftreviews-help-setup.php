<?php
/*
 *  SwiftReviews help page
 */

function swiftreviews_help_callback() {
    ?>
    <div class="wrap">
        <h2 class="swiftpage-title">Welcome to the Customer Advocate Viral Engagement (“CAVE”)  System</h2><hr>
        <div class="inner_content review-help">
            <div class="sr-help-blue-div">
                <h2>Setup Instructions are at</h2>
                <a href="https://SwiftCRM.com/support/onboarding-reviews" target="_blank">https://SwiftCRM.com/support/onboarding-reviews</a>
            </div>
            <p><?php _e('We recommend setting up the basics first before adding more complex systems.', 'swift-reviews'); ?></p>
            <p><?php _e('Further help can be seen at', 'swift-reviews'); ?><br/>
                <a href="https://SwiftCRM.com/support/tag/reviews" target="_blank">https://SwiftCRM.com/support/tag/reviews</a>
            </p>
            <p><?php _e('A full list of shortcodes can be found at', 'swift-reviews'); ?><br/>
                <a href="https://SwiftCRM.com/support/reviews-shortcodes" target="_blank">https://SwiftCRM.com/support/reviews-shortcodes</a>
            </p>
        </div>
    </div>
    <?php
}