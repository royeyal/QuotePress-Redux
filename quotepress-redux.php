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
        'has_archive'           => true
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

/**
 * Deactivation hook
 */
function quotepress_redux_deactivate() {
	// Unregister the post type, so the rules are no longer in memory.
	unregister_post_type( 'quotes' );
	// Clear the permalinks to remove our post type's rules from the database.
	flush_rewrite_rules();
}
//register_deactivation_hook( __FILE__, 'quotepress_redux_deactivate' );

/**
 * Filter quotes using AJAX 
*/

function quotepress_redux_enqueue_scripts() {
    wp_enqueue_script('quotepress-ajax', plugin_dir_url(__FILE__) . 'js/quotepress-ajax.js', array('jquery'), null, true);
    wp_localize_script('quotepress-ajax', 'quotepress_ajax_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('quotepress_ajax_nonce')
    ));
}
//add_action('wp_enqueue_scripts', 'quotepress_redux_enqueue_scripts');

function quotepress_redux_filter_quotes() {
    check_ajax_referer('quotepress_ajax_nonce', 'nonce');

    $args = array(
        'post_type' => 'quote',
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
            'taxonomy' => 'category',
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
//add_action('wp_ajax_filter_quotes', 'quotepress_redux_filter_quotes');
//add_action('wp_ajax_nopriv_filter_quotes', 'quotepress_redux_filter_quotes');
