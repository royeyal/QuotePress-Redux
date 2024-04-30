<?php
function query_post_type($query) {
    if((is_category() || is_tag()) && !is_admin()) {
        $query->set('post_type','quotes');
        return $query;
    }
}
add_filter('pre_get_posts', 'query_post_type');

add_filter( 'single_template', function( $template ) {
    global $post;
    if ( $post->post_type === 'guitar' ) {
        $locate_template = locate_template( "single-{$post->post_name}.php" );
        if ( ! empty( $locate_template ) ) {
            $template = $locate_template;
        }
    }
    return $template;
} );

add_filter( 'template_include', 'load_new_custom_tax_template');
function load_new_custom_tax_template ($tax_template) {
    if (is_category()) {
        $tax_template = dirname( __FILE__ ) . '/templates/archive.php';
    }
    return $tax_template;
}

add_filter( 'theme_file_path', 'wpse_258026_modify_theme_include_file', 20, 2 );
function wpse_258026_modify_theme_include_file( $path, $file = '' ) {
    if( 'includes/archive.php' === $file ) {
        // change path here as required
        return plugin_dir_path( __FILE__ ) . 'includes/archive.php';
    }
    return $path;
}

// Template Logic
function quotepress_redux_init_template_logic($original_template) {
    // Check Theme Template or Plugin Template for archive-quotes.php

    $file = trailingslashit(get_template_directory()) . 'archive-quotes.php';

    if(is_post_type_archive('quote')) {
    // some additional logic goes here^.
        if(file_exists($file)) {
            return trailingslashit(get_template_directory()).'archive-quotes.php';
        } else {
            return plugin_dir_path(__DIR__) . 'templates/archive-quotes.php';
        }
    } elseif(is_singular('quote')) {
        if(file_exists(get_template_directory_uri() . '/single-quotes.php')) {
            return get_template_directory_uri() . '/single-quotes.php';
        } else {
            return plugin_dir_path(__DIR__) . 'templates/single-quotes.php';
        }
    }

    return $original_template;
}
add_action('template_include', 'quotepress_redux_init_template_logic');

if ( $overridden_template = locate_template( 'archive.php' ) ) {
	/*
	 * locate_template() returns path to file.
	 * if either the child theme or the parent theme have overridden the template.
	 */
	load_template( $overridden_template );
} else {
	/*
	 * If neither the child nor parent theme have overridden the template,
	 * we load the template from the 'templates' sub-directory of the directory this file is in.
	 */
	load_template( QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/archive.php' );
}