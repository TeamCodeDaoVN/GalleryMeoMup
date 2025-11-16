<?php
/**
 * Twenty Twenty-Five Child Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function twentytwentyfive_child_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style(
        'twentytwentyfive-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );
    
    // Child theme style
    wp_enqueue_style(
        'twentytwentyfive-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('twentytwentyfive-style'),
        wp_get_theme()->get('Version')
    );
    
    // Category flat layout styles và scripts CHỈ load khi xem category ID = 1
    if (is_category(1)) {
        wp_enqueue_style(
            'category-flat-style',
            get_stylesheet_directory_uri() . '/assets/css/category-flat.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'category-flat-script',
            get_stylesheet_directory_uri() . '/assets/js/category-flat.js',
            array(),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');

/**
 * Modify query để hiển thị CẢ sub-categories khi view category ID = 1
 */
function twentytwentyfive_child_include_subcategories($query) {
    // Chỉ áp dụng cho main query, category 1, và không phải admin
    if (!is_admin() && $query->is_main_query() && $query->is_category(1)) {
        // Lấy tất cả sub-categories của category ID = 1
        $child_cats = get_term_children(1, 'category');
        
        // Thêm parent category vào mảng
        $all_cats = array_merge(array(1), $child_cats);
        
        // Set lại query để lấy posts từ tất cả categories
        $query->set('cat', implode(',', $all_cats));
        
        // Hiển thị tất cả posts (không phân trang)
        $query->set('posts_per_page', -1);
    }
}
add_action('pre_get_posts', 'twentytwentyfive_child_include_subcategories');
