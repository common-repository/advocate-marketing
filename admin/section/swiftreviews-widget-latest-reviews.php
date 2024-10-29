<?php

/**
 *  Create widget to show latest reviews
 */
class swift_review_widget_latest_reviews extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'swift_review_widget_latest_reviews',
            'description' => __('Swift review latest reviews.'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('swift_review_latest_reviews', __('Latest Reviews'), $widget_ops);
    }

    /**
     * Outputs the content for the current latest jobs widget instance.
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Recent Posts widget instance.
     */
    public function widget($args, $instance) {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        $title = (!empty($instance['title']) ) ? $instance['title'] : __('Recent Reviews');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
        if (!$number)
            $number = 5;

        $sr_style = (!empty($instance['sr_style']) ) ? $instance['sr_style'] : 'sr_style_1';

        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;

        $latest_reviews_args = array(
            'post_type' => 'swift_reviews',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        );
        $r = new WP_Query($latest_reviews_args);


        if ($sr_style === 'sr_style_2') {
            if ($r->have_posts()) {
                wp_enqueue_style('swift-review-slider', plugins_url('../../css/swift-review-slider.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-carousel', plugins_url('../../css/slick.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../../css/slick-theme.css', __FILE__), '', '', '');
                wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../../js/slick.min.js', __FILE__), array('jquery'), '', true);

                echo $args['before_widget'];
                if ($title) {
                    echo $args['before_title'] . $title . $args['after_title'];
                }

                $sr_slider_top = $sr_slider_bottom = $sr_slide_output = '';
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

                    $sr_slider_bottom .= '<div class="item">';
                    $sr_slider_bottom .= '<div class="review-rates">';
                    $sr_slider_bottom .= buildStarRating('', $rating, false);
                    $sr_slider_bottom .= '</div>';
                    $sr_slider_bottom .= '<div class="client_name">' . $reviewer_name . '</div>';
                    $sr_slider_bottom .= apply_filters('the_content', get_post_field('post_content', get_the_ID()));
                    $sr_slider_bottom .= '</div>';

                    $sr_slider_top .= '<div class="item">';
                    $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                    $sr_slider_top .= '</div>';
                endwhile;

                if (isset($_SESSION['swift_review_slide_cnt']) && !empty($_SESSION['swift_review_slide_cnt'])) {
                    $swift_review_slide_cnt = $_SESSION['swift_review_slide_cnt'];
                    $swift_review_slide_cnt++;
                } else {
                    $swift_review_slide_cnt = 1;
                    $_SESSION['swift_review_slide_cnt'] = 1;
                }
                $_SESSION['swift_review_slide_cnt'] = $swift_review_slide_cnt;
                $sr_slide_id = $swift_review_slide_cnt . time();

                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top">' . $sr_slider_top . '</div>';
                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_thumb" class="swift-review-slides">' . $sr_slider_bottom . '</div>';
                add_action('wp_footer', 'swift_review_style_2', 50, 1);
                do_action('wp_footer', $sr_slide_id);

                echo $sr_slide_output;
                echo $args['after_widget'];
            }
        } else if ($sr_style === 'sr_style_3') {
            if ($r->have_posts()) {
                wp_enqueue_style('swift-review-slider', plugins_url('../../css/swift-review-slider.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-carousel', plugins_url('../../css/slick.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../../css/slick-theme.css', __FILE__), '', '', '');
                wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../../js/slick.min.js', __FILE__), array('jquery'), '', true);

                echo $args['before_widget'];
                if ($title) {
                    echo $args['before_title'] . $title . $args['after_title'];
                }

                $sr_slider_top = $sr_slide_output = '';

                while ($r->have_posts()) : $r->the_post();
                    setup_postdata($r);
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);

                    $sr_slider_top .= '<div class="item">';
                    $sr_slider_top .= '<div class="review-rates">';
                    $sr_slider_top .= '<div class="review-details">';
                    $sr_slider_top .= buildStarRating('', $rating, false);
                    $sr_slider_top .= '<div class="client_details">';
                    $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                    $sr_slider_top .= '<div class="client_name">' . $reviewer_name . '</div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= apply_filters('the_excerpt', get_post_field('post_content', get_the_ID()));
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                endwhile;

                if (isset($_SESSION['swift_review_slide_cnt']) && !empty($_SESSION['swift_review_slide_cnt'])) {
                    $swift_review_slide_cnt = $_SESSION['swift_review_slide_cnt'];
                    $swift_review_slide_cnt++;
                } else {
                    $swift_review_slide_cnt = 1;
                    $_SESSION['swift_review_slide_cnt'] = 1;
                }
                $_SESSION['swift_review_slide_cnt'] = $swift_review_slide_cnt;
                $sr_slide_id = $swift_review_slide_cnt . time();

                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top swift_review_style_3">' . $sr_slider_top . '</div>';

                add_action('wp_footer', 'swift_review_style_3', 50, 1);
                do_action('wp_footer', $sr_slide_id);

                echo $sr_slide_output;
                echo $args['after_widget'];
            }
        } else if($sr_style === 'sr_style_4'){
            if ($r->have_posts()) {
                wp_enqueue_style('swift-review-slider', plugins_url('../../css/swift-review-slider.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-carousel', plugins_url('../../css/slick.css', __FILE__), '', '', '');
                wp_enqueue_style('swift-review-slick-theme-carousel', plugins_url('../../css/slick-theme.css', __FILE__), '', '', '');
                wp_enqueue_script('swift-review-slick-carousel-script', plugins_url('../../js/slick.min.js', __FILE__), array('jquery'), '', true);

                echo $args['before_widget'];
                if ($title) {
                    echo $args['before_title'] . $title . $args['after_title'];
                }

                $sr_slider_top = $sr_slide_output = '';

                while ($r->have_posts()) : $r->the_post();
                    setup_postdata($r);
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);

                    $sr_slider_top .= '<div class="item">';
                    $sr_slider_top .= '<div class="review-rates">';
                    $sr_slider_top .= '<div class="review-details">';
                    $sr_slider_top .= buildStarRating('', $rating, false);
                    $sr_slider_top .= apply_filters('the_excerpt', get_post_field('post_content', get_the_ID()));
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '<div class="client_details">';
                    $sr_slider_top .= '<div class="client_avatar"><img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></div>';
                    $sr_slider_top .= '<div class="client_name">' . $reviewer_name . '</div>';
                    $sr_slider_top .= '</div>';
                    $sr_slider_top .= '</div>';
                endwhile;

                if (isset($_SESSION['swift_review_slide_cnt']) && !empty($_SESSION['swift_review_slide_cnt'])) {
                    $swift_review_slide_cnt = $_SESSION['swift_review_slide_cnt'];
                    $swift_review_slide_cnt++;
                } else {
                    $swift_review_slide_cnt = 1;
                    $_SESSION['swift_review_slide_cnt'] = 1;
                }
                $_SESSION['swift_review_slide_cnt'] = $swift_review_slide_cnt;
                $sr_slide_id = $swift_review_slide_cnt . time();

                $sr_slide_output .= '<div id="swift_review_slide_' . $sr_slide_id . '_text" class="swift-review-slides-top swift_review_style_4">' . $sr_slider_top . '</div>';

                add_action('wp_footer', 'swift_review_style_4', 50, 1);
                do_action('wp_footer', $sr_slide_id);

                echo $sr_slide_output;
                echo $args['after_widget'];
            }
        }else {
            /**
             * Filters the arguments for the Recent Jobs widget.
             *
             * @param array $args An array of arguments used to retrieve the recent posts.
             */
            if ($r->have_posts()) :
                echo $args['before_widget'];

                if ($title) {
                    if (get_option('swiftreviews_listing_page')) {
                        echo $args['before_title'] . '<a href="' . get_permalink(get_option('swiftreviews_listing_page')) . '">' . $title . '</a>' . $args['after_title'];
                    } else {
                        echo $args['before_title'] . $title . $args['after_title'];
                    }
                }
                ?>
                <ul class="swift_review_latest_reviews">
                    <?php while ($r->have_posts()) : $r->the_post(); ?>

                        <?php $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true); ?>
                        <?php $review_link = get_permalink(get_the_ID()); ?>
                        <li>

                            <?php
                            $op = '';
                            $op .= '<div class="swift-review-widget-stars sr-ratings">';
                            $op .= '<div class="swift-review-widget-avatar">';
                            $op .= '<a href="' . $review_link . '">';
                            $op .= '<img itemprop="image" src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . get_the_title() . '" />';
                            $op .= '</a>';
                            $op .= '</div>';
                            $op .= '<div class="swift-review-widget-clientname">';
                            $op .= '<a href="' . $review_link . '">';
                            $op .= get_the_title();
                            $op .= '<div class="stars-out-of">';
                            $op .= buildStarRating('', $rating, false);
                            $op .= '</div>';
                            $op .= ($show_date) ? '<span class="swift-review-widget-date">' . get_the_time('l, F jS, Y', get_the_ID()) . '</span>' : '';
                            $op .= '</a>';
                            $op .= '</div>';
                            $op .= '</div>';
                            echo $op;
                            ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php
                echo $args['after_widget'];
                // Reset the global $the_post as this query will have stomped on it
                wp_reset_postdata();
            endif;
        }
    }

    /**
     * Handles updating the settings for the current Recent Posts widget instance.
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['sr_style'] = sanitize_text_field($new_instance['sr_style']);
        $instance['sr_review_type'] = sanitize_text_field($new_instance['sr_review_type']);
        $instance['sr_review_menu'] = isset($new_instance['sr_review_menu']) ? $new_instance['sr_review_menu'] : '';
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        return $instance;
    }

    /**
     * Outputs the settings form for the Recent Posts widget.
     * @param array $instance Current settings.
     */
    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $sr_style = isset($instance['sr_style']) ? esc_attr($instance['sr_style']) : '';
        $sr_review_type = isset($instance['sr_review_type']) ? esc_attr($instance['sr_review_type']) : '';
        $sr_review_menu = isset($instance['sr_review_menu']) ? esc_attr($instance['sr_review_menu']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('sr_style'); ?>"><?php _e('Style:'); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name('sr_style'); ?>" id="<?php echo $this->get_field_id('sr_style'); ?>">
                <option value="sr_style_1" <?php echo ($sr_style == 'sr_style_1') ? "selected='selected'" : ""; ?>>Small - Sidebar Stars</option>
                <option value="sr_style_2" <?php echo ($sr_style == 'sr_style_2') ? "selected='selected'" : ""; ?>>Large + Dark Theme Carousel</option>
                <option value="sr_style_3" <?php echo ($sr_style == 'sr_style_3') ? "selected='selected'" : ""; ?>>Standard Carousel</option>
                <option value="sr_style_4" <?php echo ($sr_style == 'sr_style_4') ? "selected='selected'" : ""; ?>>Big Quote Text Style</option>
            </select>
        </p>

        <p><label for="<?php echo $this->get_field_id('sr_review_type'); ?>"><?php _e('Showing:'); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name('sr_review_type'); ?>" id="<?php echo $this->get_field_id('sr_review_type'); ?>" onchange="sr_review_type_change(this)">
                <option value="sr_review_type_1" <?php echo ($sr_review_type == 'sr_review_type_1') ? "selected='selected'" : ""; ?>>All Positive Reviews</option>
                <option value="sr_review_type_2" <?php echo ($sr_review_type == 'sr_review_type_2') ? "selected='selected'" : ""; ?>>Use a Menu</option>
            </select>
        </p>

        <p class="sr_review_menu_container" <?php echo ($sr_review_type == 'sr_review_type_2') ? "style='display:block'" : "style='display:none'"; ?>><label for="<?php echo $this->get_field_id('sr_review_menu'); ?>"><?php _e('Select Menu:'); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name('sr_review_menu'); ?>" id="<?php echo $this->get_field_id('sr_review_menu'); ?>">
                <?php
                $menus = get_terms('nav_menu');
                foreach ($menus as $menu) {
                    echo '<option value="' . $menu->term_id . '" ' . ($sr_review_menu == $menu->term_id ? "selected='selected'" : "") . '>' . $menu->name . '</option>';
                }
                ?>
            </select>
        </p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of reviews to show:'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox"<?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Display post date?'); ?></label></p>
        <?php
    }

}

