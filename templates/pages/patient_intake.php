<?php
declare(strict_types=1);
$booking = $booking ?? [];
$ref = $_GET['ref'] ?? '';
$token = $_GET['token'] ?? '';
?>
<section class="section">
    <div class="container" style="max-width: 900px;">
        <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Functional Timeline & Patient Details</h1>
        
        <form method="POST" action="/patient-intake" class="form-container">
            <input type="hidden" name="ref" value="<?= e($ref) ?>">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            
            <fieldset class="form-section">
                <legend>General Information</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Patient Name *</label>
                        <input type="text" name="patient_name" value="<?= e($booking['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="form_date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age">
                    </div>
                    <div class="form-group">
                        <label>Sex</label>
                        <select name="sex">
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Timeline Created by</label>
                        <input type="text" name="timeline_created_by">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea name="address" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="surrounding_area">
                    </div>
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="occupation">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Previous Occupations</label>
                        <textarea name="previous_occupations" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Dietary Preference</label>
                        <select name="dietary_preference">
                            <option value="">Select...</option>
                            <option value="Vegetarian">Vegetarian</option>
                            <option value="Eggetarian">Eggetarian</option>
                            <option value="Omnivarian">Omnivarian</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Covid Vaccination</label>
                        <input type="text" name="covid_vaccination">
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Body Metrics</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Weight</label>
                        <input type="text" name="weight">
                    </div>
                    <div class="form-group">
                        <label>Height</label>
                        <input type="text" name="height">
                    </div>
                    <div class="form-group">
                        <label>MSQ Score</label>
                        <input type="text" name="msq">
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Food & Sensitivities</legend>
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Food Sensitivities</label>
                        <textarea name="food_sensitivities" rows="2"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Food Allergies</label>
                        <textarea name="food_allergies" rows="2"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Crave / Binge</label>
                        <textarea name="crave_binge" rows="2"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Any Diet Followed</label>
                        <textarea name="diet_followed" rows="2"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Other Sensitivities</label>
                        <textarea name="other_sensitivities" rows="2"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Toxicities</label>
                        <textarea name="toxicities" rows="2"></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Supplements & Sleep</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Animal Source Supplements</label>
                        <div style="display: flex; gap: 1rem; align-items: center; height: 42px;">
                            <label style="display: flex; gap: 0.5rem; margin:0;"><input type="radio" name="animal_supplements_yes_no" value="1"> Yes</label>
                            <label style="display: flex; gap: 0.5rem; margin:0;"><input type="radio" name="animal_supplements_yes_no" value="0" checked> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Supplement Details</label>
                        <input type="text" name="animal_supplements_details">
                    </div>
                    <div class="form-group">
                        <label>Sleep Quality</label>
                        <div style="display: flex; gap: 1rem; align-items: center; height: 42px;">
                            <label style="display: flex; gap: 0.5rem; margin:0;"><input type="radio" name="sleep_regular" value="1" checked> Regular</label>
                            <label style="display: flex; gap: 0.5rem; margin:0;"><input type="radio" name="sleep_regular" value="0"> Disturbed</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sleep Details</label>
                        <input type="text" name="sleep_details">
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Family History</legend>
                <div class="form-grid">
                    <div class="form-group"><label>Mother</label><input type="text" name="fm_mother"></div>
                    <div class="form-group"><label>Father</label><input type="text" name="fm_father"></div>
                    <div class="form-group"><label>Sister</label><input type="text" name="fm_sister"></div>
                    <div class="form-group"><label>Brother</label><input type="text" name="fm_brother"></div>
                    <div class="form-group"><label>Grandparents</label><input type="text" name="fm_grandparents"></div>
                    <div class="form-group"><label>Spouse</label><input type="text" name="fm_spouse"></div>
                    <div class="form-group"><label>Children</label><input type="text" name="fm_children"></div>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Current Concern</legend>
                <div class="form-group">
                    <textarea name="current_concern" rows="4"></textarea>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Health Issues</legend>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th>Health Issue</th>
                            <th>Detail</th>
                            <th>Start Date</th>
                            <th>Treatment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <tr>
                            <td><input type="text" name="health_issues[<?= $i ?>][issue]"></td>
                            <td><input type="text" name="health_issues[<?= $i ?>][detail]"></td>
                            <td><input type="text" name="health_issues[<?= $i ?>][start_date]"></td>
                            <td><input type="text" name="health_issues[<?= $i ?>][treatment]"></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </fieldset>

            <fieldset class="form-section">
                <legend>History</legend>
                <table class="form-table">
                    <tbody>
                        <?php 
                        $history_stages = [
                            'Birth', 'Infancy', '1 to 5 years', '6 to 12 years- Primary school',
                            'Adolescent 12 to 17', 'Menstrual History', '17 to 23', 'Date of Graduation',
                            'Marriage', '1st kid', '2nd kid', '3rd kid'
                        ];
                        foreach ($history_stages as $idx => $stage): 
                        ?>
                        <tr>
                            <td style="width: 250px; font-weight: 600;"><?= e($stage) ?>
                                <input type="hidden" name="timeline_history[<?= $idx ?>][stage]" value="<?= e($stage) ?>">
                            </td>
                            <td><input type="text" name="timeline_history[<?= $idx ?>][detail]"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </fieldset>

            <fieldset class="form-section">
                <legend>Other Details</legend>
                <div class="form-group">
                    <textarea name="other_details" rows="3"></textarea>
                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>Specific Questions</legend>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th style="width: 300px;">Questions</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $questions = [
                            'Tongue', 'Dental History', 'Covid History', 'Surgical history',
                            'Split AC or Moist Environment gives Runny Nose?',
                            'Does changing locations help?', 'Type of Water- Copper bottle-', 'Mold Exposure'
                        ];
                        foreach ($questions as $idx => $q): 
                        ?>
                        <tr>
                            <td style="font-weight: 600;"><?= e($q) ?>
                                <input type="hidden" name="specific_questions[<?= $idx ?>][question]" value="<?= e($q) ?>">
                            </td>
                            <td><input type="text" name="specific_questions[<?= $idx ?>][detail]"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </fieldset>

            <fieldset class="form-section">
                <legend>Current Medications / Supplements</legend>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th>Medications</th>
                            <th>Morning</th>
                            <th>Lunch</th>
                            <th>Dinner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <tr>
                            <td><input type="text" name="medications[<?= $i ?>][name]"></td>
                            <td><input type="text" name="medications[<?= $i ?>][morning]"></td>
                            <td><input type="text" name="medications[<?= $i ?>][lunch]"></td>
                            <td><input type="text" name="medications[<?= $i ?>][dinner]"></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </fieldset>

            <div style="margin-top: 2rem; text-align: center;">
                <button type="submit" class="btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">Submit Details</button>
            </div>
        </form>
    </div>
</section>

<style>
.form-container {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.form-section {
    border: none;
    border-top: 1px solid #e2e8f0;
    margin: 2rem 0 0 0;
    padding: 1.5rem 0 0 0;
}

.form-section:first-of-type {
    border-top: none;
    margin-top: 0;
    padding-top: 0;
}

.form-section legend {
    color: var(--primary-color);
    font-size: 1.25rem;
    font-weight: 600;
    padding: 0 0.5rem;
    margin-left: -0.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.25rem;
    margin-top: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #475569;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="date"],
.form-group select,
.form-group textarea {
    padding: 0.6rem;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-family: inherit;
    font-size: 0.95rem;
}

.form-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.form-table th,
.form-table td {
    padding: 0.5rem;
    border: 1px solid #cbd5e1;
    text-align: left;
}

.form-table th {
    background: #f1f5f9;
    font-weight: 600;
    color: #475569;
}

.form-table input[type="text"] {
    width: 100%;
    border: none;
    background: transparent;
    padding: 0.25rem;
    outline: none;
}
.form-table input[type="text"]:focus {
    background: #f8fafc;
}
</style>
