<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\View;
use FCW\Repositories\ContactRepository;
use FCW\Services\MailService;
use Throwable;

final class ContactController
{
    public function __construct(private readonly ContactRepository $contacts = new ContactRepository())
    {
    }

    public function form(?string $successMessage = null, ?string $errorMessage = null, ?string $warningMessage = null): void
    {
        View::render('pages/contact', [
            'activePage' => 'enquiry',
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
            $message = 'Enquiry from WhatsApp redirection';
        }

        if ($name === '' || $email === '' || $phone === '') {
            $this->form(null, 'All fields are required. Please complete the form and try again.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->form(null, 'Please enter a valid email address.');
            return;
        }

        try {
            $this->contacts->create($name, $email, $phone, $message);
        } catch (Throwable $exception) {
            error_log('Contact submission DB error: ' . $exception->getMessage());
            $this->form(null, 'Sorry, our message system is temporarily unavailable. Please try again later.');
            return;
        }

        $emailSent = MailService::sendContactNotification($name, $email, $phone, $message);

        $whatsappGroup = \FCW\Core\Config::contact()['WHATSAPP_GROUP'] ?? '#';
        \FCW\Core\View::redirect($whatsappGroup);
    }
}
