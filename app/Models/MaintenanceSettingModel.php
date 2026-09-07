<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceSettingModel extends Model
{
    protected $table = 'maintenance_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['is_enabled', 'message', 'updated_by'];
    protected $useTimestamps = true;
}
