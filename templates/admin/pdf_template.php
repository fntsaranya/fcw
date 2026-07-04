<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Functional Timeline</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #6a8e23; /* FCW primary green-ish color, can adjust */
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }
        .section-title {
            color: #c93072; /* matches the pink in PDF */
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2ccda; /* light pinkish */
        }
        tr:nth-child(even) {
            background-color: #dcedc8; /* light greenish */
        }
        .grid-2 {
            width: 100%;
        }
        .grid-2 td {
            border: none;
            padding: 4px 0;
            width: 50%;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Functional Chronic Wellness</h1>
        <p>Functional Timeline</p>
    </div>

    <table class="grid-2">
        <tr>
            <td><span class="label">Patient Name:</span> <?= htmlspecialchars((string)($patient_name ?? '')) ?></td>
            <td><span class="label">Date:</span> <?= htmlspecialchars((string)($form_date ?? '')) ?></td>
        </tr>
        <tr>
            <td><span class="label">Age:</span> <?= htmlspecialchars((string)($age ?? '')) ?></td>
            <td></td>
        </tr>
        <tr>
            <td><span class="label">Sex:</span> <?= htmlspecialchars((string)($sex ?? '')) ?></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Address:</span> <?= htmlspecialchars((string)($address ?? '')) ?></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Country:</span> <?= htmlspecialchars((string)($surrounding_area ?? '')) ?></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Occupation:</span> <?= htmlspecialchars((string)($occupation ?? '')) ?></td>
        </tr>

        <tr>
            <td colspan="2"><span class="label">Dietary Preference:</span> <?= htmlspecialchars((string)($dietary_preference ?? '')) ?></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Covid Vaccination:</span> <?= htmlspecialchars((string)($covid_vaccination ?? '')) ?></td>
        </tr>
    </table>

    <div class="section-title">Measurements :</div>
    <div>Ht: <?= htmlspecialchars((string)($height ?? '')) ?> | Wt: <?= htmlspecialchars((string)($weight ?? '')) ?> | BMI: <?= htmlspecialchars((string)($bmi ?? '')) ?> | Hip: <?= htmlspecialchars((string)($hip_circumference ?? '')) ?> cm | Waist: <?= htmlspecialchars((string)($waist_circumference ?? '')) ?> cm</div>

    <div class="section-title">Food</div>
    <div><span class="label">Food Sensitivities:</span> <?= nl2br(htmlspecialchars((string)($food_sensitivities ?? ''))) ?></div>
    <div><span class="label">Food Allergies:</span> <?= nl2br(htmlspecialchars((string)($food_allergies ?? ''))) ?></div>
    <div><span class="label">Crave/ Binge:</span> <?= nl2br(htmlspecialchars((string)($crave_binge ?? ''))) ?></div>
    <div><span class="label">Any Diet Followed:</span> <?= nl2br(htmlspecialchars((string)($diet_followed ?? ''))) ?></div>

    <div class="section-title">Other Sensitivities</div>
    <div><?= nl2br(htmlspecialchars((string)($other_sensitivities ?? ''))) ?></div>

    <div class="section-title">Toxicities</div>
    <div><?= nl2br(htmlspecialchars((string)($toxicities ?? ''))) ?></div>



    <div class="section-title">Sleep</div>
    <div><?= !empty($sleep_regular) ? 'Regular' : 'Disturbed' ?>
        <?php if (!empty($sleep_details)): ?> - <?= htmlspecialchars((string)($sleep_details ?? '')) ?><?php endif; ?>
    </div>

    <div class="section-title">Family History</div>
    <table class="grid-2">
        <tr><td><span class="label">Mother:</span> <?= htmlspecialchars((string)($fm_mother ?? '')) ?></td><td><span class="label">Father:</span> <?= htmlspecialchars((string)($fm_father ?? '')) ?></td></tr>
        <tr><td><span class="label">Sister:</span> <?= htmlspecialchars((string)($fm_sister ?? '')) ?></td><td><span class="label">Brother:</span> <?= htmlspecialchars((string)($fm_brother ?? '')) ?></td></tr>
        <tr><td><span class="label">Grandparents:</span> <?= htmlspecialchars((string)($fm_grandparents ?? '')) ?></td><td><span class="label">Spouse:</span> <?= htmlspecialchars((string)($fm_spouse ?? '')) ?></td></tr>
        <tr><td colspan="2"><span class="label">Children:</span> <?= htmlspecialchars((string)($fm_children ?? '')) ?></td></tr>
    </table>

    <div class="section-title">Current Concern</div>
    <div><?= nl2br(htmlspecialchars((string)($current_concern ?? ''))) ?></div>

    <div style="page-break-before: always;"></div>

    <div class="section-title">Health Issues</div>
    <?php if (!empty($health_issues) && is_array($health_issues)): ?>
    <table>
        <thead>
            <tr>
                <th>Health Issue</th>
                <th>Detail</th>
                <th>Start Date</th>
                <th>Treatment</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($health_issues as $hi): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($hi['issue'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($hi['detail'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($hi['start_date'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($hi['treatment'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="section-title">History</div>
    <?php if (!empty($timeline_history) && is_array($timeline_history)): ?>
    <table>
        <tbody>
            <?php foreach ($timeline_history as $th): ?>
                <tr>
                    <td style="width: 30%; font-weight: bold;"><?= htmlspecialchars((string)($th['stage'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($th['detail'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div style="page-break-before: always;"></div>

    <div class="section-title">Specific Questions</div>
    <?php if (!empty($specific_questions) && is_array($specific_questions)): ?>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Questions</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($specific_questions as $sq): ?>
                <tr>
                    <td style="font-weight: bold;"><?= htmlspecialchars((string)($sq['question'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($sq['detail'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="section-title">Current Medications/Supplements</div>
    <?php if (!empty($medications) && is_array($medications)): ?>
    <table>
        <thead>
            <tr>
                <th>Medications</th>
                <th>Morning</th>
                <th>Lunch</th>
                <th>Dinner</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medications as $med): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($med['name'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($med['morning'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($med['lunch'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($med['dinner'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</body>
</html>
