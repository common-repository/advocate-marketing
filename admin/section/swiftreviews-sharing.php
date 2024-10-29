<?php
/*
 *      SwiftReviews-Sharing
 */

function swiftreviews_sharing_callback() {
    $all_reviews_data = '';
    $args = array(
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'post_type' => 'swift_reviews',
        'order' => 'ASC',
        'orderby' => 'ID',
    );
    $all_reviews_data = get_posts($args);

    $all_social_share_data = array();
    $synd_sort = array();
    if (!empty($all_reviews_data)) {
        foreach ($all_reviews_data as $review_data) {
            $syn_providers = get_post_meta($review_data->ID, "swiftreviews_social_clicks", true);
            $syn_name = get_post_meta($review_data->ID, "swiftreviews_reviewer_name", true);
            $syn_email = get_post_meta($review_data->ID, "swiftreviews_reviewer_email", true);

            $syn_providers_list = unserialize($syn_providers);
            $syn_providers_click_sum = (is_array($syn_providers_list)) ? array_sum($syn_providers_list) : '';

            if (!empty($syn_providers_click_sum)) {
                $all_social_share_data[$review_data->ID]['name'] = $syn_name;
                $all_social_share_data[$review_data->ID]['email'] = $syn_email;
                $all_social_share_data[$review_data->ID]['clicks'] = $syn_providers_click_sum;
                $synd_sort[$review_data->ID]['clicks'] = $syn_providers_click_sum;
            }
        }
        array_multisort($synd_sort, SORT_DESC, $all_social_share_data);
    }
    ?>
    <div class="wrap">
        <h2>Sharing</h2>
        <hr/>
        <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
            <div id="message" class="notice notice-success is-dismissible below-h2">
                <p>Setting updated successfully.</p>
            </div>
            <?php
        }
        ?>
        <div class="inner_content">
            <table class="widefat fixed striped tbl-syndicate-sharings" id="tbl-syndicate-sharings">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Clicks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($all_social_share_data)) {
                        foreach ($all_social_share_data as $synd_data) {
                            ?>
                            <tr>
                                <td><?php echo ucwords($synd_data['name']); ?></td>
                                <td><?php echo $synd_data['email']; ?></td>
                                <td><?php echo $synd_data['clicks']; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="3"><center><h3>No Record Found</h3></center></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}