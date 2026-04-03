<?php
declare(strict_types=1);

namespace FCW;

use FCW\Controllers\AdminController;
use FCW\Controllers\AssessmentController;
use FCW\Controllers\BlogController;
use FCW\Controllers\ContactController;
use FCW\Controllers\HealthController;
use FCW\Controllers\PageController;
use FCW\Core\Database;
use FCW\Core\View;
use Throwable;

final class App
{
    public static function run(): void
    {
        self::bootstrapDatabase();

        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path = rawurldecode((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/'));
        if ($path !== '/') {
            $path = rtrim($path, '/');
            if ($path === '') {
                $path = '/';
            }
        }

        $pages = new PageController();
        $contact = new ContactController();
        $blog = new BlogController();
        $assessment = new AssessmentController();
        $admin = new AdminController();
        $health = new HealthController();

        if ($method === 'GET' && $path === '/') {
            $pages->home();
            return;
        }

        if ($method === 'GET' && $path === '/about') {
            $pages->about();
            return;
        }

        if ($method === 'GET' && $path === '/services') {
            $pages->services();
            return;
        }

        if ($method === 'GET' && $path === '/resources') {
            $pages->resources();
            return;
        }

        if ($method === 'GET' && $path === '/contact') {
            $contact->form();
            return;
        }

        if ($method === 'POST' && $path === '/contact') {
            $contact->submit();
            return;
        }

        if ($method === 'GET' && $path === '/blogs') {
            $blog->list();
            return;
        }

        if ($method === 'GET' && preg_match('#^/blogs/(\d+)$#', $path, $matches) === 1) {
            $blog->detail((int) $matches[1]);
            return;
        }

        if ($method === 'POST' && $path === '/admin/verify') {
            $blog->verifyAdmin();
            return;
        }

        if ($method === 'GET' && $path === '/admin/blog') {
            View::redirect('/admin/contacts?tab=blogs');
            return;
        }

        if ($method === 'POST' && $path === '/admin/blog') {
            $admin->addBlog();
            return;
        }

        if ($method === 'POST' && $path === '/admin/blogs/add') {
            $admin->addBlog();
            return;
        }

        if ($method === 'POST' && preg_match('#^/admin/blogs/edit/(\d+)$#', $path, $matches) === 1) {
            $admin->editBlog((int) $matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/admin/blogs/delete/(\d+)$#', $path, $matches) === 1) {
            $admin->deleteBlog((int) $matches[1]);
            return;
        }

        if ($method === 'GET' && $path === '/assessment') {
            $assessment->form();
            return;
        }

        if ($method === 'POST' && $path === '/assessment') {
            $assessment->submit();
            return;
        }

        if ($method === 'GET' && $path === '/admin/contacts') {
            $admin->dashboard();
            return;
        }

        if ($method === 'POST' && $path === '/admin/contacts/add') {
            $admin->addContact();
            return;
        }

        if ($method === 'POST' && preg_match('#^/admin/contacts/edit/(\d+)$#', $path, $matches) === 1) {
            $admin->editContact((int) $matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/admin/contacts/delete/(\d+)$#', $path, $matches) === 1) {
            $admin->deleteContact((int) $matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/admin/assessments/edit/(\d+)$#', $path, $matches) === 1) {
            $admin->editAssessment((int) $matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/admin/assessments/delete/(\d+)$#', $path, $matches) === 1) {
            $admin->deleteAssessment((int) $matches[1]);
            return;
        }

        if ($method === 'GET' && $path === '/health') {
            $health->health();
            return;
        }

        if ($method === 'GET' && $path === '/health/db') {
            $health->dbHealth();
            return;
        }

        View::render('pages/not_found', [
            'activePage' => '',
            'title' => 'Page not found',
            'message' => 'The page you requested does not exist.',
        ], 404);
    }

    private static function bootstrapDatabase(): void
    {
        try {
            Database::ensureSchema();
        } catch (Throwable $exception) {
            error_log('Database bootstrap warning: ' . $exception->getMessage());
        }
    }
}
