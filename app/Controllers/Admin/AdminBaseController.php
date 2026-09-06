<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageProcessor;
use App\Models\ContactMessageModel;

abstract class AdminBaseController extends BaseController
{
    protected function render(string $view, array $data = []): string
    {
        $data['unreadMessageCount'] = $this->getUnreadMessageCount();

        return view($view, $data);
    }

    protected function getUnreadMessageCount(): int
    {
        try {
            return (new ContactMessageModel())->where('is_new', 1)->countAllResults();
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Processes an uploaded image into thumb/mobile/desktop AVIF variants and
     * stores them under writable/uploads/{$folder}.
     *
     * @return array{thumb: string, mobile: string, desktop: string}|null relative "uploads/..." paths, or null if no valid file was uploaded
     */
    protected function uploadImage(string $inputName, string $folder): ?array
    {
        $file = $this->request->getFile($inputName);

        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $folder = trim($folder, '/');
        $target = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder;
        $baseName = bin2hex(random_bytes(8));

        try {
            $variants = (new ImageProcessor())->process($file->getTempName(), $target, $baseName);
        } catch (\Throwable $e) {
            log_message('error', 'Falha ao processar imagem enviada: {msg}', ['msg' => $e->getMessage()]);

            return null;
        }

        return [
            'thumb'   => 'uploads/' . $folder . '/' . $variants['thumb'],
            'mobile'  => 'uploads/' . $folder . '/' . $variants['mobile'],
            'desktop' => 'uploads/' . $folder . '/' . $variants['desktop'],
        ];
    }

    protected function removeImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $fullPath = WRITEPATH . ltrim($path, '/\\');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * @param array<string, string|null>|null $paths
     */
    protected function removeImageSet(?array $paths): void
    {
        foreach ((array) $paths as $path) {
            $this->removeImage($path);
        }
    }
}
