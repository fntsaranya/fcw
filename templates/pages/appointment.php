<?php
$paymentConfig = $paymentConfig ?? [];
$feeInr = (string) ($paymentConfig['fee_inr'] ?? '0');
$oldInput = $oldInput ?? [];
$minDate = date('Y-m-d');
?>
<section class="hero-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
    <div class="container">
        <div class="glass-card" style="max-width: 860px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 0.8rem;">Book Consultation Appointment</h2>

            <div style="background: rgba(45, 106, 79, 0.08); border: 1px solid rgba(45, 106, 79, 0.25); border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem; text-align: center;">
                <strong>Consultation Fee: INR <?= e($feeInr) ?></strong>
            </div>

            <?php if (!empty($errorMessage)): ?>
                <div style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; border: 1px solid #dc3545;">
                    <i class="fas fa-exclamation-circle"></i> <?= e((string) $errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="/contact" method="post" style="display: grid; gap: 1.2rem;">
                <div style="text-align: left;">
                    <label for="full_name" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="<?= e((string) ($oldInput['full_name'] ?? '')) ?>" required style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="text-align: left;">
                        <label for="email" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?= e((string) ($oldInput['email'] ?? '')) ?>" required style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                    </div>
                    <div style="text-align: left;">
                        <label for="phone" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" value="<?= e((string) ($oldInput['phone'] ?? '')) ?>" required style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="text-align: left;">
                        <label for="preferred_date" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Preferred Date *</label>
                        <input type="date" id="preferred_date" name="preferred_date" min="<?= e($minDate) ?>" value="<?= e((string) ($oldInput['preferred_date'] ?? '')) ?>" required style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                    </div>
                    <div style="text-align: left;">
                        <label for="preferred_time" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Preferred Time</label>
                        <input type="text" id="preferred_time" name="preferred_time" value="<?= e((string) ($oldInput['preferred_time'] ?? '')) ?>" placeholder="Example: 10:30 AM - 11:00 AM" style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                    </div>
                </div>

                <div style="text-align: left;">
                    <label for="concern" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Primary Concern *</label>
                    <textarea id="concern" name="concern" rows="5" required style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);"><?= e((string) ($oldInput['concern'] ?? '')) ?></textarea>
                </div>

                <button type="submit" class="btn-primary" style="font-size: 1.05rem; padding: 1rem; margin-top: 0.6rem; width: 100%;">Continue</button>
            </form>

            <div style="margin-top: 1.2rem; text-align: center;">
                <a href="/enquiry" style="color: var(--primary-color); text-decoration: underline;">Need only enquiry (without payment)? Register here.</a>
            </div>
        </div>
    </div>
</section>
