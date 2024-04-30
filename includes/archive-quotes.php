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

            <div class="quotes-archive-container">
                <?php
                // Start the Loop.
                while ( have_posts() ) :
                    the_post();
                ?>
                    <div class="quote-archive-item">
                        <?php
                        if ( has_post_thumbnail() ) {
                            // Display the featured image as a square with a class for styling.
                            the_post_thumbnail( 'thumbnail', array( 'class' => 'quote-archive-thumbnail' ) );
                        } else {
                            // Display a blank square image if no featured image is set.
                            echo '<img src="' . esc_url( QUOTEPRESSREDUX_PLUGIN_URL . 'images/blank-square.webp' ) . '" alt="Blank Square" class="quote-archive-thumbnail blank-square">';
                        }
                        ?>
                        <div class="quote-archive-content">
                            <h2 class="quote-archive-title"><?php the_title(); ?></h2>
                            <div class="quote-archive-excerpt"><?php the_excerpt(); ?></div>
                        </div>
                    </div><!-- .quote-archive-item -->
                <?php
                endwhile;
                ?>
            </div><!-- .quote-archive-grid -->

            <?php
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
