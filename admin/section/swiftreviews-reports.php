<?php
/*
 *      Review Reports
 */

function swiftreviews_reports_callback() {
    wp_enqueue_script('sr-chart-min', SWIFTREVIEWS__PLUGIN_URL . 'admin/js/Chart.min.js', '', '', true);
    wp_enqueue_script('sr-chart', SWIFTREVIEWS__PLUGIN_URL . 'admin/js/globalize.min.js', '', '', true);
    wp_enqueue_script('sr-chart-guage', SWIFTREVIEWS__PLUGIN_URL . 'admin/js/dx.chartjs.js', '', '', true);

    $chartLabels = array('0', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5');
    $chart_background_color = array('#ff0000', '#df2929', '#ea1011', '#af3939', '#934d4d', '#7e5f5d', '#7c7c7e', '#7e727c', '#7b8a73', '#75b847', '#70f609');
    $chart_hover_background_color = array('#ff0000', '#df2929', '#ea1011', '#af3939', '#934d4d', '#7e5f5d', '#7c7c7e', '#7e727c', '#7b8a73', '#75b847', '#70f609');
    ?>
    <div class="wrap">
        <h2 style="display: inline-block">Reports</h2>
        <div class="sr-report-cat-wrap pull-right">
            <?php
            $swift_reviews_terms = get_terms('swift_reviews_category', 'hide_empty=0');
            ?>
            <select id="swiftreviews_cat" class="swiftreviews_cat" name="swiftreviews_cat">
                <option value="">Select Category</option>
                <?php
                foreach ($swift_reviews_terms as $sr_term) {
                    echo '<option ' . selected(sanitize_text_field($_GET['catid']), $sr_term->term_id) . ' value="' . $sr_term->term_id . '">' . $sr_term->name . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="clear"></div>
        <hr/>
        <div class="inner_content" id="inner-content-left">
            <h2 class="">Net Advocate Score<span class="nas_total"></span></h2>
            <div class="sr-report-content">
                <?php
                $ten_star_data = $data_array1 = $ten_stars_rating_value = $srNPS = $args_ten_stars = array();
                foreach ($chartLabels as $ratingVal) {
                    $args_ten_stars = array(
                        'posts_per_page' => -1,
                        'post_type' => 'swift_reviews',
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key' => 'swiftreviews_ratings',
                                'value' => $ratingVal
                            )
                        ),
                    );
                    if (isset($_GET['catid']) && !empty($_GET['catid'])) {
                        $args_ten_stars['tax_query'][] = array(
                            'taxonomy' => 'swift_reviews_category',
                            'field' => 'term_id',
                            'terms' => sanitize_text_field($_GET['catid']),
                        );
                    }

                    $ten_stars_reveiws = new WP_Query($args_ten_stars);
                    $ten_stars_rating_value[] = $ten_stars_reveiws->found_posts;
                }
                $ten_star_data['labels'] = $chartLabels;
                $data_array1 = array('data' => $ten_stars_rating_value, 'backgroundColor' => $chart_background_color, 'hoverBackgroundColor' => $chart_hover_background_color);
                $ten_star_data['datasets'] = array($data_array1);
                $starsData_ten = json_encode($ten_star_data);
                ?>
                <canvas id="tenStars" width="400" height="400"></canvas>
            </div>
        </div>
        <div class="inner_content" id="inner-content-right">
            <!--<h2 class="">Net Promoter Score</h2>-->
            <!-- 2 -->
            <div class="sr-nps-wrap">
                <?php
                /* Detractors */
                $args1 = array(
                    'posts_per_page' => -1,
                    'post_type' => 'swift_reviews',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'swiftreviews_ratings',
                            'value' => array(0, 3),
                            'compare' => 'BETWEEN'
                        ),
                    ),
                );
                if (isset($_GET['catid']) && !empty($_GET['catid'])) {
                    $args1['tax_query'][] = array(
                        'taxonomy' => 'swift_reviews_category',
                        'field' => 'term_id',
                        'terms' => sanitize_text_field($_GET['catid']),
                    );
                }
                $reviews1 = get_posts($args1);
                $reviews_score1 = 0;
                foreach ($reviews1 as $r1) {
                    $ratings = get_post_meta($r1->ID, 'swiftreviews_ratings', true);
                    $reviews_score1 = $reviews_score1 + $ratings;
                }
                $total_reveiw1 = count($reviews1);
                wp_reset_postdata();

                /* Passives */
                $args2 = array(
                    'posts_per_page' => -1,
                    'post_type' => 'swift_reviews',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'swiftreviews_ratings',
                            'value' => array(3.5, 4),
                            'compare' => 'BETWEEN'
                        ),
                    ),
                );
                if (isset($_GET['catid']) && !empty($_GET['catid'])) {
                    $args2['tax_query'][] = array(
                        'taxonomy' => 'swift_reviews_category',
                        'field' => 'term_id',
                        'terms' => sanitize_text_field($_GET['catid']),
                    );
                }
                $reviews2 = get_posts($args2);
                $reviews_score2 = '';
                foreach ($reviews2 as $r2) {
                    $ratings = get_post_meta($r2->ID, 'swiftreviews_ratings', true);
                    $reviews_score2 = $reviews_score2 + $ratings;
                }
                $total_reveiw2 = count($reviews2);
                wp_reset_postdata();

                /* Advocates */
                $args3 = array(
                    'posts_per_page' => -1,
                    'post_type' => 'swift_reviews',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'swiftreviews_ratings',
                            'value' => array(4.5, 5),
                            'compare' => 'BETWEEN'
                        ),
                    ),
                );
                if (isset($_GET['catid']) && !empty($_GET['catid'])) {
                    $args3['tax_query'][] = array(
                        'taxonomy' => 'swift_reviews_category',
                        'field' => 'term_id',
                        'terms' => sanitize_text_field($_GET['catid']),
                    );
                }
                $reviews3 = get_posts($args3);
                $reviews_score3 = 0;
                foreach ($reviews3 as $r3) {
                    $ratings = get_post_meta($r3->ID, 'swiftreviews_ratings', true);
                    $reviews_score3 = $reviews_score3 + $ratings;
                }
                $total_reveiw3 = count($reviews3);
                wp_reset_postdata();

                $total_reveiews = $total_reveiw1 + $total_reveiw2 + $total_reveiw3;
                if (!empty($total_reveiews)) {
                    $detractors = round((($total_reveiw1 / $total_reveiews ) * 100), 2);
                    $passives = round((($total_reveiw2 / $total_reveiews ) * 100), 2);
                    $advocates = round((( $total_reveiw3 / $total_reveiews ) * 100), 2);
                }
                ?>
                <div class="clear"></div>
                <div class="circle-gauge">
                    <div id="sr-gaugeContainer" style="height:300px; width:300px; display: inline-block"></div>
                </div>
                <div>
                    <table class="sr-nps-table" cellpadding="5">
                        <tr>
                            <th>Detractors (0-6)</th>
                            <td><?php echo (!empty($detractors) ? $total_reveiw1 . " (" . $detractors . "%)" : '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Passives (7-8)</th>
                            <td><?php echo (!empty($passives) ? $total_reveiw2 . " (" . $passives . "%)" : '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Advocates (9-10)</th>
                            <td><?php echo (!empty($advocates) ? $total_reveiw3 . " (" . $advocates . "%)" : '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Total Reviews</th>
                            <td><?php echo $total_reveiews; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" value="<?php echo round($advocates - $detractors); ?>" id="nas_total" />
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {

            if (jQuery("#nas_total").val() != '') {
                jQuery(".nas_total").text(" : " + jQuery("#nas_total").val() + "%");
            }

            var ctx1 = document.getElementById("tenStars").getContext("2d");
            var data1 = <?php echo $starsData_ten; ?>;
            var myPieChart1 = new Chart(ctx1, {
                type: 'pie',
                data: data1,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
            /*Gauge*/
            var scaleSettings = {
                startValue: -20,
                endValue: 100,
                majorTick: {
                    visible: true,
                    color: 'black',
                    tickInterval: 10
                },
                minorTick: {
                    visible: true,
                    color: 'black',
                    tickInterval: 5
                }
            };

            var rangesArray = [
                {
                    startValue: -20,
                    endValue: 10,
                    color: 'red'
                }, {
                    startValue: 10,
                    endValue: 20,
                    color: 'grey'
                }, {
                    startValue: 20,
                    endValue: 40,
                    color: '#75b847'
                }, {
                    startValue: 40,
                    endValue: 100,
                    color: 'green'
                },
            ];

            jQuery("#sr-gaugeContainer").dxCircularGauge({
                scale: scaleSettings,
                value: jQuery("#nas_total").val(),
                valueIndicator: {color: 'red', spindleGapSize: 5, type: 'triangle', offset: 5},
                //subvalues: jQuery("#nas_total").val(),
                subvalueIndicator: {color: 'green'},
                rangeContainer: {
                    ranges: rangesArray,
                    offset: -4
                }
            });
        });
    </script>
    <?php
}