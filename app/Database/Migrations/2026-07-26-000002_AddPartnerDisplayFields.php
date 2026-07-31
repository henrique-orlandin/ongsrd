<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPartnerDisplayFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('partners', [
            'link_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'image',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'link_url',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'sort_order',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('partners', ['link_url', 'sort_order', 'is_active']);
    }
}
