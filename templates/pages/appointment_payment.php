<?php
$booking = $booking ?? [];
$paymentConfig = $paymentConfig ?? [];
$reference = (string) ($booking['booking_reference'] ?? '');
$amountInr = (string) ($booking['amount_inr'] ?? ($paymentConfig['fee_inr'] ?? '0'));
$currency = (string) ($booking['currency'] ?? 'INR');
$paymentStatus = (string) ($booking['payment_status'] ?? 'pending_payment');
$upiId = (string) ($paymentConfig['upi_id'] ?? '');
$upiPayeeName = (string) ($paymentConfig['upi_payee_name'] ?? '');
$gpayLogoExists = !empty($gpayLogoExists);
$phonepeLogoExists = !empty($phonepeLogoExists);
$gpayQrExists = !empty($gpayQrExists);
$phonepeQrExists = !empty($phonepeQrExists);

$amountDisplay = (float) $amountInr;
$formattedAmount = (abs($amountDisplay - floor($amountDisplay)) < 0.00001)
    ? (string) ((int) $amountDisplay)
    : number_format($amountDisplay, 2, '.', '');
$payLabel = 'Pay Rs.' . $formattedAmount;

$statusColors = [
    'pending_payment' => ['bg' => 'rgba(255, 193, 7, 0.12)', 'border' => '#e0a800', 'text' => '#8a6d3b'],
    'payment_submitted' => ['bg' => 'rgba(59, 130, 246, 0.12)', 'border' => '#2563eb', 'text' => '#1e40af'],
    'payment_verified' => ['bg' => 'rgba(45, 106, 79, 0.12)', 'border' => '#2d6a4f', 'text' => '#2d6a4f'],
    'payment_rejected' => ['bg' => 'rgba(220, 53, 69, 0.1)', 'border' => '#dc3545', 'text' => '#dc3545'],
];
$statusStyle = $statusColors[$paymentStatus] ?? $statusColors['pending_payment'];
?>
<section class="section" style="padding-top: 3.5rem; padding-bottom: 4rem;">
    <div class="container" style="max-width: 1020px;">
        <div class="glass-card" style="margin-bottom: 1.5rem;">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 0.6rem;">Complete UPI Payment</h2>
            <p style="text-align: center; margin-bottom: 0.15rem;">Reference: <strong><?= e($reference) ?></strong></p>
            <p style="text-align: center; margin-bottom: 0.2rem;">Consultation Fee: <strong><?= e($payLabel) ?></strong></p>
            <p style="text-align: center; color:#4b5563;">Payee: <?= e($upiPayeeName !== '' ? $upiPayeeName : 'Not configured') ?> | UPI ID: <?= e($upiId !== '' ? $upiId : 'Not configured') ?></p>

            <div id="paymentStatusBanner" style="background: <?= e($statusStyle['bg']) ?>; border: 1px solid <?= e($statusStyle['border']) ?>; color: <?= e($statusStyle['text']) ?>; border-radius: 10px; padding: 0.9rem; margin: 1rem 0 0;">
                <strong>Status:</strong> <span id="paymentStatusText"><?= e($paymentStatus) ?></span>
                <div id="paymentStatusDetail" style="margin-top: 0.35rem; font-size: 0.95rem;"></div>
            </div>
        </div>

        <div class="glass-card" style="margin-bottom:1.2rem; background:white;">
            <h3 style="margin-bottom:0.8rem;">Step 1: Select UPI App</h3>
            <div class="upi-method-grid">
                <button type="button" class="upi-method-tab" data-method="gpay">
                    <span class="upi-logo-wrap">
                        <?php if (!empty($gpayLogoExists) && !empty($gpayLogoPath)): ?>
                            <img src="<?= e((string) $gpayLogoPath) ?>" alt="Google Pay logo" class="upi-logo-image">
                        <?php else: ?>
                            <span class="upi-logo-fallback">G</span>
                        <?php endif; ?>
                    </span>
                    <span class="upi-label-text">Google Pay</span>
                </button>

                <button type="button" class="upi-method-tab" data-method="phonepe">
                    <span class="upi-logo-wrap">
                        <?php if (!empty($phonepeLogoExists) && !empty($phonepeLogoPath)): ?>
                            <img src="<?= e((string) $phonepeLogoPath) ?>" alt="PhonePe logo" class="upi-logo-image">
                        <?php else: ?>
                            <span class="upi-logo-fallback">P</span>
                        <?php endif; ?>
                    </span>
                    <span class="upi-label-text">PhonePe</span>
                </button>
            </div>

            <div style="margin-top:1rem;">
                <button id="payNowButton" type="button" class="btn-primary" style="width:100%; opacity:0.5; pointer-events:none;"><?= e($payLabel) ?></button>
                <p id="payHelpText" style="margin-top:0.55rem; color:#6b7280; font-size:0.92rem;">Select a UPI app option to enable payment.</p>
            </div>
        </div>

        <div id="selectedQrPanel" class="glass-card" style="display:none; background:white; margin-bottom:1.2rem;">
            <h3 style="margin-bottom:0.5rem;">Step 2: Scan and Pay</h3>
            <p id="selectedMethodText" style="margin-bottom:0.8rem; color:#374151;"></p>
            <div style="text-align:center;">
                <img id="selectedQrImage" src="" alt="Selected UPI QR" style="width:280px; max-width:100%; border:1px solid #e5e7eb; border-radius:14px; display:none;">
                <div id="selectedQrMissing" style="display:none; border: 1px dashed #ef4444; border-radius: 12px; padding: 1rem; color: #b91c1c;"></div>
            </div>
            <!-- <?php if (!empty($upiIntentUrl)): ?>
                <div style="text-align:center; margin-top:0.8rem;">
                    <a id="upiIntentLink" href="<?= e((string) $upiIntentUrl) ?>" class="btn-primary" style="text-decoration:none;">Open UPI App</a>
                </div>
            <?php endif; ?> -->
        </div>

        <div class="glass-card" style="max-width: 900px; margin: 0 auto; background:white;">
            <h3 style="margin-bottom: 0.8rem;">Step 3: Submit Payment Confirmation</h3>
            <p style="margin-bottom: 1rem;">After successful payment, enter your UPI transaction reference (optional, but recommended for faster verification).</p>

            <div id="ackMessage" style="display: none; border-radius: 10px; padding: 0.8rem; margin-bottom: 1rem;"></div>

            <form id="paymentAckForm" style="display: grid; gap: 1rem;">
                <input type="hidden" name="booking_reference" value="<?= e($reference) ?>">
                <input type="hidden" name="ack_token" value="<?= e((string) ($ackToken ?? '')) ?>">
                <input type="hidden" id="payment_channel" name="payment_channel" value="">

                <div>
                    <label for="upi_transaction_ref" style="display:block; margin-bottom:0.35rem; font-weight:500;">UPI Transaction Ref (UPI Txn ID) (Optional)</label>
                    <input type="text" id="upi_transaction_ref" name="upi_transaction_ref" maxlength="150" placeholder="UTR / Transaction ID (optional)" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #d1d5db;">
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">I Have Paid - Submit Confirmation</button>
            </form>
        </div>
    </div>
