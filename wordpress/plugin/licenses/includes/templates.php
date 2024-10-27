<?php
function osl_register_block_templates() {
    // Ensure we're using a block theme
    if (!wp_is_block_theme()) {
        return;
    }

    // Register block templates
    add_filter('block_templates', 'osl_add_block_templates', 10, 1);
}

function osl_add_block_templates($templates) {
    $plugin_templates = array(
        array(
            'slug' => 'archive-license',
            'title' => __('License Archive', 'open-source-licenses'),
            'content' => '<!-- wp:template-part {"slug":"header"} /-->
                <!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
                <main class="wp-block-group">
                    <!-- wp:pattern {"slug":"licenses/categories"} /-->
                    <!-- wp:pattern {"slug":"licenses/table-view"} /-->
                </main>
                <!-- /wp:group -->
                <!-- wp:template-part {"slug":"footer"} /-->'
        ),
        array(
            'slug' => 'taxonomy-license_category',
            'title' => __('License Category Archive', 'open-source-licenses'),
            'content' => '<!-- wp:template-part {"slug":"header"} /-->
                <!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
                <main class="wp-block-group">
                    <!-- wp:pattern {"slug":"licenses/categories"} /-->
                    <!-- wp:pattern {"slug":"licenses/table-view"} /-->
                </main>
                <!-- /wp:group -->
                <!-- wp:template-part {"slug":"footer"} /-->'
        ),
        array(
            'slug' => 'single-license',
            'title' => __('Single License', 'open-source-licenses'),
            'content' => '<!-- wp:template-part {"slug":"header"} /-->
                <!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
                <main class="wp-block-group">
                    <!-- wp:post-title {"level":1} /-->
                    <!-- wp:group {"className":"license-meta"} -->
                    <div class="wp-block-group license-meta">
                        <!-- wp:post-terms {"term":"license_category","prefix":"Categories: "} /-->
                        <!-- wp:post-terms {"term":"license_tag","prefix":"Tags: "} /-->
                    </div>
                    <!-- /wp:group -->
                    <!-- wp:post-content /-->
                </main>
                <!-- /wp:group -->
                <!-- wp:template-part {"slug":"footer"} /-->'
        )
    );

    foreach ($plugin_templates as $template) {
        $templates[] = array(
            'id'             => 'open-source-licenses//' . $template['slug'],
            'theme'          => 'open-source-licenses',
            'slug'           => $template['slug'],
            'title'          => $template['title'],
            'content'        => $template['content'],
            'source'         => 'plugin',
            'has_theme_file' => false,
            'area'           => 'uncategorized',
            'is_custom'      => false,
            'type'           => 'wp_template',
            'path'           => 'templates/' . $template['slug'] . '.html',
        );
    }

    return $templates;
}

// Register template support
function osl_register_template_support() {
    add_theme_support('block-templates');
    add_theme_support('block-template-parts');
}
add_action('after_setup_theme', 'osl_register_template_support');

// Initialize template registration
add_action('init', 'osl_register_block_templates');
