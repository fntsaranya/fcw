<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Flash;
use FCW\Core\View;
use FCW\Repositories\AppointmentRepository;
use FCW\Repositories\PatientIntakeRepository;
use Throwable;

final class PatientIntakeController
{
    private AppointmentRepository $appointments;
    private PatientIntakeRepository $intake;

    public function __construct()
    {
        $this->appointments = new AppointmentRepository();
        $this->intake = new PatientIntakeRepository();
    }

    public function show(): void
    {
        $reference = trim((string) ($_GET['ref'] ?? ''));
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($reference === '' || $token === '') {
            View::render('pages/not_found', [
                'activePage' => 'contact',
                'title' => 'Invalid link',
                'message' => 'The intake form link is invalid or incomplete.',
            ], 404);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $token);
        } catch (Throwable $exception) {
            error_log('Patient intake lookup error: ' . $exception->getMessage());
            View::renderDatabaseError('Could not load booking details right now.', 503);
            return;
        }

        if ($booking === null || $booking['payment_status'] !== 'payment_verified') {
            View::render('pages/not_found', [
                'activePage' => 'contact',
                'title' => 'Form Unavailable',
                'message' => 'This form is only available after a successful appointment payment.',
            ], 404);
            return;
        }

        // Check if form already submitted
        try {
            $existingIntake = $this->intake->findByAppointmentId((int) $booking['id']);
            if ($existingIntake !== null) {
                // If they already submitted, show thank you message
                View::render('pages/patient_intake_success', [
                    'activePage' => 'contact',
                    'booking' => $booking
                ]);
                return;
            }
        } catch (Throwable $exception) {
            error_log('Check existing intake error: ' . $exception->getMessage());
        }

