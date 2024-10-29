<?php

// Autoset featured img based on YouTube thumbnail if video is added
add_action('save_post', 'sr_check_if_content_contains_video', 10, 2);

function sr_check_if_content_contains_video($post_id, $post) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // We need to prevent trying to assign when trashing or untrashing posts in the list screen.
    // get_current_screen() was not providing a unique enough value to use here.
    if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('trash', 'untrash'))) {
        return;
    }

    $content = isset($post->post_content) ? $post->post_content : '';

    /**
     * Only check the first 800 characters of our post, by default.
     *
     * @since 1.0.0
     *
     * @param int $value Character limit to search.
     */
    $content = substr($content, 0, apply_filters('sr_featured_images_character_limit', 800));

    // Allow developers to filter the content to allow for searching in postmeta or other places.
    $content = apply_filters('sr_featured_images_from_video_filter_content', $content, $post_id);

    // Set the video id.
    $youtube_id = sr_check_for_youtube($content);
    $video_thumbnail_url = '';
    $youtube_details = '';

    if ($youtube_id) {
        $youtube_details = sr_get_youtube_details($youtube_id);
        $video_thumbnail_url = $youtube_details['video_thumbnail_url'];
        $video_url = $youtube_details['video_url'];
        $video_embed_url = $youtube_details['video_embed_url'];
    }

    if ($post_id && !has_post_thumbnail($post_id) && $content && $youtube_details) {
        $video_id = '';
        if ($youtube_id) {
            $video_id = $youtube_id;
        }
        if (!wp_is_post_revision($post_id)) {
            sr_set_video_thumbnail_as_featured_image($post_id, $video_thumbnail_url, $video_id);
        }
    }

    if ($post_id && $content && $youtube_id) {
        update_post_meta($post_id, '_is_video', true);
        update_post_meta($post_id, '_video_url', $video_url);
        update_post_meta($post_id, '_video_embed_url', $video_embed_url);
    } else {
        // Need to set because we don't have one, and we can skip on future iterations.
        // Need way to potentially force check ALL.
        update_post_meta($post_id, '_is_video', false);
        delete_post_meta($post_id, '_video_url');
        delete_post_meta($post_id, '_video_embed_url');
    }
}

function sr_check_for_youtube($content) {
    if (preg_match('#\/\/(www\.)?(youtu|youtube|youtube-nocookie)\.(com|be)\/(watch|embed)?\/?(\?v=)?([a-zA-Z0-9\-\_]+)#', $content, $youtube_matches)) {
        return $youtube_matches[6];
    }

    return false;
}

