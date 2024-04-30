<?php
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
        // 'taxonomies' => [
		// 	'category',
		// 	'post_tag',
		// ],
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
    register_post_type('quotes', $args);
}
add_action('init', 'quotepress_redux_custom_post_type', 0);

function quotepress_redux_register_taxonomy() {
    $labels = array(
          'name'              => __( 'Quotes Categories', 'text-domain' ),
          'singular_name'     => __( 'Quotes Category', 'text-domain' ),
          'search_items'      => __( 'Search Quotes Categories', 'text-domain' ),
          'all_items'         => __( 'All Quotes Categories', 'text-domain' ),
          'edit_item'         => __( 'Edit Quotes Category', 'text-domain' ),
          'update_item'       => __( 'Update Quotes Category', 'text-domain' ),
          'add_new_item'      => __( 'Add New Quotes Category', 'text-domain' ),
          'new_item_name'     => __( 'New Quotes Category Name', 'text-domain' ),
          'menu_name'         => __( 'Quotes Categories', 'text-domain' ),
      );
      
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'has_archive' => true,
        'sort' => true,
        'args' => array( 'orderby' => 'term_order' ),
        'rewrite' => array( 'slug' => 'quotes-category' ),
        'show_admin_column' => true
    );    
    register_taxonomy( 'quotes_category', array( 'quotes' ), $args);      
}
add_action( 'init', 'quotepress_redux_register_taxonomy' );
