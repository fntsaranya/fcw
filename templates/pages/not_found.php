<section class="section" style="min-height: 70vh; display:flex; align-items:center;">
    <div class="container" style="max-width: 680px; text-align: center;">
        <div class="glass-card">
            <h1 style="color: var(--primary-color);"><?= e((string) ($title ?? 'Page not found')) ?></h1>
            <p style="font-size: 1.1rem; margin-bottom: 2rem;"><?= e((string) ($message ?? 'The page you requested does not exist.')) ?></p>
            <a href="/" class="btn-primary">Back to Home</a>
        </div>
    </div>
</section>
