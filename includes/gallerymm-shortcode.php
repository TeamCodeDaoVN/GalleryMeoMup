<?php
/**
 * GalleryMM Shortcode
 * Usage:
 * [GalleryMM]
 * https://example.com/image1.jpg
 * https://example.com/image2.jpg
 * [/GalleryMM]
 */

if (!defined('ABSPATH')) exit;

add_shortcode('GalleryMM', function($atts, $content = null) {
    if (empty($content)) {
        return '<p class="gallery-error">⚠️ <strong>GalleryMM:</strong> Vui lòng thêm link ảnh!</p>';
    }
    
    // Parse & validate URLs
    $lines = array_filter(array_map('trim', explode("\n", strip_tags($content))));
    $gallery_items = [];
    $index = 0;
    
    foreach ($lines as $line) {
        if (!filter_var($line, FILTER_VALIDATE_URL)) continue;
        
        $index++;
        $gallery_items[] = sprintf(
            '<div class="gallery-item"><a href="%s"><img src="%s" alt="Gallery Image %d" loading="lazy"></a></div>',
            esc_url($line),
            esc_url($line),
            $index
        );
    }
    
    if (empty($gallery_items)) {
        return '<p class="gallery-error">⚠️ <strong>GalleryMM:</strong> Không có URL hợp lệ!</p>';
    }
    
    return '<div class="custom-gallery-grid">' . implode('', $gallery_items) . '</div>';
});
