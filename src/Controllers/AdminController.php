<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Flash;
use FCW\Core\View;
use FCW\Repositories\AssessmentRepository;
use FCW\Repositories\AppointmentRepository;
use FCW\Repositories\BlogRepository;
use FCW\Repositories\ContactRepository;
use FCW\Services\AdminAuth;
use FCW\Services\AssessmentCatalog;
use FCW\Services\AssessmentScoringService;
use InvalidArgumentException;
use Throwable;

final class AdminController
{
    public function __construct(
        private readonly ContactRepository $contacts = new ContactRepository(),
        private readonly AssessmentRepository $assessments = new AssessmentRepository(),
        private readonly BlogRepository $blogs = new BlogRepository(),
        private readonly AppointmentRepository $appointments = new AppointmentRepository()
    ) {
    }

    public function dashboard(): void
    {
        $activeTab = (string) ($_GET['tab'] ?? 'contacts');
        if (!in_array($activeTab, ['contacts', 'assessments', 'blogs', 'appointments'], true)) {
            $activeTab = 'contacts';
        }

        $contacts = [];
        $assessments = [];
        $blogs = [];
        $appointments = [];
        $failedSections = [];

        try {
            $contacts = $this->contacts->all();
        } catch (Throwable $exception) {
            $failedSections[] = 'contacts';
            error_log('Admin contacts load error: ' . $exception->getMessage());
        }

        try {
            $assessments = $this->assessments->all();
        } catch (Throwable $exception) {
            $failedSections[] = 'assessments';
            error_log('Admin assessments load error: ' . $exception->getMessage());
        }

        try {
            $blogs = $this->blogs->all();
        } catch (Throwable $exception) {
            $failedSections[] = 'blogs';
            error_log('Admin blogs load error: ' . $exception->getMessage());
        }

        try {
            $appointments = $this->appointments->all();
        } catch (Throwable $exception) {
            $failedSections[] = 'appointments';
            error_log('Admin appointments load error: ' . $exception->getMessage());
        }

        $inlineErrorMessage = null;
        if (!empty($failedSections)) {
            $inlineErrorMessage = sprintf(
                'Some admin sections failed to load (%s). Please check database schema and retry.',
                implode(', ', $failedSections)
            );
        }

        View::render('pages/admin_contacts', [
            'activePage' => '',
            'activeTab' => $activeTab,
            'contacts' => $contacts,
            'assessments' => $assessments,
            'blogs' => $blogs,
            'appointments' => $appointments,
            'totalContacts' => count($contacts),
            'totalAssessments' => count($assessments),
            'totalBlogs' => count($blogs),
            'totalAppointments' => count($appointments),
            'booleanFields' => AssessmentCatalog::booleanFields(),
            'fieldLabels' => AssessmentCatalog::fieldLabels(),
            'inlineErrorMessage' => $inlineErrorMessage,
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

    public function addBlog(): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=blogs');
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));

        if ($title === '' || $content === '') {
            Flash::add('error', 'Blog title and content are required.');
            View::redirect('/admin/contacts?tab=blogs');
        }

        try {
            $imageUrl = $this->normalizeImageUrl((string) ($_POST['image_url'] ?? ''));
            $this->blogs->create($title, $content, $imageUrl);
            Flash::add('success', 'Blog post added successfully.');
        } catch (InvalidArgumentException $exception) {
            Flash::add('error', $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('Add blog error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to add blog post at the moment.');
        }

        View::redirect('/admin/contacts?tab=blogs');
    }

    public function editBlog(int $blogId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=blogs');
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));

        if ($title === '' || $content === '') {
            Flash::add('error', 'Blog title and content are required.');
            View::redirect('/admin/contacts?tab=blogs');
        }

        try {
            $imageUrl = $this->normalizeImageUrl((string) ($_POST['image_url'] ?? ''));
            $this->blogs->update($blogId, $title, $content, $imageUrl);
            Flash::add('success', 'Blog post updated successfully.');
        } catch (InvalidArgumentException $exception) {
            Flash::add('error', $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('Edit blog error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to update blog post at the moment.');
        }

        View::redirect('/admin/contacts?tab=blogs');
    }

    public function deleteBlog(int $blogId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=blogs');
        }

        try {
            $this->blogs->delete($blogId);
            Flash::add('success', 'Blog post deleted successfully.');
        } catch (Throwable $exception) {
            error_log('Delete blog error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to delete blog post at the moment.');
        }

        View::redirect('/admin/contacts?tab=blogs');
    }

    public function verifyAppointment(int $appointmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=appointments');
        }

        $note = trim((string) ($_POST['note'] ?? ''));

        try {
            $this->appointments->markVerified($appointmentId, $note === '' ? null : $note);
            Flash::add('success', 'Appointment payment marked as verified.');
        } catch (Throwable $exception) {
            error_log('Verify appointment error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to verify appointment payment at the moment.');
        }

        View::redirect('/admin/contacts?tab=appointments');
    }

    public function rejectAppointment(int $appointmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=appointments');
        }

        $note = trim((string) ($_POST['note'] ?? ''));

        try {
            $this->appointments->markRejected($appointmentId, $note === '' ? null : $note);
            Flash::add('success', 'Appointment payment marked as rejected.');
        } catch (Throwable $exception) {
            error_log('Reject appointment error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to reject appointment payment at the moment.');
        }

        View::redirect('/admin/contacts?tab=appointments');
    }

    public function deleteAppointment(int $appointmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=appointments');
        }

        try {
            $this->appointments->delete($appointmentId);
            Flash::add('success', 'Appointment record deleted successfully.');
        } catch (Throwable $exception) {
            error_log('Delete appointment error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to delete appointment record at the moment.');
        }

        View::redirect('/admin/contacts?tab=appointments');
    }

    public function downloadPatientPdf(int $appointmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=appointments');
        }

        $intakeRepo = new \FCW\Repositories\PatientIntakeRepository();
        $intake = $intakeRepo->findByAppointmentId($appointmentId);

        if (!$intake) {
            Flash::add('error', 'No patient intake found for this appointment.');
            View::redirect('/admin/contacts?tab=appointments');
        }

        if (!class_exists('\\Dompdf\\Dompdf')) {
            Flash::add('error', 'PDF generation library is not installed. Please run composer install.');
            View::redirect('/admin/contacts?tab=appointments');
        }

        ob_start();
        extract($intake, EXTR_SKIP);
        include BASE_PATH . '/templates/admin/pdf_template.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "FCW_Timeline_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $intake['patient_name']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }

    private function normalizeImageUrl(string $imageUrlRaw): ?string
    {
        $imageUrl = trim($imageUrlRaw);
        if ($imageUrl === '') {
            return null;
        }

        if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Image URL must be a valid URL.');
        }

        return $imageUrl;
    }

    private function isPinValid(string $pin): bool
    {
        return AdminAuth::verifyPin(trim($pin));
    }
}
