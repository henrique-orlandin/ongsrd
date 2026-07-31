<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlugToNewsAndPets extends Migration
{
    public function up()
    {
        $fields = [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('news', $fields);
        $this->forge->addColumn('pets', $fields);

        $this->db->query("UPDATE news SET slug = LOWER(REPLACE(title, ' ', '-')) WHERE slug IS NULL OR slug = ''");
        $this->db->query("UPDATE pets SET slug = LOWER(REPLACE(name, ' ', '-')) WHERE slug IS NULL OR slug = ''");
    }

    public function down()
    {
        $this->forge->dropColumn('news', 'slug');
        $this->forge->dropColumn('pets', 'slug');
    }
}
