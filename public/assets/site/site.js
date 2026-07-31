(function () {
    const menuButton = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.main-nav');

    if (menuButton && nav) {
        menuButton.addEventListener('click', function () {
            const isOpen = nav.classList.toggle('is-open');
            menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    const counters = document.querySelectorAll('[data-count]');
    if (counters.length === 0) {
        return;
    }

    const animateCounter = function (el) {
        const target = parseInt(el.getAttribute('data-count') || '0', 10);
        const duration = 1400;
        const start = performance.now();

        const tick = function (now) {
            const progress = Math.min((now - start) / duration, 1);
            const current = Math.floor(target * progress);
            el.textContent = '+' + current.toLocaleString('pt-BR');

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };

        requestAnimationFrame(tick);
    };

    const observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                animateCounter(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.45 }
    );

    counters.forEach(function (counter) {
        observer.observe(counter);
    });
})();
