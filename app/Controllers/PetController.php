<?php

namespace App\Controllers;

use App\Models\PetImageModel;
use App\Models\PetModel;

class PetController extends BaseController
{
    public function list(): string
    {
        $petModel = new PetModel();
        $builder  = $petModel->builder();

        $type   = trim((string) ($this->request->getGet('type') ?? ''));
        $size   = trim((string) ($this->request->getGet('size') ?? ''));
        $gender = trim((string) ($this->request->getGet('gender') ?? ''));
        $age    = trim((string) ($this->request->getGet('age') ?? ''));
        $sort   = trim((string) ($this->request->getGet('sort') ?? 'recent'));

        if ($type !== '')   { $builder->where('type', $type); }
        if ($size !== '')   { $builder->where('size', $size); }
        if ($gender !== '') { $builder->where('gender', $gender); }

        // Age is stored as a plain number plus a unit (years|months), so filtering/sorting
        // by age has to compare everyone on the same scale — months.
        $ageInMonths = "CASE WHEN age_unit = 'months' THEN age ELSE age * 12 END";

        match ($age) {
            'filhote' => $builder->where("({$ageInMonths}) <=", 12, false),
            'jovem'   => $builder->where("({$ageInMonths}) >=", 12, false)->where("({$ageInMonths}) <=", 36, false),
            'adulto'  => $builder->where("({$ageInMonths}) >", 36, false)->where("({$ageInMonths}) <=", 96, false),
            'idoso'   => $builder->where("({$ageInMonths}) >", 96, false),
            default   => null,
        };

        match ($sort) {
            'name'    => $builder->orderBy('name', 'ASC'),
            'younger' => $builder->orderBy("({$ageInMonths})", 'ASC', false),
            'older'   => $builder->orderBy("({$ageInMonths})", 'DESC', false),
            default   => $builder->orderBy('created_at', 'DESC'),
        };

        $pets = $builder->get()->getResultArray();
        $pets = $this->attachThumbnails($pets);

        return view('site/pages/adopt', [
            'seo' => [
                'title'       => 'ONG SRD | Adoção',
                'description' => 'Conheça os pets disponíveis para adoção responsável na ONG SRD.',
            ],
            'pets'    => $pets,
            'filters' => compact('type', 'size', 'gender', 'age', 'sort'),
        ]);
    }

    public function details(string $slug): string
    {
        $pet = (new PetModel())->where('slug', $slug)->first();
        if ($pet === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $images = (new PetImageModel())
            ->where('pet_id', (int) $pet['id'])
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $thumbnail = null;
        foreach ($images as $img) {
            if ((int) $img['is_thumbnail'] === 1) {
                $thumbnail = $img['image_path'];
                break;
            }
        }
        if ($thumbnail === null && $images !== []) {
            $thumbnail = $images[0]['image_path'];
        }

        return view('site/pages/adopt-details', [
            'seo' => [
                'title'       => 'ONG SRD | ' . $pet['name'],
                'description' => strip_tags(mb_strimwidth($pet['description'], 0, 155, '...')),
            ],
            'pet'       => $pet,
            'images'    => $images,
            'thumbnail' => $thumbnail,
        ]);
    }

    private function attachThumbnails(array $pets): array
    {
        if ($pets === []) {
            return $pets;
        }
        $imageModel = new PetImageModel();
        foreach ($pets as &$pet) {
            $thumb = $imageModel->where('pet_id', (int) $pet['id'])->where('is_thumbnail', 1)->first();
            if ($thumb === null) {
                $thumb = $imageModel->where('pet_id', (int) $pet['id'])->orderBy('sort_order', 'ASC')->first();
            }
            $pet['thumbnail'] = $thumb['image_path'] ?? null;
        }
        unset($pet);
        return $pets;
    }
}
