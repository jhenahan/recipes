<?php

function recipe_init()
{
    register_post_type(
        'recipe', array(
                    'labels' => array(
                        'name' => __( 'Recipes', 'recipe' ),
                        'singular_name' => __( 'Recipe', 'recipe' ),
                        'all_items' => __( 'Recipes', 'recipe' ),
                        'new_item' => __( 'New Recipe', 'recipe' ),
                        'add_new' => __( 'Add New', 'recipe' ),
                        'add_new_item' => __( 'Add New Recipe', 'recipe' ),
                        'edit_item' => __( 'Edit Recipe', 'recipe' ),
                        'view_item' => __( 'View Recipe', 'recipe' ),
                        'search_items' => __( 'Search Recipes', 'recipe' ),
                        'not_found' => __( 'No Recipes found', 'recipe' ),
                        'not_found_in_trash' => __(
                            'No Recipes found in trash', 'recipe'
                        ),
                        'parent_item_colon' => __( 'Parent Recipe', 'recipe' ),
                        'menu_name' => __( 'Recipes', 'recipe' ),
                    ),
                    'public' => true,
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_in_nav_menus' => true,
                    'supports' => array(
                        'title',
                        'editor'
                    ),
                    'has_archive' => true,
                    'rewrite' => array(
                        'slug' => 'recipes',
                        'with_front' => 'false'
                    ),
                    'query_var' => true,
                    'menu_icon' => 'dashicons-carrot',
                    'register_meta_box_cb' => 'add_recipe_metaboxes',
                )
    );

}

add_action( 'init', 'recipe_init' );

function recipe_updated_messages( $messages )
{
    global $post;

    $permalink = get_permalink( $post );

    $messages[ 'recipe' ] = array(
        0 => '',
        // Unused. Messages start at index 1.
        1 => sprintf(
            __(
                'Recipe updated. <a target="_blank" href="%s">View Recipe</a>',
                'recipe'
            ), esc_url( $permalink )
        ),
        2 => __( 'Custom field updated.', 'recipe' ),
        3 => __( 'Custom field deleted.', 'recipe' ),
        4 => __( 'Recipe updated.', 'recipe' ),
        /* translators: %s: date and time of the revision */
        5 => isset( $_GET[ 'revision' ] )
            ? sprintf(
                __( 'Recipe restored to revision from %s', 'recipe' ),
                wp_post_revision_title( (int)$_GET[ 'revision' ], false )
            )
            : false,
        6 => sprintf(
            __( 'Recipe published. <a href="%s">View Recipe</a>', 'recipe' ),
            esc_url( $permalink )
        ),
        7 => __( 'Recipe saved.', 'recipe' ),
        8 => sprintf(
            __(
                'Recipe submitted. <a target="_blank" href="%s">Preview Recipe</a>',
                'recipe'
            ), esc_url( add_query_arg( 'preview', 'true', $permalink ) )
        ),
        9 => sprintf(
            __(
                'Recipe scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Recipe</a>',
                'recipe'
            ), // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
            esc_url( $permalink )
        ),
        10 => sprintf(
            __(
                'Recipe draft updated. <a target="_blank" href="%s">Preview Recipe</a>',
                'recipe'
            ), esc_url( add_query_arg( 'preview', 'true', $permalink ) )
        ),
    );

    return $messages;
}

add_filter( 'post_updated_messages', 'recipe_updated_messages' );

function add_recipe_metaboxes()
{
    add_meta_box(
        'recipe_meta', 'Recipe Microdata', 'add_recipe_fields', 'recipe',
        'normal', 'default'
    );
}

function add_recipe_fields()
{
    global $post;
    $nonce      = wp_create_nonce( plugin_basename( __FILE__ ) );
    $nonce_html = <<<NONCE
<input type="hidden" name="recipe_meta_nonce" id="recipe_meta_nonce" value="{$nonce}" />
NONCE;
    _e( $nonce_html );

    $boxes = recipe_metaboxes();

    foreach ( $boxes as $box_id => $contents ) {
        recipe_single_field( $post, $box_id, $contents[ 'title' ] );
    }

    recipe_ingredients_multi_field( $post );
    recipe_nutrition_multi_field( $post );
    recipe_instructions_multi_field( $post );

}

// Render Fields

function recipe_single_field( $post, $field, $field_title )
{
    $param = get_post_meta( $post->ID, $field, true );

    $param_html = <<<PARAM
    <p>{$field_title}</p>
<input type="text" name="{$field}" value="{$param}"/>
PARAM;


    _e( $param_html );

}

