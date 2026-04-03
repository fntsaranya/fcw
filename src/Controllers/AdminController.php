<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Flash;
use FCW\Core\View;
use FCW\Repositories\AssessmentRepository;
use FCW\Repositories\ContactRepository;
use FCW\Services\AdminAuth;
use FCW\Services\AssessmentCatalog;
use FCW\Services\AssessmentScoringService;
use Throwable;

final class AdminController
{
    public function __construct(
        private readonly ContactRepository $contacts = new ContactRepository(),
        private readonly AssessmentRepository $assessments = new AssessmentRepository()
    ) {
    }

    public function dashboard(): void
    {
        $activeTab = (string) ($_GET['tab'] ?? 'contacts');
        if (!in_array($activeTab, ['contacts', 'assessments'], true)) {
            $activeTab = 'contacts';
        }

        try {
            $contacts = $this->contacts->all();
            $assessments = $this->assessments->all();
        } catch (Throwable $exception) {
            error_log('Admin dashboard DB error: ' . $exception->getMessage());
            View::render('pages/admin_contacts', [
                'activePage' => '',
                'activeTab' => $activeTab,
                'contacts' => [],
                'assessments' => [],
                'totalContacts' => 0,
                'totalAssessments' => 0,
                'booleanFields' => AssessmentCatalog::booleanFields(),
                'fieldLabels' => AssessmentCatalog::fieldLabels(),
                'inlineErrorMessage' => 'Failed to load admin data. Please try again.',
            ]);
            return;
        }

        View::render('pages/admin_contacts', [
            'activePage' => '',
            'activeTab' => $activeTab,
            'contacts' => $contacts,
            'assessments' => $assessments,
            'totalContacts' => count($contacts),
            'totalAssessments' => count($assessments),
            'booleanFields' => AssessmentCatalog::booleanFields(),
            'fieldLabels' => AssessmentCatalog::fieldLabels(),
            'inlineErrorMessage' => null,
        ]);
    }

    public function addContact(): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=contacts');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));

        if ($name === '' || $email === '' || $message === '') {
            Flash::add('error', 'Name, email and message are required.');
            View::redirect('/admin/contacts?tab=contacts');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::add('error', 'Please provide a valid email address.');
            View::redirect('/admin/contacts?tab=contacts');
        }

        try {
            $this->contacts->create($name, $email, $phone, $message);
            Flash::add('success', 'Contact added successfully.');
        } catch (Throwable $exception) {
            error_log('Add contact error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to add contact at the moment.');
        }

        View::redirect('/admin/contacts?tab=contacts');
    }

    public function editContact(int $contactId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=contacts');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));

        if ($name === '' || $email === '' || $message === '') {
            Flash::add('error', 'Name, email and message are required.');
            View::redirect('/admin/contacts?tab=contacts');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::add('error', 'Please provide a valid email address.');
            View::redirect('/admin/contacts?tab=contacts');
        }

        try {
            $this->contacts->update($contactId, $name, $email, $phone, $message);
            Flash::add('success', 'Contact updated successfully.');
        } catch (Throwable $exception) {
            error_log('Edit contact error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to update contact at the moment.');
        }

        View::redirect('/admin/contacts?tab=contacts');
    }

    public function deleteContact(int $contactId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=contacts');
        }

        try {
            $this->contacts->delete($contactId);
            Flash::add('success', 'Contact deleted successfully.');
        } catch (Throwable $exception) {
            error_log('Delete contact error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to delete contact at the moment.');
        }

        View::redirect('/admin/contacts?tab=contacts');
    }

    public function editAssessment(int $assessmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=assessments');
        }

        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));

        if ($fullName === '' || $email === '') {
            Flash::add('error', 'Full name and email are required for assessments.');
            View::redirect('/admin/contacts?tab=assessments');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::add('error', 'Please provide a valid email address.');
            View::redirect('/admin/contacts?tab=assessments');
        }

        $scored = AssessmentScoringService::fromCheckboxInput($_POST);

        try {
            $this->assessments->update(
                $assessmentId,
                $fullName,
                $email,
                $phone,
                $scored['score'],
                $scored['interpretation'],
                $scored['fieldValues']
            );
            Flash::add('success', 'Assessment updated successfully.');
        } catch (Throwable $exception) {
            error_log('Edit assessment error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to update assessment right now.');
        }

        View::redirect('/admin/contacts?tab=assessments');
    }

    public function deleteAssessment(int $assessmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=assessments');
        }

        try {
            $this->assessments->delete($assessmentId);
            Flash::add('success', 'Assessment deleted successfully.');
        } catch (Throwable $exception) {
            error_log('Delete assessment error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to delete assessment at the moment.');
        }

        View::redirect('/admin/contacts?tab=assessments');
    }

    private function isPinValid(string $pin): bool
    {
        return AdminAuth::verifyPin(trim($pin));
    }
}
