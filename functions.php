<?php
/**
 * Twenty Twenty Five Child Theme Functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function twentytwentyfive_child_enqueue_styles() {
    // Enqueue parent theme style
    wp_enqueue_style(
        'twentytwentyfive-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );
    
    // Enqueue child theme style
    wp_enqueue_style(
        'twentytwentyfive-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('twentytwentyfive-parent-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue custom category grid CSS
    if (is_category(1)) {
        wp_enqueue_style(
            'category-grid-style',
            get_stylesheet_directory_uri() . '/assets/css/category-grid.css',
            array(),
            '1.0.0'
        );
        
        // Enqueue custom JavaScript for hover effects
        wp_enqueue_script(
            'category-hover-script',
            get_stylesheet_directory_uri() . '/assets/js/category-hover.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');

/**
 * Get all posts from category including subcategories
 */
function get_posts_with_subcategories($category_id) {
    // Get all child categories
    $child_categories = get_term_children($category_id, 'category');
    $all_categories = array_merge(array($category_id), $child_categories);
    
    return $all_categories;
}

/**
 * Custom query for category 1 with subcategories
 */
function modify_category_query($query) {
    if (!is_admin() && $query->is_main_query() && is_category(1)) {
        $category_ids = get_posts_with_subcategories(1);
        $query->set('cat', implode(',', $category_ids));
        $query->set('posts_per_page', 12);
    }
}
add_action('pre_get_posts', 'modify_category_query');