function sr_get_youtube_details($youtube_id) {
    $video = array();
    $video_thumbnail_url_string = 'http://img.youtube.com/vi/%s/%s';

    $video_check = wp_remote_head('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $youtube_id);
    if (200 === wp_remote_retrieve_response_code($video_check)) {
        $remote_headers = wp_remote_head(
                sprintf(
                        $video_thumbnail_url_string, $youtube_id, 'maxresdefault.jpg'
                )
        );
        $video['video_thumbnail_url'] = ( 404 === wp_remote_retrieve_response_code($remote_headers) ) ?
                sprintf(
                        $video_thumbnail_url_string, $youtube_id, 'hqdefault.jpg'
                ) :
                sprintf(
                        $video_thumbnail_url_string, $youtube_id, 'maxresdefault.jpg'
        );
        $video['video_url'] = 'https://www.youtube.com/watch?v=' . $youtube_id;
        $video['video_embed_url'] = 'https://www.youtube.com/embed/' . $youtube_id;
    }

    return $video;
}

function sr_set_video_thumbnail_as_featured_image($post_id, $video_thumbnail_url, $video_id = '') {

    // Bail if no valid video thumbnail URL.
    if (!$video_thumbnail_url || is_wp_error($video_thumbnail_url)) {
        return;
    }

    $post_title = sanitize_title(preg_replace('/[^a-zA-Z0-9\s]/', '-', get_the_title())) . '-' . $video_id;

    global $wpdb;

    $stmt = "SELECT ID FROM {$wpdb->posts}";
    $stmt .= $wpdb->prepare(
            ' WHERE post_type = %s AND guid LIKE %s', 'attachment', '%' . $wpdb->esc_like($video_id) . '%'
    );
    $attachment = $wpdb->get_col($stmt);
    if (!empty($attachment[0])) {
        $attachment_id = $attachment[0];
    } else {
        // Try to sideload the image.
        $attachment_id = sr_ms_media_sideload_image_with_new_filename($video_thumbnail_url, $post_id, $post_title, $video_id);
    }

    // Bail if unable to sideload (happens if the URL or post ID is invalid, or if the URL 404s).
    if (is_wp_error($attachment_id)) {
        return;
    }

    // Woot! We got an image, so set it as the post thumbnail.
    set_post_thumbnail($post_id, $attachment_id);
}

function sr_ms_media_sideload_image_with_new_filename($url, $post_id, $filename = null, $video_id) {

    if (!$url || !$post_id) {
        return new WP_Error('missing', esc_html__('Need a valid URL and post ID...', 'automatic-featured-images-from-videos'));
    }

    require_once( ABSPATH . 'wp-admin/includes/file.php' );

    // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp.
    $tmp = download_url($url);

    // If error storing temporarily, unlink.
    if (is_wp_error($tmp)) {
        // And output wp_error.
        return $tmp;
    }

    // Fix file filename for query strings.
    preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);
    // Extract filename from url for title.
    $url_filename = basename($matches[0]);
    // Determine file type (ext and mime/type).
    $url_type = wp_check_filetype($url_filename);

    // Override filename if given, reconstruct server path.
    if (!empty($filename)) {
        $filename = sanitize_file_name($filename);
        // Extract path parts.
        $tmppath = pathinfo($tmp);
        // Build new path.
        $new = $tmppath['dirname'] . '/' . $filename . '.' . $tmppath['extension'];
        // Renames temp file on server.
        rename($tmp, $new);
        // Push new filename (in path) to be used in file array later.
        $tmp = $new;
    }

    /* Assemble file data (should be built like $_FILES since wp_handle_sideload() will be using). */

    // Full server path to temp file.
    $file_array['tmp_name'] = $tmp;

    if (!empty($filename)) {
        // User given filename for title, add original URL extension.
        $file_array['name'] = $filename . '.' . $url_type['ext'];
    } else {
        // Just use original URL filename.
        $file_array['name'] = $url_filename;
    }

    $post_data = array(
        // Just use the original filename (no extension).
        'post_title' => get_the_title($post_id),
        // Make sure gets tied to parent.
        'post_parent' => $post_id,
    );

    // Required libraries for media_handle_sideload.
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // Do the validation and storage stuff.
    // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status.
    $att_id = media_handle_sideload($file_array, $post_id, null, $post_data);

    // If error storing permanently, unlink.
    if (is_wp_error($att_id)) {
        // Clean up.
        @unlink($file_array['tmp_name']);

        // And output wp_error.
        return $att_id;
    }

    return $att_id;
}

// if youtube video is added, remove related video
add_filter('oembed_result', 'sr_hide_youtube_related_videos', 10, 3);

if (!function_exists('sr_hide_youtube_related_videos')) {

    function sr_hide_youtube_related_videos($data, $url, $args = array()) {

        //Autoplay
        $autoplay = strpos($url, "autoplay=1") !== false ? "&autoplay=1" : "";

        //Setup the string to inject into the url
        $str_to_add = apply_filters("hyrv_extra_querystring_parameters", "wmode=transparent&amp;") . 'rel=0';

        //Regular oembed
        if (strpos($data, "feature=oembed") !== false) {
            $data = str_replace('feature=oembed', $str_to_add . $autoplay . '&amp;feature=oembed', $data);

            //Playlist
        } elseif (strpos($data, "list=") !== false) {
            $data = str_replace('list=', $str_to_add . $autoplay . '&amp;list=', $data);
        }

        //All Set
        return $data;
    }

}

//Disable the Jetpack
add_filter('jetpack_shortcodes_to_include', 'sr_hyrv_remove_jetpack_shortcode_function');

function sr_hyrv_remove_jetpack_shortcode_function($shortcodes) {
    $jetpack_shortcodes_dir = WP_CONTENT_DIR . '/plugins/jetpack/modules/shortcodes/';
    $shortcodes_to_unload = array('youtube.php');
    foreach ($shortcodes_to_unload as $shortcode) {
        if ($key = array_search($jetpack_shortcodes_dir . $shortcode, $shortcodes)) {
            unset($shortcodes[$key]);
        }
    }
    return $shortcodes;
}
