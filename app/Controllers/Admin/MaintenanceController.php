<?php

namespace App\Controllers\Admin;

use App\Models\MaintenanceSettingModel;

class MaintenanceController extends AdminBaseController
{
    public function edit()
    {
        $item = (new MaintenanceSettingModel())->first();

        return $this->render('admin/maintenance/form', ['item' => $item]);
    }

    public function update()
    {
        $rules = [
            'message' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new MaintenanceSettingModel();
        $item = $model->first();

        $payload = [
            'is_enabled' => $this->request->getPost('is_enabled') ? 1 : 0,
            'message'    => trim((string) $this->request->getPost('message')) ?: null,
            'updated_by' => auth()->id(),
        ];

        if ($item === null) {
            $model->insert($payload);
        } else {
            $model->update($item['id'], $payload);
        }

        $message = $payload['is_enabled'] === 1
            ? 'Modo de manutenção ativado. O site agora só pode ser visto por quem estiver logado no CMS.'
            : 'Modo de manutenção desativado. O site está visível para todos novamente.';

        return redirect()->to('/cms/settings/maintenance')->with('message', $message);
    }
}
