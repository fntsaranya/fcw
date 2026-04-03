document.addEventListener('DOMContentLoaded', () => {
    // Mobile Navigation Toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const mobileMedia = window.matchMedia('(max-width: 700px)');

    if (hamburger && navLinks) {
        const isMobileViewport = () => mobileMedia.matches;
        const setExpandedState = (isOpen) => {
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        const closeMobileMenu = () => {
            navLinks.classList.remove('mobile-open');
            setExpandedState(false);
        };

        const syncDesktopState = () => {
            if (!isMobileViewport()) {
                navLinks.classList.remove('mobile-open');
                navLinks.style.removeProperty('display');
                setExpandedState(false);
            }
        };

        // Start with menu closed and ensure desktop does not inherit stale mobile state.
        closeMobileMenu();
        syncDesktopState();

        hamburger.addEventListener('click', (event) => {
            if (!isMobileViewport()) {
                return;
            }
            event.preventDefault();
            event.stopPropagation();
            const willOpen = !navLinks.classList.contains('mobile-open');
            navLinks.classList.toggle('mobile-open', willOpen);
            setExpandedState(willOpen);
        });

        navLinks.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (isMobileViewport()) {
                    closeMobileMenu();
                }
            });
        });

        document.addEventListener('click', (event) => {
            if (!isMobileViewport()) {
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
            if (!isMobileViewport()) {
                closeMobileMenu();
            }
            syncDesktopState();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMobileMenu();
            }
        });

        window.addEventListener('orientationchange', () => {
            if (!isMobileViewport()) {
                closeMobileMenu();
            }
            syncDesktopState();
        });

        // Handles browser back/forward cache restores where stale classes can persist.
        window.addEventListener('pageshow', () => {
            syncDesktopState();
            if (isMobileViewport()) {
                closeMobileMenu();
            }
        });

        window.addEventListener('focus', () => {
            syncDesktopState();
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
