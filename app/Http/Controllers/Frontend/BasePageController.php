<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

abstract class BasePageController extends Controller
{
    protected function renderPage(string $title, string $description, string $section, array $extra = [])
    {
        return view('pages.generic', array_merge([
            'pageTitle' => $title,
            'pageDescription' => $description,
            'section' => $section,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => $title],
            ],
        ], $extra));
    }
}
