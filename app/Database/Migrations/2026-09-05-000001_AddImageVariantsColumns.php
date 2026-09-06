<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageVariantsColumns extends Migration
{
    /** @var array<string, string> table => single image column */
    private array $singleImageTables = [
        'about_sections'       => 'image',
        'how_to_help_sections' => 'image',
        'home_page_settings'   => 'image',
        'happy_endings'        => 'image',
        'partners'             => 'image',
        'banners'              => 'image',
    ];

    /** @var array<string, string> table => gallery image column */
    private array $galleryImageTables = [
        'pet_images'  => 'image_path',
        'news_images' => 'image_path',
    ];

    public function up()
    {
        foreach ($this->singleImageTables as $table => $column) {
            $this->forge->addColumn($table, [
                $column . '_mobile' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => $column],
                $column . '_thumb'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => $column . '_mobile'],
            ]);
        }

        foreach ($this->galleryImageTables as $table => $column) {
            $this->forge->addColumn($table, [
                $column . '_mobile' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => $column],
                $column . '_thumb'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => $column . '_mobile'],
            ]);
        }
    }

    public function down()
    {
        foreach ($this->singleImageTables as $table => $column) {
            $this->forge->dropColumn($table, [$column . '_mobile', $column . '_thumb']);
        }

        foreach ($this->galleryImageTables as $table => $column) {
            $this->forge->dropColumn($table, [$column . '_mobile', $column . '_thumb']);
        }
    }
}
