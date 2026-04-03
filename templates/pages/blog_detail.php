<section class="section" style="padding-top: 4rem;">
    <div class="container">
        <div style="margin-bottom: 2rem;">
            <a href="/blogs" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to Wellness Insights
            </a>
        </div>

        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <?php if (!empty($post['image_url'])): ?>
                <div style="width: 100%; height: 400px; background: url('<?= e((string) $post['image_url']) ?>') no-repeat center center/cover;"></div>
            <?php endif; ?>

            <div style="padding: 3rem;">
                <h1 style="color: var(--primary-color); font-size: 3rem; margin-bottom: 1rem; line-height: 1.2;"><?= e((string) $post['title']) ?></h1>
                <p style="font-size: 1rem; color: #666; margin-bottom: 2rem;">
                    <i class="far fa-calendar-alt"></i> Published on <?= e(date('F d, Y', strtotime((string) $post['created_at']))) ?>
                </p>
                <div style="line-height: 1.8; font-size: 1.1rem; color: #333; white-space: pre-wrap;"><?= e((string) $post['content']) ?></div>
            </div>
        </div>

        <div style="margin-top: 4rem; text-align: center;">
            <div class="glass-card" style="display: inline-block; padding: 2rem 4rem;">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Have Questions?</h2>
                <p style="margin-bottom: 1.5rem;">We are here to help you on your wellness journey.</p>
                <a href="/contact" class="btn-primary">Book a Consultation</a>
            </div>
        </div>
    </div>
</section>
