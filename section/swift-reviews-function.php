<?php

/*
 *      Custom excerpt function
 */

if (!function_exists('swift_reviews_get_excerpt')) {

    function swift_reviews_get_excerpt($excerpt_length = 55, $id = false, $echo = false) {
        return swift_reviews_excerpt($excerpt_length, $id, $echo);
    }

}

if (!function_exists('swift_reviews_excerpt')) {

    function swift_reviews_excerpt($excerpt_length = 55, $id = false, $echo = false) {

        $text = '';

        if ($id) {
            $the_post = get_post($id);
            $text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
        } else {
            global $post;
            $text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
        }

        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);

        $excerpt_more = ' ' . '<a href=' . get_permalink($id) . ' class="swift_reviews-readmore">...continued</a>';
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        if ($echo)
            echo apply_filters('the_content', $text);
        else
            return $text;
    }

}

function buildStarRating($star_style = '5stars', $rating = '0', $show_count = true) {
    $op = '';
    if (!empty($star_style) && $star_style == "10stars") {
        $op .= '<div class="rating-10stars">';
        if (!empty($rating) || $rating == 0) {
            $r = 0;
            for ($z = 0; $z <= 10; $z++) {
                $reviewClass = ($r > $rating || $rating == 0) ? "no-star" : 'stars-' . str_replace(".", "-", $r);
                $op .= '<label class="' . $reviewClass . '">' . $z . '</label>';
                $r = $r + 0.5;
            }
            if ($show_count)
                $op .= '<div class="stars-out-of">' . $rating . ' out of 5 stars</div>';
        }
        $op .= '</div>';
    } else if (!empty($star_style) && $star_style == "5stars") {
        $op .= '<div class="sr-ratings">';
        if (!empty($rating)) {
            for ($x = 1; $x <= $rating; $x++) {
                $op .= '<span class="star-icon full"><i class="fa fa-star"></i></span>';
            }
            if (strpos($rating, '.')) {
                $op .= '<span class="star-icon half"><i class="fa fa-star"></i></span>';
                $x++;
            }
            if ($rating != 5) {
                for ($x = 1; $x <= 5 - $rating; $x++) {
                    $op .= '<span class="star-icon"><i class="fa fa-star"></i></span>';
                }
            }
            if ($show_count)
                $op .= '<div class="stars-out-of">' . $rating . ' out of 5 stars</div>';
        } else if ($rating == 0) {
            //zero stars
            for ($x = 1; $x <= 5; $x++) {
                $op .= '<span class="star-icon"><i class="fa fa-star"></i></span>';
            }
            if ($show_count)
                $op .= '<div class="stars-out-of">' . $rating . ' out of 5 stars</div>';
        }
        $op .= '</div>';
    } else {
        /*
         *      5stars/10stars mix ratings
         */
        if (!empty($reviewer_type) && $reviewer_type == "10stars") {
            $op .= '<div class="rating-10stars">';
            if (!empty($rating) || $rating == 0) {
                $r = 0;
                for ($z = 0; $z <= 10; $z++) {
                    $reviewClass = ($r > $rating || $rating == 0) ? "no-star" : 'stars-' . str_replace(".", "-", $r);
                    $op .= '<label class="' . $reviewClass . '">' . $z . '</label>';
                    $r = $r + 0.5;
                }
                if ($show_count)
                    $op .= '<div class="stars-out-of">' . $rating . ' out of 5 stars</div>';
            }
            $op .= '</div>';
        } else {
            $op .= '<div class="sr-ratings">';
            if (!empty($rating)) {
                for ($x = 1; $x <= $rating; $x++) {
                    $op .= '<span class="star-icon full"><i class="fa fa-star"></i></span>';
                }
                if (strpos($rating, '.')) {
                    $op .= '<span class="star-icon half"><i class="fa fa-star"></i></span>';
                    $x++;
                }
                if ($rating != 5) {
                    for ($x = 1; $x <= 5 - $rating; $x++) {
                        $op .= '<span class="star-icon"><i class="fa fa-star"></i></span>';
                    }
                }
                if ($show_count)
                    $op .= '<div class="stars-out-of">' . $rating . ' out of 5 stars</div>';
            } else if ($rating == 0) {
                //zero stars
                for ($x = 1; $x <= 5; $x++) {
                    $op .= '<span class="star-icon"><i class="fa fa-star"></i></span>';
                }
                if ($show_count)
                    $op .= '<div class="stars-out-of">' . $rating . ' out of 5 stars</div>';
            }
            $op .= '</div>';
        }
    }
    return $op;
}

if (!function_exists('swift_pagination')) {

    function swift_pagination($pages = '', $range = 2) {
        $showitems = ($range * 2) + 1;

        global $paged;
        if (empty($paged))
            $paged = 1;

        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {
            echo "<div class='swift_pagination'>";
            if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
                echo "<a href='" . get_pagenum_link(1) . "'>&laquo;</a>";
            if ($paged > 1 && $showitems < $pages)
                echo "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a>";

            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    echo ($paged == $i) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a>";
                }
            }

            if ($paged < $pages && $showitems < $pages)
                echo "<a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a>";
            if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
                echo "<a href='" . get_pagenum_link($pages) . "'>&raquo;</a>";
            echo "</div>\n";
        }
    }

}

function sr_archive_query($query) {
    $swiftreviews_review_per_page = (get_option("swiftreviews_review_per_page")) ? get_option("swiftreviews_review_per_page") : 10;
    if ($query->is_post_type_archive('swift_reviews') && $query->is_main_query()) {
        $query->set('posts_per_page', $swiftreviews_review_per_page);
    }
}

add_action('pre_get_posts', 'sr_archive_query');
