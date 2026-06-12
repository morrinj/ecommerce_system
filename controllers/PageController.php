<?php
class PageController {
    public function help(): void {
        $pageTitle = 'Help Center';
        require __DIR__ . '/../views/frontend/pages/help.php';
    }

    public function shipping(): void {
        $pageTitle = 'Shipping Information';
        require __DIR__ . '/../views/frontend/pages/shipping.php';
    }

    public function returns(): void {
        $pageTitle = 'Returns & Exchanges';
        require __DIR__ . '/../views/frontend/pages/returns.php';
    }

    public function contact(): void {
        $pageTitle = 'Contact Us';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $subject = sanitize($_POST['subject'] ?? '');
            $message = sanitize($_POST['message'] ?? '');
            flash('success', 'Thank you! Your message has been received. We will get back to you within 24 hours.');
            redirect('contact');
        }
        require __DIR__ . '/../views/frontend/pages/contact.php';
    }

    public function about(): void {
        $pageTitle = 'About Us';
        require __DIR__ . '/../views/frontend/pages/about.php';
    }
}
