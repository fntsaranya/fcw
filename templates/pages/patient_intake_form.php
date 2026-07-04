<?php
declare(strict_types=1);

$reference = (string) ($booking['booking_reference'] ?? '');
$ackToken = (string) ($ackToken ?? '');
?>
<section class="section" style="background:#f8fafc; padding-top:4rem; padding-bottom:5rem;">
    <div class="container" style="max-width:900px;">
        
        <header style="margin-bottom:2rem; text-align:center;">
            <p style="color:#64748b; font-weight:700; text-transform:uppercase; font-size:0.85rem; margin-bottom:0.5rem;">Booking <?= e($reference) ?></p>
            <h1 style="color:var(--primary-color); font-size:2.5rem; margin-top:0;">Patient Intake Form</h1>
            <p style="color:#475569;">Please provide the following details to help us serve you better.</p>
        </header>

        <form method="post" action="/patient-intake?ref=<?= urlencode($reference) ?>&token=<?= urlencode($ackToken) ?>" class="glass-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:2.5rem; box-shadow:0 10px 25px rgba(0,0,0,0.05);">
            
            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:0;">1. Patient Details</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Patient Name *</label>
                    <input type="text" name="patient_name" value="<?= e((string) ($booking['full_name'] ?? '')) ?>" required style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Age</label>
                    <input type="number" name="age" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Sex</label>
                    <select name="sex" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; background:#fff;">
                        <option value="">Select...</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Form Date</label>
                    <input type="date" name="form_date" value="<?= date('Y-m-d') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>

                <div style="grid-column:1 / -1;">
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Address</label>
                    <input type="text" name="address" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Country</label>
                    <input type="text" name="surrounding_area" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Occupation</label>
                    <input type="text" name="occupation" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>

                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Dietary Preference</label>
                    <input type="text" name="dietary_preference" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">COVID Vaccination</label>
                    <input type="text" name="covid_vaccination" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Height</label>
                    <input type="text" name="height" placeholder="e.g. 175 cm" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Weight</label>
                    <input type="text" name="weight" placeholder="e.g. 70 kg" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Hip Circumference (cm)</label>
                    <input type="number" step="0.1" name="hip_circumference" placeholder="e.g. 95" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Waist Circumference (cm)</label>
                    <input type="number" step="0.1" name="waist_circumference" placeholder="e.g. 85" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">2. Current Concerns</h3>
            <div style="margin-bottom:2rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Current Health Concern(s)</label>
                <textarea name="current_concern" rows="3" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e((string) ($booking['concern'] ?? '')) ?></textarea>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">3. Lifestyle & Sensitivities</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Food Sensitivities</label>
                    <textarea name="food_sensitivities" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Food Allergies</label>
                    <textarea name="food_allergies" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Crave / Binge Foods</label>
                    <textarea name="crave_binge" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Diet Currently Followed</label>
                    <textarea name="diet_followed" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Other Sensitivities (e.g. Mold, Chemical, Smells)</label>
                    <textarea name="other_sensitivities" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Known Toxicities (e.g. Heavy Metals)</label>
                    <textarea name="toxicities" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
                </div>

                <div style="background:#f1f5f9; padding:1rem; border-radius:8px;">
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Is Sleep Regular? (7-8 hours)</label>
                    <div style="margin-bottom:0.5rem;">
                        <label style="margin-right:1rem;"><input type="radio" name="sleep_regular" value="yes" checked> Yes</label>
                        <label><input type="radio" name="sleep_regular" value="no"> No</label>
                    </div>
                    <input type="text" name="sleep_details" placeholder="If no, provide details..." style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">4. Family History</h3>
            <p style="font-size:0.9rem; color:#64748b; margin-bottom:1rem;">Briefly note relevant health conditions for family members.</p>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Mother</label>
                    <input type="text" name="fm_mother" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Father</label>
                    <input type="text" name="fm_father" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Sister</label>
                    <input type="text" name="fm_sister" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Brother</label>
                    <input type="text" name="fm_brother" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Grandparents</label>
                    <input type="text" name="fm_grandparents" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Spouse</label>
                    <input type="text" name="fm_spouse" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Children</label>
                    <input type="text" name="fm_children" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">5. Health Issues / Interventions / Diagnoses</h3>
            <div id="healthIssuesContainer" style="margin-bottom:1rem;">
                <!-- Rows will be added here -->
            </div>
            <button type="button" onclick="addHealthIssue()" style="background:#f1f5f9; border:1px dashed #94a3b8; color:#475569; padding:0.75rem 1rem; border-radius:6px; cursor:pointer; font-weight:600; width:100%; margin-bottom:2rem;">+ Add Health Issue</button>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">6. Timeline History</h3>
            <p style="font-size:0.9rem; color:#64748b; margin-bottom:1rem;">Note significant life events, stressors, or symptoms across different stages of life.</p>
            <div id="timelineContainer" style="margin-bottom:1rem;">
                <!-- Rows will be added here -->
            </div>
            <button type="button" onclick="addTimelineRow()" style="background:#f1f5f9; border:1px dashed #94a3b8; color:#475569; padding:0.75rem 1rem; border-radius:6px; cursor:pointer; font-weight:600; width:100%; margin-bottom:2rem;">+ Add Timeline Event</button>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">7. Medications & Supplements</h3>
            <div id="medicationsContainer" style="margin-bottom:1rem;">
                <!-- Rows will be added here -->
            </div>
            <button type="button" onclick="addMedication()" style="background:#f1f5f9; border:1px dashed #94a3b8; color:#475569; padding:0.75rem 1rem; border-radius:6px; cursor:pointer; font-weight:600; width:100%; margin-bottom:2rem;">+ Add Medication/Supplement</button>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">8. Additional Questions</h3>
            <div id="questionsContainer" style="margin-bottom:1rem;">
                <!-- Rows will be added here -->
            </div>
            <button type="button" onclick="addQuestion()" style="background:#f1f5f9; border:1px dashed #94a3b8; color:#475569; padding:0.75rem 1rem; border-radius:6px; cursor:pointer; font-weight:600; width:100%; margin-bottom:2rem;">+ Add Specific Question</button>



            <div style="text-align:center; margin-top:3rem;">
                <button type="submit" class="btn-primary" style="padding:1rem 3rem; font-size:1.1rem;">Submit Intake Form</button>
            </div>
        </form>
    </div>
