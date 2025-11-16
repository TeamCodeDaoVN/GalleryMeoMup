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

/**
 * GalleryMM Shortcode - Album ảnh từ nhiều link
 */
function gallerymm_shortcode($atts, $content = null) {
    // Parse attributes
    $atts = shortcode_atts(array(
        'columns' => '3',
        'gap' => '15',
    ), $atts);
    
    // Lấy danh sách URLs từ content
    $urls = array_filter(array_map('trim', explode("\n", $content)));
    
    if (empty($urls)) {
        return '<p>Vui lòng nhập link ảnh.</p>';
    }
    
    // Generate unique ID cho gallery
    static $gallery_id = 0;
    $gallery_id++;
    $unique_id = 'gmm-gallery-' . $gallery_id;
    
    // Build HTML
    $output = '<div class="gmm-gallery-wrapper" id="' . esc_attr($unique_id) . '" data-columns="' . esc_attr($atts['columns']) . '" data-gap="' . esc_attr($atts['gap']) . '">';
    $output .= '<div class="gmm-gallery-grid">';
    
    foreach ($urls as $index => $url) {
        $url = esc_url($url);
        $output .= '<div class="gmm-gallery-item" data-index="' . $index . '">';
        $output .= '<img src="' . $url . '" alt="Gallery Image ' . ($index + 1) . '" loading="lazy">';
        $output .= '</div>';
    }
    
    $output .= '</div>'; // .gmm-gallery-grid
    $output .= '</div>'; // .gmm-gallery-wrapper
    
    // Lightbox HTML
    $output .= '<div class="gmm-lightbox" id="' . esc_attr($unique_id) . '-lightbox">';
    $output .= '<span class="gmm-lightbox-close">&times;</span>';
    $output .= '<span class="gmm-lightbox-prev">&#10094;</span>';
    $output .= '<span class="gmm-lightbox-next">&#10095;</span>';
    $output .= '<img class="gmm-lightbox-content" src="" alt="">';
    $output .= '<div class="gmm-lightbox-counter"><span class="gmm-current">1</span> / <span class="gmm-total">' . count($urls) . '</span></div>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('GalleryMM', 'gallerymm_shortcode');

/**
 * Enqueue GalleryMM assets
 */
function gallerymm_enqueue_assets() {
    // Chỉ load khi có shortcode [GalleryMM]
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'GalleryMM')) {
        wp_enqueue_style(
            'gallerymm-style',
            get_stylesheet_directory_uri() . '/assets/css/gallerymm.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'gallerymm-script',
            get_stylesheet_directory_uri() . '/assets/js/gallerymm.js',
            array(),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'gallerymm_enqueue_assets');

require_once get_stylesheet_directory() . '/includes/gallerymm.php';

