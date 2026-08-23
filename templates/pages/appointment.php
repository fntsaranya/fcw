<?php
$paymentConfig = $paymentConfig ?? [];
$feeInr = (string) ($paymentConfig['fee_inr'] ?? '0');
$oldInput = $oldInput ?? [];
$minDate = date('Y-m-d');
?>
<section class="hero-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
    <div class="container">
        <div class="glass-card" style="max-width: 860px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 0.8rem;">Book Appointment</h2>

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
                        <select id="preferred_time" name="preferred_time" style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);">
                            <option value="">Select a time slot</option>
                            <?php
                            $slots = [
                                "10:00 AM - 10:30 AM", "10:30 AM - 11:00 AM",
                                "11:00 AM - 11:30 AM", "11:30 AM - 12:00 PM",
                                "12:00 PM - 12:30 PM", "12:30 PM - 01:00 PM",
                                "01:00 PM - 01:30 PM", "01:30 PM - 02:00 PM",
                                "02:00 PM - 02:30 PM", "02:30 PM - 03:00 PM",
                                "03:00 PM - 03:30 PM", "03:30 PM - 04:00 PM",
                                "04:00 PM - 04:30 PM", "04:30 PM - 05:00 PM",
                                "05:00 PM - 05:30 PM", "05:30 PM - 06:00 PM"
                            ];
                            foreach ($slots as $slot) {
                                $selected = (string) ($oldInput['preferred_time'] ?? '') === $slot ? 'selected' : '';
                                echo '<option value="' . e($slot) . '" ' . $selected . '>' . e($slot) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="text-align: left;">
                    <label for="concern" style="display: block; margin-bottom: 0.4rem; font-weight: 500;">Primary Concern *</label>
                    <textarea id="concern" name="concern" rows="5" required style="width: 100%; padding: 0.9rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.8); font-family: var(--font-body);"><?= e((string) ($oldInput['concern'] ?? '')) ?></textarea>
                </div>

                <button type="submit" class="btn-primary" style="font-size: 1.05rem; padding: 1rem; margin-top: 0.6rem; width: 100%;">Continue</button>
            </form>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookedSlots = <?= json_encode($bookedSlots ?? []) ?>;
        const dateInput = document.getElementById('preferred_date');
        const timeSelect = document.getElementById('preferred_time');
        const allOptions = Array.from(timeSelect.options);

        function updateAvailableSlots() {
            const selectedDate = dateInput.value;
            const bookedForDate = bookedSlots[selectedDate] || [];

            allOptions.forEach(option => {
                if (option.value === "") return;
                
                if (bookedForDate.includes(option.value)) {
                    option.style.display = 'none';
                    option.disabled = true;
                    if (timeSelect.value === option.value) {
                        timeSelect.value = "";
                    }
                } else {
                    option.style.display = '';
                    option.disabled = false;
                }
            });
        }

        dateInput.addEventListener('change', updateAvailableSlots);
        if (dateInput.value) {
            updateAvailableSlots();
        }
    });
</script>
