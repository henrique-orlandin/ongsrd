<?php

namespace App\Models;

use CodeIgniter\Model;

class PetImageModel extends Model
{
    protected $table = 'pet_images';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['pet_id', 'image_path', 'image_path_mobile', 'image_path_thumb', 'sort_order', 'is_thumbnail', 'created_at'];
    protected $useTimestamps = false;
}
