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
