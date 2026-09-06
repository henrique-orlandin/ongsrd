<?php

namespace App\Controllers\Admin;

use App\Models\BannerModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BannersController extends AdminBaseController
{
    public function index()
    {
        return $this->render('admin/banners/index', [
            'items' => (new BannerModel())
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'DESC')
                ->findAll(),
        ]);
    }

    public function new()
    {
        return $this->render('admin/banners/form', ['item' => null]);
    }

    public function create()
    {
        $rules = [
            'title' => 'permit_empty|max_length[180]',
            'link_url' => 'permit_empty|max_length[255]',
            'sort_order' => 'permit_empty|integer',
            'image' => 'uploaded[image]|is_image[image]|max_size[image,4096]',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $image = $this->uploadImage('image', 'banners');

        if ($image === null) {
            return redirect()->back()->withInput()->with('error', 'Nao foi possivel enviar a imagem do banner.');
        }

        (new BannerModel())->insert([
            'title' => trim((string) $this->request->getPost('title')),
            'image' => $image['desktop'],
            'image_mobile' => $image['mobile'],
            'image_thumb' => $image['thumb'],
            'link_url' => $this->normalizeLink((string) $this->request->getPost('link_url')),
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Banner criado.',
            ]);
        }

        return redirect()->to('/cms/banners')->with('message', 'Banner criado.');
    }

    public function edit(int $id)
    {
        $item = (new BannerModel())->find($id);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->render('admin/banners/form', ['item' => $item]);
    }

    public function update(int $id)
    {
        $model = new BannerModel();
        $item = $model->find($id);

        if ($item === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Banner nao encontrado.',
                ])->setStatusCode(404);
            }

            throw PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'title' => 'permit_empty|max_length[180]',
            'link_url' => 'permit_empty|max_length[255]',
            'sort_order' => 'permit_empty|integer',
            'image' => 'if_exist|is_image[image]|max_size[image,4096]',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newImage = $this->uploadImage('image', 'banners');

        $model->update($id, [
            'title' => trim((string) $this->request->getPost('title')),
            'image' => $newImage['desktop'] ?? $item['image'],
            'image_mobile' => $newImage['mobile'] ?? $item['image_mobile'],
            'image_thumb' => $newImage['thumb'] ?? $item['image_thumb'],
            'link_url' => $this->normalizeLink((string) $this->request->getPost('link_url')),
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        if ($newImage !== null) {
            $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Banner atualizado.',
            ]);
        }

        return redirect()->to('/cms/banners')->with('message', 'Banner atualizado.');
    }

    public function delete(int $id)
    {
        $model = new BannerModel();
        $item = $model->find($id);

        if ($item !== null) {
            $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
            $model->delete($id);
        }

        return redirect()->to('/cms/banners')->with('message', 'Banner excluido.');
    }

    private function normalizeLink(string $link): ?string
    {
        $value = trim($link);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        if (preg_match('#^https?://#i', $value) === 1) {
            return $value;
        }

        return 'https://' . $value;
    }
}
