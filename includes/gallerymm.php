<?php
if (!function_exists('gallerymm_shortcode')) {
    function gallerymm_shortcode($atts, $content = null) {
        $atts = shortcode_atts([
            'columns' => '5',
            'gap' => '15',
        ], $atts);

        // Chuẩn hóa line break và loại thẻ HTML
        $raw = str_replace(['<br>', '<br/>', '<br />', '</p>', '<p>'], ["\n", "\n", "\n", "\n", ""], $content);
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);
        $lines = array_filter(array_map('trim', explode("\n", strip_tags($raw))));
        $urls = array_filter($lines, fn($url) => filter_var($url, FILTER_VALIDATE_URL));

        if (!$urls) return '<p>Vui lòng nhập link ảnh hợp lệ, mỗi link trên một dòng.</p>';

        static $gid = 0;
        $gid++;
        $gallery_id = 'gmm-gallery-' . $gid;

        ob_start();
        ?>
        <style>
        /* Inline CSS GalleryMM */
        #<?php echo $gallery_id; ?> {
            --gmm-columns: <?php echo esc_attr($atts['columns']); ?>;
            --gmm-gap: <?php echo esc_attr($atts['gap']); ?>px;
            width: 100%;
            margin: 30px 0;
            position: relative;
        }
        #<?php echo $gallery_id; ?> .gmm-gallery-grid {
            display: grid;
            gap: var(--gmm-gap);
            grid-template-columns: repeat(var(--gmm-columns), 1fr);
        }
        #<?php echo $gallery_id; ?> .gmm-gallery-item {
            overflow: hidden;
            border-radius: 6px;
            aspect-ratio: 1 / 1;
            cursor: pointer;
            background-color: #f0f0f0;
        }
        #<?php echo $gallery_id; ?> .gmm-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease, filter 0.3s ease;
        }
        #<?php echo $gallery_id; ?> .gmm-gallery-item:hover img {
            transform: scale(1.08);
            filter: brightness(0.82);
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            align-items: center;
            justify-content: center;
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox.gmm-active {
            display: flex;
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-content {
            max-width: 92vw;
            max-height: 90vh;
            object-fit: contain;
            animation: gmmZoomIn 0.3s ease;
        }
        @keyframes gmmZoomIn {
            from {
                transform: scale(0.88);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-close,
        #<?php echo $gallery_id; ?> .gmm-lightbox-prev,
        #<?php echo $gallery_id; ?> .gmm-lightbox-next {
            position: absolute;
            color: white;
            font-size: 2.2rem;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            padding: 10px;
            background: rgba(0, 0, 0, 0.45);
            border-radius: 5px;
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-close {
            top: 14px;
            right: 26px;
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-prev {
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-next {
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-close:hover,
        #<?php echo $gallery_id; ?> .gmm-lightbox-prev:hover,
        #<?php echo $gallery_id; ?> .gmm-lightbox-next:hover {
            color: #ffe066;
        }
        #<?php echo $gallery_id; ?> .gmm-lightbox-counter {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 1.1rem;
            background: rgba(0, 0, 0, 0.7);
            padding: 7px 15px;
            border-radius: 18px;
        }
        @media (max-width: 768px) {
            #<?php echo $gallery_id; ?> .gmm-gallery-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            #<?php echo $gallery_id; ?> .gmm-lightbox-close {
                font-size: 1.7rem;
                top: 8px;
                right: 8px;
            }
        }
        @media (max-width: 480px) {
            #<?php echo $gallery_id; ?> .gmm-gallery-grid {
                grid-template-columns: 1fr !important;
            }
        }
        </style>

        <div class="gmm-gallery-wrapper" id="<?php echo $gallery_id; ?>" data-columns="<?php echo esc_attr($atts['columns']); ?>" data-gap="<?php echo esc_attr($atts['gap']); ?>">
            <div class="gmm-gallery-grid">
                <?php foreach ($urls as $i => $url): ?>
                    <div class="gmm-gallery-item" data-index="<?php echo $i; ?>">
                        <img src="<?php echo esc_url($url); ?>" alt="Gallery Image <?php echo ($i+1); ?>" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="gmm-lightbox" id="<?php echo $gallery_id; ?>-lightbox">
                <span class="gmm-lightbox-close">&times;</span>
                <span class="gmm-lightbox-prev">&#10094;</span>
                <span class="gmm-lightbox-next">&#10095;</span>
                <img class="gmm-lightbox-content" src="" alt="">
                <div class="gmm-lightbox-counter">
                    <span class="gmm-current">1</span> / <span class="gmm-total"><?php echo count($urls); ?></span>
                </div>
            </div>
        </div>

        <script>
        (function(){
            'use strict';
            var gallery = document.getElementById('<?php echo $gallery_id; ?>');
            var grid = gallery.querySelector('.gmm-gallery-grid');
            var columns = gallery.dataset.columns || 5;
            var gap = gallery.dataset.gap || 15;

            grid.style.setProperty('--gmm-columns', columns);
            grid.style.setProperty('--gmm-gap', gap + 'px');

            var items = gallery.querySelectorAll('.gmm-gallery-item');
            var lightbox = document.getElementById('<?php echo $gallery_id; ?>-lightbox');
            var lightboxImg = lightbox.querySelector('.gmm-lightbox-content');
            var closeBtn = lightbox.querySelector('.gmm-lightbox-close');
            var prevBtn = lightbox.querySelector('.gmm-lightbox-prev');
            var nextBtn = lightbox.querySelector('.gmm-lightbox-next');
            var currentCounter = lightbox.querySelector('.gmm-current');
            var total = items.length;
            var currentIndex = 0;

            function openLightbox(index) {
                currentIndex = index;
                lightboxImg.src = items[index].querySelector('img').src;
                currentCounter.textContent = index + 1;
                lightbox.classList.add('gmm-active');
                document.body.style.overflow = 'hidden';
            }
            function closeLightbox() {
                lightbox.classList.remove('gmm-active');
                document.body.style.overflow = '';
            }
            function showPrevImage() {
                currentIndex = (currentIndex - 1 + total) % total;
                updateLightboxImage();
            }
            function showNextImage() {
                currentIndex = (currentIndex + 1) % total;
                updateLightboxImage();
            }
            function updateLightboxImage() {
                lightboxImg.style.opacity = '0';
                setTimeout(function(){
                    lightboxImg.src = items[currentIndex].querySelector('img').src;
                    currentCounter.textContent = currentIndex + 1;
                    lightboxImg.style.opacity = '1';
                }, 150);
            }

            items.forEach(function(item, idx){
                item.onclick = function(){
                    openLightbox(idx);
                };
            });

            closeBtn.onclick = closeLightbox;
            prevBtn.onclick = function(e){ e.stopPropagation(); showPrevImage(); };
            nextBtn.onclick = function(e){ e.stopPropagation(); showNextImage(); };
            lightbox.onclick = function(e){
                if(e.target === lightbox) {
                    closeLightbox();
                }
            };
            document.addEventListener('keydown', function(e){
                if(!lightbox.classList.contains('gmm-active')) return;
                if(e.key === 'Escape') closeLightbox();
                if(e.key === 'ArrowLeft') showPrevImage();
                if(e.key === 'ArrowRight') showNextImage();
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    add_shortcode('GalleryMM', 'gallerymm_shortcode');
}
?>
