<?php
/**
 * Template Name: Quote Category Archive
 *
 * This template displays the archive page for the "Quotes Category" taxonomy.
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
                    <div id="post-<?php the_ID(); ?>" <?php post_class('quote-archive-item'); ?>>
                        <?php
                        if ( has_post_thumbnail() ) {
                            // Display the featured image as a square with a class for styling.
                            the_post_thumbnail( 'thumbnail', array( 'class' => 'quote-archive-thumbnail' ) );
                        } else {
                            // Display a blank square image if no featured image is set.
                            echo '<img src="' . esc_url( QUOTEPRESSREDUX_PLUGIN_URL . 'images/blank-square.webp' ) . '" alt="Blank Square" class="quote-archive-thumbnail blank-square">';
                        }

                        $quote_text = get_post_meta(get_the_ID(), 'quote_text', TRUE);
                        $quote_author = get_post_meta(get_the_ID(), 'quote_author', TRUE);
                        ?>
                        <blockquote class="wp-block-quote quotepress-block">
                            <p class="quote-archive-text"><?php echo esc_html($quote_author); ?></p>
                            <p class="quote-archive-author"><?php echo esc_html($quote_text); ?></p>
                        </blockquote>
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
