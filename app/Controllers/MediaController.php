<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class MediaController extends BaseController
{
    private const ALLOWED_FOLDERS = [
        'about', 'banners', 'happy-endings', 'home-page', 'news', 'partners', 'pets',
    ];

    private const MIME_TYPES = [
        'avif' => 'image/avif',
        'webp' => 'image/webp',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    ];

    public function show(string $folder, string $filename)
    {
        if (! in_array($folder, self::ALLOWED_FOLDERS, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime      = self::MIME_TYPES[$extension] ?? 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=31536000, immutable')
            ->setBody((string) file_get_contents($path));
    }
}
