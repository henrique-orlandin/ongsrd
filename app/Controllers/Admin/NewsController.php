<?php

namespace App\Controllers\Admin;

use App\Libraries\SlugGenerator;
use App\Models\NewsImageModel;
use App\Models\NewsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class NewsController extends AdminBaseController
{
    public function index()
    {
        $items = (new NewsModel())
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $imageModel = new NewsImageModel();
        foreach ($items as &$item) {
            $mainImage = $imageModel
                ->where('news_id', (int) $item['id'])
                ->where('is_main', 1)
                ->first();

            if ($mainImage === null) {
                $mainImage = $imageModel
                    ->where('news_id', (int) $item['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->first();
            }

            $item['main_image'] = $mainImage['image_path'] ?? null;
        }
        unset($item);

        return $this->render('admin/news/index', ['items' => $items]);
    }

    public function new()
    {
        return $this->render('admin/news/form', ['item' => null, 'images' => []]);
    }

    public function create()
    {
        $rules = [
            'title' => 'required|max_length[180]',
            'description' => 'required',
            'published_at' => 'required|valid_date[Y-m-d H:i]',
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

        $publishedAt = $this->normalizePublishedAt((string) $this->request->getPost('published_at'));
        if ($publishedAt === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['published_at' => 'Data de publicacao invalida.'],
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('errors', ['published_at' => 'Data de publicacao invalida.']);
        }

        $model = new NewsModel();
        $slug = SlugGenerator::generate((string) $this->request->getPost('title'));
        $model->insert([
            'title' => (string) $this->request->getPost('title'),
            'description' => (string) $this->request->getPost('description'),
            'published_at' => $publishedAt,
            'slug' => $slug,
        ]);

        $newsId = (int) $model->getInsertID();
        $error = $this->saveGallery($newsId, 0, true);
        if ($error !== null) {
            $model->delete($newsId);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error,
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('error', $error);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Noticia criada.',
            ]);
        }

        return redirect()->to('/cms/noticias')->with('message', 'Noticia criada.');
    }

    public function edit(int $id)
    {
        $item = (new NewsModel())->find($id);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $images = (new NewsImageModel())
            ->where('news_id', $id)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return $this->render('admin/news/form', ['item' => $item, 'images' => $images]);
    }

    public function update(int $id)
    {
        $model = new NewsModel();
        $item = $model->find($id);

        if ($item === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Noticia nao encontrada.',
                ])->setStatusCode(404);
            }

            throw PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'title' => 'required|max_length[180]',
            'description' => 'required',
            'published_at' => 'required|valid_date[Y-m-d H:i]',
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

        $publishedAt = $this->normalizePublishedAt((string) $this->request->getPost('published_at'));
        if ($publishedAt === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['published_at' => 'Data de publicacao invalida.'],
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('errors', ['published_at' => 'Data de publicacao invalida.']);
        }

        $baseSlug = SlugGenerator::generate((string) $this->request->getPost('title'));
        $slug     = SlugGenerator::unique($baseSlug, fn($s) => (new NewsModel())->where('slug', $s)->where('id !=', $id)->countAllResults() > 0);
        $model->update($id, [
            'title'        => (string) $this->request->getPost('title'),
            'description'  => (string) $this->request->getPost('description'),
            'published_at' => $publishedAt,
            'slug'         => $slug,
        ]);

        $existingCount = (new NewsImageModel())->where('news_id', $id)->countAllResults();
        $error = $this->saveGallery($id, $existingCount);
        if ($error !== null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error,
                ])->setStatusCode(422);
            }

            return redirect()->back()->withInput()->with('error', $error);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Noticia atualizada.',
            ]);
        }

        return redirect()->to('/cms/noticias')->with('message', 'Noticia atualizada.');
    }

    public function delete(int $id)
    {
        $model = new NewsModel();
        $imageModel = new NewsImageModel();

        $images = $imageModel->where('news_id', $id)->findAll();
        foreach ($images as $image) {
            $this->removeImage((string) $image['image_path']);
        }

        $imageModel->where('news_id', $id)->delete();
        $model->delete($id);

        return redirect()->to('/cms/noticias')->with('message', 'Noticia excluida.');
    }

    public function deleteImage(int $newsId, int $imageId)
    {
        $imageModel = new NewsImageModel();
        $image = $imageModel->where('news_id', $newsId)->find($imageId);

        if ($image !== null) {
            $wasMain = (int) $image['is_main'] === 1;
            $this->removeImage((string) $image['image_path']);
            $imageModel->delete($imageId);

            if ($wasMain) {
                $next = $imageModel
                    ->where('news_id', $newsId)
                    ->orderBy('sort_order', 'ASC')
                    ->first();

                if ($next !== null) {
                    $imageModel->update($next['id'], ['is_main' => 1]);
                }
            }
        }

        return redirect()->to('/cms/noticias/edit/' . $newsId)->with('message', 'Imagem removida.');
    }

    public function setMainImage(int $newsId, int $imageId)
    {
        $imageModel = new NewsImageModel();
        $image = $imageModel->where('news_id', $newsId)->find($imageId);

        if ($image === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Imagem nao encontrada.']);
        }

        $imageModel->where('news_id', $newsId)->set(['is_main' => 0])->update();
        $imageModel->update($imageId, ['is_main' => 1]);

        return $this->response->setJSON(['success' => true]);
    }

    private function saveGallery(int $newsId, int $existingCount = 0, bool $requireAtLeastOne = false): ?string
    {
        $files = $this->request->getFileMultiple('images');

        if ($files === null || $files === []) {
            if ($requireAtLeastOne && $existingCount === 0) {
                return 'Envie ao menos uma imagem para a noticia.';
            }

            return null;
        }

        $validFiles = [];
        foreach ($files as $file) {
            if ($file->isValid() && ! $file->hasMoved()) {
                $validFiles[] = $file;
            }
        }

        if ($validFiles === []) {
            if ($requireAtLeastOne && $existingCount === 0) {
                return 'Envie ao menos uma imagem para a noticia.';
            }

            return null;
        }

        if ($existingCount + count($validFiles) > 10) {
            return 'Cada noticia suporta no maximo 10 imagens.';
        }

        $target = ROOTPATH . 'public/uploads/news';
        if (! is_dir($target)) {
            mkdir($target, 0775, true);
        }

        $imageModel = new NewsImageModel();
        $order = $existingCount;

        foreach ($validFiles as $file) {
            if (! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) {
                return 'Apenas arquivos de imagem sao permitidos na galeria.';
            }

            $name = $file->getRandomName();
            $file->move($target, $name);

            $imageModel->insert([
                'news_id' => $newsId,
                'image_path' => 'uploads/news/' . $name,
                'sort_order' => $order,
                'is_main' => $order === 0 && $existingCount === 0 ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $order++;
        }

        return null;
    }

    private function normalizePublishedAt(string $raw): ?string
    {
        $value = trim($raw);
        if ($value === '') {
            return null;
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i', $value);
        if ($date === false) {
            return null;
        }

        return $date->format('Y-m-d H:i:s');
    }
}
