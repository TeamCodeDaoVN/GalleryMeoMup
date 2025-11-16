(function ($) {
    'use strict';

    // Simple Lightbox for Gallery
    const GalleryLightbox = {
        init: function () {
            this.createLightbox();
            this.bindEvents();
        },

        createLightbox: function () {
            if ($('#gallery-lightbox').length) return;
            
            $('body').append(`
                <div id="gallery-lightbox" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,0.95);">
                    <button class="lightbox-close" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.3);color:#fff;font-size:2rem;width:50px;height:50px;border-radius:50%;cursor:pointer;">&times;</button>
                    <button class="lightbox-prev" style="position:absolute;left:20px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.3);color:#fff;font-size:2rem;width:50px;height:50px;border-radius:50%;cursor:pointer;">&lsaquo;</button>
                    <button class="lightbox-next" style="position:absolute;right:20px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.3);color:#fff;font-size:2rem;width:50px;height:50px;border-radius:50%;cursor:pointer;">&rsaquo;</button>
                    <img src="" alt="" class="lightbox-image" style="max-width:90%;max-height:90%;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);border-radius:8px;">
                </div>
            `);
        },

        bindEvents: function () {
            let images = [];
            let currentIndex = 0;

            $(document).on('click', '.custom-gallery-grid a', function (e) {
                e.preventDefault();
                images = [];
                $(this).closest('.custom-gallery-grid').find('a').each(function () {
                    images.push($(this).attr('href'));
                });
                currentIndex = images.indexOf($(this).attr('href'));
                $('.lightbox-image').attr('src', images[currentIndex]);
                $('#gallery-lightbox').fadeIn(300);
                $('body').css('overflow', 'hidden');
            });

            $(document).on('click', '.lightbox-close, #gallery-lightbox', function () {
                $('#gallery-lightbox').fadeOut(300);
                $('body').css('overflow', '');
            });

            $(document).on('click', '.lightbox-prev', function (e) {
                e.stopPropagation();
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                $('.lightbox-image').attr('src', images[currentIndex]);
            });

            $(document).on('click', '.lightbox-next', function (e) {
                e.stopPropagation();
                currentIndex = (currentIndex + 1) % images.length;
                $('.lightbox-image').attr('src', images[currentIndex]);
            });

            $(document).on('keydown', function (e) {
                if ($('#gallery-lightbox').is(':visible')) {
                    if (e.key === 'Escape') $('.lightbox-close').click();
                    if (e.key === 'ArrowLeft') $('.lightbox-prev').click();
                    if (e.key === 'ArrowRight') $('.lightbox-next').click();
                }
            });
        }
    };

    $(document).ready(function () {
        GalleryLightbox.init();
    });

})(jQuery);
