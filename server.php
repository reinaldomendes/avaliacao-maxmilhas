<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

$file = __DIR__.'/public'.$uri;
if ($uri !== '/' && file_exists($file)) {
    $mimeTypes = [
        'css' => 'text/css',
        'js'  => 'text/js',
        'svg' => 'image/svg+xml',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'git' => 'image/gif',
    ];

    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : null;

    if ('' === trim($mime)) {
        $mime = mime_content_type($file);
    }
    header('Content-Type: '.$mime);

    return readfile($file);
}

require_once __DIR__.'/public/index.php';
