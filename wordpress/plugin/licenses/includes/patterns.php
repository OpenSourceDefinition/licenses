<?php
/**
 * Register license block patterns
 */
function register_license_block_patterns() {
    register_block_pattern_category(
        'licenses',
        array('label' => __('Licenses', 'open-source-licenses'))
    );

    // Add new categories pattern
    register_block_pattern(
        'licenses/categories',
        array(
            'title'       => __('License Categories', 'open-source-licenses'),
            'categories'  => array('licenses'),
            'content'     => get_license_categories_pattern(),
        )
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
    global $wp_query;
    
    // Use the main query if we're on a taxonomy page, otherwise create a new query
    if (is_tax('license_category')) {
        $licenses = $wp_query->posts;
    } else {
        $args = array(
            'post_type' => 'license',
            'posts_per_page' => 250,
        );
        $licenses = get_posts($args);
    }
    
    $rows = '';
    foreach ($licenses as $license) {
        $title = get_the_title($license);
        $excerpt = wp_trim_words(get_the_excerpt($license), 50, '');
        $categories = get_the_terms($license->ID, 'license_category');
        $category_names = $categories ? join(', ', wp_list_pluck($categories, 'name')) : '';
        
        $rows .= <<<HTML
        <!-- wp:tablerow -->
        <tr>
            <td class="has-text-align-left"><a href="{$license->guid}">{$title}</a></td>
            <td class="has-text-align-left">{$excerpt}</td>
            <td class="has-text-align-left">{$category_names}</td>
        </tr>
        <!-- /wp:tablerow -->
        HTML;
    }

    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:table {"className":"is-style-regular license-table","fontSize":"small"} -->
    <figure class="wp-block-table is-style-regular license-table has-small-font-size">
        <table>
            <!-- wp:table-head -->
            <thead>
                <tr>
                    <th class="has-text-align-left" style="width:20%">License</th>
                    <th class="has-text-align-left" style="width:60%">Description</th>
                    <th class="has-text-align-left" style="width:20%">Category</th>
                </tr>
            </thead>
            <!-- /wp:table-head -->
            <!-- wp:table-body -->
            <tbody>
                {$rows}
            </tbody>
            <!-- /wp:table-body -->
        </table>
    </figure>
    <!-- /wp:table -->
</div>
<!-- /wp:group -->
HTML;
}

function get_license_grid_pattern() {
    global $wp_query;
    
    // Use the main query if we're on a taxonomy page, otherwise create a new query
    if (is_tax('license_category')) {
        $licenses = $wp_query->posts;
    } else {
        $args = array(
            'post_type' => 'license',
            'posts_per_page' => 250,
        );
        $licenses = get_posts($args);
    }
    
    $cards = '';
    foreach ($licenses as $license) {
        $title = get_the_title($license);
        $excerpt = wp_trim_words(get_the_excerpt($license), 25, '');
        $categories = get_the_terms($license->ID, 'license_category');
        $category_names = $categories ? join(', ', wp_list_pluck($categories, 'name')) : '';
        
        $cards .= <<<HTML
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"width":"1px"}},"backgroundColor":"background","layout":{"type":"flex","orientation":"vertical"}} -->
        <div class="wp-block-group has-background-background-color has-background" style="border-width:1px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
            <!-- wp:heading {"level":3} -->
            <h3 class="wp-block-heading"><a href="{$license->guid}">{$title}</a></h3>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"className":"license-excerpt"} -->
            <p class="license-excerpt">{$excerpt}</p>
            <!-- /wp:paragraph -->
            <!-- wp:paragraph {"className":"license-categories"} -->
            <p class="license-categories">{$category_names}</p>
            <!-- /wp:paragraph -->
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
    // This pattern uses wp:query block which automatically handles taxonomy pages
    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:query {"queryId":1,"query":{"postType":"license","perPage":250,"inherit":true}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"layout":{"type":"default"}} -->
        <!-- wp:group {"className":"license-list-item","layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group license-list-item">
            <!-- wp:post-title {"level":3,"isLink":true} /-->
            <!-- wp:post-excerpt {"showMoreOnNewLine":false,"excerptLength":20} /-->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->

        <!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
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

// Add new function for categories pattern
function get_license_categories_pattern() {
    $categories = get_terms(array(
        'taxonomy' => 'license_category',
        'hide_empty' => true,
    ));
    
    $archive_url = get_post_type_archive_link('license');
    $category_items = <<<HTML
    <!-- wp:button {"className":"license-category-button"} -->
    <div class="wp-block-button license-category-button">
        <a href="{$archive_url}" class="wp-block-button__link wp-element-button">All Licenses</a>
    </div>
    <!-- /wp:button -->
    HTML;
    
    foreach ($categories as $category) {
        $category_url = get_term_link($category, 'license_category');
        if (is_wp_error($category_url)) continue;
        
        $category_items .= <<<HTML
        <!-- wp:button {"className":"license-category-button"} -->
        <div class="wp-block-button license-category-button">
            <a href="{$category_url}" class="wp-block-button__link wp-element-button">{$category->name}</a>
        </div>
        <!-- /wp:button -->
        HTML;
    }

    return <<<HTML
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal","flexWrap":"wrap"}} -->
    <div class="wp-block-buttons">
        {$category_items}
    </div>
    <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
HTML;
}
