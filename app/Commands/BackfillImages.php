<?php

namespace App\Commands;

use App\Libraries\ImageProcessor;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

/**
 * Converts every image currently referenced in the database (still sitting
 * under public/uploads from before the AVIF pipeline existed) into
 * thumb/mobile/desktop AVIF variants under writable/uploads, and updates the
 * DB rows to point at the new files.
 */
class BackfillImages extends BaseCommand
{
    protected $group       = 'Images';
    protected $name        = 'images:backfill';
    protected $description = 'Converte imagens existentes em public/uploads para AVIF (3 tamanhos) em writable/uploads.';

    /** @var array<string, array{0: string, 1: string}> table => [uploads folder, image column] */
    private array $singleImageTables = [
        'about_sections'       => ['about', 'image'],
        'how_to_help_sections' => ['how-to-help', 'image'],
        'home_page_settings'   => ['home-page', 'image'],
        'happy_endings'        => ['happy-endings', 'image'],
        'partners'             => ['partners', 'image'],
        'banners'              => ['banners', 'image'],
    ];

    /** @var array<string, array{0: string, 1: string}> table => [uploads folder, image column] */
    private array $galleryImageTables = [
        'pet_images'  => ['pets', 'image_path'],
        'news_images' => ['news', 'image_path'],
    ];

    public function run(array $params)
    {
        $db        = Database::connect();
        $processor = new ImageProcessor();

        foreach ($this->singleImageTables as $table => [$folder, $column]) {
            $this->convertTable($db, $processor, $table, $folder, $column);
        }

        foreach ($this->galleryImageTables as $table => [$folder, $column]) {
            $this->convertTable($db, $processor, $table, $folder, $column);
        }

        CLI::write('Backfill concluido.', 'green');
    }

    private function convertTable($db, ImageProcessor $processor, string $table, string $folder, string $column): void
    {
        $rows = $db->table($table)
            ->select('id, ' . $column)
            ->where($column . ' IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $this->convertRow($db, $processor, $table, $folder, $column, (int) $row['id'], (string) $row[$column]);
        }
    }

    private function convertRow($db, ImageProcessor $processor, string $table, string $folder, string $column, int $id, string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $sourcePath = ROOTPATH . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($relativePath, '/'));

        if (! is_file($sourcePath)) {
            CLI::write("Pulando {$table}#{$id}: arquivo original nao encontrado ({$relativePath}).", 'yellow');

            return;
        }

        $destDir  = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder;
        $baseName = bin2hex(random_bytes(8));

        try {
            $variants = $processor->process($sourcePath, $destDir, $baseName);
        } catch (Throwable $e) {
            CLI::write("Falha ao converter {$table}#{$id}: {$e->getMessage()}", 'red');

            return;
        }

        $db->table($table)->where('id', $id)->update([
            $column              => 'uploads/' . $folder . '/' . $variants['desktop'],
            $column . '_mobile'  => 'uploads/' . $folder . '/' . $variants['mobile'],
            $column . '_thumb'   => 'uploads/' . $folder . '/' . $variants['thumb'],
        ]);

        @unlink($sourcePath);
        CLI::write("Convertido {$table}#{$id}.", 'green');
    }
}
