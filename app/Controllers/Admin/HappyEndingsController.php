<?php

namespace App\Controllers\Admin;

use App\Models\HappyEndingModel;

class HappyEndingsController extends AdminBaseController
{
    public function index()
    {
        return $this->render('admin/happy_endings/index', [
            'items' => (new HappyEndingModel())->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function new()
    {
        return $this->render('admin/happy_endings/form', ['item' => null]);
    }

    public function create()
    {
        $rules = [
            'title' => 'required|max_length[180]',
            'description' => 'required',
            'image' => 'if_exist|uploaded[image]|is_image[image]|max_size[image,4096]',
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

        $image = $this->uploadImage('image', 'happy-endings');

        (new HappyEndingModel())->insert([
            'title' => (string) $this->request->getPost('title'),
            'description' => (string) $this->request->getPost('description'),
            'image' => $image['desktop'] ?? null,
            'image_mobile' => $image['mobile'] ?? null,
            'image_thumb' => $image['thumb'] ?? null,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'História criada.',
            ]);
        }

        return redirect()->to('/cms/happy-endings')->with('message', 'História criada.');
    }

    public function edit(int $id)
    {
        $item = (new HappyEndingModel())->find($id);
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('admin/happy_endings/form', ['item' => $item]);
    }

    public function update(int $id)
    {
        $model = new HappyEndingModel();
        $item = $model->find($id);

        if ($item === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'História não encontrada.',
                ])->setStatusCode(404);
            }

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'title' => 'required|max_length[180]',
            'description' => 'required',
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

        $newImage = $this->uploadImage('image', 'happy-endings');

        $model->update($id, [
            'title' => (string) $this->request->getPost('title'),
            'description' => (string) $this->request->getPost('description'),
            'image' => $newImage['desktop'] ?? $item['image'],
            'image_mobile' => $newImage['mobile'] ?? $item['image_mobile'],
            'image_thumb' => $newImage['thumb'] ?? $item['image_thumb'],
        ]);

        if ($newImage !== null) {
            $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'História atualizada.',
            ]);
        }

        return redirect()->to('/cms/happy-endings')->with('message', 'História atualizada.');
    }

    public function delete(int $id)
    {
        $model = new HappyEndingModel();
        $item = $model->find($id);

        if ($item !== null) {
            $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
            $model->delete($id);
        }

        return redirect()->to('/cms/happy-endings')->with('message', 'História excluída.');
    }
}
