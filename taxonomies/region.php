<?php

function region_init()
{
    register_taxonomy(
        'region', array( 'recipe' ), array(
                    'hierarchical' => false,
                    'public' => true,
                    'show_in_nav_menus' => true,
                    'show_ui' => true,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'rewrite' => array(
                        'slug' => 'recipes/region',
                        'with_front' => false,
                    ),
                    'capabilities' => array(
                        'manage_terms' => 'edit_posts',
                        'edit_terms' => 'edit_posts',
                        'delete_terms' => 'edit_posts',
                        'assign_terms' => 'edit_posts'
                    ),
                    'labels' => array(
                        'name' => __( 'Regions', 'region' ),
                        'singular_name' => _x(
                            'Region', 'taxonomy general name', 'region'
                        ),
                        'search_items' => __( 'Search Regions', 'region' ),
                        'popular_items' => __( 'Popular Regions', 'region' ),
                        'all_items' => __( 'All Regions', 'region' ),
                        'parent_item' => __( 'Parent Region', 'region' ),
                        'parent_item_colon' => __( 'Parent Region:', 'region' ),
                        'edit_item' => __( 'Edit Region', 'region' ),
                        'update_item' => __( 'Update Region', 'region' ),
                        'add_new_item' => __( 'New Region', 'region' ),
                        'new_item_name' => __( 'New Region', 'region' ),
                        'separate_items_with_commas' => __(
                            'Regions separated by comma', 'region'
                        ),
                        'add_or_remove_items' => __(
                            'Add or remove Regions', 'region'
                        ),
                        'choose_from_most_used' => __(
                            'Choose from the most used Regions', 'region'
                        ),
                        'menu_name' => __( 'Regions', 'region' ),
                    ),
                )
    );

}

add_action( 'init', 'region_init' );
