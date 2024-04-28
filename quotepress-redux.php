<?php
/*
Plugin Name: QuotePress Redux
Description: A plugin for managing and displaying quotes.
Version: 1.0
Author: Roy Eyal
Author URI: https://royeyal.studio/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: text_domain
*/

// Register Custom Post Type for Quotes
function quotepress_redux_custom_post_type() {
    $labels = array(
        'name'                  => _x('Quotes', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Quote', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Quotes', 'text_domain'),
        'name_admin_bar'        => __('Quote', 'text_domain'),
        'archives'              => __('Quote Archives', 'text_domain'),
        'attributes'            => __('Quote Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Quote:', 'text_domain'),
        'all_items'             => __('All Quotes', 'text_domain'),
        'add_new_item'          => __('Add New Quote', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Quote', 'text_domain'),
        'edit_item'             => __('Edit Quote', 'text_domain'),
        'update_item'           => __('Update Quote', 'text_domain'),
        'view_item'             => __('View Quote', 'text_domain'),
        'view_items'            => __('View Quotes', 'text_domain'),
        'search_items'          => __('Search Quote', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into quote', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this quote', 'text_domain'),
        'items_list'            => __('Quotes list', 'text_domain'),
        'items_list_navigation' => __('Quotes list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter quotes list', 'text_domain'),
    );
    $args = array(
        'label'                 => __('Quote', 'text_domain'),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('quote', $args);
}
add_action('init', 'quotepress_redux_custom_post_type', 0);

// Shortcode for displaying a random quote
function quotepress_redux_random_quote($atts) {
    $atts = shortcode_atts(array(
        'text_color' => 'black',
        'font_size' => '16px',
        'background_color' => 'none',
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

            $output = "<div style='color: {$atts['text_color']}; font-size: {$atts['font_size']}; background-color: {$atts['background_color']};'>";
            $output .= "<p>{$quote_text}</p>";
            $output .= "<p><em>- {$author}</em></p>";
            $output .= "</div>";

            return $output;
        }
    } else {
        return '<p>No quotes found</p>';
    }
    wp_reset_postdata();
}
add_shortcode('random_quote', 'quotepress_redux_random_quote');
?>
