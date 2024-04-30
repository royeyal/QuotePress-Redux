<?php
/**
 * Custom Fields
 */
// Register the meta box for Quote Text and Author
function quotepress_redux_add_custom_meta_boxes() {
    add_meta_box(
        'quotepress_redux_quote_details', // ID of the meta box
        __('Quote Details', 'text_domain'), // Title of the meta box
        'quotepress_redux_quote_meta_box_callback', // Callback function
        'quotes', // Post type
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
    $quote_author = get_post_meta($post->ID, 'quote_author', true);

    // HTML for the meta box fields
    echo '<p><label for="quotepress_redux_quote_text">' . __('Quote Text', 'text_domain') . '</label>';
    echo '<textarea id="quotepress_redux_quote_text" name="quotepress_redux_quote_text" rows="4" cols="50">' . esc_textarea($quote_text) . '</textarea></p>';

    echo '<p><label for="quotepress_redux_author">' . __('Author', 'text_domain') . '</label>';
    echo '<input type="text" id="quotepress_redux_author" name="quotepress_redux_author" value="' . esc_attr($quote_author) . '" size="25" /></p>';
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
        update_post_meta($post_id, 'quote_author', sanitize_text_field($_POST['quotepress_redux_author']));
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
    if ('quotes' === $post_type) {
        wp_enqueue_style('quotepress-redux-styles', QUOTEPRESSREDUX_PLUGIN_URL . 'css/quotepress-redux-styles.css');
    }
}
add_action('admin_enqueue_scripts', 'quotepress_redux_enqueue_admin_styles');