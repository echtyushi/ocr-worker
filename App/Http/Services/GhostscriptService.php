<?php

namespace App\Http\Services;

use Framework\Support\Console;

class GhostscriptService
{
    public function get_size(string $path): string
    {
        return Console::call('gswin64c',
            [
                '-q',
                '--permit-file-read=' . $path,
                '-dNODISPLAY',
                '-c',
                '"(' . $path . ') (r) file runpdfbegin pdfpagecount = quit"'
            ]
        );
    }
}