add_action('widgets_init', function() {
    register_widget('swift_review_widget_latest_reviews');
});

function swift_review_style_2($id){
    echo '
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery("#swift_review_slide_' . $id . '_thumb").not(".slick-initialized").slick({
                        accessibility: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        fade: true,
                        dots: false,
                        adaptiveHeight: false,
                        asNavFor: "#swift_review_slide_' . $id . '_text",
                        centerMode: true,
                    });
                    jQuery("#swift_review_slide_' . $id . '_text").not(".slick-initialized").slick({
                        slidesToShow: 5,
                        slidesToScroll: 3,
                        asNavFor: "#swift_review_slide_' . $id . '_thumb",
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
function swift_review_style_3($id) {
    echo '
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery("#swift_review_slide_' . $id . '_text").not(".slick-initialized").slick({
                        accessibility: true,
                        infinite: true,
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 3000,
                        dots: true,
                        responsive: [
                        {
                          breakpoint: 1024,
                          settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: true,
                            arrows: false
                          }
                        },
                        {
                          breakpoint: 600,
                          settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            arrows: false
                          }
                        },
                        {
                          breakpoint: 480,
                          settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false
                          }
                        }
                        // You can unslick at a given breakpoint now by adding:
                        // settings: "unslick"
                        // instead of a settings object
                      ]
                    });
                });
            </script>';
}
function swift_review_style_4($id) {
    echo '
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery("#swift_review_slide_' . $id . '_text").not(".slick-initialized").slick({
                        accessibility: true,
                        infinite: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 3000,
                        dots: false,
                        arrows: false,
                        adaptiveHeight: true
                    });
                });
            </script>';
}
