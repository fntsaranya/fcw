<?php
declare(strict_types=1);
$reference = (string) ($booking['booking_reference'] ?? '');
?>
<section class="section">
    <div class="container" style="max-width: 600px; text-align: center; padding-top: 5rem; padding-bottom: 5rem;">
        <i class="fas fa-check-circle" style="font-size: 5rem; color: #10b981; margin-bottom: 1.5rem;"></i>
        <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Submission Received</h2>
        <p style="font-size: 1.1rem; color: #475569; line-height: 1.6;">
            Thank you for choosing FCW. Your submission has been received successfully. Our healthcare team will review the patient's information and contact you shortly with the next steps.
        </p>
        <div style="margin-top: 2rem;">
            <a href="/" class="btn-primary" style="text-decoration: none;">Return to Home</a>
        </div>
    </div>
</section>
