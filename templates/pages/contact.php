<?php
$webinar = is_array($webinar ?? null) ? $webinar : [];
$title = (string) ($webinar['title'] ?? 'Is Your Thyroid Condition Autoimmune?');
$subtitle = (string) ($webinar['subtitle'] ?? '🦋 LIVE WEBINAR');
$description = (string) ($webinar['description'] ?? "Join our live webinar to learn how nutrition, gut health, inflammation, stress, and lifestyle habits can influence health. You'll gain practical, evidence-informed strategies.");
$eventDate = (string) ($webinar['event_date'] ?? 'Sunday, 16th August');
$eventTime = (string) ($webinar['event_time'] ?? '10:00 AM – 1:00 PM');
$speaker = (string) ($webinar['speaker'] ?? 'Saranya Mohan, Functional Clinical Nutritionist');
$venuePlatform = (string) ($webinar['venue_platform'] ?? 'Live Online');
$envFee = (float) (\FCW\Core\Config::enquiryPayment()['fee_inr'] ?? 0.00);
$feeInr = isset($webinar['fee_inr']) && (float) $webinar['fee_inr'] > 0.00
    ? (float) $webinar['fee_inr']
    : ($envFee > 0.00 ? $envFee : 0.00);
$imageUrl = trim((string) ($webinar['image_url'] ?? ''));

