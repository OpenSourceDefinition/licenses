<?php
/**
 * Template for displaying single license posts
 */

?>

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:post-title {"level":1, "className":"entry-title"} /-->

    <!-- wp:post-featured-image {"sizeSlug":"large"} /-->

    <!-- wp:post-content /-->

    <!-- wp:group {"className":"entry-meta"} -->
    <div class="wp-block-group entry-meta">
        <!-- wp:post-terms {"term":"category", "prefix":"Categories: "} /-->
        
        <!-- wp:post-terms {"term":"post_tag", "prefix":"Tags: "} /-->
    </div>
    <!-- /wp:group -->

    <!-- wp:comments -->
    <div class="wp-block-comments">
        <!-- wp:comments-title /-->
        <!-- wp:comment-template -->
            <!-- wp:comment-content /-->
            <!-- wp:comment-author-name /-->
            <!-- wp:comment-date /-->
            <!-- wp:comment-reply-link /-->
        <!-- /wp:comment-template -->
        <!-- wp:comments-pagination -->
            <!-- wp:comments-pagination-previous /-->
            <!-- wp:comments-pagination-numbers /-->
            <!-- wp:comments-pagination-next /-->
        <!-- /wp:comments-pagination -->
        <!-- wp:comment-form /-->
    </div>
    <!-- /wp:comments -->
</div>
<!-- /wp:group -->
