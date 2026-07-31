<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\HappyEndingModel;
use App\Models\HomePageSettingModel;
use App\Models\HowToHelpModel;
use App\Models\NewsImageModel;
use App\Models\NewsModel;
use App\Models\PartnerModel;
use App\Models\PetImageModel;
use App\Models\PetModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class HomeController extends BaseController
{
    public function home(): string
    {
        $homeSettings = (new HomePageSettingModel())->first();
        $partners = (new PartnerModel())
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll(8);
        $news = (new NewsModel())
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(3);
        $banners = (new BannerModel())
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('site/pages/home', [
            'seo' => [
                'title' => 'ONG SRD | Protecao Animal em Caxias do Sul',
                'description' => 'ONG SRD: protecao animal, averiguacao de denuncias, resgates e adocoes responsaveis em Caxias do Sul.',
            ],
            'homeSettings' => $homeSettings,
            'partners' => $partners,
            'news' => $this->attachNewsMainImages($news),
            'banners' => $banners,
        ]);
    }

    public function howToHelp(): string
    {
        return view('site/pages/how-to-help', [
            'seo' => [
                'title'       => 'ONG SRD | Como Ajudar',
                'description' => 'Saiba como você pode ajudar a ONG SRD a proteger animais.',
            ],
            'howToHelp' => (new HowToHelpModel())->first(),
        ]);
    }

    public function happyEndings(): string
    {
        return view('site/pages/happy-endings', [
            'seo' => [
                'title'       => 'ONG SRD | Finais Felizes',
                'description' => 'Histórias de animais resgatados que encontraram um lar.',
            ],
            'happyEndings' => (new HappyEndingModel())->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function page(string $slug): string
    {
        $safeSlug = strtolower(trim($slug));

        if ($safeSlug === '' || $safeSlug === 'cms') {
            throw PageNotFoundException::forPageNotFound();
        }

        $viewPath = APPPATH . 'Views/site/pages/' . $safeSlug . '.php';
        if (! is_file($viewPath)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('site/pages/' . $safeSlug, [
            'seo' => [
                'title' => 'ONG SRD | ' . strtoupper(str_replace('-', ' ', $safeSlug)),
                'description' => 'Pagina institucional da ONG SRD.',
            ],
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $pets
     *
     * @return array<int, array<string, mixed>>
     */
    private function attachPetThumbnails(array $pets): array
    {
        if ($pets === []) {
            return $pets;
        }

        $imageModel = new PetImageModel();

        foreach ($pets as &$pet) {
            $thumbnail = $imageModel
                ->where('pet_id', (int) $pet['id'])
                ->where('is_thumbnail', 1)
                ->first();

            if ($thumbnail === null) {
                $thumbnail = $imageModel
                    ->where('pet_id', (int) $pet['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->first();
            }

            $pet['thumbnail'] = $thumbnail['image_path'] ?? null;
        }
        unset($pet);

        return $pets;
    }

    /**
     * @param array<int, array<string, mixed>> $news
     *
     * @return array<int, array<string, mixed>>
     */
    private function attachNewsMainImages(array $news): array
    {
        if ($news === []) {
            return $news;
        }

        $imageModel = new NewsImageModel();

        foreach ($news as &$article) {
            $mainImage = $imageModel
                ->where('news_id', (int) $article['id'])
                ->where('is_main', 1)
                ->first();

            if ($mainImage === null) {
                $mainImage = $imageModel
                    ->where('news_id', (int) $article['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->first();
            }

            $article['main_image'] = $mainImage['image_path'] ?? null;
        }
        unset($article);

        return $news;
    }
}
