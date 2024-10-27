<?php
/**
 * Template for displaying license archives
 */
?>

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:group {"className":"page-header"} -->
    <div class="wp-block-group page-header">
        <!-- wp:heading {"level":1,"className":"page-title"} -->
        <h1 class="page-title">Open Source Licenses</h1>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:query {"queryId":1,"query":{"postType":"license","perPage":10}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"layout":{"type":"grid","columnCount":2},"className":"license-grid"} -->
            <!-- wp:group {"className":"post"} -->
            <div class="wp-block-group post">
                <!-- wp:post-title {"level":2,"isLink":true} /-->
                
                <!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium"} /-->
                
                <!-- wp:post-excerpt /-->
            </div>
            <!-- /wp:group -->
        <!-- /wp:post-template -->

        <!-- wp:query-pagination -->
            <!-- wp:query-pagination-previous /-->
            <!-- wp:query-pagination-numbers /-->
            <!-- wp:query-pagination-next /-->
        <!-- /wp:query-pagination -->

        <!-- wp:query-no-results -->
            <!-- wp:paragraph -->
            <p>No licenses found.</p>
            <!-- /wp:paragraph -->
        <!-- /wp:query-no-results -->
    </div>
    <!-- /wp:query -->
</div>
<!-- /wp:group -->
