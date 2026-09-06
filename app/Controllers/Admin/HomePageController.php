<?php

namespace App\Controllers\Admin;

use App\Models\HomePageSettingModel;

class HomePageController extends AdminBaseController
{
    public function edit()
    {
        $item = (new HomePageSettingModel())->first();

        return $this->render('admin/home_page/form', ['item' => $item]);
    }

    public function update()
    {
        $rules = [
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

        $model = new HomePageSettingModel();
        $item = $model->first();
        $newImage = $this->uploadImage('image', 'home-page');

        $payload = [
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
            $model->update($item['id'], $payload);
            if ($newImage !== null) {
                $this->removeImageSet([$item['image'], $item['image_mobile'], $item['image_thumb']]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configurações da página inicial salvas.',
            ]);
        }

        return redirect()->to('/cms/home-page')->with('message', 'Configurações da página inicial salvas.');
    }
}
