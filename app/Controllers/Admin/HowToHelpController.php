<?php

namespace App\Controllers\Admin;

use App\Models\HowToHelpModel;

class HowToHelpController extends AdminBaseController
{
    public function index()
    {
        $item = (new HowToHelpModel())->orderBy('id', 'DESC')->first();

        return $this->render('admin/how_to_help/form', ['item' => $item]);
    }

    public function update()
    {
        $model = new HowToHelpModel();
        $item = $model->orderBy('id', 'DESC')->first();

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

        $newImage = $this->uploadImage('image', 'how-to-help');
        $payload = [
            'title' => (string) $this->request->getPost('title'),
            'description' => (string) $this->request->getPost('description'),
        ];

        if ($newImage !== null) {
            $payload['image'] = $newImage['desktop'];
            $payload['image_mobile'] = $newImage['mobile'];
            $payload['image_thumb'] = $newImage['thumb'];
        }

        if ($item === null) {
            $model->insert($payload);
        } else {
            if ($newImage === null) {
                $payload['image'] = $item['image'];
                $payload['image_mobile'] = $item['image_mobile'];
                $payload['image_thumb'] = $item['image_thumb'];
            }

            $model->update($item['id'], $payload);

            if ($newImage !== null) {
                $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Seção Como Ajudar salva.',
            ]);
        }

        return redirect()->to('/cms/how-to-help')->with('message', 'Seção Como Ajudar salva.');
    }
}
