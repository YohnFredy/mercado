<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Optimiza y comprime una imagen subida convirtiéndola a WebP.
     *
     * Uso en cualquier componente Livewire:
     *   $path = app(ImageOptimizer::class)->optimize($image, 'images/products');
     *
     * @param  int  $maxWidth  Ancho máximo en px (mantiene aspect ratio)
     * @param  int  $quality  Calidad WebP (1-100). 75 = balance ideal calidad/peso
     */
    public function optimize(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1200,
        int $quality = 75,
    ): string {
        $gdImage = $this->createImageFromFile($file);

        $wasResized = imagesx($gdImage) > $maxWidth;
        $gdImage = $this->resizeIfNeeded($gdImage, $maxWidth);

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugName = Str::slug($originalName).'-'.Str::random(8);
        $webpPath = rtrim($directory, '/').'/'.$slugName.'.webp';
        $absoluteWebpPath = Storage::disk('public')->path($webpPath);

        Storage::disk('public')->makeDirectory($directory);

        imagewebp($gdImage, $absoluteWebpPath, $quality);
        imagedestroy($gdImage);

        $originalSize = $file->getSize();
        $webpSize = filesize($absoluteWebpPath);

        // Si la imagen original ya era más liviana y no se redimensionó, conservarla tal cual
        if (! $wasResized && $originalSize <= $webpSize) {
            unlink($absoluteWebpPath);
            $extension = strtolower($file->getClientOriginalExtension());
            $originalPath = rtrim($directory, '/').'/'.$slugName.'.'.$extension;

            $file->storeAs($directory, $slugName.'.'.$extension, 'public');

            return $originalPath;
        }

        return $webpPath;
    }

    /**
     * Crea un recurso GD desde el archivo subido.
     */
    private function createImageFromFile(UploadedFile $file): \GdImage
    {
        $mime = $file->getMimeType();
        $path = $file->getRealPath();

        $gdImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/bmp' => imagecreatefrombmp($path),
            default => throw new \InvalidArgumentException("Formato de imagen no soportado: {$mime}"),
        };

        if ($gdImage === false) {
            throw new \RuntimeException('No se pudo leer la imagen.');
        }

        // Preservar transparencia para PNG/GIF/WebP
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'])) {
            imagepalettetotruecolor($gdImage);
            imagealphablending($gdImage, true);
            imagesavealpha($gdImage, true);
        }

        return $gdImage;
    }

    /**
     * Redimensiona la imagen si excede el ancho máximo, manteniendo el aspect ratio.
     */
    private function resizeIfNeeded(\GdImage $gdImage, int $maxWidth): \GdImage
    {
        $originalWidth = imagesx($gdImage);
        $originalHeight = imagesy($gdImage);

        if ($originalWidth <= $maxWidth) {
            return $gdImage;
        }

        $ratio = $maxWidth / $originalWidth;
        $newHeight = (int) round($originalHeight * $ratio);

        $resized = imagecreatetruecolor($maxWidth, $newHeight);

        // Preservar transparencia en la imagen redimensionada
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled(
            $resized, $gdImage,
            0, 0, 0, 0,
            $maxWidth, $newHeight,
            $originalWidth, $originalHeight,
        );

        imagedestroy($gdImage);

        return $resized;
    }
}
