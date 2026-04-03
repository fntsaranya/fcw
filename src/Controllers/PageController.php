<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\View;

final class PageController
{
    public function home(): void
    {
        View::render('pages/home', ['activePage' => 'home']);
    }

    public function about(): void
    {
        View::render('pages/about', ['activePage' => 'about']);
    }

    public function services(): void
    {
        View::render('pages/services', ['activePage' => 'services']);
    }

    public function resources(): void
    {
        View::render('pages/resources', ['activePage' => 'resources']);
    }
}
