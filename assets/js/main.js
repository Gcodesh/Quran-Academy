// تفعيل العداد عند التمرير
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // The lower the slower

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count');
                const count = +counter.innerText;
                
                // Lower inc to slow and higher to slow
                const inc = target / speed;

                if (count < target) {
                    // Add inc to count and output in counter
                    counter.innerText = Math.ceil(count + inc);
                    // Call function every ms
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target + '+';
                }
            };

            updateCount();
        });
    }

    // Intersection Observer to start animation when in view
    let options = {
        root: null,
        rootMargin: '0px',
        threshold: 0.5
    };

    let observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    }, options);

    let target = document.querySelector('.stats');
    if(target) {
        observer.observe(target);
    }
});
