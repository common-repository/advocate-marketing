<?php
/*
 *      SwiftReviews-Referrals
 */

function swiftreviews_referrals_page_callback() {
    global $wpdb;
    $table_referrals = $wpdb->prefix . 'sr_referrals';

    if (isset($_POST['swiftreviews_export_all_referrals']) && wp_verify_nonce($_POST['swiftreviews_export_all_referrals'], 'swiftreviews_export_all_referrals')) {
        $export_ref_results = $wpdb->get_results("SELECT * FROM `$table_referrals`", ARRAY_A);
        if (!empty($export_ref_results)) {
            export_referrals_to_csv($export_ref_results);
        }
    }
    /*
     *  Pagination
     */
    $pagenum = isset($_GET['pagenum']) ? sanitize_text_field(absint($_GET['pagenum'])) : 1;
    $limit = 30; // number of rows in page
    $offset = ( $pagenum - 1 ) * $limit;
    $total_records = $wpdb->get_var("SELECT COUNT(`ref_id`) FROM $table_referrals");
    $num_of_pages = ceil($total_records / $limit);

    $get_ref_results = $wpdb->get_results("SELECT * FROM `$table_referrals` LIMIT $offset, $limit");
    ?>
    <div class="wrap">
        <h2>Referrals</h2>
        <hr/>
        <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
            <div id="message" class="notice notice-success is-dismissible below-h2">
                <p>Setting updated successfully.</p>
            </div>
            <?php
        }
        ?>
        <div class="inner_content">
            <div class="pull-right" style="margin-bottom: 5px;">
                <button class="button button-orange">Add</button>
                <?php if (!empty($get_ref_results)) { ?>
                    <form method="post" id="FrmRefExport" style="display: inline-block;">
                        <?php wp_nonce_field('swiftreviews_export_all_referrals', 'swiftreviews_export_all_referrals'); ?>
                        <button type="submit" name="ref_export" class="button button-primary">Export</button>
                    </form>
                <?php } ?>
            </div>
            <div class="clear"></div>
            <form name="FrmSRReferrals" id="FrmSRReferrals" method="post">
                <table class="widefat fixed striped tbl-syndication" id="tbl-syndication">
                    <thead>
                        <tr>
                            <th>Referred Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Referred By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($get_ref_results)) {
                            foreach ($get_ref_results as $ref_result) {
                                $dt = explode(" ", $ref_result->ref_date_time);
                                ?>
                                <tr>
                                    <td><?php echo $ref_result->ref_name; ?></td>
                                    <td><?php echo $ref_result->ref_phone; ?></td>
                                    <td><?php echo $ref_result->ref_email; ?></td>
                                    <td><?php echo date("d-m-Y", strtotime($dt[0])); ?></td>
                                    <td><?php echo $ref_result->ref_referred_by_name . " (" . $ref_result->ref_referred_by_email . ")"; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5"><h3><center>No referrals found!</center></h3></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php swiftreviews_pagination($num_of_pages, $pagenum, $total_records, $limit); ?>
            </form>
        </div>
    </div>
    <?php
}

if (!function_exists('swiftreviews_pagination')) {

    function swiftreviews_pagination($num_of_pages, $pagenum, $total_filtered_log, $limit) {
        $page_links = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;', 'swift-cloud'),
            'next_text' => __('&raquo;', 'swift-cloud'),
            'total' => $num_of_pages,
            'current' => $pagenum
        ));
        if ($page_links) {
            if ($total_filtered_log > $limit) {
                echo '<div class="tablenav" id="sc-pagination"><div class="tablenav-pages">' . $page_links . '</div></div>';
            }
        }
    }

}

function export_referrals_to_csv($ref_data) {
    ob_end_clean();
    $filename = 'Swift_Reviews_Referrals_List' . date('Y-m-d-H-i-s') . '.csv';
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Content-Type: text/csv', true);

    $headers = array("Name", "Phone", "Email", "Referred By", "Date");
    echo implode(',', $headers) . "\n";
    foreach ($ref_data as $ID => $log) {
        $log_arr = array();

        $dt = explode(" ", $log['ref_date_time']);

        $log_arr[] = '"' . str_replace('"', '""', $log['ref_name']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['ref_phone']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['ref_email']) . '"';
        $log_arr[] = '"' . str_replace('"', '""', $log['ref_referred_by_name'] . " (" . $log['ref_referred_by_email'] . ")") . '"';
        $log_arr[] = '"' . str_replace('"', '""', $dt[0]) . '"';
        echo @implode(",", $log_arr) . "\n";
    }
    exit;
}
