/**
 * Category Grid Hover Effects
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        // Lazy loading animation for cards
        function animateCards() {
            const cards = $('.category-post-card');

            cards.each(function (index) {
                const card = $(this);
                setTimeout(function () {
                    card.css({
                        'opacity': '0',
                        'transform': 'translateY(30px)'
                    }).animate({
                        'opacity': 1
                    }, 600, function () {
                        card.css('transform', 'translateY(0)');
                    });
                }, index * 100);
            });
        }

        // Initialize animations on page load
        if ($('.category-grid-query').length) {
            animateCards();
        }

        // Add parallax effect to featured images
        $('.category-post-card').on('mousemove', function (e) {
            const card = $(this);
            const image = card.find('.wp-block-post-featured-image img');

            if (image.length) {
                const rect = card[0].getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;

                image.css({
                    'transform': `scale(1.08) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`
                });
            }
        });

        $('.category-post-card').on('mouseleave', function () {
            const image = $(this).find('.wp-block-post-featured-image img');
            image.css({
                'transform': 'scale(1) rotateX(0) rotateY(0)'
            });
        });

        // Add smooth scroll for pagination
        $('.wp-block-query-pagination a').on('click', function (e) {
            if ($(this).attr('href').indexOf('#') === -1) {
                $('html, body').animate({
                    scrollTop: $('.category-grid-query').offset().top - 100
                }, 600);
            }
        });

        // Add read progress indicator for cards
        $('.category-post-card').each(function () {
            const card = $(this);
            const link = card.find('.wp-block-post-title a');

            if (link.length) {
                link.on('click', function () {
                    card.addClass('visited');
                    localStorage.setItem('visited_' + link.attr('href'), 'true');
                });

                // Check if already visited
                if (localStorage.getItem('visited_' + link.attr('href'))) {
                    card.addClass('visited');
                }
            }
        });

        // Infinite scroll option (commented out by default)
        /*
        let page = 2;
        let loading = false;
        
        $(window).on('scroll', function() {
            if (loading) return;
            
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            
            if (scrollTop + windowHeight > documentHeight - 500) {
                loading = true;
                loadMorePosts();
            }
        });
        
        function loadMorePosts() {
            // Implement AJAX loading here
            console.log('Loading page: ' + page);
            page++;
            loading = false;
        }
        */

    });

})(jQuery);
