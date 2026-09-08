<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgeUnitToPets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pets', [
            'age_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'default' => 'years',
                'after' => 'age',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pets', 'age_unit');
    }
}
