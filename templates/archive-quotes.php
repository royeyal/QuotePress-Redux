<?php
/**
 * Template Name: Quote Archive
 *
 * This template displays the archive page for the "Quotes" custom post type.
 */

get_header(); ?>

<?php
function quotepress_redux_filter_quotes() {
    check_ajax_referer('quotepress_ajax_nonce', 'nonce');

    $args = array(
        'post_type' => 'quotes',
        'posts_per_page' => -1
    );

    // Add tax_query to $args based on AJAX request
    $tax_query = array('relation' => 'AND');
    if (!empty($_POST['filter']['author'])) {
        $tax_query[] = array(
            'taxonomy' => 'author',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['filter']['author'])
        );
    }
    if (!empty($_POST['filter']['category'])) {
        $tax_query[] = array(
            'taxonomy' => 'quotes-category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['filter']['category'])
        );
    }
    if (!empty($_POST['filter']['tag'])) {
        $tax_query[] = array(
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['filter']['tag'])
        );
    }
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Output your quote markup
            echo '<div class="quote">' . get_the_content() . '</div>';
        }
    } else {
        echo 'No quotes found.';
    }

    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action('wp_ajax_filter_quotes', 'quotepress_redux_filter_quotes');
add_action('wp_ajax_nopriv_filter_quotes', 'quotepress_redux_filter_quotes');
?>

<div id="primary" class="content-area"">
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
