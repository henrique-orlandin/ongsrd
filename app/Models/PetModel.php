<?php

namespace App\Models;

use CodeIgniter\Model;

class PetModel extends Model
{
    protected $table = 'pets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['name', 'type', 'age', 'size', 'gender', 'description', 'slug'];
    protected $useTimestamps = true;
}
