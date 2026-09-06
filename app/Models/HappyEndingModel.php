<?php

namespace App\Models;

use CodeIgniter\Model;

class HappyEndingModel extends Model
{
    protected $table = 'happy_endings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['title', 'description', 'image', 'image_mobile', 'image_thumb'];
    protected $useTimestamps = true;
}
