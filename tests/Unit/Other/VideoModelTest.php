<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VideoModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_be_instantiated()
    {
        $video = new Video();

        $this->assertInstanceOf(Video::class, $video);
    }

    /** @test */
    public function it_uses_has_factory_trait()
    {
        $reflection = new \ReflectionClass(Video::class);
        $traits = $reflection->getTraitNames();

        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', $traits);
    }

    /** @test */
    public function it_can_create_video_record()
    {
        $video = Video::create([
            'title' => 'Test Video',
            'url' => 'https://example.com/video.mp4',
        ]);

        $this->assertDatabaseHas('videos', [
            'title' => 'Test Video',
        ]);
    }
}
