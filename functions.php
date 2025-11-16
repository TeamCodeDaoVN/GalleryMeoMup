<?php
/**
 * Twenty Twenty-Five Child Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue styles và scripts
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');
function twentytwentyfive_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->parent()->get('Version'));
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), wp_get_theme()->get('Version'));
    wp_enqueue_style('gallery-custom-style', get_stylesheet_directory_uri() . '/assets/css/gallery-custom.css', array('child-style'), '1.0.0');
    
    if (is_category(1) || has_category(1) || is_single()) {
        wp_enqueue_script('gallery-custom-script', get_stylesheet_directory_uri() . '/assets/js/gallery-custom.js', array('jquery'), '1.0.0', true);
    }
}

// Custom template cho Category ID = 1
add_filter('template_include', 'custom_gallery_category_template', 99);
function custom_gallery_category_template($template) {
    if (is_category(1)) {
        $new_template = get_stylesheet_directory() . '/templates/category-gallery.html';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}

// Body class
add_filter('body_class', 'add_gallery_body_class');
function add_gallery_body_class($classes) {
    if (is_category(1) || has_category(1)) {
        $classes[] = 'gallery-template';
    }
    return $classes;
}

// Theme setup
add_action('after_setup_theme', 'gallery_theme_setup');
function gallery_theme_setup() {
    add_theme_support('post-thumbnails');
    add_image_size('gallery-thumbnail', 400, 400, true);
}

/**
 * QUAN TRỌNG: Hiển thị posts từ Category ID = 1 + tất cả subcategories
 * Áp dụng cho cả khi xem category cha VÀ subcategories
 */
add_action('pre_get_posts', 'include_subcategory_posts');
function include_subcategory_posts($query) {
    // Chỉ áp dụng cho category page trên frontend
    if (!is_admin() && $query->is_main_query() && $query->is_category()) {
        
        $current_cat = $query->get_queried_object();
        
        // Kiểm tra xem có phải category ID = 1 HOẶC subcategory của nó không
        $is_gallery_category = false;
        
        if ($current_cat->term_id == 1) {
            // Đây là category cha (Gallery)
            $is_gallery_category = true;
        } else {
            // Kiểm tra xem có phải subcategory của Gallery không
            $parent_cats = get_ancestors($current_cat->term_id, 'category');
            if (in_array(1, $parent_cats)) {
                $is_gallery_category = true;
            }
        }
        
        // Nếu là Gallery hoặc subcategory của Gallery
        if ($is_gallery_category) {
            // Nếu là category cha (ID = 1), hiển thị tất cả posts từ cha + con
            if ($current_cat->term_id == 1) {
                $subcategories = get_categories(array(
                    'child_of' => 1,
                    'hide_empty' => false,
                ));
                
                $cat_ids = array(1);
                foreach ($subcategories as $subcat) {
                    $cat_ids[] = $subcat->term_id;
                }
                
                $query->set('cat', implode(',', $cat_ids));
            }
            // Nếu là subcategory, WordPress tự động hiển thị posts của nó
            
            $query->set('posts_per_page', 12);
        }
    }
}

/**
 * Default featured image
 */
add_filter('post_thumbnail_html', 'default_featured_image', 20, 5);
function default_featured_image($html, $post_id, $post_thumbnail_id, $size, $attr) {
    if (empty($html)) {
        $default_image_url = get_stylesheet_directory_uri() . '/assets/images/default-thumbnail.jpg';
        
        if (!file_exists(get_stylesheet_directory() . '/assets/images/default-thumbnail.jpg')) {
            $default_image_url = 'https://placehold.co/800x800/667eea/ffffff?text=Gallery';
        }
        
        $alt = get_the_title($post_id) ?: 'Default Image';
        
        $html = sprintf(
            '<img src="%s" alt="%s" class="wp-post-image" style="aspect-ratio:1;object-fit:cover;width:100%%;height:auto;display:block;border-radius:12px 12px 0 0;" />',
            esc_url($default_image_url),
            esc_attr($alt)
        );
    }
    
    return $html;
}

/**
 * Lấy subcategory name
 */
function get_post_subcategory_name($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = get_the_category($post_id);
    
    foreach ($categories as $cat) {
        if ($cat->term_id != 1 && $cat->slug != 'gallery') {
            return $cat->name;
        }
    }
    
    return '';
}

/**
 * Include GalleryMM Shortcode
 */
require_once get_stylesheet_directory() . '/includes/gallerymm-shortcode.php';
