<?php
/**
 * Shortcode for displaying a random quote
 */
function quotepress_redux_random_quote($atts) {
    $atts = shortcode_atts(array(
        'text_color' => 'black',
        'font_size' => 'clamp(1.39rem, 1.39rem + ((1vw - 0.2rem) * 0.767), 1.85rem)',
        'background_color' => 'transparent',
    ), $atts);

    $args = array(
        'post_type' => 'quote',
        'posts_per_page' => 1,
        'orderby' => 'rand'
    );
    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $quote_text = get_post_meta(get_the_ID(), 'quote_text', true);
            $author = get_post_meta(get_the_ID(), 'author', true);

            $output = "<blockquote class='wp-block-quote quotepress-block' style='color: {$atts['text_color']}; font-size: {$atts['font_size']}; background-color: {$atts['background_color']}; padding: .5rem 1.5rem; border-radius: 1rem; line-height: 1.2em'>";
            $output .= "<p>{$quote_text}</p>";
            $output .= "<p><em>- {$author}</em></p>";
            $output .= "</blockquote>";

            return $output;
        }
    } else {
        return '<p>' . __('No quotes found', 'text_domain') . '</p>';
    }
    wp_reset_postdata();
}
add_shortcode('random_quote', 'quotepress_redux_random_quote');