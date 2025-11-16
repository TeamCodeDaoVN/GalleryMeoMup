// GalleryMM - Gallery Album JavaScript
(function() {
    'use strict';
    
    // Đợi DOM load xong
    document.addEventListener('DOMContentLoaded', function() {
        initGalleryMM();
    });
    
    function initGalleryMM() {
        const galleries = document.querySelectorAll('.gmm-gallery-wrapper');
        
        galleries.forEach(function(gallery) {
            const galleryId = gallery.id;
            const lightbox = document.getElementById(galleryId + '-lightbox');
            const lightboxImg = lightbox.querySelector('.gmm-lightbox-content');
            const closeBtn = lightbox.querySelector('.gmm-lightbox-close');
            const prevBtn = lightbox.querySelector('.gmm-lightbox-prev');
            const nextBtn = lightbox.querySelector('.gmm-lightbox-next');
            const currentCounter = lightbox.querySelector('.gmm-current');
            const items = gallery.querySelectorAll('.gmm-gallery-item');
            
            let currentIndex = 0;
            const totalImages = items.length;
            
            // Custom columns và gap từ data attributes
            const columns = gallery.dataset.columns || 3;
            const gap = gallery.dataset.gap || 15;
            const grid = gallery.querySelector('.gmm-gallery-grid');
            grid.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
            grid.style.gap = `${gap}px`;
            
            // Click vào item để mở lightbox
            items.forEach(function(item, index) {
                item.addEventListener('click', function() {
                    openLightbox(index);
                });
            });
            
            // Đóng lightbox
            closeBtn.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });
            
            // Navigation
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                showPrevImage();
            });
            
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                showNextImage();
            });
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (!lightbox.classList.contains('gmm-active')) return;
                
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') showPrevImage();
                if (e.key === 'ArrowRight') showNextImage();
            });
            
            function openLightbox(index) {
                currentIndex = index;
                const imgSrc = items[index].querySelector('img').src;
                lightboxImg.src = imgSrc;
                currentCounter.textContent = index + 1;
                lightbox.classList.add('gmm-active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeLightbox() {
                lightbox.classList.remove('gmm-active');
                document.body.style.overflow = '';
            }
            
            function showPrevImage() {
                currentIndex = (currentIndex - 1 + totalImages) % totalImages;
                updateLightboxImage();
            }
            
            function showNextImage() {
                currentIndex = (currentIndex + 1) % totalImages;
                updateLightboxImage();
            }
            
            function updateLightboxImage() {
                const imgSrc = items[currentIndex].querySelector('img').src;
                lightboxImg.style.opacity = '0';
                
                setTimeout(function() {
                    lightboxImg.src = imgSrc;
                    currentCounter.textContent = currentIndex + 1;
                    lightboxImg.style.opacity = '1';
                }, 150);
            }
        });
    }
})();
