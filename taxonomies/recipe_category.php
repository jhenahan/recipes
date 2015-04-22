<?php

function recipe_category_init() {
	register_taxonomy( 'recipe_category', array( 'recipe' ), array(
		'hierarchical'      => false,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => true,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'            => array(
			'name'                       => __( 'Recipe Categories', 'recipe_category' ),
			'singular_name'              => _x( 'Recipe Category', 'taxonomy general name', 'recipe_category' ),
			'search_items'               => __( 'Search Recipe Categories', 'recipe_category' ),
			'popular_items'              => __( 'Popular Recipe Categories', 'recipe_category' ),
			'all_items'                  => __( 'All Recipe Categories', 'recipe_category' ),
			'parent_item'                => __( 'Parent Recipe Category', 'recipe_category' ),
			'parent_item_colon'          => __( 'Parent Recipe Category:', 'recipe_category' ),
			'edit_item'                  => __( 'Edit Recipe Category', 'recipe_category' ),
			'update_item'                => __( 'Update Recipe Category', 'recipe_category' ),
			'add_new_item'               => __( 'New Recipe Category', 'recipe_category' ),
			'new_item_name'              => __( 'New Recipe Category', 'recipe_category' ),
			'separate_items_with_commas' => __( 'Recipe Categories separated by comma', 'recipe_category' ),
			'add_or_remove_items'        => __( 'Add or remove Recipe Categories', 'recipe_category' ),
			'choose_from_most_used'      => __( 'Choose from the most used Recipe Categories', 'recipe_category' ),
			'menu_name'                  => __( 'Recipe Categories', 'recipe_category' ),
		),
	) );

}
add_action( 'init', 'recipe_category_init' );
