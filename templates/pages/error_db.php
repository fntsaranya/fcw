<section class="hero-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
    <div class="container" style="text-align: center;">
        <div class="glass-card" style="max-width: 600px; margin: 0 auto; padding: 3rem;">
            <i class="fas fa-database" style="font-size: 4rem; color: var(--primary-color); margin-bottom: 2rem;"></i>
            <h1 style="color: var(--primary-color); margin-bottom: 1rem;">Service Temporarily Unavailable</h1>
            <p style="font-size: 1.1rem; margin-bottom: 2rem;"><?= e((string) ($errorMessage ?? 'A temporary issue occurred.')) ?></p>
            <a href="/" class="btn-primary">Return Home</a>

            <div style="margin-top: 3rem; font-size: 0.9rem; opacity: 0.7; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 1.5rem;">
                <p>If you are the administrator, please check your <code>DATABASE_URL</code> configuration.</p>
            </div>
        </div>
    </div>
</section>
