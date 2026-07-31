<?php

namespace App\Controllers\Admin;

use App\Models\AboutModel;
use App\Models\ContactMessageModel;
use App\Models\HappyEndingModel;
use App\Models\HowToHelpModel;
use App\Models\PartnerModel;
use App\Models\PetModel;

class DashboardController extends AdminBaseController
{
    public function index()
    {
        $data = [
            'counts' => [
                'about' => (new AboutModel())->countAllResults(),
                'pets' => (new PetModel())->countAllResults(),
                'how_to_help' => (new HowToHelpModel())->countAllResults(),
                'happy_endings' => (new HappyEndingModel())->countAllResults(),
                'partners' => (new PartnerModel())->countAllResults(),
                'contact_messages' => (new ContactMessageModel())->countAllResults(),
            ],
            'latestMessages' => (new ContactMessageModel())->orderBy('created_at', 'DESC')->findAll(5),
        ];

        return $this->render('admin/dashboard/index', $data);
    }
}
