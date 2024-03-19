<?php

namespace App\Http\Controllers;

use Framework\Foundation\View;
use Framework\Http\Request;

class HomeController
{
    /**
     * Default view.
     *
     * @param Request $request
     * @return View
     */
    public function home(Request $request): View
    {
        $file = resource_path('pdf/test.pdf');

        dd($file);

        return view('home');
    }
}
