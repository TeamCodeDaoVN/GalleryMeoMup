/**
 * GalleryMM Lightbox
 * Version: 1.0.0
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Lightbox handler for GalleryMM
    $(document).on('click', '.gallerymm-link', function(e) {
        e.preventDefault();
        
        var $clicked = $(this);
        var imgSrc = $clicked.attr('href');
        var currentIndex = parseInt($clicked.data('index'));
        var $allImages = $('.gallerymm-link');
        
        // Create lightbox elements
        var $lightbox = $('<div class="gallerymm-lightbox"></div>');
        var $img = $('<img src="' + imgSrc + '" alt="Gallery Image" />');
        var $close = $('<span class="gallerymm-close" title="Đóng (ESC)">&times;</span>');
        var $prev = $('<span class="gallerymm-nav gallerymm-prev" title="Ảnh trước (←)">&#10094;</span>');
        var $next = $('<span class="gallerymm-nav gallerymm-next" title="Ảnh sau (→)">&#10095;</span>');
        var $counter = $('<div class="gallerymm-counter">' + (currentIndex + 1) + ' / ' + $allImages.length + '</div>');
        
        // Append elements
        $lightbox.append($close, $prev, $img, $next, $counter);
        $('body').append($lightbox).addClass('gallerymm-active');
        
        // Show lightbox with animation
        setTimeout(function() {
            $lightbox.addClass('active');
        }, 10);
        
        // Update image function
        function updateImage(index) {
            currentIndex = index;
            var newSrc = $allImages.eq(index).attr('href');
            
            $img.fadeOut(200, function() {
                $(this).attr('src', newSrc).fadeIn(200);
            });
            
            $counter.text((index + 1) + ' / ' + $allImages.length);
        }
        
        // Close lightbox function
        function closeLightbox() {
            $lightbox.removeClass('active');
            setTimeout(function() {
                $lightbox.remove();
                $('body').removeClass('gallerymm-active');
                $(document).off('keydown.gallerymm');
            }, 300);
        }
        
        // Event: Close button
        $close.on('click', closeLightbox);
        
        // Event: Click outside image
        $lightbox.on('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Event: Next button
        $next.on('click', function() {
            var nextIndex = (currentIndex + 1) % $allImages.length;
            updateImage(nextIndex);
        });
        
        // Event: Previous button
        $prev.on('click', function() {
            var prevIndex = (currentIndex - 1 + $allImages.length) % $allImages.length;
            updateImage(prevIndex);
        });
        
        // Keyboard navigation
        $(document).on('keydown.gallerymm', function(e) {
            switch(e.keyCode) {
                case 37: // Left arrow
                    $prev.click();
                    break;
                case 39: // Right arrow
                    $next.click();
                    break;
                case 27: // ESC
                    closeLightbox();
                    break;
            }
        });
    });
});
