<?php

get_header(); ?>

<div id="main-content" class="main-content">

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <?php
                // Start the Loop.
                while ( have_posts() ) : the_post();

                    // Include the page content template.
                    //get_template_part( 'content', 'page' );
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

                        $quote_text = get_field('quote_text');
                        $quote_author = get_field('quote_author');
                        ?>
                        <blockquote class="wp-block-quote quotepress-block">
                            <p class="quote-archive-text"><?php echo esc_html($quote_author); ?></p>
                            <p class="quote-archive-author"><?php echo esc_html($quote_text); ?></p>
                        </blockquote>
                    </div><!-- .quote-archive-item -->
                <?php
                endwhile;
            ?>
        </div><!-- #content -->
    </div><!-- #primary -->
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();