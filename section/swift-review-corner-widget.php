<?php
/*
 *      Review Corner widget
 */

add_action('wp_footer', 'swift_review_corner_widget_front', 10);

function swift_review_corner_widget_front() {
    wp_enqueue_style('swiftcloud-plugin-tooltip', plugins_url('../css/tooltipster.css', __FILE__), '', '', '');
    wp_enqueue_script('swiftcloud-tooltip-min', plugins_url('../js/tooltipster.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('swift-widget-position', plugins_url('../js/swift_widget_position.js', __FILE__), array('jquery'), '', true);

    $swift_review_widget = get_option('swiftreviews_widget_onoff');

//don't show on following page
    $review_form_page_id = get_option('swiftreviews_review_form_page');

    if (!$swift_review_widget)
        return;

    if (!empty($review_form_page_id) && is_page($review_form_page_id))
        return;

    $sr_widget_header_text = get_option('swiftreviews_widget_header_text');
    $sr_widget_header_color = get_option('swiftreviews_widget_header_color');
    $sr_widget_button_color = get_option('swiftreviews_widget_button_color');
    $sr_widget_text_color = get_option('swiftreviews_widget_text_color');
    $sr_widget_position = get_option('swiftreviews_widget_position');
    $sr_widget_rating_style = get_option('swiftreviews_widget_rating_style');

    $swift_global_position_class = swift_review_global_position_class($sr_widget_position);

    $sr_widget_header_color = !empty($sr_widget_header_color) ? $sr_widget_header_color : '#FF7200';
    $sr_widget_button_color = !empty($sr_widget_button_color) ? $sr_widget_button_color : '#CCC';

    $review_page_id = get_option('swiftreviews_review_form_page');
    $review_page_link = get_permalink($review_page_id);
    ?>
    <div class="sr-widget-wrap swiftcloud_widget <?php echo $swift_global_position_class; ?>" id="sr-widget-wrap" data-page="<?php echo $review_form_page_id; ?>" style="<?php //echo $sr_widget_position;  ?>">
        <div class="sr-widget-trigger" style="background-color: <?php echo $sr_widget_header_color; ?>;">
            <span><i class="fa fa-lg fa-angle-down"></i></span>
        </div>
        <div class="sr-widget-container" style="display: block;">
            <div class="sr-widget-header" style="background-color: <?php echo $sr_widget_header_color; ?>;">
                <span><?php echo stripslashes($sr_widget_header_text); ?></span>
            </div>
            <div class="sr-widget-content">
                <div class="sr-widget-rating-style">
                    <?php
                    switch ($sr_widget_rating_style) {
                        case "smiley-frow": {
                                ?>
                                <div class="sr-widget-smiley-frow">
                                    <ul>
                                        <li><a href="<?php echo $review_page_link . "?ratings=5"; ?>"><img src="<?php echo SWIFTREVIEWS__PLUGIN_URL . "/images/emoji1.png" ?>" alt="emoji1" /> Very satisfied</a></li>
                                        <li><a href="<?php echo $review_page_link . "?ratings=4"; ?>"><img src="<?php echo SWIFTREVIEWS__PLUGIN_URL . "/images/emoji2.png" ?>" alt="emoji2" /> Somewhat satisfied</a></li>
                                        <li><a href="<?php echo $review_page_link . "?ratings=3"; ?>"><img src="<?php echo SWIFTREVIEWS__PLUGIN_URL . "/images/emoji3.png" ?>" alt="emoji3" /> Neither satisfied nor dissatisfied</a></li>
                                        <li><a href="<?php echo $review_page_link . "?ratings=2"; ?>"><img src="<?php echo SWIFTREVIEWS__PLUGIN_URL . "/images/emoji4.png" ?>" alt="emoji4" /> Somewhat dissatisfied</a></li>
                                        <li><a href="<?php echo $review_page_link . "?ratings=1"; ?>"><img src="<?php echo SWIFTREVIEWS__PLUGIN_URL . "/images/emoji5.png" ?>" alt="emoji5" /> Very dissatisfied</a></li>
                                    </ul>
                                </div>
                                <?php
                                break;
                            };
                        case "5stars": {
                                ?>
                                <div class="sr-widget-stars">
                                    <a href="<?php echo $review_page_link . "?ratings=0"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><span class="widget-star-icon"><i class="fa fa-star"></i></span></a>
                                    <a href="<?php echo $review_page_link . "?ratings=1"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><span class="widget-star-icon"><i class="fa fa-star"></i></span></a>
                                    <a href="<?php echo $review_page_link . "?ratings=2"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><span class="widget-star-icon"><i class="fa fa-star"></i></span></a>
                                    <a href="<?php echo $review_page_link . "?ratings=3"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><span class="widget-star-icon"><i class="fa fa-star"></i></span></a>
                                    <a href="<?php echo $review_page_link . "?ratings=4"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><span class="widget-star-icon"><i class="fa fa-star"></i></span></a>
                                    <a href="<?php echo $review_page_link . "?ratings=5"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><span class="widget-star-icon"><i class="fa fa-star"></i></span></a>

                                    <div class="rating-text">
                                        <div class="rating-text-left"><span><i class="fa fa-frown-o fa-lg"></i> Absolutely Terrible</span></div>
                                        <div class="rating-text-right"><span>Excellent! <i class="fa fa-smile-o fa-lg"></i></span></div>
                                    </div>
                                </div>
                                <?php
                                break;
                            };
                        case "yes-no": {
                                ?>
                                <div class="sr-widget-btns">
                                    <label class="rating-no sr-tooltip" for="star2half" title="Below Expectations"><a href="<?php echo $review_page_link . "?ratings=2.5"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><i class="fa fa-frown-o"></i> No</a></label>
                                    <label class="rating-meh sr-tooltip" for="star3half" title="Meh"><a href="<?php echo $review_page_link . "?ratings=3.5"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><i class="fa fa-meh-o"></i> Meh</a></label>
                                    <label class="rating-yes sr-tooltip" for="star5" title="Excellent"><a href="<?php echo $review_page_link . "?ratings=5"; ?>" style="color: <?php echo $sr_widget_text_color; ?>;"><i class="fa fa-smile-o"></i> Yes</a></label>
                                </div>
                                <?php
                                break;
                            };
                        default : {
                                ?>
                                <div class="widget_rating_btns sr-widget-10stars">
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-0" href="<?php echo $review_page_link . "?ratings=0"; ?>">0</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-0-5" href="<?php echo $review_page_link . "?ratings=0.5"; ?>">1</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-1" href="<?php echo $review_page_link . "?ratings=1" . $x; ?>">2</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-1-5" href="<?php echo $review_page_link . "?ratings=1.5"; ?>">3</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-2" href="<?php echo $review_page_link . "?ratings=2" . $x; ?>">4</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-3-5" href="<?php echo $review_page_link . "?ratings=2.5"; ?>">5</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-3" href="<?php echo $review_page_link . "?ratings=3" . $x; ?>">6</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-3-5" href="<?php echo $review_page_link . "?ratings=3.5"; ?>">7</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-4" href="<?php echo $review_page_link . "?ratings=4" . $x; ?>">8</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-4-5" href="<?php echo $review_page_link . "?ratings=4.5"; ?>">9</a></div>
                                    <div class="widget_rating_div"><a style="color: <?php echo $sr_widget_text_color; ?>;background-color:<?php echo $sr_widget_button_color; ?>" class="widget_rating widget-star-5" href="<?php echo $review_page_link . "?ratings=5" . $x; ?>">10</a></div>
                                </div>
                                <div class="widget_rating_value">
                                    <span>Hate it <i class="fa fa-frown-o fa-lg"></i></span>
                                    <span>Love It! <i class="fa fa-smile-o fa-lg"></i></span>
                                </div>
                                <?php
                                break;
                            }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function() {
            /* tooltip */
            jQuery(".sr-tooltip").tooltipster();
        });
    </script>
    <?php
}

/* set global position class in swift corner widgets
 *  return : position class name
 */
if (!function_exists('swift_review_global_position_class')) {

    function swift_review_global_position_class($position) {
        switch ($position) {
            case 'left': {
                    return 'swift_left_bottom';
                    break;
                }
            case 'right': {
                    return 'swift_right_bottom';
                    break;
                }
            case 'center': {
                    return 'swift_center_bottom';
                    break;
                }

            case 'right_center': {
                    return 'swift_right_center';
                    break;
                }
            case 'left_center': {
                    return 'swift_left_center';
                    break;
                }

            case 'left_top': {
                    return 'swift_left_top';
                    break;
                }
            case 'right_top': {
                    return 'swift_right_top';
                    break;
                }
            case 'center_top': {
                    return 'swift_center_top';
                    break;
                }
        }
    }

}