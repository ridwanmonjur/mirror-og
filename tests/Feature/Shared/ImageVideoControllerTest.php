<?php

namespace Tests\Feature\Shared;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\ImageVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageVideoControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
        Storage::fake('public');
    }

    /** @test */
    public function participant_can_upload_image()
    {
        $file = UploadedFile::fake()->image('test.jpg', 800, 600);

        $response = $this->actingAs($this->participant)
            ->post('/media/upload', [
                'file' => $file,
                'type' => 'image',
            ]);

        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['url', 'media_id']);

        $this->assertDatabaseHas('image_videos', [
            'user_id' => $this->participant->id,
            'type' => 'image',
        ]);
    }

    /** @test */
    public function participant_can_upload_video()
    {
        $file = UploadedFile::fake()->create('test.mp4', 1024);

        $response = $this->actingAs($this->participant)
            ->post('/media/upload', [
                'file' => $file,
                'type' => 'video',
            ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('image_videos', [
            'user_id' => $this->participant->id,
            'type' => 'video',
        ]);
    }

    /** @test */
    public function participant_can_delete_own_media()
    {
        $media = ImageVideo::factory()->create([
            'user_id' => $this->participant->id,
            'path' => 'test/image.jpg',
        ]);

        $response = $this->actingAs($this->participant)
            ->delete("/media/{$media->id}");

        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('image_videos', [
            'id' => $media->id,
        ]);
    }

    /** @test */
    public function participant_cannot_delete_others_media()
    {
        $otherUser = $this->createParticipant();
        $media = ImageVideo::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->delete("/media/{$media->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('image_videos', [
            'id' => $media->id,
        ]);
    }

    /** @test */
    public function participant_can_stream_media()
    {
        $media = ImageVideo::factory()->create([
            'user_id' => $this->participant->id,
            'path' => 'test/video.mp4',
        ]);

        // Create a fake file in storage
        Storage::disk('public')->put('test/video.mp4', 'test content');

        $response = $this->actingAs($this->participant)
            ->get("/media/{$media->id}/stream");

        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_file_type_on_upload()
    {
        $file = UploadedFile::fake()->create('test.txt', 100);

        $response = $this->actingAs($this->participant)
            ->post('/media/upload', [
                'file' => $file,
                'type' => 'image',
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_validates_file_size_on_upload()
    {
        // Create a file larger than max size (assuming 10MB limit)
        $file = UploadedFile::fake()->create('large.jpg', 11000); // 11MB

        $response = $this->actingAs($this->participant)
            ->post('/media/upload', [
                'file' => $file,
                'type' => 'image',
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function guest_cannot_upload_media()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $this->post('/media/upload', ['file' => $file])
            ->assertRedirect('/login');
    }

    /** @test */
    public function guest_cannot_delete_media()
    {
        $media = ImageVideo::factory()->create();

        $this->delete("/media/{$media->id}")
            ->assertRedirect('/login');
    }

    /** @test */
    public function uploaded_media_has_correct_metadata()
    {
        $file = UploadedFile::fake()->image('test.jpg', 1920, 1080);

        $response = $this->actingAs($this->participant)
            ->post('/media/upload', [
                'file' => $file,
                'type' => 'image',
            ]);

        $mediaId = $response->json('media_id');
        $media = ImageVideo::find($mediaId);

        $this->assertEquals($this->participant->id, $media->user_id);
        $this->assertEquals('image', $media->type);
        $this->assertNotNull($media->path);
    }
}
