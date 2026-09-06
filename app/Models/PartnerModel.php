<?php

namespace App\Models;

use CodeIgniter\Model;

class PartnerModel extends Model
{
    protected $table = 'partners';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['name', 'image', 'image_mobile', 'image_thumb', 'link_url', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
}
