<?php
/**
 * Template Name: Quote Archive
 *
 * This template displays the archive page for the "Quotes" custom post type.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
            </header><!-- .page-header -->

            <?php
            // Start the Loop.
            while ( have_posts() ) :
                the_post();

                // Include the template for the content of the quotes (e.g., content-quote.php).
                get_template_part( 'content', 'quote' );

            endwhile;

            // Previous/next page navigation.
            the_posts_pagination();

        else :
            // If no content, include the "No quotes found" template.
            get_template_part( 'content', 'none' );

        endif;
        ?>

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>