<section class="section" style="padding-top: 4rem;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 1rem;">Wellness Insights</h1>
            <p style="font-size: 1.2rem; opacity: 0.8;">Articles, tips, and updates from Functional Chronic Wellness.</p>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color); padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center; border: 1px solid var(--primary-color);">
                <i class="fas fa-check-circle"></i> <?= e($successMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center; border: 1px solid #dc3545;">
                <i class="fas fa-exclamation-circle"></i> <?= e($errorMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="glass-card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; opacity: 0.7;">No posts yet. Stay tuned!</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                <?php foreach ($posts as $post): ?>
                    <div class="glass-card" style="overflow: hidden; padding: 0; display: flex; flex-direction: column;">
                        <?php if (!empty($post['image_url'])): ?>
                            <div style="height: 200px; background: url('<?= e((string) $post['image_url']) ?>') no-repeat center center/cover;"></div>
                        <?php else: ?>
                            <div style="height: 200px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #999;"><i class="fas fa-image fa-2x"></i></div>
                        <?php endif; ?>

                        <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;"><?= e((string) $post['title']) ?></h3>
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 1rem;"><i class="far fa-calendar-alt"></i> <?= e(date('F d, Y', strtotime((string) $post['created_at']))) ?></p>
                            <p style="line-height: 1.6; flex-grow: 1;"><?= e(str_limit((string) $post['content'], 150)) ?></p>
                            <a href="/blogs/<?= (int) $post['id'] ?>" style="margin-top: 1rem; background: transparent; border: 1px solid var(--primary-color); color: var(--primary-color); padding: 0.5rem 1rem; border-radius: 20px; cursor: pointer; align-self: flex-start; text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease;">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
