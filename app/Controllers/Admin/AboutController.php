<?php

namespace App\Controllers\Admin;

use App\Models\AboutModel;

class AboutController extends AdminBaseController
{
    public function index()
    {
        $item = (new AboutModel())->orderBy('id', 'DESC')->first();

        return $this->render('admin/about/form', ['item' => $item]);
    }

    public function update()
    {
        $model = new AboutModel();
        $item = $model->orderBy('id', 'DESC')->first();

        $rules = [
            'title' => 'required|max_length[180]',
            'description' => 'required',
            'file' => 'if_exist|is_image[file]|max_size[file,4096]',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ])->setStatusCode(422);
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        $newImage = $this->uploadImage('file', 'about');
        $payload = [
            'title' => (string) $this->request->getPost('title'),
            'description' => (string) $this->request->getPost('description'),
        ];

        if ($newImage !== null) {
            $payload['image'] = $newImage;
        }

        if ($item === null) {
            $model->insert($payload);
        } else {
            if ($newImage === null) {
                $payload['image'] = $item['image'];
            }

            $model->update($item['id'], $payload);

            if ($newImage !== null) {
                $this->removeImage($item['image']);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Seção Sobre salva.',
            ]);
        }

        return redirect()->to('/cms/about')->with('message', 'Seção Sobre salva.');
    }
}
