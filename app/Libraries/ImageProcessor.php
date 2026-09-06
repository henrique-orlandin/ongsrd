<?php

namespace App\Libraries;

use RuntimeException;

/**
 * Resizes an uploaded image into thumb/mobile/desktop variants and encodes
 * them as AVIF (falling back to WebP if the running GD build has no AVIF
 * support).
 */
class ImageProcessor
{
    private const SIZES = [
        'thumb'   => 320,
        'mobile'  => 768,
        'desktop' => 1600,
    ];

    private const MAX_DIMENSION = 8000;
    private const QUALITY = 60;

    /**
     * @return array<string, string> map of size label => generated filename (relative to $destDir)
     */
    public function process(string $sourcePath, string $destDir, string $baseName): array
    {
        if (! is_dir($destDir) && ! mkdir($destDir, 0775, true) && ! is_dir($destDir)) {
            throw new RuntimeException('Nao foi possivel criar o diretorio de destino.');
        }

        $info = getimagesize($sourcePath);
        if ($info === false) {
            throw new RuntimeException('Arquivo de imagem invalido.');
        }

        [$width, $height, $type] = $info;

        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            throw new RuntimeException('Imagem excede as dimensoes maximas permitidas.');
        }

        $source = $this->loadImage($sourcePath, $type);

        $useAvif   = function_exists('imageavif');
        $extension = $useAvif ? 'avif' : 'webp';
        $paths     = [];

        try {
            foreach (self::SIZES as $label => $maxWidth) {
                $targetWidth  = min($maxWidth, $width);
                $targetHeight = max(1, (int) round($height * ($targetWidth / $width)));

                $resized = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);

                imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

                $filename = $baseName . '-' . $label . '.' . $extension;
                $fullPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

                $saved = $useAvif
                    ? imageavif($resized, $fullPath, self::QUALITY)
                    : imagewebp($resized, $fullPath, self::QUALITY);

                imagedestroy($resized);

                if (! $saved) {
                    throw new RuntimeException("Nao foi possivel salvar a variante '{$label}'.");
                }

                $paths[$label] = $filename;
            }
        } catch (RuntimeException $e) {
            foreach ($paths as $filename) {
                @unlink(rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename);
            }

            imagedestroy($source);

            throw $e;
        }

        imagedestroy($source);

        return $paths;
    }

    /**
     * @return \GdImage
     */
    private function loadImage(string $path, int $type)
    {
        $image = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => imagecreatefrompng($path),
            IMAGETYPE_GIF  => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default        => throw new RuntimeException('Formato de imagem nao suportado.'),
        };

        if ($image === false) {
            throw new RuntimeException('Nao foi possivel ler a imagem.');
        }

        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            $orientation = (int) ($exif['Orientation'] ?? 0);

            if ($orientation > 1) {
                $rotated = match ($orientation) {
                    3 => imagerotate($image, 180, 0),
                    6 => imagerotate($image, -90, 0),
                    8 => imagerotate($image, 90, 0),
                    default => $image,
                };

                if ($rotated !== false && $rotated !== $image) {
                    imagedestroy($image);
                    $image = $rotated;
                }
            }
        }

        return $image;
    }
}
