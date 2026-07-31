(function () {
    const carousels = document.querySelectorAll('[data-carousel]');
    if (carousels.length === 0) {
        return;
    }

    const initCarousel = function (carousel) {
        const track = carousel.querySelector('.carousel-track');
        const slides = track ? Array.from(track.children) : [];
        const prevButton = carousel.querySelector('[data-carousel-prev]');
        const nextButton = carousel.querySelector('[data-carousel-next]');
        const dotsContainer = carousel.querySelector('[data-carousel-dots]');
        const autoplay = carousel.getAttribute('data-autoplay') === 'true';
        const interval = parseInt(carousel.getAttribute('data-interval') || '0', 10);

        if (!track || slides.length === 0) {
            return;
        }

        let index = 0;
        let perView = 1;
        let maxIndex = 0;
        let timer = null;
        let dots = [];

        const getPerView = function () {
            const mobile = parseInt(carousel.getAttribute('data-per-view-mobile') || '1', 10);
            const tablet = parseInt(carousel.getAttribute('data-per-view-tablet') || String(mobile), 10);
            const desktop = parseInt(carousel.getAttribute('data-per-view-desktop') || String(tablet), 10);

            if (window.innerWidth <= 620) {
                return mobile;
            }

            if (window.innerWidth <= 1060) {
                return tablet;
            }

            return desktop;
        };

        const updateControls = function () {
            if (!prevButton || !nextButton) {
                return;
            }

            const disable = maxIndex === 0;
            prevButton.disabled = disable;
            nextButton.disabled = disable;
        };

        const updateDots = function () {
            dots.forEach(function (dot, dotIndex) {
                const active = dotIndex === index;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-current', active ? 'true' : 'false');
            });
        };

        const update = function () {
            perView = Math.max(1, getPerView());
            maxIndex = Math.max(0, slides.length - perView);
            index = Math.min(index, maxIndex);

            track.style.setProperty('--per-view', String(perView));
            track.style.transform = 'translateX(-' + ((100 / perView) * index) + '%)';

            updateControls();
            updateDots();
        };

        const rebuildDots = function () {
            if (!dotsContainer) {
                return;
            }

            dotsContainer.innerHTML = '';
            dots = [];

            const pages = Math.max(1, maxIndex + 1);
            for (let page = 0; page < pages; page += 1) {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'carousel-dot';
                dot.setAttribute('aria-label', 'Ir para o slide ' + (page + 1));
                dot.addEventListener('click', function () {
                    index = page;
                    update();
                    restartAutoplay();
                });

                dotsContainer.appendChild(dot);
                dots.push(dot);
            }
        };

        const next = function () {
            if (maxIndex === 0) {
                return;
            }

            index = index >= maxIndex ? 0 : index + 1;
            update();
        };

        const prev = function () {
            if (maxIndex === 0) {
                return;
            }

            index = index <= 0 ? maxIndex : index - 1;
            update();
        };

        const stopAutoplay = function () {
            if (timer !== null) {
                window.clearInterval(timer);
                timer = null;
            }
        };

        const startAutoplay = function () {
            if (!autoplay || interval < 1500 || maxIndex === 0) {
                return;
            }

            stopAutoplay();
            timer = window.setInterval(next, interval);
        };

        const restartAutoplay = function () {
            stopAutoplay();
            startAutoplay();
        };

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                prev();
                restartAutoplay();
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                next();
                restartAutoplay();
            });
        }

        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
        carousel.addEventListener('focusin', stopAutoplay);
        carousel.addEventListener('focusout', startAutoplay);

        window.addEventListener('resize', function () {
            const oldPerView = perView;
            const newPerView = getPerView();
            if (oldPerView !== newPerView) {
                update();
                rebuildDots();
                update();
                restartAutoplay();
            }
        });

        update();
        rebuildDots();
        update();
        startAutoplay();
    };

    carousels.forEach(initCarousel);


    // onclick event when clicking on a card to redirect to the pet's page
    const cards = document.querySelectorAll('.card[data-pet-id]');
    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            // trigger the click event on the card's link tag
            const link = card.querySelector('a.link');
            if (link) {
                link.click();
            }
        });
    });
})();
