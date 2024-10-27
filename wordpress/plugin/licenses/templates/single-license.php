<?php get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="entry-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <footer class="entry-footer">
                <?php
                $categories = get_the_category();
                $tags = get_the_tags();
                if ($categories || $tags) :
                ?>
                    <div class="entry-meta">
                        <?php if ($categories) : ?>
                            <span class="cat-links">
                                Categories: <?php echo get_the_category_list(', '); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($tags) : ?>
                            <span class="tags-links">
                                Tags: <?php echo get_the_tag_list('', ', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </footer>
        </article>

        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