</section>

<style>
    .upi-method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 0.9rem;
    }

    .upi-method-tab {
        border: 1px solid rgba(47, 79, 79, 0.28);
        border-radius: 14px;
        background: #ffffff;
        padding: 0.9rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .upi-method-tab:hover {
        border-color: var(--primary-color);
        box-shadow: 0 6px 18px rgba(47, 79, 79, 0.12);
    }

    .upi-method-tab.active {
        border-color: var(--primary-color);
        background: rgba(45, 106, 79, 0.08);
        box-shadow: 0 8px 20px rgba(47, 79, 79, 0.14);
    }

    .upi-logo-wrap {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .upi-logo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upi-logo-fallback {
        font-weight: 700;
        color: #374151;
        font-size: 0.95rem;
    }

    .upi-label-text {
        font-weight: 600;
        color: #1f2937;
    }
</style>

<script>
    (function () {
        const form = document.getElementById('paymentAckForm');
        const messageBox = document.getElementById('ackMessage');
        const statusText = document.getElementById('paymentStatusText');
        const statusDetail = document.getElementById('paymentStatusDetail');
        const statusBanner = document.getElementById('paymentStatusBanner');
        const bookingReference = <?= json_encode($reference) ?>;
        const token = <?= json_encode((string) ($ackToken ?? '')) ?>;
        const payNowButton = document.getElementById('payNowButton');
        const payHelpText = document.getElementById('payHelpText');
        const paymentChannelInput = document.getElementById('payment_channel');
        const selectedQrPanel = document.getElementById('selectedQrPanel');
        const selectedMethodText = document.getElementById('selectedMethodText');
        const selectedQrImage = document.getElementById('selectedQrImage');
        const selectedQrMissing = document.getElementById('selectedQrMissing');
        const methodTabs = Array.from(document.querySelectorAll('.upi-method-tab'));

        let selectedMethod = '';

        const payLabel = <?= json_encode($payLabel) ?>;

        const upiMethods = {
            gpay: {
                label: 'Google Pay',
                qrPath: <?= json_encode((string) ($gpayQrPath ?? '')) ?>,
                qrExists: <?= json_encode(!empty($gpayQrExists)) ?>,
                missingHint: 'Google Pay QR image missing. Upload file at ' + <?= json_encode((string) ($paymentConfig['gpay_qr_image'] ?? '/static/images/gpay-qr.png')) ?>
            },
            phonepe: {
                label: 'PhonePe',
                qrPath: <?= json_encode((string) ($phonepeQrPath ?? '')) ?>,
                qrExists: <?= json_encode(!empty($phonepeQrExists)) ?>,
                missingHint: 'PhonePe QR image missing. Upload file at ' + <?= json_encode((string) ($paymentConfig['phonepe_qr_image'] ?? '/static/images/phonepe-qr.png')) ?>
            }
        };

        const statusStyles = {
            pending_payment: { bg: 'rgba(255, 193, 7, 0.12)', border: '#e0a800', text: '#8a6d3b' },
            payment_submitted: { bg: 'rgba(59, 130, 246, 0.12)', border: '#2563eb', text: '#1e40af' },
            payment_verified: { bg: 'rgba(45, 106, 79, 0.12)', border: '#2d6a4f', text: '#2d6a4f' },
            payment_rejected: { bg: 'rgba(220, 53, 69, 0.1)', border: '#dc3545', text: '#dc3545' }
        };

        function setMessage(type, text) {
            messageBox.style.display = 'block';
            if (type === 'error') {
                messageBox.style.background = 'rgba(220, 53, 69, 0.1)';
                messageBox.style.border = '1px solid #dc3545';
                messageBox.style.color = '#dc3545';
            } else {
                messageBox.style.background = 'rgba(45, 106, 79, 0.1)';
                messageBox.style.border = '1px solid #2d6a4f';
                messageBox.style.color = '#2d6a4f';
            }
            messageBox.textContent = text;
        }

        function applyStatus(status, detail) {
            statusText.textContent = status;
            statusDetail.textContent = detail || '';

            const style = statusStyles[status] || statusStyles.pending_payment;
            statusBanner.style.background = style.bg;
            statusBanner.style.border = '1px solid ' + style.border;
            statusBanner.style.color = style.text;
        }

        function setSelectedMethod(method) {
            if (!upiMethods[method]) {
                return;
            }

            selectedMethod = method;
            paymentChannelInput.value = method;

            methodTabs.forEach((tab) => {
                tab.classList.toggle('active', tab.getAttribute('data-method') === method);
            });

            payNowButton.style.opacity = '1';
            payNowButton.style.pointerEvents = 'auto';
            payNowButton.textContent = payLabel;
            payHelpText.textContent = 'Selected: ' + upiMethods[method].label + '. Click the payment button to show QR.';
        }

        methodTabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                setSelectedMethod(tab.getAttribute('data-method') || '');
            });
        });

        payNowButton.addEventListener('click', () => {
            if (!selectedMethod || !upiMethods[selectedMethod]) {
                setMessage('error', 'Please select Google Pay or PhonePe first.');
                return;
            }

            const selected = upiMethods[selectedMethod];
            selectedQrPanel.style.display = 'block';
            selectedMethodText.textContent = selected.label + ' selected. Scan this QR and complete payment.';

            if (selected.qrExists && selected.qrPath) {
                selectedQrImage.src = selected.qrPath;
                selectedQrImage.style.display = 'inline-block';
                selectedQrMissing.style.display = 'none';
            } else {
                selectedQrImage.style.display = 'none';
                selectedQrMissing.style.display = 'block';
                selectedQrMissing.textContent = selected.missingHint;
            }

            selectedQrPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        async function refreshStatus() {
            try {
                const url = '/api/payments/status/' + encodeURIComponent(bookingReference) + '?token=' + encodeURIComponent(token);
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                if (!response.ok || !data || data.status !== 'ok') {
                    return;
                }
                applyStatus(data.payment_status || 'pending_payment', data.detail || '');
            } catch (error) {
                console.error(error);
            }
        }

        if (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                if (!selectedMethod) {
                    setMessage('error', 'Please select UPI app and click payment button before submitting confirmation.');
                    return;
                }

                const formData = new FormData(form);
                const payload = {};
                formData.forEach((value, key) => {
                    payload[key] = String(value);
                });

                try {
                    const response = await fetch('/api/payments/acknowledge', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();

                    if (!response.ok || !data || data.status !== 'ok') {
                        setMessage('error', (data && data.detail) ? data.detail : 'Could not submit payment acknowledgement.');
                        return;
                    }

                    setMessage('success', data.detail || 'Payment acknowledgement submitted.');
                    await refreshStatus();
                } catch (error) {
                    console.error(error);
                    setMessage('error', 'Network issue while submitting acknowledgement. Please retry.');
                }
            });
        }

        refreshStatus();
        setInterval(refreshStatus, 12000);
    })();
</script>
