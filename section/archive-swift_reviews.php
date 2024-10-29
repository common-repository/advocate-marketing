<?php
/*
 * Description:
 */
get_header();
$swiftreview_date_flag = get_option("swiftreview_date_flag");

$rev_arr = array();
$schema_arr = array(
    "@context" => "http://schema.org",
    "@type" => "Product",
    "name" => get_option('blogname'),
    "image" => get_option("swiftreview_microformat_logo"),
//    "url" => site_url(),
//    "priceRange" => "$$"
);
?>
<div class="swift-reviews-container container">
    <div class="swift-reviews-row swift-reviews-archive-container">
        <div class="swift-reviews-col-8">
            <?php $review_cnt = 0; ?>
            <?php $review_sum = 0; ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php
                $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                $reviewer_email = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_email', true);
                $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true);
                $reviewer_type = get_post_meta(get_the_ID(), 'swiftreviews_rating_type', true);
                $reviewer_location = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_location', true);

                $op = '';
                $op .= '<div class="sr-list-item">';

//                $op .= '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
//                            <meta itemprop="bestRating" content="5">
//                            <meta itemprop="worstRating" content="0">
//                            <meta itemprop="ratingValue" content="' . $rating . '">
//                        </div>';
                //left side img
                $op .= '<div class="sr-item-left"><a href="' . get_permalink(get_the_ID()) . '"><img src="' . sr_get_gravatar(get_the_ID()) . '" alt="' . $reviewer_name . '" /></a></div>';
                $op .= '<div class="sr-item-right">';
                //ratings
                $op .= '<div class="review-rates">';
                $op .= '<a href="' . get_permalink(get_the_ID()) . '">' . buildStarRating('', $rating) . '</a>';
                $op .= "</div>";

                $review_body = get_the_content();
                $review_body = apply_filters('the_content', $review_body);
                $op .= '<div class="sr-summary"><span><a href="' . get_permalink(get_the_ID()) . '">' . ucfirst(get_the_title()) . '</a></span></div>';
                $op .= '<div class="sr-comments"><a href="' . get_permalink(get_the_ID()) . '">' . $review_body . '</a></div>';
                $op .= '<div class="sr_meta_info">';
                $op .= '<div class="sr-reviewer-name"><span class="reviewer-name">' . ucfirst($reviewer_name) . (!empty($reviewer_location) ? " - " . $reviewer_location : "") . '</span> <br /> <span ' . ($swiftreview_date_flag ? "style='display: block;'" : "style='display: none;'") . '>' . get_the_time('l, F jS, Y', get_the_ID()) . "</span></div>";
                $op .= '<div class="swift-reviews-tags-wrap">' . get_the_term_list(get_the_ID(), 'swift_reviews_category', '<ul class="swift-reviews-tags-list"><li>', '</li><li>', '</li></ul>') . '</div>';
                $op .= '</div>';
                $op .= '</div></div>';
                echo $op;

                $rev_arr[] = array(
                    "@type" => "Review",
//                    "name" => ucfirst($reviewer_name),
                    "author" => array(
                        "@type" => "Person",
                        "name" => ucfirst($reviewer_name)
                    ),
                    "datePublished" => get_the_time('Y-m-d', get_the_ID()),
                    "reviewBody" => strip_tags($review_body),
                    "reviewRating" => array(
                        "@type" => "Rating",
                        "ratingValue" => $rating,
                        "bestRating" => "5",
                        "worstRating" => "0"
                    )
                );
                $review_cnt++;
                $review_sum = $review_sum + $rating;
                ?>
            <?php endwhile; ?>
            <?php
            swift_pagination();

            $get_swiftreviews_review_form_page_id = get_option('swiftreviews_review_form_page');
            if ($get_swiftreviews_review_form_page_id) {
                echo '<div class="sr-review-page-link-section"><h3>Why not <a href="' . get_permalink($get_swiftreviews_review_form_page_id) . '">click here</a> to add your own review now?</h3></div>';
            }
            ?>
            <div class="plugin-credit">Powered by
                <a href="https://SwiftCRM.com/" target="_blank">SwiftCloud</a>&nbsp;
                <a href="https://wordpress.org/plugins/advocate-marketing/" target="_blank">Wordpress Customer Testimonials Plugin</a>
            </div>
        </div>
        <div class="swift-reviews-col-4">
            <div class="swift-reviews-sidebar-bg">
                <div class="swift-reviews-sidebar">
                    <?php include_once 'swift-reviews-sidebar.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$schema_arr['review'] = $rev_arr;
$schema_arr['aggregateRating'] = array(
    "@type" => "AggregateRating",
    "ratingValue" => $review_sum / $review_cnt,
    "ratingCount" => $review_cnt,
    "reviewCount" => $review_cnt,
    "bestRating" => "5",
    "worstRating" => "0",
);
//echo '<pre>';
//print_r($schema_arr);
?>
<script type="application/ld+json">
    // <![CDATA[
    <?php echo json_encode($schema_arr); ?>
    // ]]
</script>
<?php
get_footer();
