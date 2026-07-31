<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddThumbnailToPetImages extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pet_images', [
            'is_thumbnail' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'sort_order',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pet_images', 'is_thumbnail');
    }
}
