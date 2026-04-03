<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\View;
use FCW\Repositories\AssessmentRepository;
use FCW\Services\AssessmentCatalog;
use FCW\Services\AssessmentScoringService;
use FCW\Services\MailService;
use Throwable;

final class AssessmentController
{
    public function __construct(private readonly AssessmentRepository $assessments = new AssessmentRepository())
    {
    }

    public function form(): void
    {
        View::render('pages/assessment', [
            'activePage' => '',
            'sections' => AssessmentCatalog::sections(),
        ]);
    }

    public function submit(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));

        if ($name === '' || $email === '' || $phone === '') {
            View::render('pages/assessment', [
                'activePage' => '',
                'sections' => AssessmentCatalog::sections(),
                'errorMessage' => 'Name, email, and phone are required.',
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::render('pages/assessment', [
                'activePage' => '',
                'sections' => AssessmentCatalog::sections(),
                'errorMessage' => 'Please enter a valid email address.',
            ]);
            return;
        }

        $scored = AssessmentScoringService::fromQuestionInput($_POST);

        try {
            $assessmentId = $this->assessments->create(
                $name,
                $email,
                $phone,
                $scored['score'],
                $scored['interpretation'],
                $scored['fieldValues']
            );

            $savedAssessment = $this->assessments->find($assessmentId);
            if (is_array($savedAssessment)) {
                MailService::sendAssessmentNotification($savedAssessment);
            }
        } catch (Throwable $exception) {
            error_log('Assessment submission DB error: ' . $exception->getMessage());
            View::renderDatabaseError('Our assessment service is temporarily unavailable. Please try again later.');
            return;
        }

        View::render('pages/assessment_success', [
            'activePage' => '',
            'name' => $name,
            'score' => $scored['score'],
            'interpretation' => $scored['interpretation'],
        ]);
    }
}
