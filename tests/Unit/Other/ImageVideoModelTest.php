<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\ImageVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageVideoModelTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $media = ImageVideo::create([
            'file_path' => 'media/img/test.jpg',
            'file_type' => 'img',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
        ]);

        $this->assertEquals('media/img/test.jpg', $media->file_path);
        $this->assertEquals('img', $media->file_type);
        $this->assertEquals('image/jpeg', $media->mime_type);
        $this->assertEquals(1024, $media->size);
    }

    /** @test */
    public function it_can_create_image_media()
    {
        $media = ImageVideo::create([
            'file_path' => 'media/img/photo.jpg',
            'file_type' => 'img',
            'mime_type' => 'image/jpeg',
            'size' => 2048,
        ]);

        $this->assertEquals('img', $media->file_type);
        $this->assertStringContainsString('image/', $media->mime_type);
    }

    /** @test */
    public function it_can_create_video_media()
    {
        $media = ImageVideo::create([
            'file_path' => 'media/vid/video.mp4',
            'file_type' => 'vid',
            'mime_type' => 'video/mp4',
            'size' => 10240,
        ]);

        $this->assertEquals('vid', $media->file_type);
        $this->assertStringContainsString('video/', $media->mime_type);
    }

    /** @test */
    public function it_handles_media_uploads()
    {
        $file = UploadedFile::fake()->image('test.jpg', 100, 100);

        $request = new \Illuminate\Http\Request();
        $request->files->set('media', $file);

        $paths = ImageVideo::handleMediaUploads($request, 'media');

        $this->assertIsArray($paths);
        $this->assertCount(1, $paths);
    }

    /** @test */
    public function it_returns_empty_array_when_no_file()
    {
        $request = new \Illuminate\Http\Request();

        $paths = ImageVideo::handleMediaUploads($request, 'media');

        $this->assertIsArray($paths);
        $this->assertEmpty($paths);
    }

    /** @test */
    public function it_can_delete_media()
    {
        Storage::disk('public')->put('test.jpg', 'content');

        $media = ImageVideo::create([
            'file_path' => 'test.jpg',
            'file_type' => 'img',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
        ]);

        $media->deleteMedia();

        $this->assertDatabaseMissing('image_videos', ['id' => $media->id]);
        Storage::disk('public')->assertMissing('test.jpg');
    }
}
