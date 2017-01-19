<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

$file = __DIR__.'/public'.$uri;
if ($uri !== '/' && file_exists($file)) {
    $mimeTypes = [
         'txt' => 'text/plain',
         'htm' => 'text/html',
         'html' => 'text/html',
         'php' => 'text/html',
         'css' => 'text/css',
         'js' => 'application/javascript',
         'json' => 'application/json',
         'xml' => 'application/xml',
         'swf' => 'application/x-shockwave-flash',
         'flv' => 'video/x-flv',

         // images
         'png' => 'image/png',
         'jpe' => 'image/jpeg',
         'jpeg' => 'image/jpeg',
         'jpg' => 'image/jpeg',
         'gif' => 'image/gif',
         'bmp' => 'image/bmp',
         'ico' => 'image/vnd.microsoft.icon',
         'tiff' => 'image/tiff',
         'tif' => 'image/tiff',
         'svg' => 'image/svg+xml',
         'svgz' => 'image/svg+xml',

         // archives
         'zip' => 'application/zip',
         'rar' => 'application/x-rar-compressed',
         'exe' => 'application/x-msdownload',
         'msi' => 'application/x-msdownload',
         'cab' => 'application/vnd.ms-cab-compressed',

         // audio/video
         'mp3' => 'audio/mpeg',
         'qt' => 'video/quicktime',
         'mov' => 'video/quicktime',

         // adobe
         'pdf' => 'application/pdf',
         'psd' => 'image/vnd.adobe.photoshop',
         'ai' => 'application/postscript',
         'eps' => 'application/postscript',
         'ps' => 'application/postscript',

         // ms office
         'doc' => 'application/msword',
         'rtf' => 'application/rtf',
         'xls' => 'application/vnd.ms-excel',
         'ppt' => 'application/vnd.ms-powerpoint',

         // open office
         'odt' => 'application/vnd.oasis.opendocument.text',
         'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
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
