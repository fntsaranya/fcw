<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\View;
use FCW\Repositories\BlogRepository;
use FCW\Services\AdminAuth;
use Throwable;

final class BlogController
{
    public function __construct(private readonly BlogRepository $blogs = new BlogRepository())
    {
    }

    public function list(?string $successMessage = null, ?string $errorMessage = null): void
    {
        try {
            $posts = $this->blogs->all();
        } catch (Throwable $exception) {
            error_log('Blog listing DB error: ' . $exception->getMessage());
            View::render('pages/blog_list', [
                'activePage' => 'blogs',
                'posts' => [],
                'errorMessage' => 'Our blog service is temporarily unavailable. We are working to restore it!',
                'successMessage' => $successMessage,
            ]);
            return;
        }

        View::render('pages/blog_list', [
            'activePage' => 'blogs',
            'posts' => $posts,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function detail(int $blogId): void
    {
        try {
            $post = $this->blogs->find($blogId);
        } catch (Throwable $exception) {
            error_log('Blog detail DB error: ' . $exception->getMessage());
            View::renderDatabaseError('Could not retrieve the blog post at this time.', 503);
            return;
        }

        if ($post === null) {
            View::render('pages/not_found', [
                'activePage' => 'blogs',
                'title' => 'Blog post not found',
                'message' => 'The requested blog post does not exist.',
            ], 404);
            return;
        }

        View::render('pages/blog_detail', [
            'activePage' => 'blogs',
            'post' => $post,
        ]);
    }

    public function verifyAdmin(): void
    {
        $pin = trim((string) ($_POST['pin'] ?? ''));

        if (AdminAuth::verifyPin($pin)) {
            View::json(['status' => 'success']);
            return;
        }

        View::json(['detail' => 'Invalid PIN'], 401);
    }

    public function adminForm(?string $errorMessage = null): void
    {
        View::render('pages/admin_blog', [
            'activePage' => '',
            'errorMessage' => $errorMessage,
        ]);
    }

    public function create(): void
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));
        $imageUrlRaw = trim((string) ($_POST['image_url'] ?? ''));
        $pin = trim((string) ($_POST['pin'] ?? ''));

        if (!AdminAuth::verifyPin($pin)) {
            $this->adminForm('Invalid Admin PIN');
            return;
        }

        if ($title === '' || $content === '') {
            $this->adminForm('Title and content are required.');
            return;
        }

        $imageUrl = null;
        if ($imageUrlRaw !== '') {
            if (filter_var($imageUrlRaw, FILTER_VALIDATE_URL) === false) {
                $this->adminForm('Image URL must be a valid URL.');
                return;
            }
            $imageUrl = $imageUrlRaw;
        }

        try {
            $this->blogs->create($title, $content, $imageUrl);
        } catch (Throwable $exception) {
            error_log('Blog create DB error: ' . $exception->getMessage());
            $this->adminForm('Unable to create blog post right now. Please try again.');
            return;
        }

        $this->list('Blog post created successfully!');
    }
}
