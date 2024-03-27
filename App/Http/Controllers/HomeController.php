<?php

namespace App\Http\Controllers;

use App\Http\Services\GhostscriptService;
use Framework\Foundation\View;
use Framework\Http\Request;
use Framework\Routing\Controller;

class HomeController extends Controller
{
    /**
     * The GhostscriptService instance.
     *
     * @var GhostscriptService
     */
    private GhostscriptService $ghostscript_service;

    /**
     * HomeController constructor.
     *
     * @param GhostscriptService $ghostscript_service The GhostscriptService instance.
     */
    public function __construct(GhostscriptService $ghostscript_service)
    {
        $this->ghostscript_service = $ghostscript_service;
    }

    /**
     * Default view.
     *
     * @param Request $request
     * @return View
     */
    public function home(Request $request): View
    {
        $size = $this->ghostscript_service->get_size(public_path() . '/pdf/test.pdf');

        return view('home',
            [
                'pdf_path' => asset('pdf/test.pdf'),
                'pdf_size' => $size
            ]
        );
    }
}
