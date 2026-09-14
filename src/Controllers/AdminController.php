<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Flash;
use FCW\Core\View;
use FCW\Repositories\AssessmentRepository;
use FCW\Repositories\AppointmentRepository;
use FCW\Repositories\BlogRepository;
use FCW\Repositories\ContactRepository;
use FCW\Repositories\EnquiryRegistrationRepository;
use FCW\Repositories\WebinarRepository;
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
        private readonly AppointmentRepository $appointments = new AppointmentRepository(),
        private readonly WebinarRepository $webinars = new WebinarRepository(),
        private readonly EnquiryRegistrationRepository $enquiryRegistrations = new EnquiryRegistrationRepository()
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
        $enquiryRegistrations = [];
        $activeWebinar = [];
        $failedSections = [];

        try {
            $contacts = $this->contacts->all();
        } catch (Throwable $exception) {
            $failedSections[] = 'contacts';
            error_log('Admin contacts load error: ' . $exception->getMessage());
        }

        try {
            $enquiryRegistrations = $this->enquiryRegistrations->all();
        } catch (Throwable $exception) {
            $failedSections[] = 'enquiry registrations';
            error_log('Admin enquiry registrations load error: ' . $exception->getMessage());
        }

        try {
            $activeWebinar = $this->webinars->getActive();
        } catch (Throwable $exception) {
            $failedSections[] = 'webinar details';
            error_log('Admin active webinar load error: ' . $exception->getMessage());
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
            'enquiryRegistrations' => $enquiryRegistrations,
            'activeWebinar' => $activeWebinar,
            'assessments' => $assessments,
            'blogs' => $blogs,
            'appointments' => $appointments,
            'totalContacts' => count($contacts) + count($enquiryRegistrations),
            'totalEnquiries' => count($enquiryRegistrations),
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

    public function editIntake(int $appointmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid PIN. Authentication required.');
            View::redirect('/admin/contacts?tab=appointments');
            return;
        }
        
        $appointment = $this->appointments->findById($appointmentId);
        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found.";
            return;
        }

        $intakeRepo = new \FCW\Repositories\PatientIntakeRepository();
        $intake = $intakeRepo->findByAppointmentId($appointmentId);

        \FCW\Core\View::render('pages/admin_intake_form', [
            'appointment' => $appointment,
            'intake' => $intake
        ]);
    }

    public function updateIntake(int $appointmentId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid PIN. Authentication required.');
            View::redirect('/admin/contacts?tab=appointments');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $appointment = $this->appointments->findById($appointmentId);
        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found.";
            return;
        }

        $intakeRepo = new \FCW\Repositories\PatientIntakeRepository();

        $buildJson = function ($keys) {
            $data = [];
            $firstKey = array_key_first($keys);
            if (!empty($_POST[$firstKey]) && is_array($_POST[$firstKey])) {
                $count = count($_POST[$firstKey]);
                for ($i = 0; $i < $count; $i++) {
                    $item = [];
                    $hasValue = false;
                    foreach ($keys as $key => $jsonKey) {
                        $val = trim((string) ($_POST[$key][$i] ?? ''));
                        $item[$jsonKey] = $val;
                        if ($val !== '') {
                            $hasValue = true;
                        }
                    }
                    if ($hasValue) {
                        $data[] = $item;
                    }
                }
            }
            return $data;
        };

        $healthIssues = $buildJson(['hi_issue' => 'issue', 'hi_detail' => 'detail', 'hi_start_date' => 'start_date', 'hi_treatment' => 'treatment']);
        $timelineHistory = $buildJson(['th_stage' => 'stage', 'th_detail' => 'detail']);
        $specificQuestions = $buildJson(['sq_question' => 'question', 'sq_detail' => 'detail']);
        $medications = $buildJson(['med_name' => 'name', 'med_morning' => 'morning', 'med_lunch' => 'lunch', 'med_dinner' => 'dinner']);

        try {
            $intakeRepo->upsert(
                $appointmentId,
                trim((string) ($_POST['patient_name'] ?? '')),
                trim((string) ($_POST['form_date'] ?? '')) ?: null,
                !empty($_POST['age']) ? (int) $_POST['age'] : null,
                trim((string) ($_POST['sex'] ?? '')) ?: null,
                trim((string) ($_POST['address'] ?? '')) ?: null,
                trim((string) ($_POST['surrounding_area'] ?? '')) ?: null,
                trim((string) ($_POST['occupation'] ?? '')) ?: null,
                trim((string) ($_POST['dietary_preference'] ?? '')) ?: null,
                trim((string) ($_POST['covid_vaccination'] ?? '')) ?: null,
                trim((string) ($_POST['weight'] ?? '')) ?: null,
                trim((string) ($_POST['height'] ?? '')) ?: null,
                null, // BMI calculated elsewhere if needed, or null since PDF generates it
                trim((string) ($_POST['hip_circumference'] ?? '')) ?: null,
                trim((string) ($_POST['waist_circumference'] ?? '')) ?: null,
                trim((string) ($_POST['food_sensitivities'] ?? '')) ?: null,
                trim((string) ($_POST['food_allergies'] ?? '')) ?: null,
                trim((string) ($_POST['crave_binge'] ?? '')) ?: null,
                trim((string) ($_POST['diet_followed'] ?? '')) ?: null,
                trim((string) ($_POST['other_sensitivities'] ?? '')) ?: null,
                trim((string) ($_POST['toxicities'] ?? '')) ?: null,
                ($_POST['sleep_regular'] ?? 'yes') === 'yes',
                trim((string) ($_POST['sleep_details'] ?? '')) ?: null,
                trim((string) ($_POST['fm_mother'] ?? '')) ?: null,
                trim((string) ($_POST['fm_father'] ?? '')) ?: null,
                trim((string) ($_POST['fm_sister'] ?? '')) ?: null,
                trim((string) ($_POST['fm_brother'] ?? '')) ?: null,
                trim((string) ($_POST['fm_grandparents'] ?? '')) ?: null,
                trim((string) ($_POST['fm_spouse'] ?? '')) ?: null,
                trim((string) ($_POST['fm_children'] ?? '')) ?: null,
                trim((string) ($_POST['current_concern'] ?? '')) ?: null,
                $healthIssues,
                $timelineHistory,
                $specificQuestions,
                $medications
            );

            $_SESSION['flash_message'] = "Intake form updated successfully.";
            header('Location: /admin/contacts?tab=appointments');
            exit;
        } catch (Throwable $e) {
            error_log("Error updating admin intake: " . $e->getMessage());
            echo "Error updating intake form: " . htmlspecialchars($e->getMessage());
            exit;
        }
    }

    public function updateWebinar(): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=contacts');
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $subtitle = trim((string) ($_POST['subtitle'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $eventDate = trim((string) ($_POST['event_date'] ?? ''));
        $eventTime = trim((string) ($_POST['event_time'] ?? ''));
        $venuePlatform = trim((string) ($_POST['venue_platform'] ?? 'Live Online'));
        $speaker = trim((string) ($_POST['speaker'] ?? ''));
        $feeInr = (float) ($_POST['fee_inr'] ?? 0.00);
        $whatsappGroupLink = trim((string) ($_POST['whatsapp_group_link'] ?? ''));
        $existingImageUrl = trim((string) ($_POST['existing_image_url'] ?? ''));
        $imageUrl = trim((string) ($_POST['image_url'] ?? '')) ?: $existingImageUrl;

        if ($title === '' || $description === '') {
            Flash::add('error', 'Webinar title and description are required.');
            View::redirect('/admin/contacts?tab=contacts');
        }

        // Handle promotional banner image upload
        if (isset($_FILES['banner_image']) && is_array($_FILES['banner_image']) && ($_FILES['banner_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $file = $_FILES['banner_image'];
            $maxBytes = 5 * 1024 * 1024; // 5 MB
            if ((int) $file['size'] > $maxBytes) {
                Flash::add('error', 'Promotional image size must be less than 5 MB.');
                View::redirect('/admin/contacts?tab=contacts');
            }

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, (string) $file['tmp_name']) : '';
            if ($finfo) {
                finfo_close($finfo);
            }

            if (!in_array($mime, $allowedMimes, true)) {
                Flash::add('error', 'Invalid image format. Allowed formats: JPG, PNG, WEBP, GIF.');
                View::redirect('/admin/contacts?tab=contacts');
            }

            $uploadDir = BASE_PATH . '/static/images/webinars';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }

            $ext = match ($mime) {
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'jpg',
            };

            $filename = 'webinar_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $destination = $uploadDir . '/' . $filename;

            if (move_uploaded_file((string) $file['tmp_name'], $destination)) {
                $imageUrl = '/static/images/webinars/' . $filename;
            } else {
                error_log('Failed to move uploaded webinar image to ' . $destination);
            }
        }

        try {
            $this->webinars->saveOrUpdateActive([
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'event_date' => $eventDate,
                'event_time' => $eventTime,
                'venue_platform' => $venuePlatform,
                'speaker' => $speaker,
                'fee_inr' => $feeInr,
                'whatsapp_group_link' => $whatsappGroupLink,
                'image_url' => $imageUrl,
            ]);

            Flash::add('success', 'Webinar details and promotional banner updated successfully.');
        } catch (Throwable $exception) {
            error_log('Update webinar error: ' . $exception->getMessage());
            Flash::add('error', 'Failed to update webinar details: ' . $exception->getMessage());
        }

        View::redirect('/admin/contacts?tab=contacts');
    }

    public function deleteEnquiry(int $enquiryId): void
    {
        if (!$this->isPinValid((string) ($_POST['pin'] ?? ''))) {
            Flash::add('error', 'Invalid Admin PIN');
            View::redirect('/admin/contacts?tab=contacts');
        }

        try {
            $this->enquiryRegistrations->delete($enquiryId);
            Flash::add('success', 'Enquiry / Webinar registration record deleted successfully.');
        } catch (Throwable $exception) {
            error_log('Delete enquiry error: ' . $exception->getMessage());
            Flash::add('error', 'Unable to delete registration at the moment.');
        }

        View::redirect('/admin/contacts?tab=contacts');
    }
}
