<?php

namespace App\Controllers;

use App\Models\AboutModel;

class AboutController extends BaseController
{
    public function page(): string
    {
        $about = (new AboutModel())->first();

        return view('site/pages/about', [
            'seo' => [
                'title' => 'ONG SRD | Quem Somos',
                'description' => 'Pagina institucional da ONG SRD.',
            ],
            'about' => $about,
        ]);
    }
}
