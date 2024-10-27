<?php
/**
 * Register license block patterns
 */
function register_license_block_patterns() {
    register_block_pattern_category(
        'licenses',
        array('label' => __('Licenses', 'your-text-domain'))
    );

    register_block_pattern(
        'licenses/table-view',
        array(
            'title'       => __('Licenses Table View', 'your-text-domain'),
            'categories'  => array('licenses'),
            'content'     => get_license_table_pattern(),
        )
    );

    register_block_pattern(
        'licenses/grid-view',
        array(
            'title'       => __('Licenses Grid View', 'your-text-domain'),
            'categories'  => array('licenses'),
            'content'     => get_license_grid_pattern(),
        )
    );
}
add_action('init', 'register_license_block_patterns');

function get_license_table_pattern() {
    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1,"className":"page-title"} -->
    <h1 class="page-title">Open Source Licenses</h1>
    <!-- /wp:heading -->

    <!-- wp:query {"queryId":1,"query":{"postType":"license","perPage":10}} -->
    <div class="wp-block-query">
        <!-- wp:table {"className":"license-table"} -->
        <figure class="wp-block-table license-table">
            <table>
                <thead>
                    <tr>
                        <th>License</th>
                        <th>Description</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- wp:post-template -->
                        <tr>
                            <td><!-- wp:post-title {"level":2,"isLink":true} /--></td>
                            <td><!-- wp:post-excerpt {"showMoreOnNewLine":false} /--></td>
                            <td><!-- wp:post-terms {"term":"license_type"} /--></td>
                        </tr>
                    <!-- /wp:post-template -->
                </tbody>
            </table>
        </figure>
        <!-- /wp:table -->

        <!-- wp:query-pagination -->
            <!-- wp:query-pagination-previous /-->
            <!-- wp:query-pagination-numbers /-->
            <!-- wp:query-pagination-next /-->
        <!-- /wp:query-pagination -->
    </div>
    <!-- /wp:query -->
</div>
<!-- /wp:group -->
HTML;
}

function get_license_grid_pattern() {
    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1,"className":"page-title"} -->
    <h1 class="page-title">Open Source Licenses</h1>
    <!-- /wp:heading -->

    <!-- wp:query {"queryId":1,"query":{"postType":"license","perPage":12}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"width":"1px"}},"backgroundColor":"background","layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group">
                <!-- wp:post-title {"level":2,"isLink":true} /-->
                <!-- wp:post-excerpt {"showMoreOnNewLine":false} /-->
                <!-- wp:post-terms {"term":"license_type"} /-->
            </div>
            <!-- /wp:group -->
        <!-- /wp:post-template -->

        <!-- wp:query-pagination -->
            <!-- wp:query-pagination-previous /-->
            <!-- wp:query-pagination-numbers /-->
            <!-- wp:query-pagination-next /-->
        <!-- /wp:query-pagination -->
    </div>
    <!-- /wp:query -->
</div>
<!-- /wp:group -->
HTML;
}