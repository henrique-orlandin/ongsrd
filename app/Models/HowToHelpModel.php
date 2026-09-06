<?php

namespace App\Models;

use CodeIgniter\Model;

class HowToHelpModel extends Model
{
    protected $table = 'how_to_help_sections';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['title', 'description', 'image', 'image_mobile', 'image_thumb'];
    protected $useTimestamps = true;
}
