<?php
declare(strict_types=1);

$reference = (string) ($appointment['booking_reference'] ?? '');
$intakeData = $intake ?? [];

function val(string $key, array $intakeData, array $appointment, string $fallback = '') {
    if (isset($intakeData[$key])) {
        return (string) $intakeData[$key];
    }
    if ($key === 'patient_name' && isset($appointment['full_name'])) {
        return (string) $appointment['full_name'];
    }
    if ($key === 'form_date') {
        return date('Y-m-d');
    }
    if ($key === 'current_concern' && isset($appointment['concern'])) {
        return (string) $appointment['concern'];
    }
    return $fallback;
}

$fam = function($member) use ($intakeData) {
    $map = [
        'Mother' => 'fm_mother', 'Father' => 'fm_father', 'Sister' => 'fm_sister',
        'Brother' => 'fm_brother', 'Grandparents' => 'fm_grandparents',
        'Spouse' => 'fm_spouse', 'Children' => 'fm_children'
    ];
    $key = $map[$member] ?? '';
    return e((string)($intakeData[$key] ?? ''));
};

$healthIssuesJson = !empty($intakeData['health_issues']) ? json_encode($intakeData['health_issues'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '[]';
$timelineHistoryJson = !empty($intakeData['timeline_history']) ? json_encode($intakeData['timeline_history'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '[]';
$medicationsJson = !empty($intakeData['medications']) ? json_encode($intakeData['medications'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '[]';
$questionsJson = !empty($intakeData['specific_questions']) ? json_encode($intakeData['specific_questions'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '[]';
$sleep = val('sleep_regular', $intakeData, $appointment, 'yes');
$sex = val('sex', $intakeData, $appointment);
?>
<section class="section" style="background:#f8fafc; padding-top:4rem; padding-bottom:5rem;">
    <div class="container" style="max-width:900px;">
        
        <header style="margin-bottom:2rem; text-align:center;">
            <p style="color:#64748b; font-weight:700; text-transform:uppercase; font-size:0.85rem; margin-bottom:0.5rem;">Admin Edit | Booking <?= e($reference) ?></p>
            <h1 style="color:var(--primary-color); font-size:2.5rem; margin-top:0;">Edit Patient Intake Form</h1>
            <p style="color:#475569;">Modifying intake details for <?= e(val('patient_name', $intakeData, $appointment)) ?></p>
        </header>

        <form id="mainIntakeForm" method="post" action="/admin/appointments/intake/update/<?= (int)($appointment['id'] ?? 0) ?>" class="glass-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:2.5rem; box-shadow:0 10px 25px rgba(0,0,0,0.05);">
            
            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:0;">1. Patient Details</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Patient Name *</label>
                    <input type="text" name="patient_name" value="<?= e(val('patient_name', $intakeData, $appointment)) ?>" required style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Age</label>
                    <input type="number" name="age" value="<?= e(val('age', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Sex</label>
                    <select name="sex" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; background:#fff;">
                        <option value="">Select...</option>
                        <option value="Male" <?= $sex==='Male'?'selected':'' ?>>Male</option>
                        <option value="Female" <?= $sex==='Female'?'selected':'' ?>>Female</option>
                        <option value="Other" <?= $sex==='Other'?'selected':'' ?>>Other</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Form Date</label>
                    <input type="date" name="form_date" value="<?= e(val('form_date', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>

                <div style="grid-column:1 / -1;">
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Address</label>
                    <input type="text" name="address" value="<?= e(val('address', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Country</label>
                    <input type="text" name="surrounding_area" value="<?= e(val('surrounding_area', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Occupation</label>
                    <input type="text" name="occupation" value="<?= e(val('occupation', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>

                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Dietary Preference</label>
                    <input type="text" name="dietary_preference" value="<?= e(val('dietary_preference', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">COVID Vaccination</label>
                    <input type="text" name="covid_vaccination" value="<?= e(val('covid_vaccination', $intakeData, $appointment)) ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Height</label>
                    <input type="text" name="height" value="<?= e(val('height', $intakeData, $appointment)) ?>" placeholder="e.g. 175 cm" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Weight</label>
                    <input type="text" name="weight" value="<?= e(val('weight', $intakeData, $appointment)) ?>" placeholder="e.g. 70 kg" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Hip Circumference (cm)</label>
                    <input type="number" step="0.1" name="hip_circumference" value="<?= e(val('hip_circumference', $intakeData, $appointment)) ?>" placeholder="e.g. 95" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Waist Circumference (cm)</label>
                    <input type="number" step="0.1" name="waist_circumference" value="<?= e(val('waist_circumference', $intakeData, $appointment)) ?>" placeholder="e.g. 85" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">2. Current Concerns</h3>
            <div style="margin-bottom:2rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Current Health Concern(s)</label>
                <textarea name="current_concern" rows="3" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('current_concern', $intakeData, $appointment)) ?></textarea>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">3. Lifestyle & Sensitivities</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Food Sensitivities</label>
                    <textarea name="food_sensitivities" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('food_sensitivities', $intakeData, $appointment)) ?></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Food Allergies</label>
                    <textarea name="food_allergies" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('food_allergies', $intakeData, $appointment)) ?></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Crave / Binge Foods</label>
                    <textarea name="crave_binge" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('crave_binge', $intakeData, $appointment)) ?></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Diet Currently Followed</label>
                    <textarea name="diet_followed" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('diet_followed', $intakeData, $appointment)) ?></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Other Sensitivities (e.g. Mold, Chemical, Smells)</label>
                    <textarea name="other_sensitivities" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('other_sensitivities', $intakeData, $appointment)) ?></textarea>
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Known Toxicities (e.g. Heavy Metals)</label>
                    <textarea name="toxicities" rows="2" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem; resize:vertical;"><?= e(val('toxicities', $intakeData, $appointment)) ?></textarea>
                </div>

                <div style="background:#f1f5f9; padding:1rem; border-radius:8px;">
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Is Sleep Regular? (7-8 hours)</label>
                    <div style="margin-bottom:0.5rem;">
                        <label style="margin-right:1rem;"><input type="radio" name="sleep_regular" value="yes" <?= $sleep==='yes'?'checked':'' ?>> Yes</label>
                        <label><input type="radio" name="sleep_regular" value="no" <?= $sleep==='no'?'checked':'' ?>> No</label>
                    </div>
                    <input type="text" name="sleep_details" value="<?= e(val('sleep_details', $intakeData, $appointment)) ?>" placeholder="If no, provide details..." style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
            </div>

            <h3 style="color:var(--primary-color); border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.5rem; margin-top:2rem;">4. Family History</h3>
            <p style="font-size:0.9rem; color:#64748b; margin-bottom:1rem;">Briefly note relevant health conditions for family members.</p>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Mother</label>
                    <input type="text" name="fm_mother" value="<?= $fam('Mother') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Father</label>
                    <input type="text" name="fm_father" value="<?= $fam('Father') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Sister</label>
                    <input type="text" name="fm_sister" value="<?= $fam('Sister') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Brother</label>
                    <input type="text" name="fm_brother" value="<?= $fam('Brother') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Grandparents</label>
                    <input type="text" name="fm_grandparents" value="<?= $fam('Grandparents') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Spouse</label>
                    <input type="text" name="fm_spouse" value="<?= $fam('Spouse') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.9rem;">Children</label>
                    <input type="text" name="fm_children" value="<?= $fam('Children') ?>" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1rem;">
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
                <button type="button" onclick="document.getElementById('saveIntakeModal').style.display='flex';" class="btn-primary" style="padding:1rem 3rem; font-size:1.1rem;">Save / Update Intake Form</button>
                <a href="/admin/contacts" style="display:inline-block; margin-left:1rem; padding:1rem 3rem; font-size:1.1rem; background:#64748b; color:white; border-radius:6px; text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
</section>

<div id="saveIntakeModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px; text-align:left;">
        <h3 style="margin-top:0; color:var(--primary-color);">Confirm Update</h3>
        <p style="margin-bottom:1rem; color:#475569;">Enter Admin PIN to securely update the intake form.</p>
        <div style="display:grid; gap:0.9rem;">
            <input type="password" id="modalPinInput" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="button" onclick="submitIntakeForm()" style="flex:1; border:none; border-radius:8px; background:#0369a1; color:white; padding:0.8rem; cursor:pointer;">Update</button>
                <button type="button" onclick="document.getElementById('saveIntakeModal').style.display='none'" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </div>
    </div>
</div>

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
    function escapeHtml(unsafe) {
        return (unsafe || '').toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function createRow(htmlContent) {
        const div = document.createElement('div');
        div.className = 'dynamic-row';
        div.innerHTML = htmlContent + `<button type="button" onclick="this.parentElement.remove()" title="Remove">&times;</button>`;
        return div;
    }

    function addHealthIssue(issue = '', detail = '', startDate = '', treatment = '') {
        const container = document.getElementById('healthIssuesContainer');
        container.appendChild(createRow(`
            <input type="text" name="hi_issue[]" placeholder="Issue / Diagnosis" value="${escapeHtml(issue)}" required>
            <input type="text" name="hi_detail[]" placeholder="Doctor / Additional Info" value="${escapeHtml(detail)}">
            <input type="text" name="hi_start_date[]" placeholder="Date Started (e.g. 2018)" value="${escapeHtml(startDate)}">
            <input type="text" name="hi_treatment[]" placeholder="Treatment / Resolution" value="${escapeHtml(treatment)}">
        `));
    }

    function addTimelineRow(stage = '', detail = '') {
        const container = document.getElementById('timelineContainer');
        
        const stages = [
            "Preconception/Pregnancy", "Birth to 1 Year", "1 to 5 Years",
            "6 to 12 Years", "Teenage Years", "20s", "30s", "40s", "50s", "60s and Above"
        ];
        
        let options = '<option value="">Select Stage...</option>';
        stages.forEach(s => {
            options += `<option value="${s}" ${s === stage ? 'selected' : ''}>${s}</option>`;
        });

        container.appendChild(createRow(`
            <select name="th_stage[]" style="max-width: 200px;" required>
                ${options}
            </select>
            <input type="text" name="th_detail[]" placeholder="Events, Stressors, Interventions (e.g. Move, Death, Antibiotics, Trauma)" value="${escapeHtml(detail)}">
        `));
    }

    function addMedication(name = '', morning = '', lunch = '', dinner = '') {
        const container = document.getElementById('medicationsContainer');
        container.appendChild(createRow(`
            <input type="text" name="med_name[]" placeholder="Medication or Supplement Name" style="flex: 2;" value="${escapeHtml(name)}" required>
            <input type="text" name="med_morning[]" placeholder="Morning (qty)" value="${escapeHtml(morning)}">
            <input type="text" name="med_lunch[]" placeholder="Lunch (qty)" value="${escapeHtml(lunch)}">
            <input type="text" name="med_dinner[]" placeholder="Dinner (qty)" value="${escapeHtml(dinner)}">
        `));
    }

    function addQuestion(question = '', detail = '') {
        const container = document.getElementById('questionsContainer');
        container.appendChild(createRow(`
            <input type="text" name="sq_question[]" placeholder="Question or Note" style="flex: 1;" value="${escapeHtml(question)}" required>
            <input type="text" name="sq_detail[]" placeholder="Additional details..." style="flex: 2;" value="${escapeHtml(detail)}">
        `));
    }

    document.addEventListener('DOMContentLoaded', () => {
        const healthIssues = <?= $healthIssuesJson ?>;
        const timelineHistory = <?= $timelineHistoryJson ?>;
        const medications = <?= $medicationsJson ?>;
        const specificQuestions = <?= $questionsJson ?>;

        if (healthIssues && healthIssues.length > 0) {
            healthIssues.forEach(item => {
                addHealthIssue(item.issue, item.detail, item.start_date, item.treatment);
            });
        } else {
            addHealthIssue();
        }

        if (timelineHistory && timelineHistory.length > 0) {
            timelineHistory.forEach(item => {
                addTimelineRow(item.stage, item.detail);
            });
        } else {
            addTimelineRow();
        }

        if (medications && medications.length > 0) {
            medications.forEach(item => {
                addMedication(item.name, item.morning, item.lunch, item.dinner);
            });
        } else {
            addMedication();
        }
        
        if (specificQuestions && specificQuestions.length > 0) {
            specificQuestions.forEach(item => {
                addQuestion(item.question, item.detail);
            });
        }
    });

    function submitIntakeForm() {
        const pin = document.getElementById('modalPinInput').value;
        if (!pin) {
            alert('PIN is required.');
            return;
        }
        const form = document.getElementById('mainIntakeForm');
        let pinInput = document.getElementById('mainPinInput');
        if (!pinInput) {
            pinInput = document.createElement('input');
            pinInput.type = 'hidden';
            pinInput.name = 'pin';
            pinInput.id = 'mainPinInput';
            form.appendChild(pinInput);
        }
        pinInput.value = pin;
        form.submit();
    }
</script>
