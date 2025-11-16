<?php
/**
 * GalleryMM Shortcode
 * 
 * Cách dùng - Chỉ cần paste link ảnh:
 * [GalleryMM]
 * https://example.com/image1.jpg
 * https://example.com/image2.jpg
 * https://example.com/image3.jpg
 * [/GalleryMM]
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode
add_shortcode('GalleryMM', 'gallerymm_shortcode_handler');

/**
 * GalleryMM shortcode handler
 */
function gallerymm_shortcode_handler($atts, $content = null) {
    // Kiểm tra content trống
    if (empty($content)) {
        return '<p style="color:#dc2626;padding:1rem;background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;margin:1rem 0;">⚠️ <strong>GalleryMM:</strong> Vui lòng thêm link ảnh vào shortcode!</p>';
    }
    
    // Parse image URLs - loại bỏ dòng trống
    $lines = array_filter(array_map('trim', explode("\n", strip_tags($content))));
    
    if (empty($lines)) {
        return '<p style="color:#dc2626;padding:1rem;background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;margin:1rem 0;">⚠️ <strong>GalleryMM:</strong> Không tìm thấy link ảnh!</p>';
    }
    
    // Validate và build gallery
    $gallery_items = array();
    $index = 0;
    
    foreach ($lines as $line) {
        // Skip dòng không phải URL
        if (!filter_var($line, FILTER_VALIDATE_URL)) {
            continue;
        }
        
        $index++;
        $alt_text = 'Gallery Image ' . $index;
        
        // Dùng CÙNG URL cho cả thumbnail và full image
        $gallery_items[] = sprintf(
            '<div class="gallery-item">
                <a href="%s">
                    <img src="%s" alt="%s">
                </a>
            </div>',
            esc_url($line),
            esc_url($line),
            esc_attr($alt_text)
        );
    }
    
    // Nếu không có URL hợp lệ nào
    if (empty($gallery_items)) {
        return '<p style="color:#dc2626;padding:1rem;background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;margin:1rem 0;">⚠️ <strong>GalleryMM:</strong> Không có URL ảnh hợp lệ! Vui lòng kiểm tra lại định dạng link.</p>';
    }
    
    // Return gallery HTML với class custom-gallery-grid
    $output = '<div class="custom-gallery-grid">';
    $output .= implode('', $gallery_items);
    $output .= '</div>';
    
    return $output;
}
