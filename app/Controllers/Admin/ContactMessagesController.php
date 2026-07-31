<?php

namespace App\Controllers\Admin;

use App\Models\ContactMessageModel;

class ContactMessagesController extends AdminBaseController
{
    public function index()
    {
        return $this->render('admin/contact_messages/index', [
            'items' => (new ContactMessageModel())->orderBy('is_new', 'DESC')->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function show(int $id)
    {
        $model = new ContactMessageModel();
        $item = $model->find($id);

        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ((int) $item['is_new'] === 1) {
            $model->update($id, ['is_new' => 0]);
            $item['is_new'] = 0;
        }

        return $this->render('admin/contact_messages/show', ['item' => $item]);
    }
}
