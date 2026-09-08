<?php

namespace App\Controllers\Admin;

use App\Libraries\ImageProcessor;
use App\Libraries\SlugGenerator;
use App\Models\PetImageModel;
use App\Models\PetModel;

class PetsController extends AdminBaseController
{
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const MAX_FILE_SIZE_KB = 4096;

    public function index()
    {
        $pets = (new PetModel())->orderBy('id', 'DESC')->findAll();

        $imageModel = new PetImageModel();
        foreach ($pets as &$pet) {
            $thumb = $imageModel->where('pet_id', $pet['id'])->where('is_thumbnail', 1)->first();
            $pet['thumbnail'] = $thumb['image_path_thumb'] ?? $thumb['image_path'] ?? null;
        }
        unset($pet);

        return $this->render('admin/pets/index', ['items' => $pets]);
    }

    public function new()
    {
        return $this->render('admin/pets/form', ['item' => null, 'images' => []]);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|max_length[120]',
            'type' => 'required|in_list[Gato,Cachorro]',
            'age' => 'required|integer|greater_than_equal_to[0]',
            'age_unit' => 'required|in_list[years,months]',
            'size' => 'required|in_list[P,M,G,GG]',
            'gender' => 'required|in_list[M,F]',
            'description' => 'required',
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

        $petModel  = new PetModel();
        $baseSlug  = SlugGenerator::generate((string) $this->request->getPost('name'));
        $petModel->insert([
            'name'        => (string) $this->request->getPost('name'),
            'type'        => (string) $this->request->getPost('type'),
            'age'         => (int) $this->request->getPost('age'),
            'age_unit'    => (string) $this->request->getPost('age_unit'),
            'size'        => (string) $this->request->getPost('size'),
            'gender'      => (string) $this->request->getPost('gender'),
            'description' => (string) $this->request->getPost('description'),
            'slug'        => SlugGenerator::unique($baseSlug, fn($s) => (new PetModel())->where('slug', $s)->countAllResults() > 0),
        ]);

        $petId = (int) $petModel->getInsertID();
        $error = $this->saveGallery($petId);
        if ($error !== null) {
            $petModel->delete($petId);

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
                'message' => 'Pet criado.',
            ]);
        }

        return redirect()->to('/cms/pets')->with('message', 'Pet criado.');
    }

    public function edit(int $id)
    {
        $pet = (new PetModel())->find($id);
        if ($pet === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $images = (new PetImageModel())->where('pet_id', $id)->orderBy('sort_order', 'ASC')->findAll();

        return $this->render('admin/pets/form', ['item' => $pet, 'images' => $images]);
    }

    public function update(int $id)
    {
        $model = new PetModel();
        $pet = $model->find($id);

        if ($pet === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pet não encontrado.',
                ])->setStatusCode(404);
            }

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name' => 'required|max_length[120]',
            'type' => 'required|in_list[Gato,Cachorro]',
            'age' => 'required|integer|greater_than_equal_to[0]',
            'age_unit' => 'required|in_list[years,months]',
            'size' => 'required|in_list[P,M,G,GG]',
            'gender' => 'required|in_list[M,F]',
            'description' => 'required',
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

        $baseSlug = SlugGenerator::generate((string) $this->request->getPost('name'));
        $slug     = SlugGenerator::unique($baseSlug, fn($s) => (new PetModel())->where('slug', $s)->where('id !=', $id)->countAllResults() > 0);
        $model->update($id, [
            'name'        => (string) $this->request->getPost('name'),
            'type'        => (string) $this->request->getPost('type'),
            'age'         => (int) $this->request->getPost('age'),
            'age_unit'    => (string) $this->request->getPost('age_unit'),
            'size'        => (string) $this->request->getPost('size'),
            'gender'      => (string) $this->request->getPost('gender'),
            'description' => (string) $this->request->getPost('description'),
            'slug'        => $slug,
        ]);

        $existingCount = (new PetImageModel())->where('pet_id', $id)->countAllResults();
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
                'message' => 'Pet atualizado.',
            ]);
        }

        return redirect()->to('/cms/pets')->with('message', 'Pet atualizado.');
    }

    public function delete(int $id)
    {
        $petModel = new PetModel();
        $imageModel = new PetImageModel();

        $images = $imageModel->where('pet_id', $id)->findAll();
        foreach ($images as $image) {
            $this->removeImageSet([$image['image_path'], $image['image_path_mobile'], $image['image_path_thumb']]);
        }

        // pet_images rows cascade-delete automatically (FK ON DELETE CASCADE).
        $petModel->delete($id);

        return redirect()->to('/cms/pets')->with('message', 'Pet excluído.');
    }

    public function deleteImage(int $petId, int $imageId)
    {
        $imageModel = new PetImageModel();
        $image = $imageModel->where('pet_id', $petId)->find($imageId);

        if ($image !== null) {
            $wasThumbnail = (int) $image['is_thumbnail'] === 1;
            $this->removeImageSet([$image['image_path'], $image['image_path_mobile'], $image['image_path_thumb']]);
            $imageModel->delete($imageId);

            if ($wasThumbnail) {
                $next = $imageModel->where('pet_id', $petId)->orderBy('sort_order', 'ASC')->first();
                if ($next !== null) {
                    $imageModel->update($next['id'], ['is_thumbnail' => 1]);
                }
            }
        }

        return redirect()->to('/cms/pets/edit/' . $petId)->with('message', 'Imagem removida.');
    }

    public function setThumbnail(int $petId, int $imageId)
    {
        $imageModel = new PetImageModel();
        $image = $imageModel->where('pet_id', $petId)->find($imageId);

        if ($image === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Imagem não encontrada.']);
        }

        $imageModel->where('pet_id', $petId)->set(['is_thumbnail' => 0])->update();
        $imageModel->update($imageId, ['is_thumbnail' => 1]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Validates every file first, then processes (resizes + converts) all of
     * them, and only writes to the database inside a transaction. Any file
     * already written to disk during this call is cleaned up on failure, so
     * a bad file in the middle of a batch can no longer leave orphaned files
     * or a half-populated gallery behind.
     */
    private function saveGallery(int $petId, int $existingCount = 0): ?string
    {
        $files = $this->request->getFileMultiple('images');

        if ($files === null || $files === []) {
            return null;
        }

        $validFiles = [];
        foreach ($files as $file) {
            if (! $file->isValid() || $file->hasMoved()) {
                continue;
            }

            if (! in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
                return 'Apenas arquivos de imagem sao permitidos na galeria.';
            }

            if ($file->getSize() > self::MAX_FILE_SIZE_KB * 1024) {
                return 'Cada imagem deve ter no maximo 4MB.';
            }

            $validFiles[] = $file;
        }

        if ($validFiles === []) {
            return null;
        }

        if ($existingCount + count($validFiles) > 10) {
            return 'A galeria do pet suporta no maximo 10 imagens.';
        }

        $target = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'pets';
        $processor = new ImageProcessor();
        $processedBatches = [];

        try {
            foreach ($validFiles as $file) {
                $baseName = bin2hex(random_bytes(8));
                $processedBatches[] = $processor->process($file->getTempName(), $target, $baseName);
            }
        } catch (\Throwable $e) {
            $this->cleanupBatch('pets', $processedBatches);
            log_message('error', 'Falha ao processar galeria de pet: {msg}', ['msg' => $e->getMessage()]);

            return 'Nao foi possivel processar uma das imagens enviadas.';
        }

        $imageModel = new PetImageModel();
        $db = \Config\Database::connect();
        $db->transStart();

        $order = $existingCount;
        foreach ($processedBatches as $variants) {
            $imageModel->insert([
                'pet_id'             => $petId,
                'image_path'         => 'uploads/pets/' . $variants['desktop'],
                'image_path_mobile'  => 'uploads/pets/' . $variants['mobile'],
                'image_path_thumb'   => 'uploads/pets/' . $variants['thumb'],
                'sort_order'         => $order,
                'is_thumbnail'       => $order === 0 && $existingCount === 0 ? 1 : 0,
                'created_at'         => date('Y-m-d H:i:s'),
            ]);
            $order++;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->cleanupBatch('pets', $processedBatches);

            return 'Nao foi possivel salvar as imagens da galeria.';
        }

        return null;
    }

    /**
     * @param array<int, array<string, string>> $batches
     */
    private function cleanupBatch(string $folder, array $batches): void
    {
        foreach ($batches as $variants) {
            foreach ($variants as $filename) {
                $this->removeImage('uploads/' . $folder . '/' . $filename);
            }
        }
    }
}