$isPaid = $feeInr > 0.00;
$feeDisplay = abs($feeInr - floor($feeInr)) < 0.001 ? number_format($feeInr, 0) : number_format($feeInr, 2);
?>
<section class="hero-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
    <div class="container">
        <div class="glass-card" style="max-width: 820px; margin: 0 auto; background: rgba(255, 255, 255, 0.95); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; padding: 2rem;">
            
            <?php if ($imageUrl !== ''): ?>
                <div style="margin: -2rem -2rem 2rem -2rem; text-align: center; background: #f8fafc; border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <img src="<?= e($imageUrl) ?>" alt="<?= e($title) ?>" style="max-width: 100%; max-height: 380px; object-fit: cover; width: 100%; display: block;">
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-bottom: 1.5rem;">
                <?php if ($subtitle !== ''): ?>
                    <span style="display: inline-block; background: rgba(45, 106, 79, 0.12); color: var(--primary-color); font-weight: 700; font-size: 0.9rem; padding: 0.35rem 1rem; border-radius: 20px; margin-bottom: 0.8rem; letter-spacing: 0.5px;">
                        <?= e($subtitle) ?>
                    </span>
                <?php endif; ?>
                <h2 style="color: var(--primary-color); font-size: 1.8rem; margin-bottom: 0.8rem; line-height: 1.3;">
                    <?= e($title) ?>
                </h2>
                <p style="color: #475569; font-size: 1.05rem; line-height: 1.6; max-width: 700px; margin: 0 auto 1.5rem auto;">
                    <?= nl2br(e($description)) ?>
                </p>
            </div>

            <!-- Webinar Highlights Box -->
            <div style="background: linear-gradient(135deg, rgba(45, 106, 79, 0.05), rgba(82, 183, 136, 0.08)); border: 1px solid rgba(45, 106, 79, 0.2); border-radius: 12px; padding: 1.2rem 1.5rem; margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; text-align: left;">
                    <?php if ($eventDate !== ''): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(45, 106, 79, 0.15); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Date</div>
                                <div style="font-weight: 700; color: #1e293b;"><?= e($eventDate) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($eventTime !== ''): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(45, 106, 79, 0.15); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Time</div>
                                <div style="font-weight: 700; color: #1e293b;"><?= e($eventTime) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($speaker !== ''): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(45, 106, 79, 0.15); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Speaker</div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;"><?= e($speaker) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: <?= $isPaid ? 'rgba(234, 88, 12, 0.15)' : 'rgba(22, 163, 74, 0.15)' ?>; color: <?= $isPaid ? '#c2410c' : '#15803d' ?>; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                            <i class="fas <?= $isPaid ? 'fa-indian-rupee-sign' : 'fa-gift' ?>"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Registration Fee</div>
                            <div style="font-weight: 700; color: <?= $isPaid ? '#c2410c' : '#15803d' ?>; font-size: 1.05rem;">
                                <?= $isPaid ? 'INR ' . e($feeDisplay) : 'FREE (Limited Seats)' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; border: 1px solid var(--primary-color);">
                    <i class="fas fa-check-circle"></i> <?= e($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($warningMessage)): ?>
                <div style="background: rgba(255, 193, 7, 0.12); color: #856404; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; border: 1px solid #ffeeba;">
                    <i class="fas fa-exclamation-triangle"></i> <?= e($warningMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; border: 1px solid #dc3545;">
                    <i class="fas fa-exclamation-circle"></i> <?= e($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="/enquiry" method="post" style="display: grid; gap: 1.3rem;">
                <div style="text-align: left;">
                    <label for="name" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #334155;">Full Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name" style="width: 100%; padding: 0.9rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-family: var(--font-body); font-size: 1rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem;">
                    <div style="text-align: left;">
                        <label for="email" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #334155;">Email Address *</label>
                        <input type="email" id="email" name="email" required placeholder="you@example.com" style="width: 100%; padding: 0.9rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-family: var(--font-body); font-size: 1rem;">
                    </div>
                    <div style="text-align: left;">
                        <label for="phone" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #334155;">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="+91 98765 43210" style="width: 100%; padding: 0.9rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-family: var(--font-body); font-size: 1rem;">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 1rem; margin-top: 0.5rem; width: 100%; font-weight: 700; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(45, 106, 79, 0.25);">
                    <?php if ($isPaid): ?>
                        <i class="fas fa-lock"></i> Register & Pay INR <?= e($feeDisplay) ?>
                    <?php else: ?>
                        <i class="fas fa-check"></i> Register for Free Webinar
                    <?php endif; ?>
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="/contact" class="btn-secondary" style="display: inline-block; text-decoration: none; font-size: 0.95rem; padding: 0.7rem 1.2rem; border-radius: 8px;">
                    Need a 1-on-1 consultation? Book Paid Appointment &rarr;
                </a>
            </div>

            <div style="margin-top: 2.5rem; text-align: center; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 1.5rem;">
                <h4 style="font-size: 1.05rem; margin-bottom: 0.8rem; color: #334155;">Have Questions? Reach Out Directly</h4>
                <p style="margin-bottom: 0.3rem;"><i class="fas fa-envelope" style="color: var(--primary-color);"></i> <?= e($contact['EMAIL'] ?? '') ?></p>
                <p style="margin-bottom: 1rem;"><i class="fas fa-phone" style="color: var(--primary-color);"></i> <?= e($contact['PHONE_DISPLAY'] ?? '') ?></p>
                <div>
                    <a href="<?= e($contact['INSTAGRAM'] ?? '#') ?>" target="_blank" rel="noopener" title="Instagram" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 1.8rem;"><i class="fab fa-instagram"></i></a>
                    <a href="<?= e($contact['LINKEDIN'] ?? '#') ?>" target="_blank" rel="noopener" title="LinkedIn" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 1.8rem;"><i class="fab fa-linkedin"></i></a>
                    <a href="<?= e($contact['WHATSAPP_CHANNEL'] ?? '#') ?>" target="_blank" rel="noopener" title="WhatsApp Channel" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 1.8rem;"><i class="fab fa-whatsapp"></i></a>
                    <a href="<?= e($contact['WHATSAPP_GROUP'] ?? '#') ?>" target="_blank" rel="noopener" title="WhatsApp Group" style="color: var(--primary-color); margin: 0 0.5rem; font-size: 1.8rem;"><i class="fas fa-users"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