</section>

<style>
    .dynamic-row {
        display: flex; gap: 1rem; margin-bottom: 1rem; align-items: flex-start;
    }
    .dynamic-row input, .dynamic-row select {
        padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; flex: 1; min-width: 0;
    }
    .dynamic-row button {
        background: #ef4444; color: white; border: none; padding: 0.75rem 1rem; border-radius: 6px; cursor: pointer; font-weight: bold;
    }
    @media (max-width: 768px) {
        .dynamic-row { flex-direction: column; gap: 0.5rem; background:#f8fafc; padding:1rem; border-radius:8px; }
        .dynamic-row input, .dynamic-row select { width: 100%; }
        .dynamic-row button { width: 100%; }
    }
</style>

<script>
    function createRow(htmlContent) {
        const div = document.createElement('div');
        div.className = 'dynamic-row';
        div.innerHTML = htmlContent + `<button type="button" onclick="this.parentElement.remove()" title="Remove">&times;</button>`;
        return div;
    }

    function addHealthIssue() {
        const container = document.getElementById('healthIssuesContainer');
        container.appendChild(createRow(`
            <input type="text" name="hi_issue[]" placeholder="Issue / Diagnosis" required>
            <input type="text" name="hi_detail[]" placeholder="Doctor / Additional Info">
            <input type="text" name="hi_start_date[]" placeholder="Date Started (e.g. 2018)">
            <input type="text" name="hi_treatment[]" placeholder="Treatment / Resolution">
        `));
    }

    function addTimelineRow() {
        const container = document.getElementById('timelineContainer');
        container.appendChild(createRow(`
            <select name="th_stage[]" style="max-width: 200px;" required>
                <option value="">Select Stage...</option>
                <option value="Preconception/Pregnancy">Preconception/Pregnancy</option>
                <option value="Birth to 1 Year">Birth to 1 Year</option>
                <option value="1 to 5 Years">1 to 5 Years</option>
                <option value="6 to 12 Years">6 to 12 Years</option>
                <option value="Teenage Years">Teenage Years</option>
                <option value="20s">20s</option>
                <option value="30s">30s</option>
                <option value="40s">40s</option>
                <option value="50s">50s</option>
                <option value="60s and Above">60s and Above</option>
            </select>
            <input type="text" name="th_detail[]" placeholder="Events, Stressors, Interventions (e.g. Move, Death, Antibiotics, Trauma)">
        `));
    }

    function addMedication() {
        const container = document.getElementById('medicationsContainer');
        container.appendChild(createRow(`
            <input type="text" name="med_name[]" placeholder="Medication or Supplement Name" style="flex: 2;" required>
            <input type="text" name="med_morning[]" placeholder="Morning (qty)">
            <input type="text" name="med_lunch[]" placeholder="Lunch (qty)">
            <input type="text" name="med_dinner[]" placeholder="Dinner (qty)">
        `));
    }

    function addQuestion() {
        const container = document.getElementById('questionsContainer');
        container.appendChild(createRow(`
            <input type="text" name="sq_question[]" placeholder="Question or Note" style="flex: 1;" required>
            <input type="text" name="sq_detail[]" placeholder="Additional details..." style="flex: 2;">
        `));
    }

    // Add some initial empty rows
    document.addEventListener('DOMContentLoaded', () => {
        addHealthIssue();
        addTimelineRow();
        addMedication();
    });
</script>
