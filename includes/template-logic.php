<?php
// Template Logic
function quotepress_redux_init_template_logic($original_template) {
    // Check Theme Template or Plugin Template for archive-quotes.php

    $file = trailingslashit(get_template_directory()) . 'archive-quotes.php';

    if(is_post_type_archive('links')) {
    // some additional logic goes here^.
        if(file_exists($file)) {
            return trailingslashit(get_template_directory()).'archive-quotes.php';
        } else {
            return plugin_dir_path(__DIR__) . 'templates/archive-quotes.php';
        }
    } elseif(is_singular('links')) {
        if(file_exists(get_template_directory_uri() . '/single-quotes.php')) {
            return get_template_directory_uri() . '/single-quotes.php';
        } else {
            return plugin_dir_path(__DIR__) . 'templates/single-quotes.php';
        }
    }

    return $original_template;
}
add_action('template_include', 'quotepress_redux_init_template_logic');