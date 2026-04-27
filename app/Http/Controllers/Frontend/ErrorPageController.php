<?php

namespace App\Http\Controllers\Frontend;

class ErrorPageController extends BasePageController
{
    public function notFound()
    {
        return $this->renderErrorPage(
            'pages.errors.404',
            '404',
            'Page Not Found',
            'The page you requested does not exist or may have been moved.',
            route('home')
        );
    }

    public function serverError()
    {
        return $this->renderErrorPage(
            'pages.errors.500',
            '500',
            'Server Error',
            'Something went wrong on our side. Our team has been notified.',
            route('contact.index')
        );
    }

    public function forbidden()
    {
        return $this->renderErrorPage(
            'pages.errors.403',
            '403',
            'Access Forbidden',
            'You do not have permission to access this page.',
            route('auth.login')
        );
    }

    public function maintenance()
    {
        return $this->renderErrorPage(
            'pages.errors.maintenance',
            '503',
            'Scheduled Maintenance',
            'We are performing scheduled maintenance and will be back shortly.',
            route('home')
        );
    }

    private function renderErrorPage(string $view, string $code, string $title, string $message, string $primaryUrl)
    {
        return view($view, [
            'code' => $code,
            'pageTitle' => $title,
            'pageDescription' => $message,
            'primaryAction' => [
                'label' => 'Go Back Home',
                'url' => $primaryUrl,
            ],
            'secondaryAction' => [
                'label' => 'Contact Support',
                'url' => route('contact.index'),
            ],
        ]);
    }
}
