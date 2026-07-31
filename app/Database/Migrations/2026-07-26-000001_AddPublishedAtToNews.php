<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublishedAtToNews extends Migration
{
    public function up()
    {
        $this->forge->addColumn('news', [
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'description',
            ],
        ]);

        // Backfill existing rows so current data keeps deterministic ordering.
        $this->db->query('UPDATE news SET published_at = created_at WHERE published_at IS NULL');
    }

    public function down()
    {
        $this->forge->dropColumn('news', 'published_at');
    }
}
