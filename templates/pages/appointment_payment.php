<?php
declare(strict_types=1);

$booking = is_array($booking ?? null) ? $booking : [];
$reference = (string) ($booking['booking_reference'] ?? '');
$paymentStatus = (string) ($booking['payment_status'] ?? 'pending_payment');
$amount = (float) ($booking['amount_inr'] ?? 0);
$amountDisplay = abs($amount - floor($amount)) < 0.00001
    ? number_format($amount, 0)
    : number_format($amount, 2);
$currency = (string) ($booking['currency'] ?? 'INR');
$preferredDate = trim((string) ($booking['preferred_date'] ?? ''));
$preferredTime = trim((string) ($booking['preferred_time'] ?? ''));
$paymentConfigured = !empty($paymentConfigured);
$latestTransaction = is_array($latestTransaction ?? null) ? $latestTransaction : [];

$statusLabels = [
    'pending_payment' => 'Awaiting payment',
    'payment_initiated' => 'Payment initiated',
    'payment_authorized' => 'Payment authorized',
    'payment_verified' => 'Payment successful',
    'payment_failed' => 'Payment failed',
    'payment_partially_refunded' => 'Partially refunded',
    'payment_refunded' => 'Refunded',
    'payment_rejected' => 'Payment rejected',
];
$statusLabel = $statusLabels[$paymentStatus] ?? 'Awaiting payment';
$paymentComplete = in_array(
    $paymentStatus,
    ['payment_verified', 'payment_partially_refunded', 'payment_refunded'],
    true
);
?>
<section class="section payment-section">
    <div class="container payment-container">
        <header class="payment-heading">
            <p class="payment-eyebrow">Booking <?= e($reference) ?></p>
            <h1>Complete Payment</h1>
        </header>

        <div class="payment-shell">
            <dl class="payment-summary">
                <div>
                    <dt>Name</dt>
                    <dd><?= e((string) ($booking['full_name'] ?? '-')) ?></dd>
                </div>
                <div>
                    <dt>Preferred date</dt>
                    <dd><?= e($preferredDate !== '' ? date('d M Y', strtotime($preferredDate)) : '-') ?></dd>
                </div>
                <div>
                    <dt>Preferred time</dt>
                    <dd><?= e($preferredTime !== '' ? $preferredTime : 'To be confirmed') ?></dd>
                </div>
                <div class="payment-total">
                    <dt>Consultation fee</dt>
                    <dd><?= e($currency) ?> <?= e($amountDisplay) ?></dd>
                </div>
            </dl>

            <div
                id="paymentStatus"
                class="payment-status"
                data-status="<?= e($paymentStatus) ?>"
                role="status"
                aria-live="polite"
            >
                <span class="payment-status-dot" aria-hidden="true"></span>
                <span>
                    <strong id="paymentStatusLabel"><?= e($statusLabel) ?></strong>
                    <span id="paymentStatusDetail">
                        <?php if ($paymentComplete): ?>
                            Your transaction has been securely recorded.
                        <?php elseif (!$paymentConfigured): ?>
                            Online payment is temporarily unavailable.
                        <?php else: ?>
                            Use Razorpay to complete your payment securely.
                        <?php endif; ?>
                    </span>
                </span>
            </div>

            <div id="paymentMessage" class="payment-message" hidden></div>

            <button
                id="payButton"
                type="button"
                class="btn-primary payment-button"
                <?= (!$paymentConfigured || $paymentComplete) ? 'disabled' : '' ?>
            >
                Pay
            </button>

            <?php if (!empty($latestTransaction['gateway_payment_id'])): ?>
                <p class="payment-id">
                    Transaction ID: <strong id="gatewayPaymentId"><?= e((string) $latestTransaction['gateway_payment_id']) ?></strong>
                </p>
            <?php else: ?>
                <p class="payment-id" id="paymentIdRow" hidden>
                    Transaction ID: <strong id="gatewayPaymentId"></strong>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .payment-section {
        padding-top: 3.5rem;
        padding-bottom: 4.5rem;
    }

    .payment-container {
        max-width: 720px;
    }

    .payment-heading {
        margin-bottom: 1.5rem;
    }

    .payment-heading h1 {
        color: var(--primary-color);
        font-size: 3rem;
        line-height: 1.1;
        letter-spacing: 0;
        margin: 0;
    }

    .payment-eyebrow {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0;
        margin: 0 0 0.45rem;
        text-transform: uppercase;
    }

    .payment-shell {
        background: #ffffff;
        border: 1px solid #dbe3df;
        border-radius: 8px;
        box-shadow: 0 18px 45px rgba(31, 41, 55, 0.08);
        padding: 1.5rem;
    }

    .payment-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin: 0;
    }

    .payment-summary > div {
        border-bottom: 1px solid #e5e7eb;
        min-width: 0;
        padding: 0.85rem 0;
    }

    .payment-summary > div:nth-child(even) {
        padding-left: 1.25rem;
    }

    .payment-summary dt {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .payment-summary dd {
        color: #1f2937;
        font-size: 1rem;
        font-weight: 650;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .payment-summary .payment-total {
        border-bottom: 0;
        grid-column: 1 / -1;
        padding: 1.15rem 0 0;
    }

    .payment-summary .payment-total dd {
        color: var(--primary-color);
        font-size: 1.65rem;
        font-weight: 750;
    }

    .payment-status {
        align-items: flex-start;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #334155;
        display: flex;
        gap: 0.7rem;
        margin-top: 1.4rem;
        padding: 0.9rem 1rem;
    }

    .payment-status-dot {
        background: #d97706;
        border-radius: 50%;
        flex: 0 0 9px;
        height: 9px;
        margin-top: 0.4rem;
        width: 9px;
    }

    .payment-status strong,
    .payment-status span {
        display: block;
    }

    .payment-status strong {
        color: inherit;
        font-size: 0.95rem;
        margin-bottom: 0.15rem;
    }

    #paymentStatusDetail {
        font-size: 0.86rem;
    }

    .payment-status[data-status="payment_verified"],
    .payment-status[data-status="payment_partially_refunded"] {
        background: #ecfdf5;
        border-color: #86efac;
        color: #166534;
    }

    .payment-status[data-status="payment_verified"] .payment-status-dot,
    .payment-status[data-status="payment_partially_refunded"] .payment-status-dot {
        background: #16a34a;
    }

    .payment-status[data-status="payment_failed"],
    .payment-status[data-status="payment_rejected"] {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .payment-status[data-status="payment_failed"] .payment-status-dot,
    .payment-status[data-status="payment_rejected"] .payment-status-dot {
        background: #dc2626;
    }

    .payment-status[data-status="payment_authorized"],
    .payment-status[data-status="payment_initiated"] {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .payment-status[data-status="payment_authorized"] .payment-status-dot,
    .payment-status[data-status="payment_initiated"] .payment-status-dot {
        background: #2563eb;
    }

    .payment-status[data-status="payment_refunded"] {
        background: #f1f5f9;
        color: #475569;
    }

    .payment-status[data-status="payment_refunded"] .payment-status-dot {
        background: #64748b;
    }

    .payment-message {
        border-radius: 8px;
        font-size: 0.9rem;
        margin-top: 1rem;
        padding: 0.8rem 0.9rem;
    }

    .payment-message.is-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    .payment-message.is-success {
        background: #ecfdf5;
        border: 1px solid #86efac;
        color: #166534;
    }

    .payment-button {
        min-height: 50px;
        margin-top: 1rem;
        width: 100%;
    }

    .payment-button:disabled {
        cursor: not-allowed;
        opacity: 0.55;
    }

    .payment-id {
        color: #64748b;
        font-size: 0.8rem;
        margin: 0.8rem 0 0;
        overflow-wrap: anywhere;
        text-align: center;
    }

    @media (max-width: 620px) {
        .payment-section {
            padding-top: 2.25rem;
        }

        .payment-heading h1 {
            font-size: 2.25rem;
        }

        .payment-shell {
            padding: 1.1rem;
        }

        .payment-summary {
            grid-template-columns: 1fr;
        }

        .payment-summary > div:nth-child(even) {
            padding-left: 0;
        }
    }
</style>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (function () {
        const bookingReference = <?= json_encode($reference) ?>;
        const ackToken = <?= json_encode((string) ($ackToken ?? '')) ?>;
        const paymentConfigured = <?= json_encode($paymentConfigured) ?>;
        const payButton = document.getElementById('payButton');
        const statusBox = document.getElementById('paymentStatus');
        const statusLabel = document.getElementById('paymentStatusLabel');
        const statusDetail = document.getElementById('paymentStatusDetail');
        const messageBox = document.getElementById('paymentMessage');
        const paymentIdRow = document.getElementById('paymentIdRow');
        const gatewayPaymentId = document.getElementById('gatewayPaymentId');

        const statusLabels = {
            pending_payment: 'Awaiting payment',
            payment_initiated: 'Payment initiated',
            payment_authorized: 'Payment authorized',
            payment_verified: 'Payment successful',
            payment_failed: 'Payment failed',
            payment_partially_refunded: 'Partially refunded',
            payment_refunded: 'Refunded',
            payment_rejected: 'Payment rejected'
        };

        const terminalStatuses = [
            'payment_verified',
            'payment_partially_refunded',
            'payment_refunded'
        ];

        function setBusy(isBusy) {
            payButton.disabled = isBusy || !paymentConfigured;
            payButton.textContent = isBusy ? 'Please wait...' : 'Pay';
        }

        function showMessage(type, text) {
            messageBox.hidden = false;
            messageBox.className = 'payment-message ' + (type === 'error' ? 'is-error' : 'is-success');
            messageBox.textContent = text;
        }

        function setStatus(status, detail, paymentId) {
            statusBox.dataset.status = status;
            statusLabel.textContent = statusLabels[status] || 'Awaiting payment';
            statusDetail.textContent = detail || '';

            if (paymentId && gatewayPaymentId) {
                gatewayPaymentId.textContent = paymentId;
                if (paymentIdRow) {
                    paymentIdRow.hidden = false;
                }
            }

            if (terminalStatuses.includes(status)) {
                payButton.disabled = true;
                payButton.textContent = 'Paid';
            }
        }

        async function postJson(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();

            if (!response.ok || !data || data.status !== 'ok') {
                throw new Error((data && data.detail) ? data.detail : 'Payment request failed.');
            }

            return data;
        }

        async function verifyPayment(response) {
            const data = await postJson('/api/payments/verify', {
                booking_reference: bookingReference,
                ack_token: ackToken,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature
            });

            setStatus(data.payment_status, data.detail, response.razorpay_payment_id);
            showMessage('success', data.detail || 'Payment confirmation is processing.');
        }

        async function recordFailure(error) {
            const metadata = (error && error.metadata) ? error.metadata : {};
            if (!metadata.order_id || !metadata.payment_id) {
                return;
            }

            try {
                const data = await postJson('/api/payments/failure', {
                    booking_reference: bookingReference,
                    ack_token: ackToken,
                    razorpay_order_id: metadata.order_id,
                    razorpay_payment_id: metadata.payment_id
                });
                setStatus(data.payment_status, data.detail, metadata.payment_id);
            } catch (requestError) {
                console.error(requestError);
            }
        }

        async function openCheckout() {
            if (typeof window.Razorpay !== 'function') {
                showMessage('error', 'Secure checkout could not load. Check your connection and try again.');
                return;
            }

            setBusy(true);
            messageBox.hidden = true;

            try {
                const orderResponse = await postJson('/api/payments/order', {
                    booking_reference: bookingReference,
                    ack_token: ackToken
                });
                const checkout = orderResponse.checkout;
                let completed = false;

                const razorpay = new window.Razorpay({
                    key: checkout.key_id,
                    amount: checkout.amount,
                    currency: checkout.currency,
                    name: checkout.name,
                    description: checkout.description,
                    order_id: checkout.order_id,
                    prefill: checkout.prefill,
                    readonly: {
                        name: true,
                        email: true,
                        contact: true
                    },
                    notes: {
                        booking_reference: bookingReference
                    },
                    theme: {
                        color: checkout.theme_color
                    },
                    retry: {
                        enabled: true
                    },
                    modal: {
                        confirm_close: true,
                        ondismiss: function () {
                            if (!completed) {
                                setBusy(false);
                            }
                        }
                    },
                    handler: async function (response) {
                        completed = true;
                        try {
                            await verifyPayment(response);
                        } catch (error) {
                            console.error(error);
                            showMessage('error', error.message || 'Payment confirmation is still processing.');
                        } finally {
                            if (!terminalStatuses.includes(statusBox.dataset.status)) {
                                setBusy(false);
                            }
                        }
                    }
                });

                razorpay.on('payment.failed', async function (response) {
                    completed = true;
                    const error = response && response.error ? response.error : {};
                    await recordFailure(error);
                    setStatus('payment_failed', error.description || 'Payment was not completed.');
                    showMessage('error', error.description || 'Payment was not completed. Please try again.');
                    setBusy(false);
                });

                razorpay.open();
            } catch (error) {
                console.error(error);
                showMessage('error', error.message || 'Could not start secure payment.');
                setBusy(false);
            }
        }

        async function refreshStatus() {
            try {
                const url = '/api/payments/status/' + encodeURIComponent(bookingReference)
                    + '?token=' + encodeURIComponent(ackToken);
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                if (response.ok && data && data.status === 'ok') {
                    setStatus(data.payment_status, data.detail, data.gateway_payment_id);
                }
            } catch (error) {
                console.error(error);
            }
        }

        payButton.addEventListener('click', openCheckout);
        refreshStatus();
        window.setInterval(refreshStatus, 10000);
    })();
</script>
