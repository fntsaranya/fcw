<section class="section" style="padding-top: 4rem;">
    <div class="container">
        <div style="display: flex; gap: 1rem; margin-bottom: 2.5rem; justify-content: center; background: rgba(255, 255, 255, 0.5); padding: 0.5rem; border-radius: 50px; border: 1px solid rgba(0, 0, 0, 0.05); width: fit-content; margin-left: auto; margin-right: auto;">
            <a href="/admin/blog" class="admin-nav-tab active" style="padding: 0.8rem 2rem; text-decoration: none; color: white; background: var(--primary-color); border-radius: 40px; transition: all 0.3s; font-weight: 700; display: flex; align-items: center; gap: 0.8rem; box-shadow: 0 4px 15px rgba(47, 79, 79, 0.3);"><i class="fas fa-edit" style="font-size: 1.1rem;"></i> Blog</a>
            <a href="/admin/contacts" class="admin-nav-tab" style="padding: 0.8rem 2rem; text-decoration: none; color: var(--primary-color); border-radius: 40px; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; gap: 0.8rem;"><i class="fas fa-envelope" style="font-size: 1.1rem;"></i> Contact List</a>
        </div>

        <div class="glass-card" style="max-width: 700px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 2rem;">Create New Blog Post</h2>

            <?php if (!empty($errorMessage)): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #d32f2f; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; border: 1px solid #d32f2f;"><i class="fas fa-exclamation-circle"></i> <?= e($errorMessage) ?></div>
            <?php endif; ?>

            <form action="/admin/blog" method="post" style="display: grid; gap: 1.5rem;">
                <div>
                    <label for="title" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title</label>
                    <input type="text" id="title" name="title" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8);">
                </div>

                <div>
                    <label for="image_url" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Image URL</label>
                    <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8);">
                </div>

                <div>
                    <label for="content" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Content</label>
                    <textarea id="content" name="content" rows="10" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); font-family: inherit;"></textarea>
                </div>

                <div>
                    <label for="pin" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark);">Admin PIN</label>
                    <input type="password" id="pin" name="pin" required placeholder="Enter PIN to publish" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8);">
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-top: 1rem;">Publish Post</button>
            </form>
        </div>
    </div>
</section>