function recipe_ingredients_multi_field( $post )
{
    $ingredients = get_post_meta( $post->ID, 'recipe_ingredients', true );

    ?>
    <div id="ingredient_wrapper">
        <?php
        $count = 0;
        if ( is_array( $ingredients ) ) {
            foreach ( $ingredients as $ingredient ) {
                if ( isset( $ingredient[ 'ingredient_name' ] )
                     || isset( $ingredient[ 'ingredient_quantity' ] )
                ) {
                    $name     = $ingredient[ 'ingredient_name' ];
                    $quantity = $ingredient[ 'ingredient_quantity' ];
                    $markup   = <<<'MARKUP'
        <div class="ingredient-entry">
        <p>
            Ingredient Name
            <input type="text" name="recipe_ingredients[%1$s][ingredient_name]" value="%2$s" />
        </p>
        <p>
            Ingredient Quantity :
            <input type="text" name="recipe_ingredients[%1$s][ingredient_quantity]" value="%3$s" />
        </p>
        <button class="remove-ingredient">%4$s</button>
        </div>
MARKUP;
                    printf(
                        $markup, $count, $name, $quantity,
                        __( 'Remove Ingredient' )
                    );

                }
            }

        }
        ?>
        <span id="add-ingredient"></span>
        <button class="add-ingredient"><?php _e( 'Add Ingredient' ); ?></button>
        <script>
            var $ = jQuery.noConflict();
            $(document).ready(function () {
                var count = <?php echo $count; ?>;
                $(".add-ingredient").click(function () {
                    count = count + 1;
                    $('#add-ingredient').append('<div class="ingredient-entry"><p> Ingredient Name <input type="text" name="recipe_ingredients[' + count + '][ingredient_name]" value="" /></p><p>Ingredient Quantity : <input type="text" name="recipe_ingredients[' + count + '][ingredient_quantity]" value="" /></p><button class="remove-ingredient">Remove Ingredient</button></div>');
                    return false;
                });
                $(".remove-ingredient").live('click', function () {
                    $(this).parent().remove();
                });
            });
        </script>
    </div> <?php
}

function recipe_nutrition_multi_field( $post )
{
    $nutrition = get_post_meta( $post->ID, 'recipe_nutrition', true );

    ?>
    <div id="nutrition_info_wrapper">
        <?php
        $count = 0;
        if ( is_array( $nutrition ) ) {
            foreach ( $nutrition as $nutrition_info ) {
                if ( isset( $nutrition_info[ 'nutrition_info_name' ] )
                     || isset( $nutrition_info[ 'nutrition_info_quantity' ] )
                ) {
                    $name     = $nutrition_info[ 'nutrition_info_name' ];
                    $quantity = $nutrition_info[ 'nutrition_info_quantity' ];
                    $markup   = <<<'MARKUP'
        <div class="nutrition-fact">
        <p>
            Nutrition Fact Name
            <input type="text" name="recipe_nutrition[%1$s][nutrition_info_name]" value="%2$s" />
        </p>
        <p>
            Nutrition Fact Quantity :
            <input type="text" name="recipe_nutrition[%1$s][nutrition_info_quantity]" value="%3$s" />
        </p>
        <button class="remove-nutrition">%4$s</button>
        </div>
MARKUP;
                    printf(
                        $markup, $count, $name, $quantity,
                        __( 'Remove Nutrition Fact' )
                    );

                }
            }

        }
        ?>
        <span id="add-nutrition"></span>
        <button class="add-nutrition"><?php _e(
                'Add Nutrition Fact'
            ); ?></button>
        <script>
            var $ = jQuery.noConflict();
            $(document).ready(function () {
                var count = <?php echo $count; ?>;
                $(".add-nutrition").click(function () {
                    count = count + 1;
                    $('#add-nutrition').append('<div class="nutrition-fact"><p> Nutrition Fact Name <input type="text" name="recipe_nutrition[' + count + '][nutrition_info_name]" value="" /></p><p>Nutrition Fact Quantity : <input type="text" name="recipe_nutrition[' + count + '][nutrition_info_quantity]" value="" /></p><button class="remove-nutrition">Remove Nutrition Fact</button></div>');
                    return false;
                });
                $(".remove-nutrition").live('click', function () {
                    $(this).parent().remove();
                });
            });
        </script>
    </div> <?php
}

