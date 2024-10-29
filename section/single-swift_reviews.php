<?php
/*
 * Description: single swift review
 */
get_header();
?>
<div class="swift-reviews-container container">
    <div class="single_swift_reviews_container">
        <div class="swift-reviews-row swift-reviews-archive-container">
            <div class="swift-reviews-col-8">
                <?php while (have_posts()) : the_post(); ?>
                    <?php $reviewer_name = get_post_meta(get_the_ID(), 'swiftreviews_reviewer_name', true); ?>
                    <h1 class="text-center"><a href="<?php echo get_permalink(get_the_ID()); ?>"><?php the_title(); ?></a></h1>
                    <div class="client_avatar"><img src="<?php echo sr_get_gravatar(get_the_ID()); ?>" alt="<?php echo $reviewer_name; ?>" /></div>
                    <?php
                    $rating = get_post_meta(get_the_ID(), 'swiftreviews_ratings', true);
                    $op = '<div class="review-rates">';
                    $op .= '<a href="' . get_permalink(get_the_ID()) . '">' . buildStarRating('', $rating) . '</a>';
                    $op .= "</div>";

                    $review_body = get_the_content();
                    $review_body = apply_filters('the_content', $review_body);
                    $op .= '<div class="review_body">' . $review_body . '</div>';

                    $swiftreview_date_flag = get_option("swiftreview_date_flag");
                    $op .= '<div class="sr_meta_info">';
                    $op .= '<div class="sr-reviewer-name"><span class="reviewer-name">' . ucfirst($reviewer_name) . '</span> <br /> <span itemprop="datePublished" ' . ($swiftreview_date_flag ? "style='display: block;'" : "style='display: none;'") . '>' . get_the_time('l, F jS, Y', get_the_ID()) . "</span></div>";
                    $op .= '<div class="swift-reviews-tags-wrap">' . get_the_term_list(get_the_ID(), 'swift_reviews_category', '<ul class="swift-reviews-tags-list"><li>', '</li><li>', '</li></ul>') . '</div>';
                    $op .= '</div>';
                    echo $op;
                    ?>
                <?php endwhile; ?>
            </div>
            <div class="swift-reviews-col-4">
                <div class="swift-reviews-sidebar-bg">
                    <div class="swift-reviews-sidebar">
                        <?php include_once 'swift-reviews-sidebar.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="swift-reviews-row">
            <div class="swift-reviews-col-8">
                <?php edit_post_link('Edit', '<p class="sr-edit-post tooltip-right" data-tooltip="Only you can see this because you\'re logged in.">', '</p>'); ?>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
