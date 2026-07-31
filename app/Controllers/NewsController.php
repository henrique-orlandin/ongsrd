<?php

namespace App\Controllers;

use App\Models\NewsImageModel;
use App\Models\NewsModel;

class NewsController extends BaseController
{
    public function list(): string
    {
        $news = (new NewsModel())
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $news = $this->attachMainImages($news);

        return view('site/pages/news', [
            'seo' => [
                'title'       => 'ONG SRD | Notícias',
                'description' => 'Notícias e atualizações da ONG SRD.',
            ],
            'news' => $news,
        ]);
    }

    public function details(string $slug): string
    {
        $news = (new NewsModel())->where('slug', $slug)->first();

        if ($news === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $images = (new NewsImageModel())
            ->where('news_id', (int) $news['id'])
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $mainImage = null;
        foreach ($images as $img) {
            if ((int) $img['is_main'] === 1) {
                $mainImage = $img['image_path'];
                break;
            }
        }
        if ($mainImage === null && $images !== []) {
            $mainImage = $images[0]['image_path'];
        }

        return view('site/pages/news-details', [
            'seo' => [
                'title'       => 'ONG SRD | ' . $news['title'],
                'description' => strip_tags(mb_strimwidth($news['description'], 0, 155, '...')),
            ],
            'news'       => $news,
            'images'     => $images,
            'mainImage'  => $mainImage,
        ]);
    }

    private function attachMainImages(array $news): array
    {
        if ($news === []) {
            return $news;
        }
        $imageModel = new NewsImageModel();
        foreach ($news as &$article) {
            $main = $imageModel->where('news_id', (int) $article['id'])->where('is_main', 1)->first();
            if ($main === null) {
                $main = $imageModel->where('news_id', (int) $article['id'])->orderBy('sort_order', 'ASC')->first();
            }
            $article['main_image'] = $main['image_path'] ?? null;
        }
        unset($article);
        return $news;
    }
}
