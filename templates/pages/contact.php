<section class="hero-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
    <div class="container">
        <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 1rem;">Register for Enquiry</h2>
            <p style="text-align: center; margin-bottom: 2rem;">Have a question first? Share your details and we will connect with you.</p>

            <?php if (!empty($successMessage)): ?>
                <div style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color); padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center; border: 1px solid var(--primary-color);">
                    <i class="fas fa-check-circle"></i> <?= e($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($warningMessage)): ?>
                <div style="background: rgba(255, 193, 7, 0.12); color: #856404; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center; border: 1px solid #ffeeba;">
                    <i class="fas fa-exclamation-triangle"></i> <?= e($warningMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center; border: 1px solid #dc3545;">
                    <i class="fas fa-exclamation-circle"></i> <?= e($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="/enquiry" method="post" style="display: grid; gap: 1.5rem;">
                <div style="text-align: left;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
                    <input type="text" id="name" name="name" required style="width: 100%; padding: 1rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="text-align: left;">
                        <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 1rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                    </div>
                    <div style="text-align: left;">
                        <label for="phone" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required style="width: 100%; padding: 1rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                    </div>
                </div>

                <div style="text-align: left;">
                    <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">How can we help you?</label>
                    <textarea id="message" name="message" rows="5" required style="width: 100%; padding: 1rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); font-family: var(--font-body);"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 1rem; margin-top: 1rem; width: 100%;">Register Enquiry</button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="/contact" class="btn-primary" style="display: inline-block; text-decoration: none;">Need to book directly? Go to Paid Appointment</a>
            </div>

            <div style="margin-top: 3rem; text-align: center; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 2rem;">
                <h3 style="font-size: 1.2rem; margin-bottom: 1rem;">Or Contact Us Directly</h3>
                <p><i class="fas fa-envelope" style="color: var(--primary-color);"></i> <?= e($contact['EMAIL'] ?? '') ?></p>
                <p><i class="fas fa-phone" style="color: var(--primary-color);"></i> <?= e($contact['PHONE_DISPLAY'] ?? '') ?></p>
                <div style="margin-top: 1.5rem;">
                    <a href="<?= e($contact['INSTAGRAM'] ?? '#') ?>" target="_blank" rel="noopener" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 2rem;"><i class="fab fa-instagram"></i></a>
                    <a href="<?= e($contact['LINKEDIN'] ?? '#') ?>" target="_blank" rel="noopener" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 2rem;"><i class="fab fa-linkedin"></i></a>
                    <a href="<?= e($contact['WHATSAPP_CHANNEL'] ?? '#') ?>" target="_blank" rel="noopener" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 2rem;"><i class="fab fa-whatsapp"></i></a>
                    <a href="<?= e($contact['WHATSAPP_GROUP'] ?? '#') ?>" target="_blank" rel="noopener" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 2rem;"><i class="fas fa-users"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
