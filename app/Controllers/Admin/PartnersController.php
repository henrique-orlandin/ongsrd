<?php

namespace App\Controllers\Admin;

use App\Models\PartnerModel;

class PartnersController extends AdminBaseController
{
    public function index()
    {
        return $this->render('admin/partners/index', [
            'items' => (new PartnerModel())
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'DESC')
                ->findAll(),
        ]);
    }

    public function new()
    {
        return $this->render('admin/partners/form', ['item' => null]);
    }

    public function create()
    {
        $normalizedLink = $this->normalizeLink((string) $this->request->getPost('link_url'));
        if ($normalizedLink === false) {
            $errors = ['link_url' => 'Informe uma URL valida.'];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors,
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $rules = [
            'name' => 'required|max_length[160]',
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

        $image = $this->uploadImage('image', 'partners');

        (new PartnerModel())->insert([
            'name' => (string) $this->request->getPost('name'),
            'image' => $image['desktop'] ?? null,
            'image_mobile' => $image['mobile'] ?? null,
            'image_thumb' => $image['thumb'] ?? null,
            'link_url' => $normalizedLink,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Parceiro criado.',
            ]);
        }

        return redirect()->to('/cms/partners')->with('message', 'Parceiro criado.');
    }

    public function edit(int $id)
    {
        $item = (new PartnerModel())->find($id);
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('admin/partners/form', ['item' => $item]);
    }

    public function update(int $id)
    {
        $model = new PartnerModel();
        $item = $model->find($id);

        if ($item === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parceiro não encontrado.',
                ])->setStatusCode(404);
            }

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $normalizedLink = $this->normalizeLink((string) $this->request->getPost('link_url'));
        if ($normalizedLink === false) {
            $errors = ['link_url' => 'Informe uma URL valida.'];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors,
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $rules = [
            'name' => 'required|max_length[160]',
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

        $newImage = $this->uploadImage('image', 'partners');

        $model->update($id, [
            'name' => (string) $this->request->getPost('name'),
            'image' => $newImage['desktop'] ?? $item['image'],
            'image_mobile' => $newImage['mobile'] ?? $item['image_mobile'],
            'image_thumb' => $newImage['thumb'] ?? $item['image_thumb'],
            'link_url' => $normalizedLink,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        if ($newImage !== null) {
            $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Parceiro atualizado.',
            ]);
        }

        return redirect()->to('/cms/partners')->with('message', 'Parceiro atualizado.');
    }

    public function delete(int $id)
    {
        $model = new PartnerModel();
        $item = $model->find($id);

        if ($item !== null) {
            $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
            $model->delete($id);
        }

        return redirect()->to('/cms/partners')->with('message', 'Parceiro excluído.');
    }

    /**
     * @return string|null|false
     */
    private function normalizeLink(string $link)
    {
        $value = trim($link);
        if ($value === '') {
            return null;
        }

        if (! preg_match('#^https?://#i', $value)) {
            $value = 'https://' . $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return $value;
    }
}
