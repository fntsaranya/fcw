<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Config;
use FCW\Core\View;
use FCW\Repositories\ContactRepository;
use FCW\Repositories\EnquiryRegistrationRepository;
use FCW\Repositories\WebinarRepository;
use FCW\Services\MailService;
use Throwable;

final class ContactController
{
    public function __construct(
        private readonly ContactRepository $contacts = new ContactRepository(),
        private readonly WebinarRepository $webinars = new WebinarRepository(),
        private readonly EnquiryRegistrationRepository $enquiryRegistrations = new EnquiryRegistrationRepository()
    ) {
    }

    public function form(?string $successMessage = null, ?string $errorMessage = null, ?string $warningMessage = null): void
    {
        $webinar = [];
        try {
            $webinar = $this->webinars->getActive();
        } catch (Throwable $e) {
            error_log('Error loading active webinar: ' . $e->getMessage());
        }

        View::render('pages/contact', [
            'activePage' => 'enquiry',
            'webinar' => $webinar,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'warningMessage' => $warningMessage,
        ]);
    }

    public function submit(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($message === '') {
            $message = 'Webinar / Enquiry Registration';
        }

        if ($name === '' || $email === '' || $phone === '') {
            $this->form(null, 'All fields are required. Please complete the form and try again.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->form(null, 'Please enter a valid email address.');
            return;
        }

        $webinar = [];
        try {
            $webinar = $this->webinars->getActive();
        } catch (Throwable $e) {
            error_log('Error loading active webinar on submit: ' . $e->getMessage());
        }

        $envFee = (float) (Config::enquiryPayment()['fee_inr'] ?? 0.00);
        $feeInr = isset($webinar['fee_inr']) && (float) $webinar['fee_inr'] > 0.00
            ? (float) $webinar['fee_inr']
            : ($envFee > 0.00 ? $envFee : 0.00);
        $clientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');

        // 1. Create Enquiry / Webinar Registration
        $registration = null;
        try {
            $registration = $this->enquiryRegistrations->createRegistration(
                $name,
                $email,
                $phone,
                $message,
                $webinar,
                $feeInr,
                $clientIp,
                $userAgent
            );
        } catch (Throwable $exception) {
            error_log('Enquiry registration DB error: ' . $exception->getMessage());
            $this->form(null, 'Sorry, registration is temporarily unavailable. Please try again later.');
            return;
        }

        // 2. Also record in contacts table for backwards compatibility
        try {
            $this->contacts->create($name, $email, $phone, $message . ' [Webinar: ' . ($webinar['title'] ?? '') . ']');
        } catch (Throwable $e) {
            error_log('Contact sync error: ' . $e->getMessage());
        }

        // 3. Send email notification (non-blocking)
        try {
            MailService::sendContactNotification($name, $email, $phone, $message);
        } catch (Throwable $e) {
            error_log('Mail notification warning: ' . $e->getMessage());
        }

        // 4. Determine redirect based on fee
        if ($feeInr > 0.00) {
            $paymentUrl = sprintf(
                '/enquiry/payment?ref=%s&token=%s',
                rawurlencode((string) $registration['registration_reference']),
                rawurlencode((string) $registration['ack_token'])
            );
            View::redirect($paymentUrl);
            return;
        }

        // If free, redirect immediately to designated WhatsApp Group
        $whatsappLink = !empty($webinar['whatsapp_group_link'])
            ? (string) $webinar['whatsapp_group_link']
            : (string) (Config::contact()['WHATSAPP_GROUP'] ?? '#');

        if (!empty($registration['id'])) {
            $this->enquiryRegistrations->markWhatsAppRedirected((int) $registration['id']);
        }

        View::redirect($whatsappLink);
    }
}
