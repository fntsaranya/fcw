document.addEventListener('DOMContentLoaded', () => {
    // Mobile Navigation Toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const mobileMedia = window.matchMedia('(max-width: 768px)');

    if (hamburger && navLinks) {
        const closeMobileMenu = () => {
            navLinks.classList.remove('mobile-open');
        };

        hamburger.addEventListener('click', (event) => {
            if (!mobileMedia.matches) {
                return;
            }
            event.stopPropagation();
            navLinks.classList.toggle('mobile-open');
        });

        navLinks.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (mobileMedia.matches) {
                    closeMobileMenu();
                }
            });
        });

        document.addEventListener('click', (event) => {
            if (!mobileMedia.matches) {
                return;
            }

            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            if (!navLinks.contains(target) && !hamburger.contains(target)) {
                closeMobileMenu();
            }
        });

        window.addEventListener('resize', () => {
            if (!mobileMedia.matches) {
                closeMobileMenu();
            }
        });
    }

    // Scroll Animation Observers
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.glass-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(el);
    });
});