        View::render('pages/patient_intake_form', [
            'activePage' => 'contact',
            'booking' => $booking,
            'ackToken' => $token,
        ]);
    }

    public function submit(): void
    {
        $reference = trim((string) ($_GET['ref'] ?? ''));
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($reference === '' || $token === '') {
            Flash::add('error', 'Invalid submission link.');
            View::redirect('/patient-intake?ref=' . urlencode($reference) . '&token=' . urlencode($token));
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $token);
        } catch (Throwable $exception) {
            error_log('Patient intake lookup error: ' . $exception->getMessage());
            View::renderDatabaseError('Could not load booking details right now.', 503);
            return;
        }

        if ($booking === null || $booking['payment_status'] !== 'payment_verified') {
            View::render('pages/not_found', [
                'activePage' => 'contact',
                'title' => 'Form Unavailable',
                'message' => 'This form is only available after a successful appointment payment.',
            ], 404);
            return;
        }

        try {
            // Arrays from dynamic form fields
            $healthIssues = [];
            if (!empty($_POST['hi_issue']) && is_array($_POST['hi_issue'])) {
                foreach ($_POST['hi_issue'] as $index => $issue) {
                    if (trim((string) $issue) !== '') {
                        $healthIssues[] = [
                            'issue' => trim((string) $issue),
                            'detail' => trim((string) ($_POST['hi_detail'][$index] ?? '')),
                            'start_date' => trim((string) ($_POST['hi_start_date'][$index] ?? '')),
                            'treatment' => trim((string) ($_POST['hi_treatment'][$index] ?? ''))
                        ];
                    }
                }
            }

            $timelineHistory = [];
            if (!empty($_POST['th_stage']) && is_array($_POST['th_stage'])) {
                foreach ($_POST['th_stage'] as $index => $stage) {
                    if (trim((string) $stage) !== '') {
                        $timelineHistory[] = [
                            'stage' => trim((string) $stage),
                            'detail' => trim((string) ($_POST['th_detail'][$index] ?? ''))
                        ];
                    }
                }
            }

            $specificQuestions = [];
            if (!empty($_POST['sq_question']) && is_array($_POST['sq_question'])) {
                foreach ($_POST['sq_question'] as $index => $question) {
                    if (trim((string) $question) !== '') {
                        $specificQuestions[] = [
                            'question' => trim((string) $question),
                            'detail' => trim((string) ($_POST['sq_detail'][$index] ?? ''))
                        ];
                    }
                }
            }

            $medications = [];
            if (!empty($_POST['med_name']) && is_array($_POST['med_name'])) {
                foreach ($_POST['med_name'] as $index => $medName) {
                    if (trim((string) $medName) !== '') {
                        $medications[] = [
                            'name' => trim((string) $medName),
                            'morning' => trim((string) ($_POST['med_morning'][$index] ?? '')),
                            'lunch' => trim((string) ($_POST['med_lunch'][$index] ?? '')),
                            'dinner' => trim((string) ($_POST['med_dinner'][$index] ?? ''))
                        ];
                    }
                }
            }

            $weight = empty($_POST['weight']) ? null : trim((string) $_POST['weight']);
            $height = empty($_POST['height']) ? null : trim((string) $_POST['height']);
            $bmi = null;
            if ($weight !== null && $height !== null) {
                $w = (float) $weight;
                $h = (float) $height;
                if ($h > 0 && $w > 0) {
                    $bmiVal = $w / (($h/100) * ($h/100));
                    $bmi = number_format($bmiVal, 1);
                }
            }

            $this->intake->create(
                (int) $booking['id'],
                trim((string) ($_POST['patient_name'] ?? '')),
                empty($_POST['form_date']) ? null : trim((string) $_POST['form_date']),
                empty($_POST['age']) ? null : (int) $_POST['age'],
                empty($_POST['sex']) ? null : trim((string) $_POST['sex']),
                empty($_POST['address']) ? null : trim((string) $_POST['address']),
                empty($_POST['surrounding_area']) ? null : trim((string) $_POST['surrounding_area']),
                empty($_POST['occupation']) ? null : trim((string) $_POST['occupation']),
                empty($_POST['dietary_preference']) ? null : trim((string) $_POST['dietary_preference']),
                empty($_POST['covid_vaccination']) ? null : trim((string) $_POST['covid_vaccination']),
                $weight,
                $height,
                $bmi,
                empty($_POST['hip_circumference']) ? null : trim((string) $_POST['hip_circumference']),
                empty($_POST['waist_circumference']) ? null : trim((string) $_POST['waist_circumference']),
                empty($_POST['food_sensitivities']) ? null : trim((string) $_POST['food_sensitivities']),
                empty($_POST['food_allergies']) ? null : trim((string) $_POST['food_allergies']),
                empty($_POST['crave_binge']) ? null : trim((string) $_POST['crave_binge']),
                empty($_POST['diet_followed']) ? null : trim((string) $_POST['diet_followed']),
                empty($_POST['other_sensitivities']) ? null : trim((string) $_POST['other_sensitivities']),
                empty($_POST['toxicities']) ? null : trim((string) $_POST['toxicities']),
                !isset($_POST['sleep_regular']) || $_POST['sleep_regular'] !== 'no',
                empty($_POST['sleep_details']) ? null : trim((string) $_POST['sleep_details']),
                empty($_POST['fm_mother']) ? null : trim((string) $_POST['fm_mother']),
                empty($_POST['fm_father']) ? null : trim((string) $_POST['fm_father']),
                empty($_POST['fm_sister']) ? null : trim((string) $_POST['fm_sister']),
                empty($_POST['fm_brother']) ? null : trim((string) $_POST['fm_brother']),
                empty($_POST['fm_grandparents']) ? null : trim((string) $_POST['fm_grandparents']),
                empty($_POST['fm_spouse']) ? null : trim((string) $_POST['fm_spouse']),
                empty($_POST['fm_children']) ? null : trim((string) $_POST['fm_children']),
                empty($_POST['current_concern']) ? null : trim((string) $_POST['current_concern']),
                $healthIssues,
                $timelineHistory,
                $specificQuestions,
                $medications
            );

            // Redirect to success page (via GET again)
            View::redirect('/patient-intake?ref=' . urlencode($reference) . '&token=' . urlencode($token));
        } catch (Throwable $exception) {
            error_log('Patient intake save error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to submit your form right now. Please try again.');
            View::redirect('/patient-intake?ref=' . urlencode($reference) . '&token=' . urlencode($token));
        }
    }
}
