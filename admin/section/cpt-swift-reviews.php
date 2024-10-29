<?php
/*
 *      CPT: Swift Reviews
 */

add_action('init', 'cpt_swiftreviews');

function cpt_swiftreviews() {
    $icon_url = plugins_url('../images/swiftcloud.png', __FILE__);
    $labels = array(
        'name' => _x('Swift Reviews', 'post type general name', 'swift-reviews'),
        'singular_name' => _x('Swift Review', 'post type singular name', 'swift-reviews'),
        'menu_name' => _x('Swift Reviews', 'admin menu', 'swift-reviews'),
        'add_new' => _x('Add New Review', 'Review', 'swift-reviews'),
        'add_new_item' => __('Add New Review', 'swift-reviews'),
        'new_item' => __('New Review', 'swift-reviews'),
        'edit_item' => __('Edit Review', 'swift-reviews'),
        'view_item' => __('View Review', 'swift-reviews'),
        'all_items' => __('All Reviews', 'swift-reviews'),
        'search_items' => __('Search Reviews', 'swift-reviews'),
        'not_found' => __('No Reviews found.', 'swift-reviews'),
        'not_found_in_trash' => __('No Reviews found in Trash.', 'swift-reviews')
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'query_var' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => true,
        'menu_icon' => __($icon_url, 'swift-reviews'),
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail'),
        'taxonomies' => array('swift_reviews_category'),
        'rewrite' => array('slug' => 'reviews'),
    );
    register_post_type('swift_reviews', $args);

    /* -------------------------------------
     *      Add new taxonomy
     */
    $cat_labels = array(
        'name' => _x('Swift Reviews Categories', 'taxonomy general name'),
        'singular_name' => _x('Category', 'taxonomy singular name'),
        'add_new_item' => __('Add New Category'),
        'new_item_name' => __('New Category Name'),
        'menu_name' => __('Categories'),
        'search_items' => __('Search Category'),
        'not_found' => __('No Category found.'),
    );

    $cat_args = array(
        'hierarchical' => true,
        'labels' => $cat_labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'swift_reviews_category'),
    );

    register_taxonomy('swift_reviews_category', 'swift_reviews', $cat_args);

    // insert default review category
    $my_cat_id = wp_insert_term("Reviews", "swift_reviews_category", array('slug' => 'my-reviews', 'parent' => 0));
    if (!empty($my_cat_id) && !isset($my_cat_id->errors)) {
        update_option('swiftreviews_default_category', sanitize_text_field($my_cat_id->term_id));
    } else {
        update_option('swiftreviews_default_category', sanitize_text_field($my_cat_id->error_data['term_exists']));
    }

    // insert default cats
    $default_cat = array(
        "By Location" => array(
            "child" => array(
                'Mos Eisley',
                'Tatooine'
            )
        ),
        "By Worker" => array(
            "child" => array(
                'Luke Skywalker',
                'Darth Vader'
            )
        ),
        "By Product" => array(
            "child" => array(
                'Lightsaber',
                'Speeder'
            )
        ),
    );
    foreach ($default_cat as $d_cat_key => $d_cat_val) {
        // insert parent category
        if (isset($d_cat_val['child'])) {
            $parent_cat = $d_cat_key;
        } else {
            $parent_cat = $d_cat_val;
        }
        $term_id = wp_insert_term($parent_cat, "swift_reviews_category", array('parent' => 0));

        if (!is_wp_error($term_id) && !empty($term_id['term_id']) && isset($d_cat_val['child']) && !empty($d_cat_val['child'])) {
            foreach ($d_cat_val['child'] as $child_key => $child_val) {
                // insert child category
                if (isset($child_val['subchild'])) {
                    $child_cat = $child_key;
                } else {
                    $child_cat = $child_val;
                }
                $child_term_id = wp_insert_term($child_cat, "swift_reviews_category", array('parent' => $term_id['term_id']));


                if (!is_wp_error($child_term_id) && !empty($child_term_id['term_id']) && isset($child_val['subchild']) && !empty($child_val['subchild'])) {
                    foreach ($child_val['subchild'] as $subchild) {
                        // insert subchild category
                        $subchild_term_id = wp_insert_term($subchild, "swift_reviews_category", array('parent' => $child_term_id['term_id']));
                    }
                }
            }
        }
    }//foreach
    // flush rewrite rules
    flush_rewrite_rules();
}

/*
 *  Custom field :Reviews
 */

add_action('add_meta_boxes', 'sr_reviews_metaboxes');

function sr_reviews_metaboxes() {
    add_meta_box('swiftreviews_ratings', 'Swift Review Ratings', 'sr_review_rating', 'swift_reviews', 'normal', 'default');
    add_meta_box('swiftreviews_capture', 'Swift Review Local Capture', 'sr_review_local_capture', 'swift_reviews', 'normal', 'default');
}

