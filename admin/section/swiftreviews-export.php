<?php
/*
 *      SwiftReviews-Referrals
 */

function swiftreviews_export_callback() {
    if (isset($_POST['swiftreviews_export_all_reviews']) && wp_verify_nonce($_POST['swiftreviews_export_all_reviews'], 'swiftreviews_export_all_reviews')) {
        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post_type' => 'swift_reviews',
            'order' => 'ASC',
            'orderby' => 'ID',
        );
        $all_reviews_data = get_posts($args);
        if (!empty($all_reviews_data)) {
            foreach ($all_reviews_data as $review_data) {

                $term_list = wp_get_post_terms($review_data->ID, 'swift_reviews_category', array("fields" => "names"));
                $review_cats = (implode(", ", $term_list));
                $hidden_vars = get_post_meta($review_data->ID, 'swiftreviews_hidden_vars', true);


                $export_reviews_data[] = array(
                    "title" => get_the_title($review_data->ID),
                    "review_date" => get_the_date("d-m-Y", $review_data->ID),
                    "ratings" => get_post_meta($review_data->ID, "swiftreviews_ratings", true),
                    "reviewer_name" => get_post_meta($review_data->ID, "swiftreviews_reviewer_name", true),
                    "reviewer_email" => get_post_meta($review_data->ID, "swiftreviews_reviewer_email", true),
                    "category" => $review_cats,
                    "hidden_vars" => unserialize($hidden_vars)
                );
            }
            export_reviews_to_csv($export_reviews_data);
        } else {
            wp_redirect(admin_url("admin.php?page=swiftreviews_export&update=1"));
            die;
        }
    }
    ?>
    <div class="wrap">
        <h2>Export Reviews</h2>
        <hr/>
        <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
            <div id="message" class="notice notice-success is-dismissible below-h2">
                <p>No reviews for export.</p>
            </div>
            <?php
        }
        ?>
        <div class="inner_content">
            <form name="FrmSRExportReviews" id="FrmSRExportReviews" method="post">
                <table class="form-table">
                    <tr>
                        <td>
                            <?php wp_nonce_field('swiftreviews_export_all_reviews', 'swiftreviews_export_all_reviews'); ?>
                            <button type="submit" class="button button-primary">Export All Reviews</button>
                            <br/>
                            <small>get all reviews in CSV file</small>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php
}

function export_reviews_to_csv($review_data) {
    ob_end_clean();
    $filename = 'Swift_Reviews_' . date('Y-m-d-H-i-s') . '.csv';
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Content-Type: text/csv', true);

    $headers = array("Title", "Ratings", "Reviewer Name", "Reviewer Email", "Category", "Date", "Hidden Vars");
    echo implode(',', $headers) . "\n";

    foreach ($review_data as $ID => $log) {
        $log_arr = array();
        $log_arr[] = '"' . str_replace('"', '""', $log['title']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['ratings']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['reviewer_name']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['reviewer_email']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['category']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['review_date']) . '"';
        if (!empty($log['hidden_vars'])) {
            $op = '';
            foreach ($log['hidden_vars'] as $hv_key => $hv_val) {
                $val = !empty($hv_val) ? $hv_val : '--';
                $op.=$hv_key . " : " . $val . "\n";
            }
            $log_arr[] = '"' . str_replace('"', '""', $op) . '"';
        }
        echo @implode(",", $log_arr) . "\n";
    }
    exit;
}