<section class="hero-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
    <div class="container">
        <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 1rem;">Register for Enquiry</h2>
            <p style="text-align: center; margin-bottom: 2rem;">
🦋 <strong>FREE LIVE WEBINAR</strong><br><br>

<strong>Is Your Thyroid Condition Autoimmune?</strong><br><br>

Join our <strong>FREE 3-hour live webinar</strong> to learn how nutrition, gut health, inflammation, stress, and lifestyle habits can influence thyroid health. You'll gain practical, evidence-informed strategies to better understand and support your thyroid.<br><br>

📅 <strong>Date:</strong> Sunday, 16th August<br>
🕙 <strong>Time:</strong> 10:00 AM – 1:00 PM<br>
🎓 <strong>Speaker:</strong> Saranya Mohan, Functional Clinical Nutritionist<br>
💻 <strong>Mode:</strong> Live Online<br>
💚 <strong>Registration:</strong> FREE (Limited Seats)<br><br>

<strong>Reserve your seat today!</strong>
</p>

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

            <form action="/enquiry" method="post" target="_blank" onsubmit="setTimeout(() => { this.reset(); alert('Thank you! Your registration has been received. You are now being redirected to our WhatsApp group in a new tab.'); }, 500);" style="display: grid; gap: 1.5rem;">
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


                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 1rem; margin-top: 1rem; width: 100%;">Register</button>
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