function recipe_instructions_multi_field( $post )
{
    $instructions = get_post_meta( $post->ID, 'recipe_instructions', true );

    ?>
    <div id="instruction_info_wrapper">
        <?php
        $count = 0;
        if ( is_array( $instructions ) ) {
            foreach ( $instructions as $instruction ) {
                $count++;
                if ( isset( $instruction[ 'instruction_name' ] )

                ) {
                    $name   = $instruction[ 'instruction_name' ];
                    $markup = <<<'MARKUP'
        <div class="instruction-entry">
        <p>
            Step %1$s
            <input type="text" name="recipe_instructions[%1$s][instruction_name]" value="%2$s" />
        </p>
        <button class="remove-instruction">%3$s</button>
        </div>
MARKUP;
                    printf(
                        $markup, $count, $name, __( 'Remove Instruction' )
                    );

                }
            }

        }
        ?>
        <span id="add-instruction"></span>
        <button class="add-instruction"><?php _e(
                'Add Instruction'
            ); ?></button>
        <script>
            var $ = jQuery.noConflict();
            $(document).ready(function () {
                var count = <?php echo $count; ?>;
                $(".add-instruction").click(function () {
                    count = count + 1;
                    $('#add-instruction').append('<div class="instruction-entry"><p> Step ' + count + ' <input type="text" name="recipe_instructions[' + count + '][instruction_name]" value="" /></p><button class="remove-instruction">Remove Instruction</button></div>');
                    return false;
                });
                $(".remove-instruction").live('click', function () {
                    $(this).parent().remove();
                });
            });
        </script>
    </div> <?php
}

// Save box content

add_action( 'save_post', 'recipe_save_meta' );

function recipe_save_meta( $post_id )
{
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST[ 'recipe_meta_nonce' ] )
         || !wp_verify_nonce(
            $_POST[ 'recipe_meta_nonce' ], plugin_basename( __FILE__ )
        )
    ) {
        return $post_id;
    }

    $boxes = recipe_metaboxes();

    foreach ( $boxes as $box_id => $contents ) {
        recipe_save_single_field( $post_id, $box_id );
    }


    $multi_boxes = recipe_multi_boxes();
    foreach ( $multi_boxes as $box_id => $contents ) {
        recipe_save_multi_field( $post_id, $box_id );
    }
}

function recipe_save_single_field( $post_id, $field )
{
    $new_meta_value = ( isset( $_POST[ $field ] )
        ? sanitize_text_field( $_POST[ $field ] )
        : '' );


    $meta_key = $field;

    $meta_value = get_post_meta( $post_id, $meta_key, true );

    if ( $new_meta_value && '' == $meta_value ) {
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );
    }

    elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
        update_post_meta( $post_id, $meta_key, $new_meta_value );
    }

    elseif ( '' == $new_meta_value && $meta_value ) {
        delete_post_meta( $post_id, $meta_key, $meta_value );
    }
}

function recipe_save_multi_field( $post_id, $field )
{
    $param = $_POST[ $field ];

    $new_meta_value = $param;

    $meta_key = $field;

    $meta_value = get_post_meta( $post_id, $meta_key, true );

    if ( $new_meta_value && '' == $meta_value ) {
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );
    }

    elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
        update_post_meta( $post_id, $meta_key, $new_meta_value );
    }

    elseif ( '' == $new_meta_value && $meta_value ) {
        delete_post_meta( $post_id, $meta_key, $meta_value );
    }
}


function recipe_metaboxes()
{
    $boxes = array(
        'recipe_duration' => array(
            'title' => 'Duration',
            'callback' => 'recipe_duration',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),
        'recipe_method' => array(
            'title' => 'Cooking Method',
            'callback' => 'recipe_method',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),
        'recipe_prep_time' => array(
            'title' => 'Preparation Time',
            'callback' => 'recipe_prep_time',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),

        'recipe_yield' => array(
            'title' => 'Yield',
            'callback' => 'recipe_yield',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),
        'recipe_total_time' => array(
            'title' => 'Total Time',
            'callback' => 'recipe_total_time',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),

    );

    return $boxes;
}

function recipe_multi_boxes()
{
    $boxes = array(
        'recipe_ingredients' => array(
            'title' => 'Ingredients',
            'callback' => 'recipe_ingredients',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),
        'recipe_nutrition' => array(
            'title' => 'Nutrition Information',
            'callback' => 'recipe_nutrition',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),
        'recipe_instructions' => array(
            'title' => 'Instructions',
            'callback' => 'recipe_instructions',
            'post_type' => 'recipe',
            'context' => 'normal',
            'priority' => 'default'
        ),
    );

    return $boxes;
}