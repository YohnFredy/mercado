<?php

namespace Tests\Feature;

use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_optimizes_jpeg_to_webp(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/products');

        Storage::disk('public')->assertExists($path);
        $this->assertStringStartsWith('images/products/', $path);
        $this->assertStringEndsWith('.webp', $path);
    }

    public function test_optimizes_png_to_webp(): void
    {
        $file = UploadedFile::fake()->image('logo.png', 400, 400);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/brands');

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.webp', $path);
    }

    public function test_resizes_image_exceeding_max_width(): void
    {
        $file = UploadedFile::fake()->image('large.jpg', 3000, 2000);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/products', maxWidth: 1200);

        Storage::disk('public')->assertExists($path);

        $absolutePath = Storage::disk('public')->path($path);
        $info = getimagesize($absolutePath);

        $this->assertLessThanOrEqual(1200, $info[0], 'Width should not exceed maxWidth');
        $this->assertEquals(IMAGETYPE_WEBP, $info[2]);
    }

    public function test_preserves_small_image_dimensions(): void
    {
        $file = UploadedFile::fake()->image('small.jpg', 600, 400);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/categories', maxWidth: 1200);

        $absolutePath = Storage::disk('public')->path($path);
        $info = getimagesize($absolutePath);

        $this->assertEquals(600, $info[0], 'Width should remain unchanged for small images');
        $this->assertEquals(400, $info[1], 'Height should remain unchanged for small images');
    }

    public function test_maintains_aspect_ratio_when_resizing(): void
    {
        $file = UploadedFile::fake()->image('wide.jpg', 2400, 1200);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/products', maxWidth: 1200);

        $absolutePath = Storage::disk('public')->path($path);
        $info = getimagesize($absolutePath);

        $this->assertEquals(1200, $info[0]);
        $this->assertEquals(600, $info[1], 'Height should be halved to maintain 2:1 ratio');
    }

    public function test_rejects_unsupported_file_format(): void
    {
        $file = UploadedFile::fake()->create('document.svg', 100, 'image/svg+xml');

        $optimizer = app(ImageOptimizer::class);

        $this->expectException(\InvalidArgumentException::class);
        $optimizer->optimize($file, 'images/products');
    }

    public function test_stores_in_correct_directory(): void
    {
        $file = UploadedFile::fake()->image('test.jpg', 500, 500);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/categories');

        $this->assertStringStartsWith('images/categories/', $path);
    }

    public function test_custom_max_width_is_respected(): void
    {
        $file = UploadedFile::fake()->image('test.jpg', 1000, 800);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/products', maxWidth: 500);

        $absolutePath = Storage::disk('public')->path($path);
        $info = getimagesize($absolutePath);

        $this->assertLessThanOrEqual(500, $info[0]);
    }

    public function test_generated_filenames_are_unique(): void
    {
        $file = UploadedFile::fake()->image('test.jpg', 200, 200);

        $optimizer = app(ImageOptimizer::class);
        $path1 = $optimizer->optimize($file, 'images/products');
        $path2 = $optimizer->optimize($file, 'images/products');

        $this->assertNotEquals($path1, $path2, 'Each call should generate a unique filename');
    }

    public function test_preserves_original_filename_in_path(): void
    {
        $file = UploadedFile::fake()->image('Mi Producto Especial.jpg', 400, 400);

        $optimizer = app(ImageOptimizer::class);
        $path = $optimizer->optimize($file, 'images/products');

        $this->assertStringContainsString('mi-producto-especial-', $path);
        $this->assertStringEndsWith('.webp', $path);
    }
}
