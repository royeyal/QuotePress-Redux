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
        'label'                 => esc_html__( 'Quotes', 'text-domain' ),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports' => [
			'title',
			'editor',
			'thumbnail',
			'custom-fields',
		],
        'taxonomies' => [
			'category',
			'post_tag',
		],
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-quote',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => true,
    );
    register_post_type('quote', $args);
}
add_action('init', 'quotepress_redux_custom_post_type', 0);

function wpmu_register_taxonomy() {

    $labels = array(
          'name'              => __( 'Services', 'wpmu' ),
          'singular_name'     => __( 'Service', 'wpmu' ),
          'search_items'      => __( 'Search Services', 'wpmu' ),
          'all_items'         => __( 'All Services', 'wpmu' ),
          'edit_item'         => __( 'Edit Services', 'wpmu' ),
          'update_item'       => __( 'Update Services', 'wpmu' ),
          'add_new_item'      => __( 'Add New Services', 'wpmu' ),
          'new_item_name'     => __( 'New Service Name', 'wpmu' ),
          'menu_name'         => __( 'Services', 'wpmu' ),
      );
      
      $args = array(
          'labels' => $labels,
          'hierarchical' => true,
          'sort' => true,
          'args' => array( 'orderby' => 'term_order' ),
          'rewrite' => array( 'slug' => 'services' ),
          'show_admin_column' => true
      );
      
      register_taxonomy( 'service', array( 'quote' ), $args);
      
  }
  add_action( 'init', 'wpmu_register_taxonomy' );
  

/**
 * Custom Fields
 */
// Register the meta box for Quote Text and Author
function quotepress_redux_add_custom_meta_boxes() {
    add_meta_box(
        'quotepress_redux_quote_details', // ID of the meta box
        __('Quote Details', 'text_domain'), // Title of the meta box
        'quotepress_redux_quote_meta_box_callback', // Callback function
        'quote', // Post type
        'normal', // Context
        'high' // Priority
    );
}
add_action('add_meta_boxes', 'quotepress_redux_add_custom_meta_boxes');

// Meta box callback function to display the fields
function quotepress_redux_quote_meta_box_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('quotepress_redux_save_quote_meta', 'quotepress_redux_quote_nonce');

    // Retrieve existing values from the database
    $quote_text = get_post_meta($post->ID, 'quote_text', true);
    $author = get_post_meta($post->ID, 'author', true);

    // HTML for the meta box fields
    echo '<p><label for="quotepress_redux_quote_text">' . __('Quote Text', 'text_domain') . '</label>';
    echo '<textarea id="quotepress_redux_quote_text" name="quotepress_redux_quote_text" rows="4" cols="50">' . esc_textarea($quote_text) . '</textarea></p>';

    echo '<p><label for="quotepress_redux_author">' . __('Author', 'text_domain') . '</label>';
    echo '<input type="text" id="quotepress_redux_author" name="quotepress_redux_author" value="' . esc_attr($author) . '" size="25" /></p>';
}

// Save the meta box data
function quotepress_redux_save_quote_meta($post_id) {
    // Check if nonce is set and valid
    if (!isset($_POST['quotepress_redux_quote_nonce']) || !wp_verify_nonce($_POST['quotepress_redux_quote_nonce'], 'quotepress_redux_save_quote_meta')) {
        return;
    }

    // Check if the user has permissions to save data
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if it's not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save or update the Quote Text
    if (isset($_POST['quotepress_redux_quote_text'])) {
        update_post_meta($post_id, 'quote_text', sanitize_text_field($_POST['quotepress_redux_quote_text']));
    }

    // Save or update the Author
    if (isset($_POST['quotepress_redux_author'])) {
        update_post_meta($post_id, 'author', sanitize_text_field($_POST['quotepress_redux_author']));
    }
}
add_action('save_post', 'quotepress_redux_save_quote_meta');

/**
 * Style custom meta boxes
 */
function quotepress_redux_enqueue_admin_styles($hook) {
    // Only enqueue the styles on the edit screens of the Quotes post type
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }
    global $post_type;
    if ('quote' === $post_type) {
        wp_enqueue_style('quotepress-redux-styles', plugin_dir_url(__FILE__) . 'css/quotepress-redux-styles.css');
    }
}
add_action('admin_enqueue_scripts', 'quotepress_redux_enqueue_admin_styles');


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

            $output = "<blockquote class='wp-block-quote' style='color: {$atts['text_color']}; font-size: {$atts['font_size']}; background-color: {$atts['background_color']};'>";
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
