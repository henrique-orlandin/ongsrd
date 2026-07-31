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

        (new HappyEndingModel())->insert([
            'title' => (string) $this->request->getPost('title'),
            'description' => (string) $this->request->getPost('description'),
            'image' => $this->uploadImage('image', 'happy-endings'),
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
            'image' => $newImage ?? $item['image'],
        ]);

        if ($newImage !== null) {
            $this->removeImage($item['image']);
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
            $this->removeImage($item['image']);
            $model->delete($id);
        }

        return redirect()->to('/cms/happy-endings')->with('message', 'História excluída.');
    }
}
