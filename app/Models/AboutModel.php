<?php

namespace App\Models;

use CodeIgniter\Model;

class AboutModel extends Model
{
    protected $table = 'about_sections';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['title', 'description', 'image', 'image_mobile', 'image_thumb'];
    protected $useTimestamps = true;
}
