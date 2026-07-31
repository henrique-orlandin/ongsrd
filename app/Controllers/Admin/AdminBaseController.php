<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
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

    protected function uploadImage(string $inputName, string $folder): ?string
    {
        $file = $this->request->getFile($inputName);

        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $target = ROOTPATH . 'public/uploads/' . trim($folder, '/');

        if (! is_dir($target)) {
            mkdir($target, 0775, true);
        }

        $name = $file->getRandomName();
        $file->move($target, $name);

        return 'uploads/' . trim($folder, '/') . '/' . $name;
    }

    protected function removeImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $fullPath = ROOTPATH . 'public/' . ltrim($path, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
