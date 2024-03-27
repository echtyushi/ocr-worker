<?php

namespace App\Http\Controllers;

use Framework\Foundation\View;
use Framework\Http\Request;
use Framework\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Default view.
     *
     * @param Request $request
     * @return View
     */
    public function home(Request $request): View
    {
        $pdfPath = resource_path('pdf/test.pdf');
        $imagePath = resource_path('images/page_1.jpg');
        $htmlPath = resource_path('html/output');

        Console::call('gswin64c', [
            '-o', $imagePath,
            '-sDEVICE=jpeg',
            '-dJPEGQ=100',
            '-r300',
            '-dFirstPage=18',
            '-dLastPage=18',
            $pdfPath
        ]);

        Console::call('tesseract', [
            $imagePath,
            $htmlPath,
            '-l eng',
            '--psm 6',
            '--oem 2'
        ]);

        echo file_get_contents(resource_path('html/output.txt'));

        return view('home');
    }
}
