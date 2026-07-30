<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * Serve a stored media file with CORS headers.
     * Route: GET /api/v1/media/{path}   (path can contain slashes)
     */
    public function serve(Request $request, string $path): StreamedResponse|\Illuminate\Http\Response
    {
        // path is relative inside the 'public' disk (storage/app/public/)
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Media not found.');
        }

        $fullPath = Storage::disk('public')->path($path);
        $mime     = mime_content_type($fullPath) ?: 'application/octet-stream';
        $size     = filesize($fullPath);

        return response()->stream(function () use ($fullPath) {
            $handle = fopen($fullPath, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 8192);
                flush();
            }
            fclose($handle);
        }, 200, [
            'Content-Type'                => $mime,
            'Content-Length'              => $size,
            'Cache-Control'               => 'public, max-age=86400',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods'=> 'GET, OPTIONS',
            'Access-Control-Allow-Headers'=> '*',
        ]);
    }
}
