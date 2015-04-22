<?php
/*
Plugin Name: Recipes
Version: 0.1
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: recipes
Domain Path: /languages
*/

define( 'PLUGIN_DIR', dirname(__FILE__).'/' );
define( 'POST_TYPE_DIR', PLUGIN_DIR . 'post-types/');
define( 'TAXONOMY_DIR', PLUGIN_DIR . 'taxonomies/');
define( 'WIDGET_DIR', PLUGIN_DIR . 'widgets/');

include_once(POST_TYPE_DIR . 'recipe.php');
include_once(TAXONOMY_DIR . 'region.php');
include_once(TAXONOMY_DIR . 'recipe_category.php');
include_once(WIDGET_DIR . 'recent_recipes.php');

