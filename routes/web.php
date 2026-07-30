<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::options('/storage/{path}', function () {
    return response('', 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
    ]);
})->where('path', '.*');

Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        $res = response()->json(['error' => 'File not found'], 404);
        $res->headers->set('Access-Control-Allow-Origin', '*');
        return $res;
    }
    $type = mime_content_type($fullPath) ?: 'application/octet-stream';
    $response = response()->file($fullPath);
    $response->headers->set('Content-Type', $type);
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', '*');
    return $response;
})->where('path', '.*');
