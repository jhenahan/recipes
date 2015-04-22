<?php

add_action( 'widgets_init', 'init_recent_recipes' );
function init_recent_recipes()
{
    return register_widget( 'recent_recipes' );
}

class recent_recipes extends WP_Widget
{
    function recent_recipes()
    {
        parent::WP_Widget( 'recent_recipes', $name = 'Recent Recipes' );
    }

    function update( $new_instance, $old_instance )
    {
        $instance                      = $old_instance;
        $instance[ 'title' ]           = strip_tags( $new_instance[ 'title' ] );
        $instance[ 'numberOfRecipes' ] =
            strip_tags( $new_instance[ 'numberOfRecipes' ] );
        return $instance;
    }

    function form( $instance )
    {
        if ( $instance ) {
            $title            = esc_attr( $instance[ 'title' ] );
            $numberOfRecipes = esc_attr( $instance[ 'numberOfRecipes' ] );
        }
        else {
            $title            = '';
            $numberOfRecipes = '';
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e(
                    'Title', 'recent_recipes'
                ); ?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>"
                   type="text" value="<?php echo $title; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id(
                'numberOfRecipes'
            ); ?>"><?php _e(
                    'Number of Recipes:', 'recent_recipes'
                ); ?></label>
            <select
                id="<?php echo $this->get_field_id( 'numberOfRecipes' ); ?>"
                name="<?php echo $this->get_field_name(
                    'numberOfRecipes'
                ); ?>">
                <?php for ( $x = 1; $x <= 10; $x++ ): ?>
                    <option <?php echo $x == $numberOfRecipes
                        ? 'selected="selected"'
                        : ''; ?>
                        value="<?php echo $x; ?>"><?php echo $x; ?></option>
                <?php endfor; ?>
            </select>
        </p>
    <?php
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $numberOfRecipes = $instance['numberOfRecipes'];
        echo $before_widget;
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
        $this->getRecipes($numberOfRecipes);
        echo $after_widget;
    }

    function getRecipes($numberOfRecipes) { //html
        global $post;
        $recipes = new WP_Query();
        $recipes->query('post_type=recipe&posts_per_page=' . $numberOfRecipes );
        if($recipes->found_posts > 0) {
            echo '<ul class="recipe_widget">';
            while ($recipes->have_posts()) {
                $recipes->the_post();
                $listItem = '<li>';
                $listItem .= '<a href="' . get_permalink() . '">';
                $listItem .= get_the_title() . '</a>';
                $listItem .= '</li>';
                echo $listItem;
            }
            echo '</ul>';
            wp_reset_postdata();
        }else{
            echo '<p style="padding:25px;">No listing found</p>';
        }
    }
}