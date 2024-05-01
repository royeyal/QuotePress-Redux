<?php
function get_custom_post_type_template( $archive_template ) {
    global $post;

    if ( is_post_type_archive ( 'quotes' ) ) {
         $archive_template = QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/archive-quotes.php';
    }
    return $archive_template;
}
add_filter( 'archive_template', 'get_custom_post_type_template' ) ;

function my_single_custom_template($single) {
    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'quotes' ) {
        if ( file_exists( QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/single-quotes.php' ) ) {
            return QUOTEPRESSREDUX_PLUGIN_DIR . '/templates/single-quotes.php';
        }
    }

    return $single;

}
add_filter('single_template', 'my_single_custom_template');
