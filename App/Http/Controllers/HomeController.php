<?php

namespace App\Http\Controllers;

use App\Http\Services\GhostscriptService;
use Framework\Foundation\View;
use Framework\Http\JsonResponse;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Support\Console;
use Framework\Support\File;

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

    public function process(Request $request): JsonResponse
    {
        // Retrieve the image data sent from the frontend
        $image_data = $request->json()->get('image_data');

        // Decode the base64 image data
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image_data));

        // Save the image to a temporary file
        $imagePath = public_path('images/page.jpg');
        file_put_contents($imagePath, $image);

        // Execute Tesseract OCR command
        Console::call('tesseract', [
            $imagePath,
            public_path('output'),
            '-l eng',
        ]);

        // Read the output.txt file (e.g., HTML) generated by Tesseract
        $ocr_text = file_get_contents(public_path('output.txt'));

        // Delete temporary files after processing
//        File::delete([$imagePath, $htmlPath]);

        // Return the OCR text in the response
        return response()->json([
            'data' => $ocr_text
        ]);
    }
}
