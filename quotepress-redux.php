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

// Define constants for readability
define('QUOTEPRESSREDUX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('QUOTEPRESSREDUX_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once QUOTEPRESSREDUX_PLUGIN_DIR . 'includes/custom-post-type.php';
require_once QUOTEPRESSREDUX_PLUGIN_DIR . 'includes/shortcode.php';
require_once QUOTEPRESSREDUX_PLUGIN_DIR . 'includes/custom-fields.php';

// Archive page for the custom taxonomy
function quotepress_redux_get_custom_post_type_template( $archive_template ) {
    global $post;

    if ( is_post_type_archive ( 'quotes' ) || is_tax('quotes_category') ) {
         $archive_template = QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/archive-quotes.php';
    }
    return $archive_template;
}
add_filter( 'archive_template', 'quotepress_redux_get_custom_post_type_template' ) ;

function quotepress_redux_custom_template($single) {
    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'quotes' ) {
        if ( file_exists( QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/single-quotes.php' ) ) {
            return QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/single-quotes.php';
        }
    }
    return $single;
}
add_filter('single_template', 'quotepress_redux_custom_template');

// require_once QUOTEPRESSREDUX_PLUGIN_DIR . 'includes/sidebar-widget.php';
// require_once QUOTEPRESSREDUX_PLUGIN_DIR . 'includes/settings-page.php';

// Frontend styles
function quotepress_redux_enqueue_styles() {
    wp_enqueue_style('quotepress-redux-styles', plugin_dir_url(__FILE__) . 'css/quotepress-redux.css');
}
add_action('wp_enqueue_scripts', 'quotepress_redux_enqueue_styles');


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
add_action('wp_enqueue_scripts', 'quotepress_redux_enqueue_scripts');

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
add_action('wp_ajax_filter_quotes', 'quotepress_redux_filter_quotes');
add_action('wp_ajax_nopriv_filter_quotes', 'quotepress_redux_filter_quotes');