function sr_review_rating($post) {
    $getRating = get_post_meta($post->ID, 'swiftreviews_ratings', true);
    $reviewer_name = get_post_meta($post->ID, 'swiftreviews_reviewer_name', true);
    $reviewer_email = get_post_meta($post->ID, 'swiftreviews_reviewer_email', true);
    $reviewer_location = get_post_meta($post->ID, 'swiftreviews_reviewer_location', true);
    $review_improvements = get_post_meta($post->ID, 'swiftreviews_improvements', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="swiftreviews_ratings">Ratings:  </label></th>
            <td>
                <select name="swiftreviews_ratings" id="swiftreviews_ratings">
                    <option value="">Ratings</option>
                    <option <?php echo selected($getRating, '0'); ?> value="0">0</option>
                    <option <?php echo selected($getRating, '0.5'); ?> value="0.5">0.5</option>
                    <option <?php echo selected($getRating, '1'); ?> value="1">1.0</option>
                    <option <?php echo selected($getRating, '1.5'); ?> value="1.5">1.5</option>
                    <option <?php echo selected($getRating, '2'); ?> value="2">2.0</option>
                    <option <?php echo selected($getRating, '2.5'); ?> value="2.5">2.5</option>
                    <option <?php echo selected($getRating, '3'); ?> value="3">3.0</option>
                    <option <?php echo selected($getRating, '3.5'); ?> value="3.5">3.5</option>
                    <option <?php echo selected($getRating, '4'); ?> value="4">4.0</option>
                    <option <?php echo selected($getRating, '4.5'); ?> value="4.5">4.5</option>
                    <option <?php echo selected($getRating, '5'); ?> value="5">5.0</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="swiftreviews_reviewer_name">Reviewer Name:</label></th>
            <td><input type="text" name="swiftreviews_reviewer_name" id="swiftreviews_reviewer_name" class="regular-text" value="<?php echo $reviewer_name; ?>" /></td>
        </tr>
        <tr>
            <th><label for="swiftreviews_reviewer_email">Reviewer Email:</label></th>
            <td><input type="text" name="swiftreviews_reviewer_email" id="swiftreviews_reviewer_email" class="regular-text" value="<?php echo $reviewer_email; ?>" /></td>
        </tr>
        <tr>
            <th><label for="swiftreviews_reviewer_location">Reviewer Location:</label></th>
            <td><input type="text" name="swiftreviews_reviewer_location" id="swiftreviews_reviewer_location" class="regular-text" value="<?php echo $reviewer_location; ?>" /></td>
        </tr>
        <tr>
            <th><label for="swiftreviews_improvements">Rating Improvement:</label></th>
            <td><textarea name="swiftreviews_improvements" id="swiftreviews_improvements" cols="70" rows="8"><?php echo $review_improvements ?></textarea></td>
        </tr>
    </table>
    <?php
}

function sr_review_rating_improvements($post) {
    ?>

    <?php
}

function sr_review_local_capture($post) {
    $negative_reviews = get_post_meta($post->ID, 'swiftreviews_negative_reviews', true);
    $positive_reviews = get_post_meta($post->ID, 'swiftreviews_referrals', true);
    $photo_contest = get_post_meta($post->ID, 'swiftreviews_photo_contest_data', true);
    $video_url = get_post_meta($post->ID, 'swiftreviews_contest_video_data', true);
    $hidden_vars = get_post_meta($post->ID, 'swiftreviews_hidden_vars', true);
    $op = '';

    if (!empty($negative_reviews)) {
        $negative_data = json_decode($negative_reviews, true);
        $op .= '<p style="margin-bottom:0;"><b>Comments: </b></p>';
        $op .= '<p style="margin-top:0;">' . $negative_data['comments'] . '</p>';
        $op .= '<p><b>Phone: </b>' . $negative_data['phone'] . '</p>';
    }
    if (!empty($positive_reviews)) {
        $positive_data = explode(",", $positive_reviews);
        $op .= '<p style="margin-bottom:0;"><b>Referrals: </b></p>';
        $op .= '<ul style="list-style: inside none disc; margin-top: 5px;">';
        foreach ($positive_data as $ref) {
            $op .= '<li>' . $ref . '</li>';
        }
        $op .= '</ul>';
    }
    if (!empty($photo_contest) || !empty($video_url)) {
        $op .= '<p style="margin-bottom:0;"><b>Photo Contest: </b></p>';
        if (!empty($photo_contest)) {
            $op .= '<ul style="margin-top:0;">';
            foreach ($photo_contest as $key => $val) {
                $op .= '<li style="display:inline-block;margin:10px 10px 10px 0;"><img src="' . $val['url'] . '" alt="photo contest image" style="max-width:150px;width:100%;"></li>';
            }
            $op .= "</ul>";
        }
        if (!empty($video_url)) {
            $op .= '<a href="' . $video_url . '" target="_blank">' . $video_url . "</a>";
        }
    }
    if (!empty($hidden_vars)) {
        $op .= '<p style="margin-bottom:0;"><b>Hidden Vars: </b></p>';
        $op .= '<ul>';
        foreach (unserialize($hidden_vars) as $hv_key => $hv_val) {
            $op .= '<li>';
            $op .= $hv_key . " : ";
            $op .= !empty($hv_val) ? $hv_val : "<b>--</b>";
            $op .= '</li>';
        }
        $op .= '</ul>';
    }

    if (empty($op)) {
        $op = "No data found!";
    }
    echo $op;
}

/**
 *      Save meta
 */
add_action('save_post', 'sr_save_ratings');

function sr_save_ratings($post_id) {
    if (isset($_POST["swiftreviews_ratings"])) {
        $rating = sanitize_text_field($_POST['swiftreviews_ratings']);
        update_post_meta($post_id, 'swiftreviews_ratings', $rating);
    }

    if (isset($_POST["swiftreviews_reviewer_name"])) {
        $swiftreviews_reviewer_name = sanitize_text_field($_POST['swiftreviews_reviewer_name']);
        update_post_meta($post_id, 'swiftreviews_reviewer_name', $swiftreviews_reviewer_name);
    }

    if (isset($_POST["swiftreviews_reviewer_email"])) {
        $swiftreviews_reviewer_email = sanitize_text_field($_POST['swiftreviews_reviewer_email']);
        update_post_meta($post_id, 'swiftreviews_reviewer_email', $swiftreviews_reviewer_email);
    }

    if (isset($_POST["swiftreviews_reviewer_location"])) {
        $swiftreviews_reviewer_location = sanitize_text_field($_POST['swiftreviews_reviewer_location']);
        update_post_meta($post_id, 'swiftreviews_reviewer_location', $swiftreviews_reviewer_location);
    }

    if (isset($_POST["swiftreviews_improvements"])) {
        $swiftreviews_improvements = sanitize_text_field($_POST['swiftreviews_improvements']);
        update_post_meta($post_id, 'swiftreviews_improvements', $swiftreviews_improvements);
    }
}

add_filter('single_template', 'swift_reviews_templates_callback');
if (!function_exists('swift_reviews_templates_callback')) {

    function swift_reviews_templates_callback($template) {
        $post_types = array('swift_reviews');
        if (is_singular($post_types) && !file_exists(get_stylesheet_directory() . '/single-swift_reviews.php')) {
            $template = SWIFTREVIEWS__PLUGIN_DIR . '/section/single-swift_reviews.php';
        }
        return $template;
    }

}

add_filter('archive_template', 'swift_reviews_set_archive_template_callback');
if (!function_exists('swift_reviews_set_archive_template_callback')) {

    function swift_reviews_set_archive_template_callback($archive_template) {
        global $post;
        if (get_post_type() == 'swift_reviews' && is_archive('swift_reviews')) {
            $archive_template = SWIFTREVIEWS__PLUGIN_DIR . '/section/archive-swift_reviews.php';
        }
        return $archive_template;
    }

}

/**
 *         Add sidebar
 */
add_action('widgets_init', 'swift_reviews_widget_init');
if (!function_exists('swift_reviews_widget_init')) {

    function swift_reviews_widget_init() {
        register_sidebar(array(
            'name' => __('SwiftReviews Sidebar', 'swift_reviews'),
            'id' => 'swift-reviews-sidebar',
            'description' => __('Add widgets here to appear in swift Reviews sidebar', 'swift_reviews'),
            'before_widget' => '<div class="swift-reviews-widget-inner">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="swift-reviews-widget-title">',
            'after_title' => '</h3>',
        ));
    }

}

function change_swift_review_post_types_slug($args, $post_type) {
    $sr_seo_slug = get_option('sr_seo_slug');
    if ('swift_reviews' === $post_type && !empty($sr_seo_slug)) {
        $args['rewrite']['slug'] = $sr_seo_slug;
    }
    return $args;
}

add_filter('register_post_type_args', 'change_swift_review_post_types_slug', 10, 2);


add_filter('wp_title', 'sr_archive_titles');

function sr_archive_titles($orig_title) {

    global $post;
    $post_type = (isset($post->post_type) && !empty($post->post_type)) ? $post->post_type : "";

    if ($post_type == 'swift_reviews' && is_archive('swift_reviews')) { //FIRST CHECK IF IT'S AN ARCHIVE
        $types = array(
            array(
                'post_type' => 'swift_reviews', //Your custom post type name
                'title' => bloginfo('name') . ' Reviews' //The title tag you'd like displayed
            ),
        );

        //CHECK IF THE POST TYPE IS IN THE ARRAY
        foreach ($types as $k => $v) {
            if (in_array($post_type, $types[$k])) {
                return $types[$k]['title'];
            }
        }
    } else { //NOT AN ARCHIVE, RETURN THE ORIGINAL TITLE
        return $orig_title;
    }
}