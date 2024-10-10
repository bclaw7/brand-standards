<?php
/**
 * Custom functions for Brand Standards
 */

if (!function_exists('custom_brand_standard_navigation')) {
    function custom_brand_standard_navigation() {
        $args = array(
            'post_type' => 'brand_standard',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'fields' => 'ids'
        );
        $brand_standard_ids = get_posts($args);
        $current_key = array_search(get_the_ID(), $brand_standard_ids);
        
        $prev_id = isset($brand_standard_ids[$current_key - 1]) ? $brand_standard_ids[$current_key - 1] : false;
        $next_id = isset($brand_standard_ids[$current_key + 1]) ? $brand_standard_ids[$current_key + 1] : false;
        
        echo '<div class="custom-navigation">';
        if ($prev_id) {
            $prev_nav_title = get_post_meta($prev_id, '_nav_title', true);
            $prev_display_title = !empty($prev_nav_title) ? $prev_nav_title : get_the_title($prev_id);
            echo '<a href="' . get_permalink($prev_id) . '" class="prev-link">&larr; ' . esc_html($prev_display_title) . '</a>';
        }
        if ($next_id) {
            $next_nav_title = get_post_meta($next_id, '_nav_title', true);
            $next_display_title = !empty($next_nav_title) ? $next_nav_title : get_the_title($next_id);
            echo '<a href="' . get_permalink($next_id) . '" class="next-link">' . esc_html($next_display_title) . ' &rarr;</a>';
        }
        echo '</div>';
    }
}