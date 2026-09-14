<?php
declare(strict_types=1);

$registration = is_array($registration ?? null) ? $registration : [];
$reference = (string) ($registration['registration_reference'] ?? '');
$paymentStatus = (string) ($registration['payment_status'] ?? 'pending_payment');
$amount = (float) ($registration['amount_inr'] ?? 0);
$amountDisplay = abs($amount - floor($amount)) < 0.00001
    ? number_format($amount, 0)
    : number_format($amount, 2);
$currency = (string) ($registration['currency'] ?? 'INR');
$paymentConfigured = !empty($paymentConfigured);
$whatsappGroupUrl = (string) ($whatsappGroupUrl ?? '#');
$webinar = is_array($webinar ?? null) ? $webinar : [];

$statusLabels = [
    'pending_payment' => 'Awaiting payment',
    'payment_verified' => 'Payment successful',
    'payment_failed' => 'Payment failed',
    'free_registered' => 'Registered (Free)',
];
$statusLabel = $statusLabels[$paymentStatus] ?? 'Awaiting payment';
$paymentComplete = in_array($paymentStatus, ['payment_verified', 'free_registered'], true);
?>
<section class="section payment-section" style="min-height: 80vh; padding: 4rem 1rem; display: flex; align-items: center; justify-content: center;">
    <div class="container" style="max-width: 680px; margin: 0 auto;">
        <div class="glass-card" style="background: #ffffff; border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 35px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.06);">
            
            <header style="text-align: center; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem;">
                <div style="display: inline-block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; color: var(--accent-color); margin-bottom: 0.4rem;">
                    Registration #<?= e($reference) ?>
                </div>
                <h1 style="color: var(--primary-color); font-size: 1.8rem; margin: 0 0 0.5rem 0;">Webinar Registration Payment</h1>
                <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Complete your payment to secure your seat and join the official WhatsApp Group.</p>
            </header>

            <div style="background: #f8fafc; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #e2e8f0;">
                <div style="display: grid; gap: 0.8rem; font-size: 0.95rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.6rem;">
                        <span style="color: #64748b;">Participant Name:</span>
                        <strong style="color: #1e293b;"><?= e((string) ($registration['name'] ?? '-')) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.6rem;">
                        <span style="color: #64748b;">Email Address:</span>
                        <strong style="color: #1e293b;"><?= e((string) ($registration['email'] ?? '-')) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.6rem;">
                        <span style="color: #64748b;">Phone Number:</span>
                        <strong style="color: #1e293b;"><?= e((string) ($registration['phone'] ?? '-')) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.6rem;">
                        <span style="color: #64748b;">Webinar:</span>
                        <strong style="color: #1e293b; text-align: right; max-width: 60%;"><?= e((string) ($registration['webinar_title'] ?? 'Webinar Session')) ?></strong>
                    </div>
                    <?php if (!empty($webinar['event_date'])): ?>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.6rem;">
                            <span style="color: #64748b;">Date & Time:</span>
                            <strong style="color: #1e293b;"><?= e((string)$webinar['event_date']) ?> (<?= e((string)($webinar['event_time'] ?? '')) ?>)</strong>
                        </div>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; font-size: 1.15rem;">
                        <span style="color: #1e293b; font-weight: 700;">Total Amount:</span>
                        <strong style="color: var(--primary-color); font-weight: 800;"><?= e($currency) ?> <?= e($amountDisplay) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Status Banner -->
            <div id="paymentStatusBox" style="border-radius: 10px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.8rem; <?= $paymentComplete ? 'background: rgba(22, 163, 74, 0.1); border: 1px solid #16a34a; color: #15803d;' : ($paymentStatus === 'payment_failed' ? 'background: rgba(220, 38, 38, 0.1); border: 1px solid #dc2626; color: #b91c1c;' : 'background: rgba(45, 106, 79, 0.08); border: 1px solid var(--primary-color); color: var(--primary-color);') ?>">
                <i id="statusIcon" class="fas <?= $paymentComplete ? 'fa-check-circle' : ($paymentStatus === 'payment_failed' ? 'fa-times-circle' : 'fa-info-circle') ?>" style="font-size: 1.3rem;"></i>
                <div>
                    <div id="statusTitle" style="font-weight: 700; font-size: 1rem;"><?= e($statusLabel) ?></div>
                    <div id="statusDetail" style="font-size: 0.88rem;">
                        <?php if ($paymentComplete): ?>
                            Your registration and payment have been confirmed!
                        <?php elseif ($paymentStatus === 'payment_failed'): ?>
                            <?= e((string) ($registration['failure_reason'] ?? 'Payment was cancelled or could not be processed.')) ?>
                        <?php else: ?>
                            Please click the button below to pay securely via Razorpay (UPI, Cards, Netbanking).
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="errorMessageContainer" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.95rem;">
                <div style="font-weight: 700; margin-bottom: 0.2rem;"><i class="fas fa-exclamation-triangle"></i> Notice</div>
                <div id="errorMessageText"></div>
            </div>

            <!-- Pay Button (Shown when not paid) -->
            <button
                id="payButton"
                type="button"
                class="btn-primary"
                style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 700; border-radius: 8px; cursor: pointer; <?= $paymentComplete ? 'display: none;' : '' ?>"
                <?= (!$paymentConfigured) ? 'disabled' : '' ?>
            >
                <i class="fas fa-lock"></i> Pay <?= e($currency) ?> <?= e($amountDisplay) ?> via Razorpay
            </button>

            <!-- Join WhatsApp Group Button (Shown ONLY when payment is verified) -->
            <div id="whatsappContainer" style="<?= $paymentComplete ? '' : 'display: none;' ?> text-align: center;">
                <div style="background: rgba(37, 211, 102, 0.1); border: 1px solid #25d366; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <i class="fab fa-whatsapp" style="font-size: 2.5rem; color: #25d366; margin-bottom: 0.5rem; display: block;"></i>
                    <h3 style="color: #128c7e; margin-bottom: 0.5rem;">You're Registered!</h3>
                    <p style="color: #334155; font-size: 0.95rem; margin-bottom: 1rem;">Click the button below to join the private webinar WhatsApp Group for joining links and updates.</p>
                    <a
                        id="whatsappLink"
                        href="<?= e($whatsappGroupUrl) ?>"
                        target="_blank"
                        rel="noopener"
                        class="btn-primary"
                        style="background: #25d366; color: white; display: inline-block; padding: 0.9rem 1.8rem; font-size: 1.05rem; font-weight: 700; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.35);"
                    >
                        <i class="fab fa-whatsapp"></i> Join Webinar WhatsApp Group
                    </a>
                </div>
            </div>

            <!-- Return Link -->
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="/enquiry" style="color: #64748b; text-decoration: none; font-size: 0.9rem;">
                    &larr; Return to Webinar Registration Page
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Load Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const payButton = document.getElementById('payButton');
        const statusBox = document.getElementById('paymentStatusBox');
        const statusIcon = document.getElementById('statusIcon');
        const statusTitle = document.getElementById('statusTitle');
        const statusDetail = document.getElementById('statusDetail');
        const errorContainer = document.getElementById('errorMessageContainer');
        const errorMessageText = document.getElementById('errorMessageText');
        const whatsappContainer = document.getElementById('whatsappContainer');
        const whatsappLink = document.getElementById('whatsappLink');

        const reference = <?= json_encode($reference) ?>;
        const ackToken = <?= json_encode((string)($registration['ack_token'] ?? '')) ?>;
        const isAlreadyComplete = <?= $paymentComplete ? 'true' : 'false' ?>;

        if (!payButton || isAlreadyComplete) {
            return;
        }

        async function postJson(url, payload) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok || !data || data.status !== 'ok') {
                throw new Error((data && data.detail) ? data.detail : 'Payment request failed.');
            }
            return data;
        }

        function setStatus(status, title, detail) {
            statusTitle.textContent = title;
            statusDetail.textContent = detail;

            if (status === 'payment_verified') {
                statusBox.style.background = 'rgba(22, 163, 74, 0.1)';
                statusBox.style.borderColor = '#16a34a';
                statusBox.style.color = '#15803d';
                statusIcon.className = 'fas fa-check-circle';
                payButton.style.display = 'none';
                whatsappContainer.style.display = 'block';
                errorContainer.style.display = 'none';
            } else if (status === 'payment_failed') {
                statusBox.style.background = 'rgba(220, 38, 38, 0.1)';
                statusBox.style.borderColor = '#dc2626';
                statusBox.style.color = '#b91c1c';
                statusIcon.className = 'fas fa-times-circle';
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-redo"></i> Retry Payment';
                whatsappContainer.style.display = 'none';
            }
        }

        function showError(msg) {
            errorMessageText.textContent = msg;
            errorContainer.style.display = 'block';
        }

        async function launchPayment() {
            if (typeof window.Razorpay !== 'function') {
                showError('Razorpay payment gateway is loading. If it does not appear automatically, please click the button below.');
                return;
            }

            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing Payment...';
            errorContainer.style.display = 'none';

            try {
                const orderData = await postJson('/api/enquiry-payments/order', {
                    registration_reference: reference,
                    ack_token: ackToken
                });

                if (orderData.already_paid) {
                    setStatus('payment_verified', 'Payment Already Completed', orderData.detail);
                    return;
                }

                const checkout = orderData.checkout;
                let completed = false;

                const rzp = new window.Razorpay({
                    key: checkout.key_id,
                    amount: checkout.amount,
                    currency: checkout.currency,
                    name: checkout.name,
                    description: checkout.description,
                    order_id: checkout.order_id,
                    prefill: checkout.prefill || {},
                    theme: {
                        color: checkout.theme_color || '#2d6a4f'
                    },
                    modal: {
                        ondismiss: async function () {
                            if (!completed) {
                                payButton.disabled = false;
                                payButton.innerHTML = '<i class="fas fa-redo"></i> Try Payment Again';
                                setStatus('payment_failed', 'Payment Cancelled', 'You cancelled the payment. Click below to try again.');
                                showError('Payment was not completed. You can try again or return to the registration page.');

                                try {
                                    await postJson('/api/enquiry-payments/failure', {
                                        registration_reference: reference,
                                        ack_token: ackToken,
                                        razorpay_order_id: checkout.order_id,
                                        error_code: 'PAYMENT_CANCELLED',
                                        error_description: 'User dismissed Razorpay checkout window.'
                                    });
                                } catch (e) {
                                    // ignore failure log error
                                }
                            }
                        }
                    },
                    handler: async function (response) {
                        completed = true;
                        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying Payment...';

                        try {
                            const verifyData = await postJson('/api/enquiry-payments/verify', {
                                registration_reference: reference,
                                ack_token: ackToken,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            });

                            setStatus('payment_verified', 'Payment Verified!', 'Your seat is confirmed! Redirecting to WhatsApp Group...');

                            if (typeof window.fbq === 'function') {
                                window.fbq('track', 'Purchase', {
                                    value: <?= json_encode($amount) ?>,
                                    currency: 'INR',
                                    content_name: <?= json_encode((string) ($registration['webinar_title'] ?? 'Webinar Registration')) ?>
                                });
                            }
                            
                            if (verifyData.whatsapp_group_url && verifyData.whatsapp_group_url !== '#') {
                                whatsappLink.href = verifyData.whatsapp_group_url;
                                setTimeout(() => {
                                    window.location.href = verifyData.whatsapp_group_url;
                                }, 1000);
                            }
                        } catch (verifyErr) {
                            showError('Verification failed: ' + verifyErr.message);
                            setStatus('payment_failed', 'Verification Error', verifyErr.message);
                        }
                    }
                });

                rzp.on('payment.failed', async function (response) {
                    completed = true;
                    const err = response.error || {};
                    const desc = err.description || 'Payment transaction failed.';
                    showError(desc);
                    setStatus('payment_failed', 'Payment Failed', desc);

                    try {
                        await postJson('/api/enquiry-payments/failure', {
                            registration_reference: reference,
                            ack_token: ackToken,
                            razorpay_order_id: checkout.order_id,
                            razorpay_payment_id: (err.metadata && err.metadata.payment_id) ? err.metadata.payment_id : '',
                            error_code: err.code || 'PAYMENT_FAILED',
                            error_description: desc
                        });
                    } catch (e) {
                        // ignore
                    }
                });

                rzp.open();
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-lock"></i> Pay ' + <?= json_encode($currency . ' ' . $amountDisplay) ?> + ' via Razorpay';

            } catch (err) {
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-redo"></i> Try Payment Again';
                showError(err.message || 'Could not initiate checkout.');
                setStatus('payment_failed', 'Error', err.message);
            }
        }

        payButton.addEventListener('click', launchPayment);

        // Auto-launch checkout if script loaded and not already complete
        setTimeout(function () {
            if (!isAlreadyComplete && typeof window.Razorpay === 'function') {
                launchPayment();
            }
        }, 700);
    });
</script>
