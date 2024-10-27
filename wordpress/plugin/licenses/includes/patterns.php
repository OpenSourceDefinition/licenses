<?php
/**
 * Register license block patterns
 */
function register_license_block_patterns() {
    register_block_pattern_category(
        'licenses',
        array('label' => __('Licenses', 'open-source-licenses'))
    );

    register_block_pattern(
        'licenses/table-view',
        array(
            'title'       => __('Licenses Table View', 'open-source-licenses'),
            'categories'  => array('licenses'),
            'content'     => get_license_table_pattern(),
        )
    );

    register_block_pattern(
        'licenses/grid-view',
        array(
            'title'       => __('Licenses Grid View', 'open-source-licenses'),
            'categories'  => array('licenses'),
            'content'     => get_license_grid_pattern(),
        )
    );

    register_block_pattern(
        'licenses/list-view',
        array(
            'title'       => __('Licenses List View', 'open-source-licenses'),
            'categories'  => array('licenses'),
            'content'     => get_license_list_pattern(),
        )
    );
}
add_action('init', 'register_license_block_patterns');

function get_license_table_pattern() {
    $args = array(
        'post_type' => 'license',
        'posts_per_page' => 250,
    );
    $licenses = get_posts($args);
    
    $rows = '';
    foreach ($licenses as $license) {
        $title = get_the_title($license);
        // Remove ellipsis and just do a clean word trim
        $excerpt = wp_trim_words(get_the_excerpt($license), 50, '');
        $categories = get_the_terms($license->ID, 'license_category');
        $category_names = $categories ? join(', ', wp_list_pluck($categories, 'name')) : '';
        
        $rows .= <<<HTML
        <tr>
            <td class="has-text-align-left"><a href="{$license->guid}">{$title}</a></td>
            <td class="has-text-align-left">{$excerpt}</td>
            <td class="has-text-align-left">{$category_names}</td>
        </tr>
        HTML;
    }

    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:table {"className":"is-style-regular license-table","fontSize":"small"} -->
    <figure class="wp-block-table is-style-regular license-table has-small-font-size">
        <table>
            <thead>
                <tr>
                    <th class="has-text-align-left" style="width:20%">License</th>
                    <th class="has-text-align-left" style="width:60%">Description</th>
                    <th class="has-text-align-left" style="width:20%">Category</th>
                </tr>
            </thead>
            <tbody>
                {$rows}
            </tbody>
        </table>
    </figure>
    <!-- /wp:table -->
</div>
<!-- /wp:group -->
HTML;
}

function get_license_grid_pattern() {
    $args = array(
        'post_type' => 'license',
        'posts_per_page' => 250,
    );
    $licenses = get_posts($args);
    
    $cards = '';
    foreach ($licenses as $license) {
        $title = get_the_title($license);
        $excerpt = wp_trim_words(get_the_excerpt($license), 25, '');
        $categories = get_the_terms($license->ID, 'license_category');
        $category_names = $categories ? join(', ', wp_list_pluck($categories, 'name')) : '';
        
        $cards .= <<<HTML
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"width":"1px"}},"backgroundColor":"background","layout":{"type":"flex","orientation":"vertical"}} -->
        <div class="wp-block-group has-background-background-color has-background" style="border-width:1px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
            <h3 class="wp-block-heading"><a href="{$license->guid}">{$title}</a></h3>
            <div class="license-excerpt">{$excerpt}</div>
            <div class="license-categories">{$category_names}</div>
        </div>
        <!-- /wp:group -->
        HTML;
    }

    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1,"className":"page-title"} -->
    <h1 class="wp-block-heading page-title">Open Source Licenses</h1>
    <!-- /wp:heading -->

    <!-- wp:group {"className":"license-grid","layout":{"type":"grid","minimumColumnWidth":"300px"}} -->
    <div class="wp-block-group license-grid">
        {$cards}
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

function get_license_list_pattern() {
    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:query {"queryId":1,"query":{"postType":"license","perPage":250}} -->
    <div class="wp-block-query">
        <!-- wp:post-template -->
            <!-- wp:group {"className":"license-list-item","layout":{"type":"flex","flexWrap":"nowrap"}} -->
            <div class="wp-block-group license-list-item">
                <!-- wp:post-title {"level":3,"isLink":true} /-->
                <!-- wp:post-excerpt {"showMoreOnNewLine":false,"excerptLength":20} /-->
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
