<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsImageModel extends Model
{
    protected $table = 'news_images';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['news_id', 'image_path', 'image_path_mobile', 'image_path_thumb', 'sort_order', 'is_main', 'created_at'];
    protected $useTimestamps = false;
}
