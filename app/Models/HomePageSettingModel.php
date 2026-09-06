<?php

namespace App\Models;

use CodeIgniter\Model;

class HomePageSettingModel extends Model
{
    protected $table = 'home_page_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['description', 'image', 'image_mobile', 'image_thumb'];
    protected $useTimestamps = true;
}
