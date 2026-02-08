/**
 * Premium Home Page Interactive Scripts
 * Handling Counters, Netflix Scrolls, and Glass Reveal Effects
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. Premium Counter Logic
    function initCounters() {
        const counters = document.querySelectorAll('.stat-counter, .count');

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseFloat(el.dataset.target);
                    animateValue(el, 0, target, 2000);
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = progress * (end - start) + start;

            // Format for decimals if needed (ratings)
            if (end % 1 !== 0) {
                obj.innerHTML = value.toFixed(1);
            } else {
                obj.innerHTML = Math.floor(value).toLocaleString();
            }

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                // Final value ensure plus sign if originally there
                if (end >= 1000) obj.innerHTML += "+";
            }
        };
        window.requestAnimationFrame(step);
    }

    // 2. Scroll-Reveal Stagger Enhancement
    function initReveal() {
        const revealElements = document.querySelectorAll('[data-aos]');
        // AOS is already included in footer, we just ensure it's refreshed
        if (typeof AOS !== 'undefined') {
            AOS.refresh();
        }
    }

    // 3. Netflix Row - Mouse Drag Scroll (Optional but Premium)
    function initNetflixScroll() {
        const slider = document.querySelector('.netflix-scroll-container');
        if (!slider) return;

        let isDown = false;
        let计量Start;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('active');
            计量Start = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('mouseleave', () => {
            isDown = false;
        });
        slider.addEventListener('mouseup', () => {
            isDown = false;
        });
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - 计量Start) * 2;
            slider.scrollLeft = scrollLeft - walk;
        });
    }

    // Initialize All
    initCounters();
    initNetflixScroll();
    initReveal();

    // Scroll Progress Bar (Refined)
    const progressBar = document.getElementById('scrollProgress');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + "%";
        }, { passive: true });
    }
});
