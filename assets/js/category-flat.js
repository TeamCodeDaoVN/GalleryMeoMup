/**
 * Category Flat Layout JavaScript
 * Ẩn parent category, chỉ hiện sub-category
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // Ẩn parent category (ID = 1), chỉ hiện sub-category
        hideParentCategory();

        // Lazy loading cho images
        lazyLoadImages();

    });

    /**
     * Ẩn parent category "Gallery", chỉ hiển thị sub-category
     */
    function hideParentCategory() {
        const categoryBadges = document.querySelectorAll('.flat-category-badge .wp-block-post-terms, .flat-category-badge .sub-category-only');

        categoryBadges.forEach(function (badge) {
            const links = badge.querySelectorAll('a');

            // Nếu có nhiều hơn 1 category
            if (links.length > 1) {
                // Ẩn tất cả category có tên "Gallery" (parent category)
                links.forEach(function (link) {
                    const categoryName = link.textContent.trim().toLowerCase();
                    if (categoryName === 'gallery') {
                        link.style.display = 'none';
                    }
                });
            }
            // Nếu chỉ có 1 category và đó là "Gallery"
            else if (links.length === 1) {
                const categoryName = links[0].textContent.trim().toLowerCase();
                if (categoryName === 'gallery') {
                    // Ẩn toàn bộ badge
                    badge.style.display = 'none';
                    const badgeParent = badge.closest('.flat-category-badge');
                    if (badgeParent) {
                        badgeParent.style.display = 'none';
                    }
                }
            }
        });
    }

    /**
     * Lazy loading cho images
     */
    function lazyLoadImages() {
        if ('IntersectionObserver' in window) {
            const images = document.querySelectorAll('.flat-featured-cover img');

            const imageObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });

            images.forEach(function (img) {
                imageObserver.observe(img);
            });
        }
    }

})();
